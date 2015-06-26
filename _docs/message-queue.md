---
layout: docs
title: Message Queue
meta_title: appserver.io message queue
meta_description: Using a Message-Queue gives you the power to use threads in PHP without taking care of the pitfalls!
position: 100
group: Docs
subNav:
  - title: Got mail
    href: got-mail
  - title: Send a message
    href: send-a-message
permalink: /get-started/documentation/message-queue.html
---

A Message-Queue provides a means to process long running tasks in an encapsulated context.
For example, if you want to import a lot of products in your online shop, you can send a
message to the Message-Queue, which starts the import process in the background, without
preventing the calling process to continue.

> Using a Message-Queue gives you the power to use threads without taking care of the pitfalls.

## Got mail!

Before sending a message, you have to specify what has to happen, when you received one. The
Message-Queue allows you to specify so-called `Queues`. Each `Queue` can have a receiver that
has to be a `MessageBean`. A `MessageBean` is very similar to a [@Stateless SessionBean](#@stateless-session-bean)
but has only one single point of entry, the `onMessage()` message method. Whenever a message
is sent to the queue, the Message-Queue simple pushes it on the stack. In the background a
`QueueWorker` is running in another context and queries the stack for new messages. If a new
message is available, it will be instantiated and processed.

The following example shows how to create a simple `Queue`.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<message-queues>
  <message-queue type="ImportReceiver">
    <destination>pms/import</destination>
  </message-queue>
</message-queues>
```

Save this in a file called `/opt/appserver/myapp/META-INF/message-queues.xml`. The next thing
needed is the `MessageBean`, which will allow us to receive and process a message in a separate thread.

```php
<?php

namespace Namespace\Modulename;

use AppserverIo\Psr\Pms\MessageInterface;
use AppserverIo\Messaging\AbstractMessageListener;

/**
 * @MessageDriven
 */
class ImportReceiver extends AbstractReceiver
{

    /**
     * Will be invoked when a new message for this message bean will be available.
     *
     * @param \AppserverIo\Psr\Pms\MessageInterface $message   A message this message bean is listen for
     * @param string                                $sessionId The session ID
     *
     * @return void
     * @see \AppserverIo\Psr\Pms\MessageListenerInterface::onMessage()
     */
    public function onMessage(MessageInterface $message, $sessionId)
    {
    $data = array_map('str_getcsv', file($message->getMessage()->__toString()));
    foreach ($data as $row) {
      // write the data to the database here
    }
  }
}
```

> In addition to the functionality you implement in the `onMessage()` message method, it is import you use the annotation `@MessageDriven` to register the class as a `MessageBean`. This allows the persistence container to be aware of your bean and to register and initialize it during startup of the appserver.

Running your import in a separate thread is pretty simple. The next sections demonstrates how to send a message to the `Queue`.

## Send a message

Messages are POPOs that can be sent over the network. If you want to send a message, you have
to initialize the Message-Queue Client and specify which `Queue` you want to send the message to.

Again, we will extend the `Servlet` to start an import process on a POST request. 

```php
<?php

namespace Namespace\Module;

use AppserverIo\Psr\Servlet\ServletConfig;
use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\Http\HttpServletRequest;
use AppserverIo\Psr\Servlet\Http\HttpServletResponse;
use AppserverIo\Psr\MessageQueueProtocol\Messages\StringMessage;

/**
 * This is the famous 'Hello World' as servlet implementation.
 */
class HelloWorldServlet extends HttpServlet
{

  /**
   * The name of the request parameter with the name of the CSV 
   * file containing the data to be imported.
   *
   * @var string
   */
  const PARAMETER_FILENAME = 'filename';

  /**
   * The text to be rendered.
   *
   * @var string
   */
  protected $helloWorld = '';

  /**
   * We want to have an instance of our stateful session bean injected.
   *
   * @var \Namespace\Module\MyStatefulSessionBean
   */
   protected $myStatefulSessionBean;

  /**
   * The application instance.
   *
   * @var \AppserverIo\Psr\Application\ApplicationInterface
   */
  protected $application;

  /**
   * The queue session to send a message with.
   *
   * @var \AppserverIo\MessageQueueClient\QueueSession
   * @Resource(name="pms/import")
   */
  protected $queueSender;

  /**
   * Initializes the servlet with the passed configuration.
   *
   * @param \AppserverIo\Psr\Servlet\ServletConfig $config 
   *   The configuration used for servlet initialization
   *
   * @return void
   */
  public function init(ServletConfig $config)
  {

    // call parent method
    parent::init($config);

    // prepare the text here
    $this->helloWorld = 'Hello World! (has been invoked %d times)';

    // @todo Do all the bootstrapping here, because this method will
    //       be invoked only once, when the Servlet Engines starts up
  }

  /**
   * Handles a HTTP GET request.
   *
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequest  $servletRequest  
   *   The request instance
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponse $servletResponse 
   *   The response instance
   *
   * @return void
   * @see \AppserverIo\Psr\Servlet\Http\HttpServlet::doGet()
   */
  public function doGet(
    HttpServletRequest $servletRequest,
    HttpServletResponse $servletResponse)
  {

    // start a session, because our @Stateful SessionBean
    // needs thesession-ID to bound to
    $servletRequest->getSession()->start(true);

    // render 'Hello World! (has been invoked 1 times)' 
    // for example - after the first request
    $servletResponse->appendBodyStream(
      sprintf($this->helloWorld, $this->myStatefulSessionBean->raiseMe())
    );
  }

  /**
   * Handles a HTTP POST request.
   *
   * Loads the filename containing the CSV data we want to import as request
   * parameter and sends it, wrapped as message, to the queue.
   *
   * @param \AppserverIo\Psr\Servlet\Http\ServletRequest  $servletRequest
   *   The request instance
   * @param \AppserverIo\Psr\Servlet\Http\ServletResponse $servletResponse
   *   The response instance
   *
   * @return void
   * @see \AppserverIo\Psr\Servlet\Http\HttpServlet::doPost()
   * @throws \AppserverIo\Psr\Servlet\ServletException 
   *   Is thrown because the request method is not implemented yet
   */
  public function doPost(
    HttpServletRequest $servletRequest,
    HttpServletResponse $servletResponse)
  {

    // load the filename we have to import
    $filename = $servletRequest->getParameter(
      HelloWorldServlet::PARAMETER_FILENAME
    );

    // send the name of the file to import to the message queue
    $this->queueSender->send(new StringMessage($filename), false);
  }

  /**
   * Injects the session bean by its setter method.
   *
   * @param \Namespace\Modulename\MyStatefulSessionBean $myStatefulSessionBean 
   *   The instance to inject
   * @EnterpriseBean(name="MyStatefulSessionBean")
   */
  public function setMySessionBean(MyStatefulSessionBean $myStatefulSessionBean)
  {
    $this->myStatefulSessionBean = $myStatefulSessionBean;
  }

  /**
   * Injects the application instance by its setter method.
   *
   * @param \AppserverIo\Psr\Application\ApplicationInterface $application
   *   The application instance to inject
   * @Resource(name="ApplicationInterface")
   */
  public function setApplication(ApplicationInterface $application)
  {
    $this->application = $application;
  }
}
```

> To make it simpler, as shown above, you can use the `@Resource` annotation. With this annotation, the container will inject a sender
> instance, which will send the name of the file.
