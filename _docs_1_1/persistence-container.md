---
layout: docs_1_1
title: Persistence Container
meta_title: Persistence Container - appserver.io
meta_description: The Persistence Container is one of the main services appserver.io provides. But persisting data to a database is only one functionality.
position: 80
group: Docs
subNav:
  - title: Persistence Manager
    href: persistence-manager
  - title: Persistence-Container Options
    href: persistence-container-options
  - title: Server-Side Component Types
    href: server-side-component-types
permalink: /get-started/documentation/1.1/persistence-container.html
---

In addition to the `Servlet-Engine`, the Persistence Container is one of the main services appserver.io provides. The name `Persistence-Container` might lead to some misunderstanding, as many people think that it mostly refers to database persistence. In Java, there are EJB-Containers that provide a broad set of functionalities like [Bean- or Container-Managed-Persistence](http://en.wikipedia.org/wiki/Enterprise_JavaBeans), whereas appserver.io only provides a small subset of the functionality similar to platforms like [Wildfly](http://en.wikipedia.org/wiki/WildFly). In the following, the possibilities of the `Persistence-Container` and its usage for writing enterprise-ready applications, are described in detail.

## Persistence Manager

Since version 1.1 we've an integrated Persistence Manager, based on Doctrine 2.5. The Persistence Manager is responsible to handle the Entity Managers defined by the deployed applications. Each Entity Manager itself simply is a wrapped Doctrine Entity Manager instance and enables you to access all the functionality, Doctrine is delivered with.

### Datasource

A Datasource specifies the connection to all kind of databases. As the application server uses Doctrine to handle database connections, the configuration parameters are very close to the one, you have to specify if you're using Doctrine in a usual project with one of the frameworks out there.

As all other configuration parameters, the Datasource configuration, also has to be done in a XML configuration file. In contrast to other application specific configuration, Datasource's can come with your application or can be defined globally, e. g. in the webroot folder of the application server. In that case, the application doesn't need to have to specify a datasource. Instead, the Persistence Unit, that will be explained in the next chapter, can reference one. If your application provides it's own Datasource, what will be the common case, it has to be specified in a file within the `META-INF` folder and has to end with `-ds.xml`, for example `appserver-ds.xml`.

In the following example, you can see the configuration for two Datasource's.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<datasources xmlns="http://www.appserver.io/appserver">

    <datasource name="appserver.io-example-mysql">
        <database>
            <driver>pdo_mysql</driver>
            <user>appserver</user>
            <password>appserver.i0</password>
            <databaseName>appserver_ApplicationServer</databaseName>
            <databaseHost>127.0.0.1</databaseHost>
        </database>
    </datasource>

    <datasource name="appserver.io-example-sqlite">
        <database>
            <driver>pdo_sqlite</driver>
            <user>appserver</user>
            <password>appserver</password>
            <path>META-INF/data/appserver_ApplicationServer.sqlite</path>
            <memory>false</memory>
        </database>
    </datasource>

</datasources>
```

The first example defines the Datasource for a MySQL database, the second one for a SQLite database that will be created in the `META-INF/data` folder.

| Param name           | Type    | Description                                                    |
| ---------------------| --------| ---------------------------------------------------------------|
| `driver`             | string  | The driver specifies the actual implementations of the DBAL interfaces to use. |
| `user`               | string  | Username to use when connecting to the database. |
| `password`           | string  | Password to use when connecting to the database. |
| `databaseHost`       | string  | Hostname of the database to connect to. |
| `databasePort`       | integer | Port of the database to connect to. |
| `databaseName`       | string  | Name of the database/schema to connect to. |
| `path`               | string  | The filesystem path to the database file. Mutually exclusive with memory. path takes precedence. |
| `memory`             | boolean | True if the SQLite database should be in-memory (non-persistent). Mutually exclusive with path. path takes precedence. |
| `unixSocket`         | string  | Name of the socket used to connect to the database. |
| `driverOptions`      | string  | Any supported additional flags. |
| `charset`            | string  | The charset used when connecting to the database. |

You'll find a more detailed description about the possible driver/parameter options in the [Doctring DBAL configuration](http://doctrine-orm.readthedocs.org/projects/doctrine-dbal/en/latest/reference/configuration.html).

> Please be aware, a Datasource doesn't specify a Doctrine Entity Manager, it simply specifies a connection. To specify a Doctrine Entity Manager, what usually is what you want to do, you also need a Persistence Unit.

### Persistence Unit

A Persistence Unit declares the Enity Manager that should be initialized by the application server and will be injected into your application's components when requested. Persistence Unit's can be configured by a XML file named `META-INF/persistence.xml` inside your application. Each Persistence Unit references a datasource that can be defined as described in section [Datasource](#datasource).

The following example is a Persistence Unit configuration and give you a brief introduction about what configuration options are available.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<persistence xmlns="http://www.appserver.io/appserver">

    <persistenceUnits>

        <persistenceUnit name="ExampleEntityManager"
            interface="Doctrine\ORM\EntityManagerInterface"
            type="Doctrine\ORM\EntityManager"
            factory="AppserverIo\Appserver\PersistenceContainer\Doctrine\EntityManagerFactory">
            
            <metadataConfiguration type="annotation">
            
                <directories>
                    <directory>/common/classes/AppserverIo/Apps/Example/Entities/Impl</directory>
                </directories>

                <params>
                    <param name="isDevMode" type="boolean">true</param>
                    <param name="useSimpleAnnotationReader" type="boolean">false</param>
                    <param name="proxyDir" type="string">false</param>
                </params>

            </metadataConfiguration>

            <datasource name="appserver.io-example-mysql"/>

            <annotationRegistries>
                <annotationRegistry namespace="JMS\Serializer\Annotation">
                    <directories>
                        <directory>/vendor/jms/serializer/src</directory>
                    </directories>
                </annotationRegistry>
            </annotationRegistries>

        </persistenceUnit>

    </persistenceUnits>

</persistence>
```

Each Persistence Unit needs at least the `<metadataConfiguration/>` and the `<datasource/>` nodes work properly. The `<annotationRegistries/>` node is optional.

The `<persistenceUnit/>` node has for mandatory attributes:

* The `name` attribute, which needs a unique name that will be used to reference the Persistence Unit for DI in your components
* The `interface` attribute specifies the interface of the Entity Manager, that has to be `Doctrine\ORM\EntityManagerInterface` unless you wrote your own custom Entity Manager
* The `type` attribute, that specifies the Entity Manager class to use, unless your wrote your own custom this has to be `Doctrine\ORM\EntityManager`
* The `factory` class that creates the Entity Manager instance, by default `AppserverIo\Appserver\PersistenceContainer\Doctrine\EntityManagerFactory` unless you write your own factory class

The subnode `<metadataConfiguration/>` has only one, even the `type` attribute. Theses attribute can have one of the three values

* `xml`: For a setup based on XML files
* `yaml`: For a YAML based setup
* `annotation`: For an annotation based setup

Depending on the values the factory either invokes the apropriate method on the `Setup` class to create the Entity Manager, e. g. if you choose `annotation`, the factory will invoke `Setup::createAnnotationMetadataConfiguration()`.

The last mandatory subnode is `<datasource/>`. The value of the `name` attribute must also be a valid Datasource `name`. In that way, you can relate the Persistence Unit with the Datasource with the same name you've entered here.

Finally we've the `<annotationRegistries/>` subnode, that allows you to specify additional annotation libraries, such `JMS`. You can register as many annotation libraries as necessary by specifying the details in a separate `<annotationRegistry/>` node. This node only supports the `namespace` attribute, that has to contain the annotation libraries namespace. Additionally you've to specifiy the directories to be parsed for the classes containing the annotation specification. Therefore you can define a `<directories/>` subnode that contains several `<directory/>` nodes with the path to a directory that has to be parsed.

## Server-Side Component Types

Although providing persisting data to a database is one functionality of the `Persistence-Container`, it is not the most important one. The following reasons support the usage of the `Persistence-Container`. Since PHP is used as a scripting language, it lacks the possibility of having objects, we call them components, persistent in memory. The `Persistence-Container` enables you to do exactly this. It provides performance and many other possibilities you would not benefit from if working with the well-known LAMP stack.

One may wonder how it is possible to have a component persistent in memory using PHP, a scripting language. Usually after every request the instance will be destroyed. The simple answer is: As appserver.io provides containers that run as daemons. You can specify components that are loaded when the application server starts and are in memory until the server shuts down. For simplicity reasons, the classes are called [Beans](http://en.wikipedia.org/wiki/Enterprise_JavaBeans), as it is done in Java.

There are three different types of beans, `Session Beans`, `Message Beans` and `Entity Beans`. In version 1.0.0 we do not deliver support for `Entity Beans` because the responsibility is up to ORM libraries like Doctrine. We support Doctrine to handle database persistence.

> These `Server-Side Component Types` can be distributed across a network, free of charge for developers. If components have been deployed on different instances, the distribution has to be activated by configuration.

### Session Beans

A session bean is a plain PHP class. You must not instantiate it directly because the application server takes care of its complete lifecycle.

> A session bean MUST provide a non-argument constructor, optionally no constructor.

Therefore, if a developer needs access to a session bean, he requests the application server for an instance. The request can either be initiated by a client or Dependency Injection. In both cases, a proxy to the session bean is delivered that allows invoking its methods. Depending on your configuration, the proxy also allows you to call this method over a network as a `Remote Method Call`. This enables you to figure out if the session bean is located on the same application server instance or on another one in your network.

When writing a session bean, the developer has to specify the type of bean he wants to implement. This can either be done by adding an annotation to the classes DocBlock or by specifying it in a deployment descriptor. As it seems to be easier to add the annotation and, in most cases, it is sufficient, we recommend this for the start.

We differentiate three kinds of session beans, even `Stateless`, `Stateful` and `Singleton` session beans.

#### Stateless Session Beans (SLSBs)

A `SLSBs` state is only available for the time you invoke a method on it. As this bean type is designed for efficiency and simplicity, the developer does not need to take care about memory consumption, concurrency or lifecycle.

> `SLSBs` are very similar to PHP`s default request behaviour, as they are created to handle the request and will be destroyed after the request is finished.

##### Lifecycle

With each request, a new `SLSB` instance will be created. The instance is destroyed by the container after the request is handled.

##### Example

The following example demonstrates the implementation of a `SLSB` that provides functionality to create a user from the arguments passed to the `createUser()` method. The `SLSB` is registered with the name `AStatelessSessionBean` in the application servers `Naming Directory`. Additionally, we register a reference to the Persistence Unit `ExampleEntityManager `, we've described in the section [Persistence Unit](#persistence-unit). We use the injected Entity Manager to store the created user entity in the database.

Registering a bean and a Persistence Unit in the [Naming Directory](<{{ "/get-started/documentation/naming-directory.html" | prepend: site.baseurl }}>) is necessary to use it for [Dependency Injection](<{{ "/get-started/documentation/dependency-injection.html" | prepend: site.baseurl }}>) as explained in the documentation.

```php
<?php

namespace AppserverIo\Example\SessionBeans;

use AppserverIo\Example\Entities\User;

/**
 * @Stateless(name="AStatelessSessionBean")
 */
class AStatelessSessionBean
{

  /**
   * The Doctrine EntityManager instance.
   *
   * @var \Doctrine\ORM\EntityManagerInterface
   * @PersistenceUnit(unitName="ExampleEntityManager")
   */
  protected $entityManager;

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

    // create the new user
    $user = new User();
    $user->setUsername($username);
    $user->setPassword($this->hashPassword($password));
    
    // save the user to the database
    $this->entityManager->persist($entity);
  }
}
```

Then we can implement a servlet that invokes the method with the credentials loaded from the request. The servlet can look like this.

```php
<?php

namespace AppserverIo\Example\Servlets;

use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;

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
   * login a user with credentials found as request parameters.
   *
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface
   *   $servletRequest The request instance
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface
   *   $servletResponse The response instance
   *
   * @return void
   * @see \AppserverIo\Psr\Servlet\Http\HttpServlet::doGet()
   */
  public function doPost(
    HttpServletRequestInterface $servletRequest,
    HttpServletResponseInterface $servletResponse)
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

If we now invoke a `POST` request on our servlet, sending `username` and `password` parameters, the application server will inject the `SLSB` at runtime and invoke the `doPost()` method. In return, this step invokes the `createUser()` method on the `SLSB` and adds a success message to the response.

#### Stateful Session Beans (SFSBs)

The `SFSB` is something between the two other types. It is bound to the session with the ID pass to the client, when an instance is requested. A `SFSB` is very useful, if you want to implement something like a shopping cart. If the shopping cart instance will be declared as a class member of `SFSB`, it is persistent for the sessions lifetime.

In contrast to a HTTP Session, `SFSBs` enables you to have session bound persistence, without the need to explicitly add data to a session object. This makes development easy and comfortable. As `SFSBs` are persisted in memory and not serialized to files, the application server has to make sure that the number of instances are flushed when their lifetime has been reached. By doing so, the number of instances is minimized.

##### Lifecycle

`SFSBs` are created by the container when they are requested and no instance exists, based on the passed session-ID. After the request has been processed, the instance will be re-attached to the container ready to handle the next request.

> If the session is removed, times out, or the application server restarts, the data of a `SFSB` will be lost. Because `SFSBs` use the HTTP session-ID, it is necessary to start an HTTP session before you invoke methods on it.

##### Example

As described above, a `SFSB` has a state that is bound to a HTTP session. It is necessary to start the HTTP session once before accessing it. Imagine we have a servlet and want to a access a `SFSB` used to login a user with credentials found as request parameters. After a successfull login, the user entity should be persisted in the `SFSB` in order to protect the following `GET` requests.

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

> A `SFSB` is pretty easy to use and has to be implemented as a plain old PHP class. It is important that the user entity, once set in the `SFSB`, is available at every request as long as the HTTP session is available.

The necessary servlet is also a very simple example that implements the login on a `POST` request, whereas the `GET` request is protected.

```php
<?php

namespace AppserverIo\Example\Servlets;

use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;

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
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface
   *   $servletRequest The request instance
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface
   *   $servletResponse The response instance
   *
   * @return void
   * @see \AppserverIo\Psr\Servlet\Http\HttpServlet::doGet()
   */
  public function doPost(
    HttpServletRequestInterface $servletRequest,
    HttpServletResponseInterface $servletResponse)
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
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface
   *   $servletRequest The request instance
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface
   *   $servletResponse The response instance
   *
   * @return void
   * @see \AppserverIo\Psr\Servlet\Http\HttpServlet::doGet()
   */
  public function doGet(
    HttpServletRequestInterface $servletRequest,
    HttpServletResponseInterface $servletResponse)
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

> You do not have to restart the session in the `GET` request because the `Servlet-Engine` is aware of the session-ID passed as request header and uses it when the `SFSB` is injected on runtime.

#### Singleton Session Beans (SSBs)

A `SSB` is created by the container only once for each application. Thus, whenever an instance is requested, it will be the same. If a variable is set as a `SSB` member, it is available until it is overwritten, or the application server is restarted.

##### Concurrency

Concurrency is, in case of a `SSB`, a more complex issue. In contrast to `SLSBs` and `SFSBs`, the data is shared across requests. The container has to make sure that only one request has access to the data of a `SFSB`. Therefore, requests are serialized and blocked until the instance is available again.

> To enable a `SSB` for sharing its data across requests, it has to extend the `\Stackable` class. This class comes with the PECL [pthreads](https://github.com/appserver-io-php/pthreads.git) extension that brings multithreading to PHP. appserver.io uses a fork of the 1.x branch, due to some restrictions introduced with 2.x branch.

##### Lifecycle

In contrast to a `SLSB`, the lifecycle of a `SSB` is different. Once the instance is created, it is shared among all requests. Instead of destroying the instance after each request the instance persists in memory until the application is shut down or restarted.

> A `SSB` gives developers great power because all data added to a member stays in memory until someone unsets it. Thus, a `SSB` is an excellent option for sharing data across requests. However, great power comes with great responsibility for the developer. This is why he always has to  keep an eye on a `SSB`'s memory consumption.

##### Explicit Startup

In combination with having data persistent in memory, a `SSB` can be pre-loaded on application startup. This can be done by adding the `@Startup` annotation to the classes DocBlock. Using explicit startup functionality and loading data from a configuration file or a DB persistent in memory, leads to massive performance improvements.

##### Example

To demonstrate the usage of a `SSB` the previous example of the `SFSB` is extended by a counter tracking the number of successful logins.

```php
<?php

namespace AppserverIo\Example\SessionBeans;

/**
 * @Singleton
 */
class ASingletonSessionBean extends \Stackable
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

To use the `SSB` in a `SFSB`, the `SSB` can be injected with the `@EnterpriseBeans` annotation. Additionally, the `login()` method has to be customized to raise and return the number of successful logins by invoking the `raise()` method of the `SSB`.

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
   * @return integer The number of successful logins since the last restart
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
   * Checks if a user has been logged into the system, if not, an exception
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

Finally, the servlet receives the number of successul logins since the application server's last restart and adds it to the response.

```php
<?php

namespace AppserverIo\Example\Servlets;

use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;

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
   * login the user with credentials found as request parameters.
   *
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface
   *   $servletRequest The request instance
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface
   *   $servletResponse The response instance
   *
   * @return void
   * @see \AppserverIo\Psr\Servlet\Http\HttpServlet::doGet()
   */
  public function doPost(
    HttpServletRequestInterface $servletRequest,
    HttpServletResponseInterface $servletResponse)
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
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface
   *   $servletRequest The request instance
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface
   *   $servletResponse The response instance
   *
   * @return void
   * @see \AppserverIo\Psr\Servlet\Http\HttpServlet::doGet()
   */
  public function doGet(
    HttpServletRequestInterface $servletRequest,
    HttpServletResponseInterface $servletResponse)
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

### Message Beans (MDBs)

Other than session beans, `MDBs` are **NOT** invoked by a proxy, but are sent to a `Message Broker` as receiver of the messages. The `Message Broker` adds them to a queue until they are collected and proccessed in a separate thread.

> Using `MDBs` enables you to execute long running processes `asynchronously` because waiting for an answer after having set a message to the `Message Broker` is no longer neccessary. Unlike session beans, `MDBs` have to implement the `AppserverIo\Psr\Pms\MessageListenerInterface` interface. Like session beans, `MDBs` **MUST** provide a non-argument constructor, optionally no constructor.

As `MDBs` are mostly used in context of a [Message-Queue](<{{ "/get-started/documentation/message-queue.html" | prepend: site.baseurl }}>), this section does not describe the functionality in detail.

### Lifecycle Callbacks

`Lifecycle Callbacks` enable a developer to declare callback methods depending on the bean's lifecycle. We support `post-construct` and `pre-destroy` callbacks for all types of beans, as well as `post-attach` and `pre-detach` for SFSBs. `Lifecycle Callbacks` can be configured either by annotations or the deployment descriptor. Declaring `Lifecycle Callbacks` by annotations is more intuitive, as you easily add the annotation to the methods DocBlock. Therefore, we go with the annotations here.

> Keep in mind that `Lifecycle Callbacks` are optional, MUST be `public`, MUST NOT have any arguments and CAN NOT deliver checked exceptions. Exceptions are handled by the container and result in a `critical` log message.

#### Post-Construct Callback

As the bean's lifecycle is controlled by the container and `Dependency Injection` works either by property or method injection, a `Post-Construct` callback enables a developer to implement a method that is invoked by the container after the bean has been created and all instances have been injected.

> This callback can be very helpful for implementing functionalities like cache systems that need to load data from a datasource once and update it frequently.

#### Pre-Destroy Callback

The second callback is the `Pre-Destroy` callback. This is fired before the container destroys the instance of the bean.

#### Post-Attach Callback (SFSB only)

These callback will be invoked by the container, before a SFSB will be re-attached. Re-attached means, that the class instance will be stored in an internal Stackable instance, until another bean tries to load it from the container by DI.

> This callback is the right place to destroy resources or instances of classes that can **NOT** be serialzed. Think of the Doctrine Entity Manager, that has a PDO database connection inside which can not be persisted by the application server between requests. Internally PHP serializes the SFSB, which makes it impossible to hold resources or PHP class instances.

#### Pre-Detach Callback (SFSB only)

This is the reverse callback for `post-attach` and will be invoked by the container after the SFSB has been loaded from the internal Stackable instance. If you've destroyed a resource in the `post-attach` callback this will be the right place to restore it.

#### Example

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
   * Lifecycle Callback that will be invoked by the container on
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
   * Lifecycle Callback that will be invoked by the container before the
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

This extends the `SSB` with some kind of real persistence by loading the counter from a simple textfile on application startup or writing it back before the `SSB`is destroyed.

### Interceptors

`Interceptors` enable a developer to weave cross-cutting concerns into his application, without adding code to business methods. An `Interceptor` is an advice. The functionality behind the secenes is [AOP](<{{ "/get-started/documentation/aop.html" | prepend: site.baseurl }}>).

To add a very basic ACL authorization functionality that use an `Interceptor`, we have to implement a simple aspect first. The aspect looks like this

```php
<?php

namespace AppserverIo\Example\Aspects;

use AppserverIo\Doppelgaenger\Interfaces\MethodInvocationInterface;

/**
 * @Aspect
 */
class AuthorizationInterceptor
{

  /**
   * Advice used to check user authorization on method call.
   *
   * @param \AppserverIo\Doppelgaenger\Interfaces\MethodInvocationInterface $methodInvocation
   *   Initially invoked method
   *
   * @return void
   * @throws \AppserverIo\Example\Exceptions\AuthorizationException
   *   Is thrown if access is denied for the user logged into the system
   *
   * @Before
   */
  public function authorize(MethodInvocationInterface $methodInvocation)
  {

    // load class and method name
    $className = $methodInvocation->getStructureName();
    $methodName = $methodInvocation->getName();

    // load context, a instance of AStatefulSessionBean
    $context = $methodInvocation->getContext();

    // load the application context
    $application = $context->getApplication();

    // load user logged into the system
    $user = $context->getUser();

    // load the SLSB handling the ACLs
    $aclSessionBean = $application->search('AclSessionBean');

    /*
     * Query whether the user is allowed to invoke the method and will throw
     * an exception that could be catched/handled in the servlet for example
     */
    $aclSessionBean->allowed($methodInvocation, $user);

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

> Keep in mind that the `$methodInvocation->getContext()` method allows access to the component the advice has been declared in, in our example this is the `SSB` instance below.

If we want to authorize the user logged into the system for the method call to a session bean method, we simply have to declare it by adding an annotation like the following.

```php
<?php

namespace AppserverIo\Example\SessionBeans;

use AppserverIo\Example\Interceptors\AuthorizationInterceptor;

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

  /**
   * Returns the user logged into the system.
   *
   * @return \AppserverIo\Apps\Example\Entities\User
   *   The user logged into the system
   */
  public function getUser()
  {
    return $this->user;
  }

  /**
   * A business method protected by a before advice that will query authorization
   * for the users method call by invoking the authorize() method of our
   * interceptor.
   *
   * @return void
   * @Before("advise(AuthorizationInterceptor->authorize())")
   */
  public function protectedMethod()
  {
    // do something protected here
  }
}
```

The `AclSessionBean` is NOT implemented in this example because this description only gives a rough indication on how to implement such a functionality and how an `Interceptor` can be used.  