---
layout: post
title:  Persistence-Container Basics
date:   2014-12-30 18:00:00
author: wagnert
version: 1.0.0beta4
categories: [Persistence-Container]
---

Maybe you had a look at our previous post about the [Servlet-Engine Basics](/servlet-engine/2014/12/24/servlet-engine-basics.html). Beside the Servlet-Engine, the [Persistence-Container](http://appserver.io/documentation/persistence-container.html) is one of the main services appserver.io provides. The name Persistence-Container, can lead to some missunderstanding in our case, as many people think that it mostly refers to database persistence. In Java there are EJB-Containers that provide a broad set of functionalities like [Bean- or Container-Managed-Persistence](http://en.wikipedia.org/wiki/Enterprise_JavaBeans), whereas appserver.io actually only provides a small subset of the functionality as plattforms like [Wildfly](http://en.wikipedia.org/wiki/WildFly) does. Persisting data to a database is only one functionality the Persistence-Container can provide, but by far not the most important one.

### New options using a Persistence-Container
***

As not persisting data to a database is the main purpose of a Persistence-Container, we've to figure other reasons you may use it. As PHP till now was used as a scripting language, it'll lack of the possiblity to have objects, let's call them components, persistent in memory. The Persistence-Container gives you the possiblity to exactly do this. This is, admittedly, not a problem it can solve for you, but in fact it is a powerful option. This option, beside performance of course, gives you many possibilities you will not benefit from when working with the well known LAMP stack. This post is all about the possibilities the Persistence-Container provides and how they can enable you to write enterprise applications.

### Server-Side Component Types
***

You may wonder how it should be possible to have a component persistent in memory using PHP, a scripting language! Usually after every request the instance will be destroyed? The simple answer is: As appserver.io runs as a daemon, or better, it provides containers that runs as daemons, you can specify component, that'll be loaded when the application server starts and will be in memory until the server has been shutdown. To make it simple, we call that classes [Beans](http://en.wikipedia.org/wiki/Enterprise_JavaBeans), as they do it in Java. 

We've three different types of beans, `Session Beans`, `Message Beans` and `Entity Beans`. In version 1.0.0 we don't have support for `Entity Beans`, because we see mainly think that the responsiblity therefor is up to ORM libraries like Doctrine. So we support Doctrine to handle database persistence.

All bean types must provide a non-argument constructor, optionally no constructor.

> Based on that possibility, an application server like appserver.io gives you the power to distribute the components of your application over your network what includes a great and seamless scalability.

#### Session Beans

A session bean basically is a plain PHP class. You MUST not instantiate it directly, because the application server takes care of its complete lifecycle.

Therefore, an developer needs access to a session bean, he requests the application server to give him an instance, what can be done by a client or [Dependency Injection](#dependency-injection). In both cases, you will get a proxy to the session bean that allows you to invoke all methods, the session bean provides, as you can do if you would have a real instance. But, depending on your configuration, the proxy also allows you to call this method over a network as a remote method call. This makes it obvious for you if your session bean is on the same application server instance or on another one in your network.

When you write a session bean, you have to specify the type of bean you want to implement. This can either be done by adding an annotation to the class doc block or specifing it in a configuration file. As it seems to be easier to add the annotation and, in most cases this is sufficient, we recommend that for the start.

We differ between three kinds of session beans, even `Stateless`, `Stateful` and `Singleton` session beans.

##### Stateless Session Beans (SLSBs)

A `SLSB` has NO state, only for the time you invoke a method on it. As these bean type is designed for efficiency and simplicity the developer doesn't need to take care about memory consumption, concurrency or lifecycle.

> `SLSBs` behave very similar to PHP`s default request behaviour, as they are instances created to handle the request and will be destroyed when the request has been finished. 

###### Lifecycle

On each request an new `SLSB` instance will be created. After handling the request, the instance will simply be destroyed.

###### Example

So let's implement a `SLSB` that provides functionality to create a user from the arguments passed to the `createUser()` method. The `SLSB` will be registered under the name `AStatelessSessionBean` in the application servers [Naming Directory](#naming-directory). Registering a bean in the Naming Directory is necessary to use it for [Dependency Injection](#dependency-injection) that'll be explained later on.

```php
<?php

namespace AppserverIo\Example\SessionBeans;

/**
 * @Stateless(name="AStatelessSessionBean")
 */
class AStatelessSessionBean
{
  
  /**
   * Creates and returns a new md5 hash for the passed password.
   * 
   * @param string $password The password we want to hash
   * 
   * @return string The md5 hash representation of the password
   */
  public function hashPassword($password)
  {
    return md5($password);
  }
  
  /* Creates a new user, hashes the password before.
   *
   * @param string $username The username of the user to create
   * @param string $password The password bound to the user
   *
   * @return void
   */
  public function createUser($username, $password)
  {
    
    // hash the password
    $hashedPassword = $this->hashPassword($password);
    
    /*
     * Implement functionality to create user in DB
     */
  }
}
```

Then we can implement a servlet that invokes the method with the credentials loaded from the request. The servlet could look like this.

```php
<?php

namespace AppserverIo\Example\Servlets;

use AppserverIo\Psr\Servlet\ServletConfig;
use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\Http\HttpServletRequest;
use AppserverIo\Psr\Servlet\Http\HttpServletResponse;

/**
 * This servlets implements functionality to store user data by
 * invoking a SLSB instance.
 *
 * @Route(name="user", urlPattern={"/user.do", "/user.do*"})
 */
class UserServlet extends HttpServlet
{

  /**
   * The SLSB instance we want to have injected, used to store the user.
   *
   * @var \AppserverIo\Example\SessionBeans\AStatelessSessionBean
   * @EnterpriseBean(name="AStatelessSessionBean")
   */
  protected $aStatelessSessionBean;

  /**
   * Handles a HTTP POST request.
   *
   * This is a very simple example that shows how to start a new session to
   * login the a user with credentials found as request parameters.
   *
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequest  $servletRequest
   *   The request instance
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponse $servletResponse
   *   The response instance
   *
   * @return void
   * @see \AppserverIo\Psr\Servlet\Http\HttpServlet::doGet()
   */
  public function doPost(
    HttpServletRequest $servletRequest,
    HttpServletResponse $servletResponse)
  {

    // create the user by invoking the SLSB createUser() method
    $this->aStatelessSessionBean->createUser(
      $username = $servletRequest->getParameter('username'),
      $servletRequest->getParameter('password')
    );
    
    // add a message to the response
    $servletResponse->appendBodyStream("$username has successfully been created!");
  }
}
```

If we now would invoke a `POST` request on our servlet, sending a `username` and a `password` parameter, the application server will inject the `SLSB` at runtime and invoke the `doPost()` method. That will invoke the `createUser()` method on the `SLSB` and adds a success message to the response.  

##### Stateful Session Beans (SFSBs)

The `SFSB` is something between the two other types. It is bound to the session with the ID pass to the client, when an instance is requested. A `SFSB` is very useful, if you want to implement something like a shopping cart. If you declare the shopping cart instance a class member of your session bean, this will make it persistent for your session lifetime.

In opposite to a HTTP Session, `SFSBs` enables you to have session bound persistence, without the need to explicit add the data to a session object. That makes development pretty easy and more comfortable. As `SFSBs` are persisted in memory and not serialized to files, the Application Server has to take care, that in order ot minimize the number of instances carried around, are flushed when their lifetime has been reached.

###### Lifecycle

`SFSBs` are created by the container when requested and no instance, based on the passed session-ID, is available. After the request has been processed, the instance will be re-attached to the container ready to handle the next request.

> If the session is removed, times out, or the application server restarts, the data of a `SFSB` will be lost. Because `SFSBs` use the HTTP session-ID, it is necessary to start an HTTP session before you invoke methods on it.

###### Example

As described above, a `SFSB` has a state that is bound to a HTTP session. It is necessary to start the HTTP session once before accessing it. Let's imagine we've a servlet and want to a access a `SFSB` used to login a user with credentials found as request parameters. After a successfull login, the user entity should be persisted in the `SFSB` in order to protect the following `GET` requests.

```php
<?php

namespace AppserverIo\Example\SessionBeans;

/**
 * @Stateful
 */
class AStatefulSessionBean
{

  /**
   * The user, logged into the system.
   *
   * @var \AppserverIo\Apps\Example\Entities\User
   */
  protected $user;

  /**
   * Logs the user into the system.
   *
   * @param string $username The username to login
   * @param string $password The password used to login
   *
   * @return void
   */
  public function login($username, $password)
  {
    
    /*
     * Implement login functionality, e. g. check user/password in DB
     */
     
    // make user entity persistent by setting it as SFSB property
    $this->user = $user;
  }
  
  /**
   * Checks if a user has been logged into the system, if not an exception
   * will be thrown.
   *
   * @return void
   * @throws \Exception Is thrown if no user is logged into the system
   */
  public function isLoggedIn()
  {
    if (isset($this->user) === false) {
      throw new \Exception('Please log-in first!');
    }
  }
}
```

> The `SFSB` is pretty easy and is implemented as a plain old PHP class. Important is, that the user entity, once set in the `SFSB` is available at every request, as long as the HTTP session is available.

The servlet is also a very simple example that implements the login on a `POST` request, whereas the `GET` request is protected.

```php
<?php

namespace AppserverIo\Example\Servlets;

use AppserverIo\Psr\Servlet\ServletConfig;
use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\Http\HttpServletRequest;
use AppserverIo\Psr\Servlet\Http\HttpServletResponse;

/**
 * This servlets implements login functionality using a SFSB.
 *
 * @Route(name="login", urlPattern={"/login.do", "/login.do*"})
 */
class LoginServlet extends HttpServlet
{

  /**
   * The SFSB instance we want to have injected, used for login.
   *
   * @var \AppserverIo\Example\SessionBeans\AStatefulSessionBean
   * @EnterpriseBean(name="AStatefulSessionBean")
   */
  protected $aStatefulSessionBean;

  /**
   * Handles a HTTP POST request.
   *
   * This is a very simple example that shows how to start a new session to
   * login the a user with credentials found as request parameters.
   *
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequest  $servletRequest
   *   The request instance
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponse $servletResponse
   *   The response instance
   *
   * @return void
   * @see \AppserverIo\Psr\Servlet\Http\HttpServlet::doGet()
   */
  public function doPost(
    HttpServletRequest $servletRequest,
    HttpServletResponse $servletResponse)
  {

    // create a new session, if not available
    $session = $servletRequest->getSession(true);

    // start the session and add the cookie to the response
    $session->start();

    // login by invoking the SFSB login() method
    $this->aStatefulSessionBean->login(
      $servletRequest->getParameter('username'),
      $servletRequest->getParameter('password')
    );
    
    // add a message to the response
    $servletResponse->appendBodyStream("You've successfully been logged in!");
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

    try {
    
      // check for a user logged in
      $this->aStatefulSessionBean->isLoggedIn();
      
      /*
       * do some other, almost protected, stuff here
       */
    
    } catch(\Exception $e) {
      $servletResponse->setStatusCode(500);
      $servletResponse->appendBodyStream($e->getMessage());
    }
  }
}
```

> You don't have to restart the session in the `GET` request again, because the `Servlet-Engine` is aware of the session-ID passed as request header and uses it when the `SFSB` will be injected on runtime.

##### Singleton Session Beans (SSBs)

A `SSB` will be created by the container only one time for each application. This means, whenever you'll request an instance, you'll receive the same one. If you set a variable in the Session Bean, it'll be available until you'll overwrite it, or the application server has been restarted.

###### Concurrency

Concurrency is, in case of a `SSB`, a bit more complicated. Oher than `SLSBs` and `SFSBs` the data will be shared across request, which means, that the container have to make sure, that only one request a time can access the data of a `SFSB`. Therefore requests are serialized and blocked until the instance will become available again.

###### Lifecycle

In opposite to a `SLSB`, the lifecycle of a `SSB` is a bit different. Once the instance has been created, it'll be shared between all requests, and instead of destroying the instance after each request the instance persists in memory until the application will be shutdown or restarted.

> A `SSB` gives you great power, because all data you add to a member will stay in memory until you unset it. So, if you want to share data across some requests, a `SSB` can be a good option for you. But remember: With great power, great responsibilty came together. So, always have an eye on memory consumption of your `SSB`, because YOU are responsible for that now!

###### Explicit Startup

In combination with the possiblity to have data persistent in memory, a `SSB` additionally allows you, to be pre-loaded on application startup. This can be done by adding the `Startup` annotation to the class doc block. Using the explict startup together with the possiblity to have the data persistent in memory, you'll be able to improve performance of your application, by pre-loading data from a database or a configuration file on application startup.

###### Example

To give you an example of how a `SSB` can be used reasonable, we'll extend our example from the `SFSB` with a counter that tracks the number of successful logins.

```php
<?php

namespace AppserverIo\Example\SessionBeans;

/**
 * @Singleton
 */
class ASingletonSessionBean
{

  /**
   * The number of successful logins since the last restart.
   *
   * @var integer
   */
  protected $counter;

  /**
   * Raises the login counter.
   *
   * @return integer The new number of successful logins
   */
  public function raise()
  {
    return $this->counter++;
  }
}
```

To use the `SSB` in our `SFSB`, the developer can inject it by using the `@EnterpriseBeans` annotation. Additionally the `login()` method has to be customized to raise and return the number of successful logins by invoking the `raise()` method of the `SSB`.

```php
<?php

namespace AppserverIo\Example\SessionBeans;

/**
 * @Stateful
 */
class AStatefulSessionBean
{

  /**
   * The SSB instance that counts succesful logins.
   *
   * @var \AppserverIo\Example\SessionBeans\ASingletonSessionBean
   * @EnterpriseBean(name="ASingletonSessionBean")
   */
  protected $aSingletonSessionBean;

  /**
   * The user, logged into the system.
   *
   * @var \AppserverIo\Apps\Example\Entities\User
   */
  protected $user;

  /**
   * Logs the user into the system.
   *
   * @param string $username The username to login
   * @param string $password The password used to login
   *
   * @return void
   */
  public function login($username, $password)
  {
    
    /*
     * Implement login functionality, e. g. check user/password in DB
     */
    
    // make user entity persistent by setting it as SFSB property
    $this->user = $user;
    
    // raise and return the successfull login counter
    return $this->aSingletonSessionBean->raise();
  }
  
  /**
   * Checks if a user has been logged into the system, if not an exception
   * will be thrown.
   *
   * @return void
   * @throws \Exception Is thrown if no user is logged into the system
   */
  public function isLoggedIn()
  {
    if (isset($this->user) === false) {
      throw new \Exception('Please log-in first!');
    }
  }
}
```

Finally the servlet receives the number ob successul logins since the application server last restart and add's it to the response.

```php
<?php

namespace AppserverIo\Example\Servlets;

use AppserverIo\Psr\Servlet\ServletConfig;
use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\Http\HttpServletRequest;
use AppserverIo\Psr\Servlet\Http\HttpServletResponse;

/**
 * This servlets implements login functionality using a SFSB.
 *
 * @Route(name="login", urlPattern={"/login.do", "/login.do*"})
 */
class LoginServlet extends HttpServlet
{

  /**
   * The SFSB instance we want to have injected, used for login.
   *
   * @var \AppserverIo\Example\SessionBeans\AStatefulSessionBean
   * @EnterpriseBean(name="AStatefulSessionBean")
   */
  protected $aStatefulSessionBean;

  /**
   * Handles a HTTP POST request.
   *
   * This is a very simple example that shows how to start a new session to
   * login the a user with credentials found as request parameters.
   *
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequest  $servletRequest
   *   The request instance
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponse $servletResponse
   *   The response instance
   *
   * @return void
   * @see \AppserverIo\Psr\Servlet\Http\HttpServlet::doGet()
   */
  public function doPost(
    HttpServletRequest $servletRequest,
    HttpServletResponse $servletResponse)
  {

    // create a new session, if not available
    $session = $servletRequest->getSession(true);

    // start the session and add the cookie to the response
    $session->start();

    // login by invoking the SFSB login() method + receive number
    // of successful logins since last application server restart
    $successfulLogins = $this->aStatefulSessionBean->login(
      $servletRequest->getParameter('username'),
      $servletRequest->getParameter('password')
    );
    
    // add the number of successful login attempts to the response
    $servletResponse->appendBodyStream(
      "$successfulLogins login attempts since last restart!"
    );
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

    try {
    
      // check for a user logged in
      $this->aStatefulSessionBean->isLoggedIn();
      
      /*
       * do some other, almost protected, stuff here
       */
    
    } catch(\Exception $e) {
      $servletResponse->setStatusCode(500);
      $servletResponse->appendBodyStream($e->getMessage());
    }
  }
}
```

#### Message Beans (MDBs)

Other than session beans, you MUST not invoke `MDBs` over a proxy, but as receiver of the messages you can send. The messages are not directly sent to a `MDB` instead they are sent to a `Message Broker`. The `Message Broker` adds them to a queue until a worker, what will be separate thread, collects and processes it.

> Using `MDBs` enables you to execute long running processes `asynchronously`, because you don't have to wait for an answer after sending a message to the `Message Broker`.

In opposite to session beans, `MDBs` have to implement the `AppserverIo\Psr\Pms\MessageListenerInterface` interface. As `MDBs` are mostly used in context of a [Message-Queue](http://appserver.io/documentation/message-queue.html), this blog post will not describe functionality in deep. Instead we'll write a separate blog post that is all about `MDBs` and context of a `Message-Queue`.

#### Lifecycle Callbacks

`Lifecycle Callbacks` enables a developer to declare callback methods depending on the beans lifecycle. Actually we only support `post-construct` and `pre-destroy` callbacks. `Lifecycle Callbacks` can be configured either by annotations or the XML configuration. Declaring `Lifecycle Callbacks` by annotations is more intuitive, as you have to add the annotation directly to the method. Therfore we go with the annotations here.

> Be aware, that `Lifecycle Callbacks` are optional, must be `public`, must NOT have any arguments and can't throw checked exceptions. Exceptions will be catched by the container and result in a `critical` log message.

##### Post-Construct Callback

As the beans lifecycle is controlled by the container and DI works either by property or method injection, a `Post-Construct` callback enables a developer to implement a method that'll be invoked by the container after the bean has been created and all instances injected.

> This callback can be very helpful for implementing functionalty like cache systems that need to load data from a datasource once and will update it only frequently.

##### Pre-Destroy Callback

The second callback is the `Pre-Destroy` callback. This will be fired before the container destroys the instance of the bean.

##### Example

As a simple example we add a `Post-Construct` and a `Pre-Destroy` callback to our `SSB` example from the last section. 

```php
<?php

namespace AppserverIo\Example\SessionBeans;

/**
 * @Singleton
 */
class ASingletonSessionBean
{

  /**
   * The number of successful logins since the last restart.
   *
   * @var integer
   */
  protected $counter;
  
  /**
   * Lifecycle Callback that'll be invoked by the container on
   * application startup.
   *
   * @return void
   * @PostConstruct
   */
  public function startup()
  {
    // try to load the counter from a simple textfile
    if ($counter = file_get_contents('/tmp/counter.txt')) {
      $this->counter = (integer) $counter;
    } else {
      $this->counter = 0;
    }
  }

  /**
   * Lifecycle Callback that'll be invoked by the container before the
   * bean will be destroyed.
   *
   * @return void
   * @PreDestroy
   */
  public function shutdown()
  {
    // write the counter back to a simple textfile
    file_put_contents('/tmp/counter.txt', $this->counter);
  }

  /**
   * Raises the login counter.
   *
   * @return integer The new number of successful logins
   */
  public function raise()
  {
    return $this->counter++;
  }
}
```

This extends the `SSB` with some kind of real persistence by loading the counter from a simple textfile on application startup or writing it back before the `SSB` will be destroyed. 

#### Interceptors

`Interceptors` allows you to weave cross-cutting concerns into your application, without adding code to your business methods. The functionality behind the secenes is [AOP](http://appserver.io/documentation/aop.html) and an `Interceptor` is nothing else than an advice.

To add a very basic logging functionality we've to implement a simple aspect first, something like this

```php
<?php

namespace AppserverIo\Example\Aspects;

/**
 * @Aspect
 */
class LogInterceptor
{

  /**
   * Advice used to log the call to any advised method.
   *
   * @param \AppserverIo\Doppelgaenger\Entities\MethodInvocation $methodInvocation 
   *   Initially invoked method
   *
   * @return void
   */
  public function logInfo(MethodInvocation $methodInvocation)
  {

    // load class and method name
    $className = $methodInvocation->getStructureName();
    $methodName = $methodInvocation->getName()

    // log the method invocation
    $methodInvocation->getContext()
      ->getApplication()
      ->getInitialContext()
      ->getSystemLogger()
      ->info(
        sprintf('The method %s::%s is about to be called', className, methodName)
      );
  }
}
```

> Keep in mind, that the `$methodInvocation->getContext()` method gives you access to component the advice has been declared, in our example this is the `Stateless Session Bean`!

So if we want to log each call to a session bean method, we simply have to declare it by adding an annotation like

```php
<?php

namespace AppserverIo\Example\SessionBeans;

/**
 * @Stateless
 */
class LoggedBean
{

  /**
   * The application instance, injected by DI.
   *
   * @var AppserverIo\Psr\Application\ApplicationInterface
   * @Resource(name="ApplicationInterface")
   */
  protected $application;
 
  /**
   * Returns the application instance. This is necessary to access the logger
   * in the aspects logInfo() method.
   */
  public function getApplication()
  {
    return $this->application;
  }

  /**
   * A business method with an around advice that will simple log the
   * method call by invoking the logInfo() method of our aspect.
   *
   * @return void
   * @Around("advise(LogInterceptor->logInfo())")
   */
  public function someBusinessMethod()
  {
    // do something here
  }
}
```

### Naming Directory
***

Every container running in the application server has a internal registry, we call it Naming Directory. In Java this is call `Enterprise Naming Context` or `ENC`, in short. The naming directory is something like an object store, the container registers references to beans and resources.

The application server handles access to all resources an application provides. Resources can be beans, contexts, the application instance itself or the managers provided by an application. All that resources are registered in the `Naming Directory` which allows you the access them if needed.

#### Register Resources

When the application server starts, it parses the `META-INF/classes` and `WEB-INF/classes` folders by default to find classes with supported annotations. If a class is found, the class will be registered in the application servers naming directory under the name you specify in the annotations `name` Attribute, in following example `AStatelessSessionBean`. As the `name` attribute is optional, the bean will be registered in the naming directory with the short class name, if not specified.

When you want to inject that bean later, you have to know the name it has been registered with. In the following example, the bean will be registered in the naming directory under `php:global/example/AStatelessSessionBean`, whereas `example` is the name of the application. When using annotations to inject components, you don't have to know the fully qualified name, because the application server knows the actual context, tries to lookup the bean and injects it.

### Dependency Injection
***

As we probably use DI to inject instances of [Server-Side Component Types](#server-side-component-types) this section gives you a brief introduction of how DI works in the `Persistence-Container` context. 

Dependency Injection, furthermore DI, enables developers to write cleaner, reusable and maintainable code with less coupling by injecting necessary instances at runtime instead of instantiating them in the class itself. Within the application server, each application has it's own scope and therefore a  own dependency injection container. This prevents your application from fatal errors like `Cannot redeclare class ...`.

DI can be a complicated subject, escpecially if it come together with application state! Let's try to explain the most important things in short. 

#### What can be injected

The application server itself doesn't use DI, instead it provides DI as a service for the applications running within. Actually all session and message driven beans, the application instance and all managers can be injected.  But, before you can let the DI container inject an instance to your class, you have to register it. Registering beans can either be done by annotations or a deployment descriptor.

The following example shows you how to annotated a `SLSB` and make it available for DI.

```php
<?php

namespace AppserverIo\Example\SessionBeans;

/**
 * @Stateless(name="AStatelessSessionBean")
 */
class AStatelessSessionBean
{
  
  /**
   * Creates and returns a new md5 hash for the passed password.
   * 
   * @param string $password The password we want to hash
   * 
   * @return string The md5 hash representation of the password
   */
  public function hashPassword($password)
  {
    return md5($password);
  }
}
```

After register your beans, what is pretty simple when using annotations, you're ready to inject them!

#### How to inject an instance

Basically DI can be a manual process where you `inject` an instance, needed by another class by passing it to the constructor for example. Inside the `Persistence-Container`, the injection is an process you can't see, it's more some kind of magic which happens behind the scenes. So instead of manually pass the necessary instances to a classes constructor, the DI container will do that for you.

A developer simply has to tell the DI container what instance has to be injected at runtime. So let's have a look at the options he has.

##### Property Injection

The first possibility we have is to annotate a class property using the `@EnterpriseBean` annotation. The annotation accepts a `name` attribute which allows you to specify the name of a bean you've registered before. The following example shows you how to annotate a class property and let the application server inject an instance of `AStatelessSessionBean` at runtime.

```php
<?php

namespace AppserverIo\Example\SessionBeans;

/**
 * @Stateful
 */
class AStatefulSessionBean
{

  /**
   * The SessionBean instance we want to have injected.
   *
   * @var \AppserverIo\Example\SessionBeans\AStatelessSessionBean
   * @EnterpriseBean(name="AStatelessSessionBean")
   */
  protected $aStatelessSessionBean;

  /**
   * Encrypts and stores a password.
   *
   * @param string $password The password to be encrypted and stored
   *
   * @return void
   */
  public function savePassword($password)
  {
    
    // encrypt password by calling the SLSB
    $encryptedPassword = $this->aStatelessSessionBean->hashPassword($password);
    
    /*
     * Implement functionality to store password to database here
     */
  }
}
```

As the `@EnterpriseBean` annotation with the `name` attribute is not the only option to inject instances, a more detailed description about the available annotations will follow later.

> Property injection is preferred, because of massive performance improvements.

##### Setter Injection

The second possibility to inject an instance is the setter injection. Setter injection allows developers to inject instances by using methods. 

```php
<?php

namespace AppserverIo\Example\SessionBeans;

/**
 * @Stateful
 */
class AStatefulSessionBean
{

  /**
   * The SessionBean instance we want to have injected.
   *
   * @var \AppserverIo\Example\SessionBeans\AStatelessSessionBean
   */
  protected $aStatelessSessionBean;
  
  /**
   * Injects the stateless session bean.
   *
   * @param \AppserverIo\Example\SessionBeans\AStatelessSessionBean $aStatelessSessionBean
   *     The stateless session to be injected
   *
   * @return void
   * @EnterpriseBean(name="AStatelessSessionBean")
   */
  public function injectAStatelessSessionBean($aStatelessSessionBean)
  {
    $this->aStatelessSessionBean = $aStatelessSessionBean;
  }

  /**
   * Encrypts and stores a password.
   *
   * @param string $password The password to be encrypted and stored
   *
   * @return void
   */
  public function savePassword($password)
  {
    
    // encrypt password by calling the SLSB
    $encryptedPassword = $this->aStatelessSessionBean->hashPassword($password);
    
    /*
     * Implement functionality to store password to database here
     */
  }
}
```

In that example the container will inject an instance of `AStatelessSessionBean` at runtime by invoking the `injectAStatelessSessionBean` method passing the instance as argument.

> Method injection only works on methods with exactly one argument. As described above, the container don't inject a real instance of the bean, instead it injects a proxy. That proxy actually not extends the class or, if given, implements the interfaces of your bean. So do NOT type hint the argument with the class or a interface name!
