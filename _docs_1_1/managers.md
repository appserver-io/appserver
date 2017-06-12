---
layout: docs_1_1
title: Managers
meta_title: appserver.io Managers
meta_description: Managers provide and encapsulates most of appserver.io  application server core functionality, like dependency injection, as well as bean, session and authentication handling.
position: 48
group: Docs
subNav:
  - title: Object Manager
    href: object-manager
  - title: Dependency Injection Provider
    href: dependency-injection-provider
  - title: Bean Manager
    href: bean-manager
  - title: Timer Service
    href: timer-service
  - title: Persistence Manager
    href: persistence-manager
  - title: Message Queue Manager
    href: message-queue-manager
  - title: Servlet Manager
    href: servlet-manager
  - title: Session Manager
    href: session-manager
  - title: Authentication Manager
    href: authentication-manager
permalink: /get-started/documentation/1.1/configuration.html
---

The default managers implements the main part of the functionality appserver.io provides when writing applications.
  
The managers itself provides mostly mandatory functionality to write powerful web applications. Manager classes are, in most cased, the bridge to the infrastructure. For example, the Servlet, the Session and the Authentication Manager needs the Servlet Engine to work properly. The functionality of the Bean Manager, the Message Queue, the Timer Service and the Persistence Manager are bound to the Persistence Container instead.

For an overview of the possible configuration options for the managers, have a look at the `etc/appserver/conf.d/context.xml` template file. Below is a short description for the available managers and the configuration options for the most important one's.

appserver.io allows the implementation and registration of additional managers. To integrate a framework like Symfony the best approach will probably be to write a custom manager that initializes the necessary instances at startup and invokes the necessary framework classes at runtime.

As mentioned before, appserver.io comes with a set of default managers, tha provides a wide range of functionality.

## Generic Managers

Generic Manager implementations are used in nearly every service, as they provide almost low level functionality.

### Object Manager

Holds the object descriptions for the application's servlets and beans. The object descriptions are necessary for object creation and dependency injection. The object manager also allows the configuration of additional descriptor implementations, e. g. if someone what's to implement a new framework and appserver.io needs knowledge about it's classes for DI purposes.

For example, the Rout.Lt framework add's another descriptor `AppserverIo\Routlt\Description\PathDescriptor` to the Object Manager by extending the configuration with

```xml
<manager
  name="ObjectManagerInterface"
  type="AppserverIo\Appserver\DependencyInjectionContainer\ObjectManager"
  factory="AppserverIo\Appserver\DependencyInjectionContainer\ObjectManagerFactory">
  <descriptors>
    <descriptor>AppserverIo\Description\ServletDescriptor</descriptor>
    <descriptor>AppserverIo\Description\MessageDrivenBeanDescriptor</descriptor>
    <descriptor>AppserverIo\Description\StatefulSessionBeanDescriptor</descriptor>
    <descriptor>AppserverIo\Description\SingletonSessionBeanDescriptor</descriptor>
    <descriptor>AppserverIo\Description\StatelessSessionBeanDescriptor</descriptor>
    <descriptor>AppserverIo\Routlt\Description\PathDescriptor</descriptor>
  </descriptors>
</manager>
```

The example above is copied from the [example](<https://github.com/appserver-io-apps/example>) application package.

### Dependency Injection Provider

Handles the dependency injection for Servlets and Beans and needs the [Object Manager](#object-manager) therefore. 

In contrast to DI container of a framework like Symfony, the appserver.io provide has to handle different Session Bean types that will exists as long as the container runs, like Singleton Session Beans, or even longer like Stateful Session Beans. This makes things a little bit more complicated, as these bean types are managed by the [Bean Manager](#bean-manager) and will get there dependencies injected, whenever a new or an existing instance will be requested. 

```xml
<manager 
  name="ProviderInterface" 
  type="AppserverIo\Appserver\DependencyInjectionContainer\Provider"
  factory="AppserverIo\Appserver\DependencyInjectionContainer\ProviderFactory"/>  
```

Up to version 1.1.4 the Dependency Injection Provider will only support property and setter injection. With version 1.1.5, the DI provider will implement [PSR-11](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-11-container.md) and comes with support for constructor injection as well as DI support for classes that are **NOT** application server specific bean types.

Read more about how that topic in the chapter [Dependeny Injection](<{{ "/get-started/documentation/dependency-injection.html" | prepend: site.baseurl }}>). 

## Servlet Engine related Managers

The [Servlet Engine](<{{ "/get-started/documentation/servlet-engine.html" | prepend: site.baseurl }}>) represents a powerful service, that supports developers when implemening web applications following the MVC pattern by providing Routing, Session Handling as well as Authentication + Authorization.

The follwing Manager implementations are responsible for the core functionality of the Servlet Engine.

### Servlet Manager

Provides configuration, initialization and lookup functionality for Servlets. As Servlet has to be configured in the `WEB-INF/web.xml`, the Servlet Manager allows to specify a `baseDirectory` parameter as well as a directory that will be parsed for Servlet classes on start up.

```xml
<manager 
  name="ServletContextInterface" 
  type="AppserverIo\Appserver\ServletEngine\ServletManager"
  factory="AppserverIo\Appserver\ServletEngine\ServletManagerFactory"
  contextFactory="AppserverIo\Appserver\Naming\NamingContextFactory">
  <params>
    <param name="baseDirectory" type="string">WEB-INF</param>
  </params>
  <directories>
    <directory>/WEB-INF/classes</directory>
  </directories>
</manager>
```

### Session Manager

Handles servlet session configuration and persistence. The session manager configuration allows several session handlers that are responsible to persist the user sessions to a persistence layer implementation. Each session handler allows a `sessionMarshaller` parameter that expectes the FQCN of a marshaller that implements the functionality to serializing/unserialize the session data.

If more than one session handler has been specified, the session data will be persisted in all of them, so that it'll be possible to create a session persistence chain e. g. to improve performance by storing session date in Redis first and have the filesystem as fallback.

```xml
<manager 
  name="SessionManagerInterface" 
  type="AppserverIo\Appserver\ServletEngine\StandardSessionManager"
  factory="AppserverIo\Appserver\ServletEngine\StandardSessionManagerFactory">
  <sessionHandlers>
    <sessionHandler 
      name="filesystem"
	   type="AppserverIo\Appserver\ServletEngine\Session\FilesystemSessionHandler"
	   factory="AppserverIo\Appserver\ServletEngine\Session\SessionHandlerFactory">
      <!-- params>
         <param 
           name="sessionMarshaller"
           type="string">AppserverIo\Appserver\ServletEngine\StandardSessionMarshaller</param>
      </params -->
    </sessionHandler>
  </sessionHandlers>
</manager>
```

### Authentication Manager

Handles servlet authentication and authorization. The authentication manager initializes the authenticators and maps them to the incoming requests, to autenthicate it against the login modules configured for the security domain.

As the Session Manager for the session handlers, the Authentication Manager also provides the configuration of a login module chain. When multiple login modules have been configured, a login attempt tries to authenticate agains one after onther, until a successfull login has been possible. Depending on the login module configuration, it'll also possible to make sure that authentication to all configured login moduls **MUST** be successfull and throw an exception if not.

#### Database Authentication

The following configuration is an example configuration and shows, how the autentication manager can be configured using the `DatabasePDOLoginModule` to authenticate incoming requests against a database that has to be defined by a datasource named `appserver.io-example-application`.

```xml
<manager 
  name="AuthenticationManagerInterface" 
  type="AppserverIo\Appserver\ServletEngine\Security\StandardAuthenticationManager" 
  factory="AppserverIo\Appserver\ServletEngine\Security\StandardAuthenticationManagerFactory">
  <securityDomains>
    <securityDomain name="example-realm">
      <authConfig>
        <loginModules>
          <loginModule 
            type="AppserverIo\Appserver\ServletEngine\Security\Auth\Spi\DatabasePDOLoginModule" 
            flag="required">
            <params>
              <param 
                name="lookupName" 
                type="string">
                  php:env/${container.name}/ds/appserver.io-example-application
              </param>
              <param 
                name="principalsQuery" 
                type="string">
                  select password from user where username = ?
              </param>
              <param 
                name="rolesQuery" 
                type="string">
                  select r.name, 'Roles' from role r inner join user p on r.userIdFk = p.userId where p.username = ?
              </param>
              <param name="hashAlgorithm" type="string">SHA-512</param>
              <param name="hashEncoding" type="string">hex</param>
              <param name="password-stacking" type="string">useFirstPass</param>
            </params>
          </loginModule>
        </loginModules>
      </authConfig>
    </securityDomain>
  </securityDomains>
</manager>
```

The matching datasource, that has to be deployed either globally or by the application itself, could to look like the following example

```xml
<?xml version="1.0" encoding="UTF-8"?>
<datasources xmlns="http://www.appserver.io/appserver">
  <datasource name="appserver.io-example-application">
    <database>
      <driver>pdo_sqlite</driver>
      <path>META-INF/data/appserver_ApplicationServer.sqlite</path>
      <memory>false</memory>
    </database>
  </datasource>
</datasources>
```

and has also been copied from our [example](<https://github.com/appserver-io-apps/example>) application package.

#### LDAP Authentication

Up with version 1.1.4 appserver.io provides a LDAP login module that allows login to an LDAP backend e. g. OpenLDAP with a simple configuration like

```xml
<manager 
  name="AuthenticationManagerInterface" 
  type="AppserverIo\Appserver\ServletEngine\Security\StandardAuthenticationManager" 
  factory="AppserverIo\Appserver\ServletEngine\Security\StandardAuthenticationManagerFactory">
  <securityDomains>
    <securityDomain name="example-realm">
      <authConfig>
        <loginModules>
          <loginModule 
            type="AppserverIo\Appserver\ServletEngine\Security\Auth\Spi\LdapLoginModule" flag="required">
            <params>
              <param name="hashAlgorithm" type="string">null</param>
              <param name="url" type="string">openldap</param>
              <param name="port" type="string">389</param>
              <param name="baseDN" type="string">dc=example,dc=org</param>
              <param name="bindDN" type="string">cn=admin,dc=example,dc=org</param>
              <param name="bindCredential" type="string">admin</param>
              <param name="baseFilter" type="string">(&amp;(objectClass=person)(uid={0}))</param>
              <param name="rolesDN" type="string">dc=example,dc=org</param>
              <param name="roleFilter" type="string">memberUid={0}</param>
              <param name="allowEmptyPasswords" type="string">false</param>
            </params>
          </loginModule>
        </loginModules>
      </authConfig>
    </securityDomain>
  </securityDomains>
</manager>
```

## Persistence Container related Managers

The [Persistence Container](<{{ "/get-started/documentation/persistence-container.html" | prepend: site.baseurl }}>) represents the service providing [Service Side Component Types](<{{ "//get-started/documentation/1.1/persistence-container.html#server-side-component-types" | prepend: site.baseurl }}>) and [Persistence Manager](<{{ "//get-started/documentation/1.1/persistence-container.html#persistence-manager" | prepend: site.baseurl }}>).

The follwing Manager implementations are responsible for the core functionality of the Persistence Container.

### Bean Manager

Provides the configuration, initialization and lookup functionality for Session and Message Driven Beans.

Because of their different livecycle, the Bean Manager has to manage Stateful und Singleton Session Beans (SSB) in a very specifc way, in contrast to Stateless Session (SLSB) or Message Beans (MDB). Therefore it provides additional configuration parameters that allows to specify the lifetime of SFSB as well as the garbage collection frequency used to clean up SFSBs that has been timed out.

```xml
<manager 
  name="BeanContextInterface" 
  type="AppserverIo\Appserver\PersistenceContainer\BeanManager"
  factory="AppserverIo\Appserver\PersistenceContainer\BeanManagerFactory"
  contextFactory="AppserverIo\Appserver\Naming\NamingContextFactory">
  <params>
    <param name="baseDirectory" type="string">META-INF</param>
    <param name="lifetime" type="integer">1440</param>
    <param name="garbageCollectionProbability" type="float">0.1</param>
  </params>
  <directories>
    <directory>/META-INF/classes</directory>
  </directories>
</manager>
```

The Bean Manager is more or less invisble to the developer, as the Bean configuration will either be done by annotations or the deployment descriptor `META-INF/epb.xml`, which data allows the developer to override the annotations.

### Timer Service

Allows the scheduled execution of methods on Singleton and Stateless Session Beans as well as Message Driven Beans. If the timer service has to invoke a Beans method can be configured either by unsing the `@Schedule` annotation on the apropriate Bean method or the developer that schedules the Timer Service by writing the necessary code reading the schedule e. g. from a database. 

```xml
<manager 
  name="TimerServiceContextInterface" 
  type="AppserverIo\Appserver\PersistenceContainer\TimerServiceRegistry" 
  factory="AppserverIo\Appserver\PersistenceContainer\TimerServiceRegistryFactory"/>
```

A more detailed description how to create a schedule on a Beans method can be found in the [Timer Service](<{{ "/get-started/documentation/timer-service.html" | prepend: site.baseurl }}>) documentation.
  
### Persistence Manager

The Persistence Manager handles the information about the application's Doctrine Entity Manager instances. To have Doctrine's Entity Manager injected in a Bean, the Persistence Manager creates the requested instances based on the datasource and persistence unit information it parses on start up.

```xml
<manager 
  name="PersistenceContextInterface" 
  type="AppserverIo\Appserver\PersistenceContainer\PersistenceManager" 
  factory="AppserverIo\Appserver\PersistenceContainer\PersistenceManagerFactory" 
  contextFactory="AppserverIo\Appserver\Naming\NamingContextFactory"/>
```

How multiple Doctrine Entity Manager instance can be configured and injected in Beans will be described in the [Persistence Manager](<{{ "/get-started/documentation/1.1/persistence-container.html#persistence-manager" | prepend: site.baseurl }}>) documentation.

> To replace Doctrine with another ORM, writing a custom Persistence Manager will be the probably the best option. 

## Message Queue related Managers

The [Message-Queue](<{{ "/get-started/documentation/1.1/message-queue.html" | prepend: site.baseurl }}>) service allows developers to decouple business logic from a request or start processes in background and run them in parallel.

### Queue Manager

Handles the Message Queues provided by the application. The Queue Manager is responsible to parse the message queue configuration `META-INF/message-queue.xml` and create the necessary queue workers that are necessary to process the messages. 

```xml
<manager 
  name="QueueContextInterface" 
  type="AppserverIo\Appserver\MessageQueue\QueueManager"
  factory="AppserverIo\Appserver\MessageQueue\QueueManagerFactory">
  <params>
    <param name="baseDirectory" type="string">META-INF</param>
    <param name="maximumJobsToProcess" type="integer">200</param>
  </params>
</manager>
```

## Writing your own Manager

Writing a custom Manager is pretty simple. The Manager class has to implement the interface `\AppserverIo\Psr\Application\ManagerInterface`. Additionally a factory class which implements the interface `\AppserverIo\Appserver\Core\Interfaces\ManagerFactoryInterface` is necessary. The factory class creates a new instance and add's it the passed application like

```php
<?php

namespace My\Namespace;

use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Core\Api\Node\ManagerNodeInterface;
use AppserverIo\Appserver\Core\Interfaces\ManagerFactoryInterface;

/**
 * The factory for the object manager.
 */
class MyManagerFactory implements ManagerFactoryInterface
{

  /**
   * The main method that creates new instances in a separate context.
   *
   * @param \AppserverIo\Psr\Application\ApplicationInterface         $application          The application instance
   * @param \AppserverIo\Appserver\Core\Api\Node\ManagerNodeInterface $managerConfiguration The manager configuration
   *
   * @return void
   */
  public static function visit(ApplicationInterface $application, ManagerNodeInterface $managerConfiguration)
  {
    $application->addManager(new ObjectManager(), $managerConfiguration);
  }
}
```

A very basic (and almost **NOT** working) Manager implementation, that provides kind of a Servlet management functionality could look like

```php
<?php

namespace My\Namespace;

use AppserverIo\Psr\Context\ContextInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\ServletEngine\ServletManager;

/**
 * A simple manager implementation.
 */
class MyManager extends ServletManager
{

  /**
   * Has been automatically invoked by the container after the application
   * instance has been created.
   *
   * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
   *
   * @return void
   */
  public function initialize(ApplicationInterface $application)
  {
    // initialize the manager here, e. g. pre-load the actions of a framework like Symfony here
  }

  /**
   * Runs a lookup for the instance with the passed class name and
   * session ID.
   *
   * @param string $path      The path to the requested instance
   * @param string $sessionId The session ID
   * @param array  $args      The arguments passed to the servlet constructor
   *
   * @return object The requested servlet
   */
  public function lookup($servletPath, $sessionId = null, array $args = array())
  {
    // return the requested instance here
  }
}
```

Override the default Servlet Manager in the application's custom `META-INF/context.xml` like

```xml
<manager 
  name="ServletContextInterface" 
  type="My\Namespace\MyManager"
  factory="My\Namespace\MyManagerFactory"/>
```

This would allow a developer to replace Servlets with something else. But keep in mind, the returning object **MUST** also implement the `\AppserverIo\Psr\Servlet\ServletInterface`, as the calling Servlet Module would expect some kind of Servlet implementation. To replace the whole Servlet Engine, the related Servlet Module has also to be replaced.

> Please keep in mind, that e. g. replacing the complete Servlet Engine with something like a Symonfy Servlet Engine would be quite a huge challenge, as it consists of much more components as the Servlet Manager only. Replacing the Servlet Manager would be only a very small subset of what will be necessary and should give a developer only an impression of how things could be done.