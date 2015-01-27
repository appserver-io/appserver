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

We've three different types of Beans, `Session Beans`, `Message Beans` and `Entity Beans`. In version 1.0.0 we don't have support for `Entity Beans`, because we see mainly think that the responsiblity therefor is up to ORM libraries like Doctrine. So we support Doctrine to handle database persistence.

> Based on that possibility, an Application Server like appserver.io gives you the power to distribute the components of your application over your network what includes a great and seamless scalability.

#### Session Beans

A `Session Bean` basically is a plain PHP class. You MUST not instantiate it directly, because the application server takes care of its complete lifecycle.

Therefore, if you need an instance of a SessionBean, you'll ask the application server to give you an instance, what can be done by a client or DI. In both cases, you will get a proxy to the session bean that allows you to invoke all methods, the SessionBean provides, as you can do if you would have a real instance. But, depending on your configuration, the proxy also allows you to call this method over a network as a remote method call. This makes it obvious for you if your SessionBean is on the same application server instance or on another one in your network.

When you write a Session Bean, you have to specify the type of Bean you want to implement. This can either be done by adding an annotation to the class doc block or specifing it in a configuration file. As it seems to be easier to add the annotation and, in most cases this is sufficient, we recommend that for the start.

We differ between three kinds of `Session Beans` named `Stateless`, `Stateful` and `Singleton`.

##### Stateless Session Beans (SLSBs)

A `Stateless Session Bean` will always be instantiated when requested. It has NO state, only for the time you invoke a method on it. Therefore it is the type of Session Bean that will be probably the easiest to handle.

##### Stateful Session Beans (SFSBs)

The `Stateful Session Bean` is something between the two other types. It is stateful for the session with the ID you pass to the client when you request the instance. A `Stateful Session Bean` is very useful, if you want to implement something like a shopping cart. If you declare the shopping cart instance a class member of your `Session Bean`, this will make it persistent for your session lifetime.

In opposite to a HTTP Session, `Stateful Session Beans` enables you to have session bound persistence, without the need to explicit add the data to a session object. That makes development pretty easy and more comfortable. As `Stateful Session Beans` are persisted in memory and not serialized to files, the Application Server has to take care, that in order ot minimize the number of instances carried around, are flushed when their lifetime has been reached.

##### Singleton Session Beans (SSBs)

A `Singleton Session Bean` will be created by the container only one time for each application. This means, whenever you'll request an instance, you'll receive the same one. If you set a variable in the Session Bean, it'll be available until you'll overwrite it, or the application server has been restarted.

###### Concurrency

Concurrency is, in case of a `Singleton Session Bean`, a bit more complicated. Oher than `Stateless` and `Stateful Session Beans` the data has to be shared across request, which means, that only one request a time has access to the data of a `Stateful Session Bean`. Requests are serialized and blocked until the instance will become available again. 

###### Lifecycle

In opposite to a `Stateless Session Bean`, the lifecycle of a `Singleton Session Bean` is a bit different. Once the instance has been created, it'll be shared between all requests, and instead of destroying the instance after each request the instance persists in memory until the application will be shutdown or restarted.

> A `Singleton Session Bean` gives you great power, because all data you add to a member will stay in memory until you unset it. So, if you want to share data across some requests, a `Singleton Session Bean` can be a good option for you. But remember: With great power, great responsibilty came together. So, always have an eye on memory consumption of your `Singleton Session Bean`, because YOU are responsible for that now!

###### Explicit Startup

In combination with the possiblity to have data persistent in memory, a `Singleton Session Bean` additionally allows you, to be pre-loaded on application startup. This can be done by adding the `Startup` annotation to the class doc block. Using the explict startup together with the possiblity to have the data persistent in memory, you'll be able to improve performance of your application, by pre-loading data from a database or a configuration file on application startup.

#### Message Beans (MDBs)

Other than `Session Beans`, you MUST not invoke `Message Beans` over a proxy, but as receiver of the messages you can send. The messages are not directly sent to a `Message Bean` instead they are sent to a `Message Broker`. The `Message Broker` adds them to a queue until a worker, what will be separate thread, collects and processes it.

> Using `Message Beans` enables you to execute long running processes `asynchronously`, because you don't have to wait for an answer after sending a message to the `Message Broker`.

#### Lifecycle Callbacks

`Lifecycle Callbacks` enables a developer to declare callback methods depending on the `Beans` lifecycle. Actually we only support `post-construct` and `pre-destroy` callbacks. `Lifecycle Callbacks` can be configured either by annotations or the XML configuration. Declaring `Lifecycle Callbacks` by annotations is more intuitive, as you have to add the annotation directly to the method. Therfore we go with the annotations here.

> Be aware, that `Lifecycle Callbacks` must be `public` and must NOT have any parameter.

##### Post Construct Callback

As `Beans` can also have a constructor,  lifecycle is controlled by the container and will never be accessed directly, a `Post Construct Callback` enables a developer to implement a method that works similar to a constructor. That method 

##### Pre Destroy Callback

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
