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

#### Session Beans

A Session Bean basically is a plain PHP class. You MUST not instantiate it directly, because the application server takes care of its complete lifecycle.

Therefore, if you need an instance of a SessionBean, you'll ask the application server to give you an instance, what can be done by a client or DI. In both cases, you will get a proxy to the session bean that allows you to invoke all methods, the SessionBean provides, as you can do if you would have a real instance. But, depending on your configuration, the proxy also allows you to call this method over a network as a remote method call. This makes it obvious for you if your SessionBean is on the same application server instance or on another one in your network. 

> Based on that possibility, an Application Server like appserver.io gives you the power to distribute the components of your application over your network what includes a great and seamless scalability.

##### Different types of Session Beans

When you write a Session Bean, you have to specify the type of Bean you want to implement. This can either be done by adding an annotation to the class doc block or specifing it in a configuration file. As it seems to be easier to add the annotation and, in most cases this is sufficient, we recommend that for the start.

We differ between three kinds of Session Beans named Singleton, Stateless and Stateful.

###### Singleton Session Beans (SSBs)

A `Singleton Session Bean` will be created by the container only one time for each application. This means, whenever you'll request an instance, you'll receive the same one. If you set a variable in the Session Bean, it'll be available until you'll overwrite it, or the application server has been restarted.

###### Stateless Session Beans (SLSBs)

In opposite to a `Singleton Session Bean`, a `Stateless Session Bean` will always be instantiated when you request it. It has NO state, only for the time you invoke a method on it. Therefore it is the type of Session Bean that will be probably the easiest to handle.

###### Stateful Session Beans (SFSBs)

The `Stateful Session Bean` is something between the two other types. It is stateful for the session with the ID you pass to the client when you request the instance. A `Stateful Session Bean` is very useful, if you want to implement something like a shopping cart. If you declare the shopping cart instance a class member of your `Session Bean`, this will make it persistent for your session lifetime.

In opposite to a HTTP Session, `Stateful Session Beans` enables you to have session bound persistence, without the need to explicit add the data to a session object. That makes development pretty easy and more comfortable. As `Stateful Session Beans` are persisted in memory and not serialized to files, the Application Server has to take care, that in order ot minimize the number of instances carried around, are flushed when their lifetime has been reached.

#### Message Beans (MDBs)

Other than `Session Beans`, you MUST not invoke `Message Beans` over a proxy, but as receiver of the messages you can send. The messages are not directly sent to a `Message Bean` instead they are sent to a `Message Broker`. The `Message Broker` adds them to a queue until a worker, what will be separate thread, collects and processes it.

> Using `Message Beans` enables you to process long running processes `asynchronously`, because you don't have to wait for an answer after sending a message to the `Message Broker`.
