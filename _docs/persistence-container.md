---
layout: docs
title: Persistence Container
position: 80
group: Docs
permalink: /documentation/persistence-container.html
---

> [Singleton SessionBean](#singleton-sessionbean)
> [Stateless SessionBean](#stateless-sessionbean)  
> [Stateful SessionBean](#stateful-sessionbean)  
> [Example](#example)  

As described in the introduction the application is designed inside a runtime environment like
an application server as appserver.io is. The following example gives you a short introduction 
how you can create a stateful session bean and the way you can invoke it's method on client side.

First thing you've to do is to create your SessionBean. What is a `SessionBean`? It's not simple
to describe it in only a few words, but I'll try. A `SessionBean` basically is a plain PHP class.
You MUST not instantiate it directly, because the application server takes care of its complete
lifecycle. Therefore, if you need an instance of a SessionBean, you'll ask the application server 
to give you an instance. This can be done by a [client](<https://github.com/appserver-io/persistencecontainerclient>).

The persistence container client will give you a proxy to the session bean that allows you to
invoke all methods the SessionBean provides as you can do if you would have a real instance. But
the proxy also allows you to call this method over a network as remote method call. Using the 
persistence container client makes it obvious for you if your SessionBean is on the same 
application server instance or on another one in your network. This gives you the possibility
to distribute the components of your application over your network what includes a great and
seamless scalability.

You have to tell the persistence container of the type the `SessionBean` should have. This MUST 
be done by simply add an annotation to the class doc block. The possible annotations therefore 
are

* @Singleton
* @Stateless
* @Stateful

The `SessionBean` types are self explanatory I think.

## Singleton SessionBean

A SessionBean with a `@Singleton` annotation will be created only one time for each application.
This means, whenever you'll request an instance, you'll receive the same one. If you set a
variable in the `SessionBean`, it'll be available until you'll overwrite it, or the application
server has been restarted.

## Stateless SessionBean

In opposite to a `SessionBean` with a `@Singleton` annotation, a `SessionBean` with a `@Stateless` annotation will always be instantiated when you request it. It has NO state, only for the time you invoke a method on it.

## Stateful SessionBean

The `@Stateful` `SessionBean` is something between the other types. It is stateful for the session
with the ID you pass to the client when you request the instance. A stateful `SessionBean` is 
useful if you want to implement something like a shopping cart. If you declare the shopping cart 
instance a class member of your SessionBean makes it persistent for your session lifetime.

## Example

The following example shows you a really simple implementation of a stateful `SessionBean` providing
a counter that'll be raised whenever you call the `raiseMe()` method.

```php
<?php

namespace Namespace\Module;

/**
 * This is demo implementation of stateful session bean.
 *
 * @Stateful
 */
class MyStatefulSessionBean
{

  /**
   * Stateful counter that exists as long as your session exists.
   *
   * @var integer
   */
  protected $counter = 0;

  /**
   * Example method that raises the counter by one each time you'll invoke it.
   *
   * @return integer The raised counter
   */
  public function raiseMe()
  {
    return $this->counter++;
  }
}
```

Save the `SessionBean` in `/opt/appserver/myapp/META-INF/classes/Namespace/Module/MyStatefulSessionBean.php`.

As described above, you MUST not instantiate it directly. To request an instance of the `SessionBean`
you MUST use the persistence container client. With the `lookup()` method you'll receive a proxy to
your `SessionBean`, on that you can invoke the methods as you can do with a real instance.

To develop our `HelloWorldServlet` further, let's raise the counter with each request to the `Servlet`. To
do this, we've to refactor the `doGet()` method 

```php
<?php

namespace Namespace\Module;

use AppserverIo\Psr\Servlet\ServletConfig;
use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\Http\HttpServletRequest;
use AppserverIo\Psr\Servlet\Http\HttpServletResponse;

/**
 * This is the famous 'Hello World' as servlet implementation.
 */
class HelloWorldServlet extends HttpServlet
{

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
   * Initializes the servlet with the passed configuration.
   *
   * @param \AppserverIo\Psr\Servlet\ServletConfig $config 
   *   The configuration to initialize the servlet with
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
    //       be invoked only once when the Servlet Engines starts up
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
}
```

That's it!

> As we use a `@Stateful` `SessionBean` in this example, we MUST start a session the container can
> bind the `SessionBean` to. If you would use a @Singleton SessionBean, the effect would be the
> same, but it will not be necessary to start the session. In consequence, each `Servlet` that 
> invokes the `raiseMe()` method on the `SessionBean` would raise the counter.