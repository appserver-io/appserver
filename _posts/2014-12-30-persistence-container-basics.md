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

### Dependency Injection
***

As we probably use DI to inject instances of [Server-Side Component Types](#server-side-component-types) we'll give you a brief introduction of how DI works in the `Persistence-Container` context. 

Dependency Injection, furthermore DI, enables developers to write cleaner, reusable and maintainable code with less coupling by injecting necessary instances at runtime instead of instantiating them in the class itself. Within the application server, each application has it's own scope and therefore a  own dependency injection container. This prevents your application from fatal errors like `Cannot redeclare class ...`.

#### What can be injected

Generally everything! The application server itself doesn't use DI, instead it provides DI as a service for the applications running within. But, before you can let the DI container inject an instance to your class, you have to register it. Registering a bean to allow using it for DI is pretty simple by using annotations.

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

Basically DI can be a manual process where you `inject` an instance, needed by another class by passing it to the constructor. Inside the `Persistence-Container`, the injection is an process you can't see, it's more a kind of magic which happens behind the scenes. So instead of manually pass the necessary instances to a classes constructor, the DI container will do that for you.

You simple has to tell the DI container what you need, let's have a look at the details.

##### Property Injection

The first possibility we have is to annotate a class property

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

With the `name` attribute of the `@EnterpriseBean`annotation you have the possibility to specify the name of the bean, you registered before by annotating it. A more detailed description about the available annotations will follow later.

> Property injection is the preferred, because of massive performance improvements. 

##### Setter Injection

The second possibility to inject an instance is setter injection.

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

What happens behind the scenes? DI can be a complicated subject, escpecially if it come together with application state! Let's try to explain the most important things in short. When the application server starts, it parses the `META-INF/classes` and `WEB-INF/classes` folders by default to find classes with supported annotations. If a class is found, the class will be registered in the application servers naming directory under the name you specify in the annotations `name` Attribute, in this example `AStatelessSessionBean`. The `name` attribute is optional, so if the developer don't specify it, the short class name will be used to register it in the naming directory.

When you want to inject that bean, you have to know the name it has been registered with. In the example above, the bean will be registered in the naming directory under `php:global/example/AStatelessSessionBean`. When using annotations to inject components, you don't have to know the fully qualified name, because the application server knows the actual context, tries to lookup the bean and injects it.

### Server-Side Component Types
***

You may wonder how it should be possible to have a component persistent in memory using PHP, a scripting language! Usually after every request the instance will be destroyed? The simple answer is: As appserver.io runs as a daemon, or better, it provides containers that runs as daemons, you can specify component, that'll be loaded when the application server starts and will be in memory until the server has been shutdown. To make it simple, we call that classes [Beans](http://en.wikipedia.org/wiki/Enterprise_JavaBeans), as they do it in Java. 

We've three different types of beans, `Session Beans`, `Message Beans` and `Entity Beans`. In version 1.0.0 we don't have support for `Entity Beans`, because we see mainly think that the responsiblity therefor is up to ORM libraries like Doctrine. So we support Doctrine to handle database persistence.

All bean types must provide a non-argument constructor, optionally no constructor.

> Based on that possibility, an Application Server like appserver.io gives you the power to distribute the components of your application over your network what includes a great and seamless scalability.

#### Session Beans

A `Session Bean` basically is a plain PHP class. You MUST not instantiate it directly, because the application server takes care of its complete lifecycle.

Therefore, if you need an instance of a SessionBean, you'll ask the application server to give you an instance, what can be done by a client or DI. In both cases, you will get a proxy to the session bean that allows you to invoke all methods, the SessionBean provides, as you can do if you would have a real instance. But, depending on your configuration, the proxy also allows you to call this method over a network as a remote method call. This makes it obvious for you if your SessionBean is on the same application server instance or on another one in your network.

When you write a Session Bean, you have to specify the type of Bean you want to implement. This can either be done by adding an annotation to the class doc block or specifing it in a configuration file. As it seems to be easier to add the annotation and, in most cases this is sufficient, we recommend that for the start.

We differ between three kinds of `Session Beans` named `Stateless`, `Stateful` and `Singleton`.

##### Stateless Session Beans (SLSBs)

A `SLSB` has NO state, only for the time you invoke a method on it. As these bean type is designed for efficiency and simplicity the developer doesn't need to take care about memory consumption, concurrency or lifecycle.

> `SLSBs` are similar to PHP`s default request behaviour, where instances are created to handle a request and will be destroyed when the request has been finished. 

###### Lifecycle

On each request an new `SLSB` instance will be created. After handling the request, the instance will simply be destroyed.

###### Example

```php
<?php

namespace AppserverIo\Apps\Example\Services;

/**
 * @Stateless
 */
class AStatelessSessionBean
{

  /**
     * Loads and returns the entity with the ID passed as parameter.
     *
     * @param integer $id The ID of the entity to load
     *
     * @return object The entity
     */
    public function load($id)
    {
      // load data from database here  
    }
}
```

##### Stateful Session Beans (SFSBs)

The `SFSB` is something between the two other types. It is stateful for the session with the ID you pass to the client when you request the instance. A `SFSB` is very useful, if you want to implement something like a shopping cart. If you declare the shopping cart instance a class member of your session bean, this will make it persistent for your session lifetime.

In opposite to a HTTP Session, `SFSBs` enables you to have session bound persistence, without the need to explicit add the data to a session object. That makes development pretty easy and more comfortable. As `SFSBs` are persisted in memory and not serialized to files, the Application Server has to take care, that in order ot minimize the number of instances carried around, are flushed when their lifetime has been reached.

###### Lifecycle

`SFSBs` are created by the container when requested and no instance, based on the passed session-ID, is available. After the request has been processed, the instance will be re-attached to the container ready to handle the next request.

> If the session is removed, times out, or the application server restarts, the data of a 'SFSB' will be lost.

##### Singleton Session Beans (SSBs)

A `SSB` will be created by the container only one time for each application. This means, whenever you'll request an instance, you'll receive the same one. If you set a variable in the Session Bean, it'll be available until you'll overwrite it, or the application server has been restarted.

###### Concurrency

Concurrency is, in case of a `SSB`, a bit more complicated. Oher than `SLSBs` and `SFSBs` the data will be shared across request, which means, that the container have to make sure, that only one request a time can access the data of a `SFSB`. Therefore requests are serialized and blocked until the instance will become available again.

###### Lifecycle

In opposite to a `SLSB`, the lifecycle of a `SSB` is a bit different. Once the instance has been created, it'll be shared between all requests, and instead of destroying the instance after each request the instance persists in memory until the application will be shutdown or restarted.

> A `SSB` gives you great power, because all data you add to a member will stay in memory until you unset it. So, if you want to share data across some requests, a `SSB` can be a good option for you. But remember: With great power, great responsibilty came together. So, always have an eye on memory consumption of your `SSB`, because YOU are responsible for that now!

###### Explicit Startup

In combination with the possiblity to have data persistent in memory, a `SSB` additionally allows you, to be pre-loaded on application startup. This can be done by adding the `Startup` annotation to the class doc block. Using the explict startup together with the possiblity to have the data persistent in memory, you'll be able to improve performance of your application, by pre-loading data from a database or a configuration file on application startup.

#### Message Beans (MDBs)

Other than session beans, you MUST not invoke `MDBs` over a proxy, but as receiver of the messages you can send. The messages are not directly sent to a `MDB` instead they are sent to a `Message Broker`. The `Message Broker` adds them to a queue until a worker, what will be separate thread, collects and processes it.

> Using `MDBs` enables you to execute long running processes `asynchronously`, because you don't have to wait for an answer after sending a message to the `Message Broker`.

In opposite to session beans, `MDBs` have to implement the `AppserverIo\Psr\Pms\MessageListenerInterface` interface. As `MDBs` are mostly used in context of a [Message-Queue](http://appserver.io/documentation/message-queue.html), this blog post will not describe functionality in deep. Instead we'll write a separate blog post that is all about `MDBs` and context of a `Message-Queue`.

#### Lifecycle Callbacks

`Lifecycle Callbacks` enables a developer to declare callback methods depending on the beans lifecycle. Actually we only support `post-construct` and `pre-destroy` callbacks. `Lifecycle Callbacks` can be configured either by annotations or the XML configuration. Declaring `Lifecycle Callbacks` by annotations is more intuitive, as you have to add the annotation directly to the method. Therfore we go with the annotations here.

> Be aware, that `Lifecycle Callbacks` are optional, must be `public`, must NOT have any arguments and can't throw checked exceptions. Exceptions will be catched by the container and result in a `critical` log message.

##### Post Construct Callback

As the beans lifecycle is controlled by the container and DI works either by property or method injection, a `Post Construct Callback` enables a developer to implement a method that'll be invoked by the container after the bean has been created and all instances injected.

> This callback can be very helpful for implementing functionalty like cache systems that need to load data from a datasource once and will update it only frequently.

##### Pre Destroy Callback

The second callback is the `Pre Destroy Callback`. This will be fired before the container destroys the instance of the bean.

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

So if we want to log each call to a `Session Bean` method, we simply have to declare it by adding an annotation like

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
