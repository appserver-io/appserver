# appserver.io, a PHP application server

[![Gitter](https://badges.gitter.im/Join Chat.svg)](https://gitter.im/appserver-io/appserver?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
 [![Build Status](https://img.shields.io/travis/appserver-io/appserver/master.svg?style=flat-square)](http://travis-ci.org/appserver-io/appserver)
 [![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/appserver-io/appserver/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/appserver-io/appserver/?branch=master)
 [![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/appserver-io/appserver/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/appserver-io/appserver/?branch=master)

This is the main repository for the [appserver.io](http://www.appserver.io/) project.

appserver.io is a multithreaded application server for PHP, written
in PHP. Yes, pure PHP! If you know anything about PHP, you're probably thinking we might be crazy. Well, we aren't. We are dead serious (but we most certainly still love having fun!).

appserver.io overcomes some of the biggest overhead issues most PHP (CGI) programs have in common, through a blazingly fast and rock solid infrastructure and with concepts new to PHP. At the same time, appserver.io offers PHP developers the fundamental core features found in most popular frameworks today, yet not intending to be a framework at all. It is a suprizingly fun infrastructure for PHP development, allowing you to build powerful applications without needing the bulk of a PHP framework.

appserver.io includes great features like...

 - Its own performant Web Server and HTTP foundation.
 - A powerful Servlet Engine, with true multi-threading
 - A Dependency Injection Container, for building modern, modular and testable code
 - Multiple Persistence Containers, for sessions and other stateful components
 - A Message Queue System, for contolling the execution of long running tasks
 - A Timer Service, for running scheduled tasks

and much more.

appserver.io also supports [Aspect Oriented Programming](http://en.wikipedia.org/wiki/Aspect-oriented_programming)(AOP), which is a programming paradigm also found in the most popular frameworks today, like Laravel. AOP allows the separation of cross-cutting concerns within a program, allowing developers to create even more modular systems.

With appserver.io, it is our goal to establish a solution as the next standard for enterprise applications written in PHP. With your help, we can reach this goal.

Give it a try!


#### Table of Contents

* [Semantic Versioning](#semantic-versioning)
* [Installation](#installation)
* [Basic Usage](#basic-usage)
* [HTTP(S) Server](#https-server)
* [Servlet-Engine](#servlet-engine)
* [Annotations](#annotations)
* [Dependency Injection](#dependency-injection)
* [Persistence-Container](#persistence-container)
* [Message-Queue](#message-queue)
* [Timer-Service](#timer-service)
* [AOP](#aop)
* [Design-by-Contract](#design-by-contract)
* [Runtime Environment](#runtime-environment)
* [Configuration](#configuration)
* [Deployment](#deployment)
* [Uninstall](#uninstall)

# Semantic Versioning

appserver.io follows semantic versioning. For the purpose of defining a public API we introduced [appserver.io specific `PSRs`](http://appserver.io/get-started/psrs.html). These PSRs are the core to appserver.io's public API, but within their own definition and versioning, meaning that **semantic versioning applies to these PSRs and not the appserver package itself**.

# Installation

appserver.io can be installed on several operating systems. It also supports several methods of acquiring the software. To get your appserver.io package you can do one of the following:

* Download one of our [**releases**](<https://github.com/appserver-io/appserver/releases>)
  right from this repository which provide tested install packages

* Grab any of our [**nightlies**](<http://builds.appserver.io/>) from our project page to get
  bleeding edge install packages, (and may also containg bugs - only for testing and not for production use!!)

* Build your own package using [ANT](<http://ant.apache.org/>)! To do so clone the [runtime](<https://github.com/appserver-io-php/runtime>) first. Then update at least the `os.family` and `os.distribution` build properties according to your environment and build the appserver with the ANT `build` and `create-package` target.

The package will install with these basic default characteristics:

* Install dir: `/opt/appserver`
* Autostart after installation, no autostart on reboot
* Reachable under pre-configured ports as described [here](#basic-usage)

For OS specific steps and characteristics see below for tested environments.

## Mac OS X

> Runs and tested on Mac OS X 10.8.x and higher!

For Mac OS X > 10.8.x we provide a `.pkg` file for [download](http://appserver.io/get-started/downloads.html#mac-osx) that contains the runtime and the distribution. Double-clicking on the `.pkg` starts and guides you through the installation process.

## Windows

> Runs and tested on Windows 7 (32-bit) and higher!

As we deliver the Windows appserver as a .jar file you can [download](http://appserver.io/get-started/downloads.html#windows), a installed Java Runtime Environment (or JDK
that is) is a vital requirement for using it. If the JRE/JDK is not installed, you have to do so
first. You can get the JRE from [Oracle's download page](<http://www.oracle.com/technetwork/java/javase/downloads/jre7-downloads-1880261.html>).
If this requirement is met you can start the installation by simply double-clicking the .jar archive.

## Debian

> Runs and tested on Debian Squeeze (64-bit) and higher!

If you're on a Debian system you might also try our `.deb` repository:

```
root@debian:~# echo "deb http://deb.appserver.io/ wheezy main" > /etc/apt/sources.list.d/appserver.list
root@debian:~# wget http://deb.appserver.io/appserver.gpg -O - | apt-key add -
root@debian:~# aptitude update
root@debian:~# aptitude install appserver-dist
```

Optionally you can [download](http://appserver.io/get-started/downloads.html#debian) the `.deb` files and install them by double-clicking on them. This will invoke the system default package manager and guides you through the installation process. Please install the runtime first, as this is a dependency for the distribution.

## Fedora

> Runs and tested on versions Fedora 20 (64-bit) and higher!

We  also provide `.rpm` [files](http://appserver.io/get-started/downloads.html#fedora) for Fedora, that you can download and start the installation process by double-clicking on it. This will start the systems default package manager and guides you through the installation process.

## CentOS

> Runs and tested on CentOS 6.5 (64-bit) and higher!

Installation and basic usage is the same as on Fedora, but we provide different [packages](http://appserver.io/get-started/downloads.html#cent-os). CentOS requires additional repositories
like [remi](<http://rpms.famillecollet.com/>) or [EPEL](<http://fedoraproject.org/wiki/EPEL>) to
satisfy additional dependencies.

## Raspbian

As an experiment we offer a Raspbian and brought the appserver to an ARM environment. What should
we say, it worked! :D With `os.distribution` = raspbian you might give it a try and build it
yourself. Plan for at least 5 hours though, as we currently do not offer prepared install package.

# Basic Usage

The appserver will automatically start after your installation wizard (or package manager) finishes
the setup. You can use it without limitations after installation is completed..

Below you can find basic instructions on how to make use of the appserver. After the installation,
you might want to have a look at the example application, which is also included with the installation. You can reach the app at `http://127.0.0.1:9080/example`

Start your favorite browser and have a look at what appserver can do. :) To enter the site, use
the default login `appserver/appserver.i0`.

## Start and Stop Scripts

There are several standalone processes, which are needed for the proper
functioning of different features within appserver.

There are start and stop scripts included in appserver for all *nix like operating systems.
These work the same as on any other *nix systems. They are:

* `appserver`: The main process which will start the appserver itself

* `appserver-php5-fpm`: php-fpm + appserver configuration. Our default FastCGI backend. Other FCGI system can be added the same way.

* `appserver-watcher`: A watchdog which monitors filesystem changes and manages appserver restarts

On a normal system, all three of these processes should run to enable the full feature set. To
ultimately run the appserver, only the appserver process is needed. However, you will miss simple on-the-fly
deployment (`appserver-watcher`) and might have problems with legacy applications.

Depending on the FastCGI backend you want to use, you might ditch `appserver-php5-fpm` for other
processes e.g. you could also use an [hhvm](http://hhvm.com/) backend.

Currently we support three different types of init scripts, which support the commands `start`, `stop`,
`status` and `restart` (additional commands might be available on other systems).

**Mac OS X (LAUNCHD)**
The LAUNCHD launch daemons are located within the appserver installation at `/opt/appserver/sbin`.
They can be used with the schema `/opt/appserver/sbin/<DAEMON> <COMMAND>`

**Debian, Raspbian, CentOS, ...(SystemV)**
Commonly known and located in `/etc/init.d/` they too support the commands mentioned above provided
in the form `/etc/init.d/<DAEMON> <COMMAND>`.

**Fedora, ... (systemd)**
systemd init scripts can be used using the `systemctl` command with the syntax `systemctl <COMMAND> <DAEMON>`.

**Windows**

On Windows we sadly do not offer any of these scripts. After the installation, you can start the
Application Server with the ``server.bat`` file located within the root directory of your installation.
Best thing to do would be starting a command prompt as an administrator and run the following commands
(assuming default installation path):

```
C:\Windows\system32>cd "C:\Program Files\appserver"
C:\Program Files\appserver>server.bat
```

# HTTP(S) Server

The configuration of the HTTP(S) Server itself is mostly self-explanatory, so just have a look at the default config
file and, if you'd like, try to change the settings.
Please make sure you restart the appserver after making any changes. :)
A detailed overview of all configuration settings will follow ...

## Configure a Virtual Host

Using virtual hosts, you can extend the default server configuration and produce a host specific
environment for your app to run.

You can do so by adding a virtual host configuration to your global server configuration file. See
the example for a XML based configuration below:

```xml
<virtualHosts>
  <virtualHost name="example.local">
    <params>
      <param name="admin" type="string">
        admin@appserver.io
      </param>
      <param name="documentRoot" type="string">
        /opt/appserver/webapps/example
      </param>
    </params>
  </virtualHost>
</virtualHosts>
```

The above configuration sits within the server element and opens up the virtual host `example.local`
which has a different document root than the global configuration has. The virtual host is born. :-)

The `virtualHost` element can hold params, rewrite rules or environment variables which are only
available for the host specifically.

## Configure your Environment Variables

You can set environment variables using either the global or the virtual host based configuration.
The example below shows a basic usage of environment variables in XML format.

```xml
<environmentVariables>
  <environmentVariable
    condition=""
    definition="EXAMPLE_VAR=example" />
  <environmentVariable
    condition="Apple@$HTTP_USER_AGENT"
    definition="USER_HAS_APPLE=true" />
</environmentVariables>
```

There are several ways in which this feature is used. You can get a rough idea when having a
look at Apache modules [mod_env](<http://httpd.apache.org/docs/2.2/mod/mod_env.html>) and
[mod_setenvif](<http://httpd.apache.org/docs/2.2/mod/mod_setenvif.html>) which we adopted.

You can make definitions of environment variables dependent on REGEX based conditions which will
be performed on so called backreferences. These backreferences are request related server variables
like `HTTP_USER_AGENT`.

A condition has the format `<REGEX_CONDITION>@$<BACKREFERENCE>`. If the condition is empty the
environment variable will be set every time.

The definition you can use has the form `<NAME_OF_VAR>=<THE_VALUE_TO_SET>`. The definition has
some specialities too:

- Setting a var to `null` will unset the variable if it existed before
- You can use backreferences for the value you want to set as well. But those are limited to
  environment variables of the PHP process
- Values will be treated as strings

# Servlet-Engine

Originally Java Servlets were Java's counterpart to other dynamic web technologies like PHP or the
Microsoft .NET platform. In contrast to PHP, a Servlet written in Java is not a script that will
be interpreted per request. Instead, it is a class, which is instantiated, when the Servlet Engine starts up.
This means, the servlet class is already in memory and stays in memory.

> In most cases, this is a major advantage compared to the common PHP way to constantly reload the script on each
> request. Since most PHP applications are based on frameworks like Symfony or Laravel have grown
> immensly during the last few years, the reloading of all the script files required by the application,
> slows down performance considerably. This is one of the reasons why caching is a major
> part of all good PHP frameworks. On one hand, caching improves performance enough so
> the application responds to the request in an acceptable timeframe. On the other hand, it is also the
> origin of many problems, such as how to invalidate parts of the cache during an applications
> runtime.

Servlets enable you to implement your application logic as you normally would, but without the need to worry about the expensive
reloading process, which is common to normal PHP applications. A Servlet is a super
fast and simple way to implement an entry point to handle HTTP requests, which allow you to
execute all performance critical tasks, like bootstrapping (with a simple method called `init()`), when
the Servlet Engine starts up.

## What is a Servlet

A Servlet is a simple class that has to extend from `AppserverIo\Psr\Servlet\Http\HttpServlet`.
Your application logic can then be implemented by overwriting the `service()` method or better
by overwriting the request specific methods like `doGet()` if you want to handle a GET request.

## Create a simple Servlet

Let's write a simple example and start with a famous `Hello World` servlet

```php

namespace Namespace\Module;

use AppserverIo\Psr\Servlet\ServletConfigInterface;
use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;

/**
 * This is the famous 'Hello World' as servlet implementation.
 *
 * @Route(name="helloWorld",
 *        displayName="I'm the 'Hello World!' servlet",
 *        description="A annotated 'Hello World!' servlet implementation.",
 *        urlPattern={"/helloWorld.do", "/helloWorld.do*"})
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
   * Initializes the servlet with the passed configuration.
   *
   * @param \AppserverIo\Psr\Servlet\ServletConfigInterface $config
   *   The configuration to initialize the servlet with
   *
   * @return void
   */
  public function init(ServletConfigInterface $config)
  {

    // call parent method
    parent::init($config);

    // prepare the text here
    $this->helloWorld = 'Hello World!';

    // @todo Do all the bootstrapping here, because this method will
    //       be invoked only once when the Servlet Engines starts up
  }

  /**
   * Handles a HTTP GET request.
   *
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest
   *   The request instance
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse
   *   The response instance
   *
   * @return void
   * @see \AppserverIo\Psr\Servlet\Http\HttpServlet::doGet()
   */
  public function doGet(
    HttpServletRequestInterface $servletRequest,
    HttpServletResponseInterface $servletResponse)
  {
    $servletResponse->appendBodyStream($this->helloWorld);
  }
}
```

and save it as `/opt/appserver/webapps/myapp/WEB-INF/classes/Namespace/Module/HelloWorldServlet.php`.

Is that all? Yes! [Restart](#start-and-stop-scripts) the application server and open
`http://127.0.0.1:9080/myapp/helloWorld.do` in your favorite browser, and ... vóila :)

> A restart is always required since you changed code in your Servlet, because the Servlet
> will be loaded and initialized when the the application server starts. Without a restart
> the application server will not know you had made any changes.

# Annotations

Since one of our main goals is to make configuration as simple as possible, we decided to use
annotations wherever possible. As annotations are not supported natively by PHP, we provide
annotation support over our [lang](https://github.com/appserver-io/lang) package.

Beside the use of annotations in our application server components, it will also be possible to extend your
application with annotations by using the functionality appserver delivers out-of-the-box.

If you, for example, think about extending the actions of the controller component in your
MVC framework with a @Route annotation, you can do that in the following way

```php

namespace Namespace\Module;

use AppserverIo\Appserver\Lang\Reflection\ReflectionClass;
use AppserverIo\Appserver\Lang\Reflection\ReflectionAnnotation;

class Route extends ReflectionAnnotation
{

  /**
   * Returns the value of the name attribute.
   *
   * @return string The annotations name attribute
   */
  public function getPattern()
  {
    return $this->values['pattern'];
  }
}

class IndexController
{

  /**
   * Default action implementation.
   *
   * @return void
   * @Route(pattern="/index/index")
   */
  public function indexAction()
  {
    // do something here
  }
}

// create a reflection class to load the methods annotation
$reflectionClass = new ReflectionClass('IndexController');
$reflectionMethod = $reflectionClass->getMethod('indexAction');
$reflectionAnnotation = $reflectionMethod->getAnnotation('Route');
$pattern = $reflectionAnnotation->newInstance()->getPattern();
```

Most of the annotation implementations provided by our [Enterprise Beans](https://github.com/appserver-io-psr/epb)
PSR and used for [Dependency Injection](#dependency-injection), which will be described below,
are based on that annotation implementation.

# Dependency Injection

Dependency Injection(DI) enables developers to write cleaner, reusable and maintainable
code with less coupling by injecting necessary instances at runtime, instead of instantiating them in
the class itself. Within the application server, each application has its own scope and therefore its
own dependency injection container. This prevents your application from fatal errors like `Cannot redeclare class ...`.

## What can be injected

Generally everything! The application server itself doesn't use DI, instead it provides DI as a
service for the applications running within it. But, before you can let the DI container inject an
instance to your class, you have to register it. Registering a class for DI is pretty simple. To
register a class in the DI container the most common way is to use annotations.

```php

namespace Namespace\Modulename

/**
 * @Stateless(name="MyStatefulSessionBean")
 */
class MyStatefulSessionBean
{
}
```

When the application server starts, it parses the `META-INF/classes` and `WEB-INF/classes` folder
classes with supported annotations. If a class is found, the class will be registered in the
application servers naming directory under the name you specify in the annotations `name` Attribute,
in this example `MyStatefulSessionBean`.

## How to inject an instance

Basically DI can be a manual process where you `inject` an instance, needed by another class by
passing it to the constructor. Inside the application server, the injection is a process you can't
see, it's more a kind of magic that happens behind the scenes. So instead of manually passing the
necessary instances to a classes constructor, the DI container will do that for you.

You simply have to tell the DI container what you need. Let's have a look at how that is done.

### Property Injection

The first possibility we have is to annotate a class property

```php

namespace Namespace\Module;

use AppserverIo\Psr\Servlet\ServletConfigInterface;
use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;

/**
 * This is the famous 'Hello World' as servlet implementation.
 */
class HelloWorldServlet extends HttpServlet
{

  /**
   * The SessionBean instance we want to have injected.
   *
   * @var \Namespace\Modulename\MyStatefulSessionBean
   * @EnterpriseBean(name="MyStatefulSessionBean")
   */
  protected $myStatefulSessionBean;

  /**
   * The text to be rendered.
   *
   * @var string
   */
  protected $helloWorld = '';

  /**
   * Initializes the servlet with the passed configuration.
   *
   * @param \AppserverIo\Psr\Servlet\ServletConfigInterface $config
   *   The configuration to initialize the servlet with
   *
   * @return void
   */
  public function init(ServletConfigInterface $config)
  {

    // call parent method
    parent::init($config);

    // prepare the text here
    $this->helloWorld = 'Hello World!';

    // @todo Do all the bootstrapping here, because this method will
    //       be invoked only once when the Servlet Engines starts up
  }

  /**
   * Handles a HTTP GET request.
   *
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest
   *   The request instance
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse
   *   The response instance
   *
   * @return void
   * @see \AppserverIo\Psr\Servlet\Http\HttpServlet::doGet()
   */
  public function doGet(
    HttpServletRequestInterface $servletRequest,
    HttpServletResponseInterface $servletResponse)
  {
    $servletResponse->appendBodyStream($this->helloWorld);
  }
}
```

With the `name` attribute of the `@EnterpriseBean` annotation, you have the possibility to specify the
name of the bean you registered before by annotating it. A more detailed description about the
available annotations is part of the [Persistence-Container](#persistence-container).

### Setter Injection

The second possibility to inject an instance is setter injection.

```php

namespace Namespace\Module;

use AppserverIo\Psr\Servlet\ServletConfigInterface;
use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;

/**
 * This is the famous 'Hello World' as servlet implementation.
 */
class HelloWorldServlet extends HttpServlet
{

  /**
   * The SessionBean instance we want to have injected.
   *
   * @var \Namespace\Modulename\MyStatefulSessionBean
   */
  protected $myStatefulSessionBean;

  /**
   * The text to be rendered.
   *
   * @var string
   */
  protected $helloWorld = '';

  /**
   * Initializes the servlet with the passed configuration.
   *
   * @param \AppserverIo\Psr\Servlet\ServletConfigInterface $config
   *   The configuration to initialize the servlet with
   *
   * @return void
   */
  public function init(ServletConfigInterface $config)
  {

    // call parent method
    parent::init($config);

    // prepare the text here
    $this->helloWorld = 'Hello World!';

    // @todo Do all the bootstrapping here, because this method will
    //       be invoked only once when the Servlet Engines starts up
  }

  /**
   * Handles a HTTP GET request.
   *
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest
   *   The request instance
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse
   *   The response instance
   *
   * @return void
   * @see \AppserverIo\Psr\Servlet\Http\HttpServlet::doGet()
   */
  public function doGet(
    HttpServletRequestInterface $servletRequest,
    HttpServletResponseInterface $servletResponse)
  {
    $servletResponse->appendBodyStream($this->helloWorld);
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

> This method is the preferred one, because it will be refactored not to use reflection in further
> versions.

# Persistence-Container

As described in the introduction, the application is designed inside a runtime environment, like
an application server as appserver.io is. The following example gives you a short introduction
on how you can create a stateful session bean and the way you can invoke it's method on the client side.

First thing you can to do is create your SessionBean. What is a SessionBean? A SessionBean is basically a plain PHP class.
However, you MUST not instantiate it directly, because the application server takes care of its complete
lifecycle. Therefore, if you need an instance of a SessionBean, you must ask the application server
to give you an instance.

The persistence container will give you a proxy to the session bean that allows you to
invoke all methods the SessionBean provides, just like you would do with a normal instance. But,
the proxy also allows you to call this method over a network as remote method call. Using the
persistence container client makes it easy for you, if your SessionBean is on the same
application server instance or even on another appserver in your network. This gives you the possibility
to distribute the components of your application over your network, which means a great and
seamless scalability.

You have to tell the persistence container what type of SessionBean you would like to have. This MUST
be done by simply adding an annotation to the class doc block. The possible annotations therefore
are

* @Singleton
* @Stateless
* @Stateful


## @Singleton SessionBean

A SessionBean with a @Singleton annotation will be created only one time for each application.
This means, whenever you'll request an instance, you'll receive the same one. If you set a
variable in the SessionBean, it'll be available until you'll overwrite it, or the application
server has been restarted.

## @Stateless SessionBean

Contrary to a singleton session bean, a SessionBean with a @Stateless annotation will always
be instantiated, when you request it. It has NO state, and is only valid for the time you invoke a method on
it.

## @Stateful SessionBean

The @Stateful SessionBean is a compromise between the two other types. It is stateful for the session
with the ID you pass to the client, when you request the instance. A stateful SessionBean is
useful, for instance, if you want to implement something like a shopping cart. If you declare the shopping cart
instance, a class member of your SessionBean makes it persistent for your session lifetime.

## Example

The following example shows you a really simple implementation of a stateful SessionBean providing
a counter that'll be raised whenever you call the `raiseMe()` method.

```php

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

Save the SessionBean in `/opt/appserver/myapp/META-INF/classes/Namespace/Module/MyStatefulSessionBean.php`.

As described above, you MUST not instantiate it directly. To request an instance of the SessionBean
you MUST use the persistence container client. With the `lookup()` method you'll receive a proxy to
your SessionBean, on that you can invoke the methods as you can do with a real instance.

To develop our HelloWorldServlet further, let's raise the counter with each request to the servlet. To
do this, we have to refactor the `doGet()` method

```php

namespace Namespace\Module;

use AppserverIo\Psr\Servlet\ServletConfigInterface;
use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;

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
   * @param \AppserverIo\Psr\Servlet\ServletConfigInterface $config
   *   The configuration to initialize the servlet with
   *
   * @return void
   */
  public function init(ServletConfigInterface $config)
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
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest
   *   The request instance
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse
   *   The response instance
   *
   * @return void
   * @see \AppserverIo\Psr\Servlet\Http\HttpServlet::doGet()
   */
  public function doGet(
    HttpServletRequestInterface $servletRequest,
    HttpServletResponseInterface $servletResponse)
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

> As we use a @Stateful SessionBean in this example, we MUST start a session so the persistence container can
> bind the SessionBean. If you would have used a @Singleton SessionBean, the effect would be the
> practically the same, but it would have been possible to start a SessionBean per session. As a consequence, each Servlet that
> invokes the `raiseMe()` method on the singleton SessionBean would raise the counter, meaning possibly every call to the server would raise the counter and not each particular call for a particular session.

# Message-Queue

A Message-Queue provides the possibility to process long running tasks in an encapsulated context.
For example, if you want to import a lot of products in your online shop, you can send a
message to the Message-Queue, which then will start the import process in the background without
preventing the calling process to continue.

> Using a Message-Queue gives you the power to use threads without taking care of the pitfalls!

## Got mail!

Before we can send a message, we have to specify what should happen, when we receive one! The
Message-Queue allows you to specify so called `Queues`. Each `Queue` can have a receiver, which
must be a so called `MessageBean`. A `MessageBean` is very similar to a [@Stateless SessionBean](#stateless-sessionbean),
but has only a single point of entry, the `onMessage()` message method. Whenever a message
is sent to the queue, the Message-Queue simply pushes it onto the stack. In the background, a
`QueueWorker` is running in another context and queries the stack for new messages. If a new
message is available, it will be pulled from the stack, as a new instance of the receiver, which the `Queue`
is bound to and will be instantiated to pass the message for processing.

So let us create a simple `Queue` with

```xml
<?xml version="1.0" encoding="UTF-8"?>
<message-queues>
  <message-queue type="ImportReceiver">
    <destination>pms/import</destination>
  </message-queue>
</message-queues>
```

and save this in a file called `/opt/appserver/myapp/META-INF/message-queues.xml`. The next thing
we need is the `MessageBean` that allows us to receive and process a message in a separate thread.

```php

namespace Namespace\Modulename;

use AppserverIo\Appserver\MessageQueue\Receiver\AbstractReceiver;

/**
 * @MessageDriven
 */
class ImportReceiver extends AbstractReceiver
{

  /**
   * Will be invoked when a new message for this message bean will be available.
   *
   * @param \AppserverIo\Psr\MessageQueueProtocol\Message $message   A message this message bean is listen for
   * @param string                                        $sessionId The session ID
   *
   * @return void
   * @see \AppserverIo\Psr\MessageQueueProtocol\Receiver::onMessage()
   */
  public function onMessage(Message $message, $sessionId)
  {
    $data = array_map('str_getcsv', file($message->getMessage()->__toString()));
    foreach ($data as $row) {
      // write the data to the database here
    }
  }
}
```

> Please note: beside the functionality you have to implement with the `onMessage()`
> message method, you must also use the annotation `@MessageDriven` on your class. You MUST annotate the MessageBean in this fashion,
> in order for the container to know about and register it on startup.

Pretty simple for running your import in a separate thread? But what about sending a message to
this `Queue`?

## Send a message

Messages are POPOs (Plain Old PHP Objects) that can be sent over the network. So if you want to send a message, you have
to initialize the Message-Queue Client and specify which `Queue` you want to send the message to.

Again, we will extend our `Servlet` to start an import process on a POST request

```php

namespace Namespace\Module;

use AppserverIo\Psr\Servlet\ServletConfigInterface;
use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;
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
   * @param \AppserverIo\Psr\Servlet\ServletConfigInterface $config
   *   The configuration to initialize the servlet with
   *
   * @return void
   */
  public function init(ServletConfigInterface $config)
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
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest
   *   The request instance
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse
   *   The response instance
   *
   * @return void
   * @see \AppserverIo\Psr\Servlet\Http\HttpServlet::doGet()
   */
  public function doGet(
    HttpServletRequestInterface $servletRequest,
    HttpServletResponseInterface $servletResponse)
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
    HttpServletRequestInterface $servletRequest,
    HttpServletResponseInterface $servletResponse)
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

> To make the process easier, you can use the `@Resource` annotation to let the container inject a sender
> instance, which we can use to send the name of the file containing the data to the `Queue`.

# Timer-Service

In most of your projects you have the need to schedule tasks to be processed at regular intervals
or at a given dates in the future. As PHP itself is a scripting language, it lacks such functionality
and developers usually end up using utilities like CRON, when working on Mac OS X or a Linux distribution, which also requires low level access to the server. And, if you
are working on Windows, it's even a bit more complicated. There is a tool called Scheduler in Windows, but
using it is not as simple to as CRON. Neither of these options are really "programmable" and this is where a Timer Service comes into the game and makes scheduling tasks within your appserver application a dream come true..

As CRON does, the Timer Service allows you to schedule processing of your functional tasks at a given
date or in regular intervals. In contrast to CRON however, it allows you to schedule processing the methods
of your Beans (which remember, are already processed and stored in memory).

How can this be done?

You might know the answer by now, simply by adding an annotation to your method, as follows

```php

namespace Namespace\Modulename;

/**
 * @Singleton(name="ASingletonProcessor")
 */
class ASingletonProcessor extends \Stackable
{

  /**
   * A dummy method invoked by the container upon timer schedule.
   *
   * @param TimerInterface $timer The timer instance
   *
   * @return void
   * @Schedule(dayOfMonth=EVERY, month=EVERY, year=EVERY, minute=EVERY, hour=EVERY)
   */
  public function invokedByTimer(TimerInterface $timer)
  {
    // do something here every minute
  }
}
```

The `@Schedule` annotation on the `invokedByTimer()` method schedules the invocation of this
method every minute without the need to have a CRON configured or running. Such `Timers` can
also be created programatically. If you want to know more about this functionality, have a look at our [example](https://github.com/appserver-io-apps/example).

# AOP

Although in its early days, AOP used to be a buzzword. It has, however, become a development paradigm followed by many of the PHP frameworks out there today. It has been followed for many years already by other languages like Java. Since there is actually no stable PECL extension, nor is AOP part of the PHP core, creating such an environment creates a number of challenges to make applications based on AOP to perform well. Because of its nature, AOP needs to be deeply weaved into your code. Most of the solutions available for PHP solve that by generating so called `proxy classes` that wrap the original methods and allow to weave the advices before, after or around the original implementation.

Since in appserver, we're in a multi-threaded environment and performance is one of our main goals, we were not
able to use anyone one of the available solutions. As we also need to generate proxy classes, we decided
to do that through an autoloader. And since we have enabled an autoloader as part of the appserver.io distribution, you
don't have to configure anything to use AOP in your code.

## How to add an Advice

Integrating AOP in your app can be done in two ways. The first one is to define the pointcuts (and also
advices if you like) in the same class they will get woven into, the second method is to separate them. In the following section we want to describe the second approach.

Let's say we want to log all GET requests on our HelloWorldServlet without adding any
code to the servlet itself. To do this, we first have to create an Aspect class like

```php

namespace Namespace\Module;

/**
 * @Aspect
 */
class LoggerAspect
{

  /**
   * Pointcut which targets all GET requests for all servlets
   *
   * @return void
   * @Pointcut("call(\Namespace\Module\*->doGet())")
   */
  public function allDoGet()
  {
  }

  /**
   * Advice used to log the call to any advised method.
   *
   * @param \AppserverIo\Doppelgaenger\Interfaces\MethodInvocationInterface $methodInvocation
   *   Initially invoked method
   *
   * @return null
   * @Before("pointcut(allIndexActions())")
   */
  public function logInfoAdvice(MethodInvocationInterface $methodInvocation)
  {

    // load class and method name
    $className = $methodInvocation->getStructureName();
    $methodName = $methodInvocation->getName()

    // log the method invocation
    $methodInvocation->getContext()
      ->getServletRequest()
      ->getContext()
      ->getInitialContext()
      ->getSystemLogger()
      ->info(
        sprintf('The method %s::%s is about to be called', className, methodName)
      );
  }
}
```

Store the class in `/opt/appserver/myapp/META-INF/classes/Namespace/Module/LoggerAspect` and
[restart](#start-and-stop-scripts) the application server.

To see the the log message, open the console (Linux/Mac OS X) and enter

```bash
$ tail -f /opt/appserver/var/log/appserver-errors.log
```

Then open `http://127.0.0.1:9080/myapp/helloWorld.do` in your favorite browser, and have a look
at the console.

> AOP is a very powerful instrument to enrich your application with functionality with "controlled" coupling.
> But as in most cases, great power comes with great responsibility. So, it is really
> necessary to keep in mind, where your Aspect classes are and what they do. If not, someone
> will wonder what happens and may need a good amount of time to figure out a problem. To avoid this, we'll
> provide an XML based advice declaration in future versions.

# Design-by-Contract

Beside AOP, [Design-by-Contract](http://en.wikipedia.org/wiki/Design_by_contract) is another
interesting approach we support out-of-the-box, when you think about the architecture of your
software.

First introduced by Bertrand Meyer in connection with his design of the Eiffel programming language,
Design-by-Contract allows you to define formal, precise and verifiable interface specifications of
software components.

Design-by-Contract extends the ordinary definition of classes, abstract classes and interfaces by
adding pre-/postconditions and invariants referred to as `contracts`. As Design-by-Contract is, as
is AOP, not part of the PHP core, we also use annotations to specify these contracts.

## What can be done?

As stated above, this library aims to bring you the power of Design by Contract, an approach to make
your applications more robust and easier to debug. This contains basic features as:

- Use your basic `DocBlock` annotations `@param` and `@return` as type hints (scalar and class/interface
  based), including special features like `typed arrays` using e. g. `array<int>` (currently only works for collections with complex types)
- Specify complex method contracts in PHP syntax using `@requires` as precondition and `@ensures` as
  postcondition
- Specify a state of validity for your classes (e.g. `$this->attribute !== null`) which will be true
  all times using `@invariant`
- The above (not including type safety) will be inherited by every child structure, strengthening your
  object hierarchies
- The library will warn you (exception or log message) on violations of these contracts

## How does it work?

We use a system of autoloading and code creation to ensure our annotations will be enforced.
This features a 4 step process:

- Autoloader : Handles autoloading and will know if contract enforcement is needed for a certain file.
  If so (and the cache is empty) the call will be directed to the Generator/Parser Combo
- Parser : Will parse the needed file using [`Tokenizer`](<http://www.php.net/manual/en/book.tokenizer.php>)
  and provide information for further handling.
- Generator : Will use stream filters to create a new file structure definition containing configured enforcement
- Cache : Will allow us to omit Parser and Generator for future calls, to speed up usage significantly.

## Usage

Supposed, we want to make sure, that the counter in our stateful SessionBean will always be an integer, we can
define a simple contract, therefore

```php

namespace Namespace\Module;

/**
 * This is demo implementation of stateful session bean.
 *
 * @Stateful
 * @invariant is_integer($this->counter)
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

Depending on your configuration, if a method would try to set a string on the counter variable, the
Design-by-Contract implementation would either throw an exception or write an error message to our
log file under `/opt/appserver/var/log/appserver-errors.log`.

# Runtime Environment

The appserver.io runtime environment is delivered by the package [runtime](<https://github.com/appserver-io-php/runtime>).
This package  provides a runtime which is system independent and encloses a thread-safe
compiled PHP environment. Besides the most recent PHP 5.5.x version the package comes with following installed
extensions:

* [pthreads](http://github.com/appserver-io-php/pthreads)
* [appserver](https://github.com/appserver-io/php-ext-appserver) (contains some replacement functions
  which behave badly in a multi-threaded environment)

Additionally, the PECL extensions [XDebug](http://pecl.php.net/package/xdebug) and [ev](http://pecl.php.net/package/ev)
are compiled as shared modules. `XDebug` is necessary to render detailed code coverage reports when
running unit and integration tests. `ev` will be used to integrate a timer service in one of the future
versions.

# Configuration

We believe that the appserver should be highly configurable, so anyone interested can fiddle
around with it. Therefore, we provide a central configuration file located at `/opt/appserver/etc/appserver.xml`.

This file contains the complete [architecture](#the-architecture) as an XML structure.

So if you want to change used components, introduce new services or scale the system by adding
additional servers you can do so with some lines of XML.You might have a look at a basic
`appserver.xml`.

## The Architecture

In this example we have a shortened piece of the `appserver.xml` file to understand how the
architecture is driven by configuration.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<appserver xmlns="http://www.appserver.io/appserver">

  <!-- define user, group and default umask applied when creating directories and files -->
  <params>
    <param name="user" type="string">_www</param>
    <param name="group" type="string">staff</param>
    <param name="umask" type="string">0002</param>
  </params>

  <containers>

    <!-- by default we combine all servers in one container -->
    <container name="combined-appserver" type="AppserverIo\Core\GenericContainer">
      <description>
        <![CDATA[
          This is an example of a webserver container
          that handles http requests in common way
        ]]>
      </description>
      <deployment type="AppserverIo\Appserver\Core\GenericDeployment" />
      <host
        name="localhost"
        appBase="/webapps"
        serverAdmin="info@appserver.io"
        serverSoftware="appserver/1.0.0-beta (mac) PHP/5.5.16" />

        <servers>

          <!-- this is the default configuration for the HTTP server -->
          <server
            name="http"
            type="\AppserverIo\Server\Servers\MultiThreadedServer"
            worker="\AppserverIo\Server\Workers\ThreadWorker"
            socket="\AppserverIo\Server\Sockets\StreamSocket"
            serverContext="\AppserverIo\Server\Contexts\ServerContext"
            requestContext="\AppserverIo\Server\Contexts\RequestContext"
            loggerName="System">

            <!-- define the parameters to configure the server instance -->
            <params>
              <param name="admin" type="string">info@appserver.io</param>
              <param name="software" type="string">
                appserver/1.0.0.0 (darwin) PHP/5.5.16
              </param>
                <param name="transport" type="string">tcp</param>
                <param name="address" type="string">127.0.0.1</param>
                <param name="port" type="integer">9080</param>
                <param name="workerNumber" type="integer">64</param>
                <param name="workerAcceptMin" type="integer">3</param>
                <param name="workerAcceptMax" type="integer">8</param>
                <param name="documentRoot" type="string">webapps</param>
                <param name="directoryIndex" type="string">
                    index.do index.php index.html index.htm
                </param>
                <param name="keepAliveMax" type="integer">64</param>
                <param name="keepAliveTimeout" type="integer">5</param>
                <param name="errorsPageTemplatePath" type="string">
                    var/www/errors/error.phtml
                </param>
            </params>

            <!-- define the environment variables -->
            <environmentVariables>
              <environmentVariable
                condition="" definition="LOGGER_ACCESS=Access" />
            </environmentVariables>

            <!-- define the connection handler(s) -->
            <connectionHandlers>
              <connectionHandler
                type="\AppserverIo\WebServer\ConnectionHandlers\HttpConnectionHandler" />
            </connectionHandlers>

            <!-- define authentication basic/digest -->
            <authentications>
              <authentication uri="^\/admin.*">
                <params>
                  <param name="type" type="string">
                    \AppserverIo\Http\Authentication\BasicAuthentication
                  </param>
                  <param name="realm" type="string">
                    appserver.io Basic Authentication System
                  </param>
                  <param name="hash" type="string">
                    YXBwc2VydmVyOmFwcHNlcnZlci5pMA==
                  </param>
                </params>
              </authentication>
            </authentications>

            <!-- allow access to everything -->
            <accesses>
              <access type="allow">
                <params>
                  <param name="X_REQUEST_URI" type="string">.*</param>
                </params>
              </access>
            </accesses>

            <!-- define a virtual host -->
            <virtualHosts>
              <virtualHost name="example.local">
                <params>
                  <param name="admin" type="string">
                    admin@appserver.io
                  </param>
                  <param name="documentRoot" type="string">
                    /opt/appserver/webapps/example
                  </param>
                </params>
              </virtualHost>
            </virtualHosts>

            <!-- the webserver modules we want to load -->
            <modules>
              <!-- REQUEST_POST hook -->
              <module
                type="\AppserverIo\WebServer\Modules\VirtualHostModule"/>
              <module
                type="\AppserverIo\WebServer\Modules\AuthenticationModule"/>
              <module
                type="\AppserverIo\WebServer\Modules\EnvironmentVariableModule" />
              <module
                type="\AppserverIo\WebServer\Modules\RewriteModule"/>
              <module
                type="\AppserverIo\WebServer\Modules\DirectoryModule"/>
              <module
                type="\AppserverIo\WebServer\Modules\AccessModule"/>
              <module
                type="\AppserverIo\WebServer\Modules\CoreModule"/>
              <module
                type="\AppserverIo\WebServer\Modules\PhpModule"/>
              <module
                type="\AppserverIo\WebServer\Modules\FastCgiModule"/>
              <module
                type="\AppserverIo\Appserver\ServletEngine\ServletEngine" />
              <!-- RESPONSE_PRE hook -->
              <module
                type="\AppserverIo\WebServer\Modules\DeflateModule"/>
              <!-- RESPONSE_POST hook -->
              <module
                type="\AppserverIo\Appserver\Core\Modules\ProfileModule"/>
            </modules>

            <!-- bound the file extensions to the responsible module -->
            <fileHandlers>
              <fileHandler name="servlet" extension=".do" />
              <fileHandler name="fastcgi" extension=".php">
                <params>
                  <param name="host" type="string">127.0.0.1</param>
                  <param name="port" type="integer">9010</param>
                </params>
              </fileHandler>
            </fileHandlers>

        </server>

        <!-- Here, additional servers might be added -->

      </servers>
    </container>
  </containers>
</appserver>
```

In the above example you can see three important components of the appserver architecture being
used. The [*container*](docs/docs/architecture.md#container>), [*server*](docs/docs/architecture.md#server) and a
[*protocol*](docs/docs/architecture.md#protocol>) (if you did not read about our basic [architecture](docs/docs/architecture.md)
you should now). We are basically building up a container which holds a server using the websocket
protocol to handle incoming requests.

### Container Configuration

A *container* is created by using the `container` element within the `containers` collection
of the `appserver` document element. Two parts of the XML create a specific container, in the system on startup:

* The `type` attribute states a class extending our `AbstractContainerThread`, which defines a
  container to be a specific type of container.

* The `deployment` element states a class containing preparations for starting up the container.
  It can be considered a hook, which will be invoked before the container will be available.

That is basically everything there is to do to create a new container. To make use of it, it has
to contain at least one *server* within its `servers` collection.

### Server Configuration

The *servers* contained by our *container* can also be loosely drafted by the XML configuration and
will be instantiated on container boot-up. To enable a *server* you have to mention three basic
attributes of the element:

* The `type` specifies a class implementing the `ServerInterface`, which implements the basic
  behaviour of the server on receiving a connection and how it will handle it.

* The `socket` attribute specifies the type of socket the server should open. E.g. a stream or
  asynchronous socket

* The `serverContext` specifies the server's source of configuration and container for runtime
  information e.g. ServerVariables like `DOCUMENT_ROOT`

So we have our specific server which will open a certain port and operate in a defined context. But
to make the server handle a certain type of request, it needs to know which *protocol* to speak.

This can be done using the `connectionHandler` element. Certain server wrappers can handle certain
protocols. Therefore, we can use the protocols which a server wrapper, e.g. `WebServer` supports in
form of connection handlers. [WebServer](<https://github.com/appserver.io/webserver>)
offers a `HttpConnectionHandler` class. By using it, the server is able to understand the HTTP
protocol.

### Application Configuration

Beside the Container and Server configuration, it is also possible to configure an application. Each application
can have it's own autoloaders and managers. By default, each application found in the application
servers webapp directory `/opt/appserver/webapps` will be initialized with the defaults, defined
in `/opt/appserver/etc/appserver/conf.d/context.xml`

```xml
<?xml version="1.0" encoding="UTF-8"?>
<context
  name="globalBaseContext"
  factory="AppserverIo\Appserver\Application\ApplicationFactory"
  type="AppserverIo\Appserver\Application\Application"
  xmlns="http://www.appserver.io/appserver">

  <!--
  <params>
    <param name="cache.dir" type="string">/cache</param>
    <param name="session.dir" type="string">/session</param>
  </params>
  -->

  <classLoaders>

    <!-- necessary to load files from the vendor directory of your application -->
    <classLoader
      name="ComposerClassLoader"
      interface="ClassLoaderInterface"
      type="AppserverIo\Appserver\Core\ComposerClassLoader"
      factory="AppserverIo\Appserver\Core\ComposerClassLoaderFactory">
      <directories>
        <directory>/vendor</directory>
      </directories>
    </classLoader>

    <!-- necessary to load files from WEB-INF/classes and META-INF/classes, also -->
    <!-- provides the functionality for Design-by-Contract and AOP               -->
    <classLoader
      name="DgClassLoader"
      interface="ClassLoaderInterface"
      type="AppserverIo\Appserver\Core\DgClassLoader"
      factory="AppserverIo\Appserver\Core\DgClassLoaderFactory">
      <params>
        <param name="environment" type="string">production</param>
        <param name="enforcementLevel" type="integer">7</param>
        <param name="typeSafety" type="boolean">1</param>
        <param name="processing" type="string">logging</param>
      </params>
      <directories>
        <directory enforced="true">/common/classes</directory>
        <directory enforced="true">/WEB-INF/classes</directory>
        <directory enforced="true">/META-INF/classes</directory>
      </directories>
    </classLoader>
  </classLoaders>

  <managers>

    <!-- provides object management services -->
    <manager
      name="ObjectManagerInterface"
      type="AppserverIo\Appserver\DependencyInjectionContainer\ObjectManager"
      factory="AppserverIo\Appserver\DependencyInjectionContainer\ObjectManagerFactory">
      <descriptors>
        <descriptor>AppserverIo\Appserver\DependencyInjectionContainer\Description\ServletDescriptor</descriptor>
        <descriptor>AppserverIo\Appserver\DependencyInjectionContainer\Description\SingletonSessionBeanDescriptor</descriptor>
        <descriptor>AppserverIo\Appserver\DependencyInjectionContainer\Description\StatefulSessionBeanDescriptor</descriptor>
        <descriptor>AppserverIo\Appserver\DependencyInjectionContainer\Description\StatelessSessionBeanDescriptor</descriptor>
        <descriptor>AppserverIo\Appserver\DependencyInjectionContainer\Description\MessageDrivenBeanDescriptor</descriptor>
      </descriptors>
    </manager>

    <!-- provides services necessary for DI -->
    <manager
      name="ProviderInterface"
      type="AppserverIo\Appserver\DependencyInjectionContainer\Provider"
      factory="AppserverIo\Appserver\DependencyInjectionContainer\ProviderFactory"/>

    <!-- provides the services necessary to handle Session- and MessageBeans -->
    <manager
      name="BeanContextInterface"
      type="AppserverIo\Appserver\PersistenceContainer\BeanManager"
      factory="AppserverIo\Appserver\PersistenceContainer\BeanManagerFactory">
        <!-- params>
          <param name="lifetime" type="integer">1440</param>
          <param name="garbageCollectionProbability" type="float">0.1</param>
        </params -->
        <directories>
          <directory>/META-INF/classes</directory>
        </directories>
      </manager>

      <!-- provides the functionality to define and run a Queue -->
      <manager
        name="QueueContextInterface"
        type="AppserverIo\Appserver\MessageQueue\QueueManager"
        factory="AppserverIo\Appserver\MessageQueue\QueueManagerFactory"/>

      <!-- provides the functionality to define Servlets handling HTTP request -->
      <manager
        name="ServletContextInterface"
        type="AppserverIo\Appserver\ServletEngine\ServletManager"
        factory="AppserverIo\Appserver\ServletEngine\ServletManagerFactory">
        <directories>
          <directory>/WEB-INF/classes</directory>
        </directories>
      </manager>

      <!-- provides functionality to handle HTTP sessions -->
      <manager
        name="SessionManagerInterface"
        type="AppserverIo\Appserver\ServletEngine\StandardSessionManager"
        factory="AppserverIo\Appserver\ServletEngine\StandardSessionManagerFactory"/>

      <!-- provides functionality to handle Timers -->
      <manager
        name="TimerServiceContextInterface"
        type="AppserverIo\Appserver\PersistenceContainer\TimerServiceRegistry"
        factory="AppserverIo\Appserver\PersistenceContainer\TimerServiceRegistryFactory"/>

      <!-- provides functionality to handle HTTP basic/digest authentication -->
      <manager
        name="AuthenticationManagerInterface"
        type="AppserverIo\Appserver\ServletEngine\Authentication\StandardAuthenticationManager"
        factory="AppserverIo\Appserver\ServletEngine\Authentication\StandardAuthenticationManagerFactory"/>

      <!-- provides functionality to preload Advices found in WEB-INF/classes or META-INF/classes -->
      <manager
        name="AspectManagerInterface"
        type="AppserverIo\Appserver\AspectContainer\AspectManager"
        factory="AppserverIo\Appserver\AspectContainer\AspectManagerFactory"/>
  </managers>
</context>
```

If your application doesn't use any of the defined class loaders or managers or you want to implement
your own managers, you can define them in a `context.xml` file, which you must include with your
application. Your own customized file, has to be stored in `META-INF/context.xml`. When the application
server starts, this file will be parsed and your application will be initialized with the class loaders
and managers you have defined there.

> Please be aware: the default class loaders and managers provide most of the functionality
> described above. So if you remove them from the `context.xml`, you have to expect unexpected and incorrect behaviour.

### Module Configuration

The web server comes with a package of default modules. The functionality that allows us to configure
a virtual host or environment variables, for example, is also provided by two very important modules.

#### Rewrite Module

The rewrite module can be used according to the `\AppserverIo\WebServer\Interfaces\HttpModuleInterface` interface.
It needs an initial call of the `init` method and will process any request offered to the `process` method.
The module is best used within the [`webserver`](<https://github.com/appserver-io/webserver>)
project, as it offers all the needed infrastructure.

##### Rules

Most important part of the rewrite module is the way in which it can perform rewrites. All rewrites are
based on rewrite rules which consist of three important parts:

- *condition string* : Conditions, which have to be met in order for the rule to take effect.
  This is explained in more detail [under condition syntax](#condition-syntax)

- *target string* : The target to rewrite the requested URI to. Within this string you can use
  backreferences similar
  to the Apache mod_rewrite module with the difference that you have to use the `$ syntax`
  (instead of the `$/%/%{} syntax` of Apache).

  Matching rule conditions, which you specifically pick out via regex are also part of available back-references
  as well as server and environment variables.

  *Simple example* : A condition like `(.+)@$X_REQUEST_URI` would produce a back reference `$1`
  with the value `/index` for a requested URI `/index`. The target string `$1/welcome.html` would
  therefore result in a rewrite to `/index/welcome.html`

- *flag string* : You can use flags similar to mod_rewrite which are used to make rules react in a
  certain way or influence further processing. Learn more in the section [on flage](#flags)

##### Condition Syntax

The Syntax of possible conditions is roughly based on the possibilities of Apache's RewriteCondition
and RewriteRule combined.

To make use of such a combination you can chain conditions together using the `{OR}` symbol for
OR-combined, and the `{AND}` symbol for AND-combined conditions.

Please be aware that AND takes precedence over OR! Conditions can either be PCRE regex or certain fixed
expressions. So a condition string of `([A-Z]+\.txt){OR}^/([0-9]+){AND}-f` would match only real files
(through `-f`) which either begin with numbers or end with capital letters and the extension .txt.

As you might have noticed: Backslashes do **not have to be escaped**.

You might also be curious of the `-f` condition. This is a direct copy of Apaches -f RewriteCondition.
We also support several other expressions to regex based conditions which are:

 - *<<COMPARE_STRING>* : Is the operand lexically preceding `<COMPARE_STRING>`?
 - *><COMPARE_STRING>* : Is the operand lexically following `<COMPARE_STRING>`?
 - *=<COMPARE_STRING>* : Is the operand lexically equal to `<COMPARE_STRING>`?
 - *-d* : Is the operand a directory?
 - *-f* : Is the operand a real file?
 - *-s* : Is the operand a real file of a size greater than 0?
 - *-l* : Is the operand a symbolic link?
 - *-x* : Is the operand an executable file?

If you are wondering what the `operand` might be: it is **whatever you want it to be**! You can specify
any operand you like using the `@` symbol. All conditions within a rule will use the next operand to
their right and if none is given the requested URI. For example:

- *`([A-Z]+\.txt){OR}^/([0-9]+)`* Will take the requested URI for both conditions (note the `{OR}` symbol)
- *`([A-Z]+\.txt){OR}^/([0-9]+)@$DOCUMENT_ROOT`* Will test both conditions against the document root
- *`([A-Z]+\.txt)@$DOCUMENT_ROOT{OR}^/([0-9]+)`* Will only test the first one against the document root
  and the second against the requested URI

You might have noted the `$` symbol before `DOCUMENT_ROOT` and remembered it from the back-reference
syntax. That's because all Apache common server vars can be explicitly used as back-references too!

That does not work for you? Need the exact opposite? No problem!

All conditions, whether regex or expression based can be negated using the `!` symbol in front of
them! So `!^([0-9]+)` would match all strings which do NOT begin with a number and `!-d` would match
all non-directories.

##### Flags

Flags are used to further influence processing. You can specify as many flags per rewrite as you like,
but be aware of their impact! Syntax for several flags is simple: just separate them with a `,` symbol.
Flags which might accept a parameter can be assigned one by using the `=` symbol. Currently supported
flags are:

- *L* : As rules are normally processed one after the other, the `L` flag will make the flagged rule
  the last one processed if matched.

- *R* : If this flag is set we will redirect the client to the URL specified in the `target string`.
   If this is just a URI, we will redirect to the same host. You might also specify a custom status
   code between 300 and 399 to indicate the reason for/kind of the redirect. Default is `301` aka
   `permanent`

- *M* : Stands for map. Using this flag you can specify an external source (have a look at the Injector
  classes of the WebServer project) of a target map. With `M=<MY_BACKREFERENCE>` you can specify what
  the map's index has to be to match. This matching is done **only** if the rewrite condition matches and will
  behave as another condition

#### Virtual-Host Module

The module can be used according to the `\AppserverIo\WebServer\Interfaces\HttpModuleInterface`
interface. It needs an initial call of the `init` method and will process any request offered to
the `process` method. The module is best used within the [webserver](<https://github.com/appserver-io/webserver>)
project as it offers all needed infrastructure.

If you need to configure a virtual host, it should look like the
following example, which enables a Magento installation under `http://magento.dev:9080`.

```xml
<virtualHosts>
  <virtualHost name="magento.dev">
    <params>
        <param name="admin" type="string">info@appserver.io</param>
        <param name="documentRoot" type="string">webapps/magento</param>
    </params>
    <rewrites>
        <rewrite condition="-d{OR}-f{OR}-l" target="" flag="L" />
        <rewrite condition="(.*)" target="index.php/$1" flag="L" />
    </rewrites>
    <accesses>
        <access type="allow">
            <params>
                <param name="X_REQUEST_URI" type="string">
                  ^\/([^\/]+\/)?(media|skin|js|index\.php).*
                </param>
            </params>
        </access>
    </accesses>
  </virtualHost>
</virtualHosts>
```

## Configuration Defaults

You will see that we provide basic front-end implementations of services the appserver runtime
provides. If you want to use these services yourself, you should have a look into the code of our
apps and read about [app development](#deployment).

You might be curious about the different ports we use. Per default, the appserver will open several
ports at which it's services are available. As we do not want to block (or be blocked by) other
services, we use ports in a higher range.

As a default, we use the following ports:

* WebContainer

    - HTTP Server: `9080`
    - HTTPS Server: `9443`

* Persistence-MQ-Container

    - Persistence-Container: `8585`
    - Message-Queue: `8587`

You can change the default port mapping by using the [configuration file](#the-architecture).
If you are interested in our naming, you can see our container->server pattern, you might want to
have a deeper look into our [architecture](http://appserver.io/get-started/documentation/1.1/architecture.html)

# Deployment

The deploy directory in the appserver.io Application Server distribution is the location end users can place their
deployment content (e. g. phar files) to have it deployed into the server runtime.

Users, particularly those running production systems, are encouraged to use the appserver.io AS management APIs to
upload and deploy deployment content.

## Deployment Modes

The scanner actually only supports a manual deployment mode, which means that you have to restart the server to process
deployment of your content. In this mode, the scanner will not attempt to directly monitor the deployment content and
decide if or when the end user wishes the content to be deployed or undeployed. Instead, the scanner relies on a system
of marker files, with the user's addition or removal of a marker file serving as a sort of command telling the scanner
to deploy, undeploy or redeploy content.

It is also possible to copy your unzipped content directly into the webapps folder. After restarting the webserver
your content will then be deployed without having any impact on the deployment scanner, because only zipped (.phar)
content will be recognized.

## Marker Files

The marker files always have the same name as the deployment content to which they relate, but with an additional file
suffix appended. For example, the marker file to indicate the example.phar file should be deployed is named
example.phar.dodeploy. Different marker file suffixes have different meanings.

The relevant marker file types are:

| Marker       | Description                                                     |
|:-------------|:----------------------------------------------------------------|
| .dodeploy    | Placed by the user to indicate that the given content should be deployed or redeployed into the runtime.                     |
| .deploying   | Placed by the deployment scanner service to indicate that it has noticed a .dodeploy file and is in the process of deploying the content. This marker file will be deleted, when the deployment process completes.                                   |
| .deployed    | Placed by the deployment scanner service to indicate that the given content has been deployed into the runtime. If an end user deletes this file and no other marker is available, the content will be undeployed.                                     |
| .failed      | Placed by the deployment scanner service to indicate that the given content failed to deploy into the runtime. The content of the file will include some information about the cause of the failure. Note that, removing this file will make the deployment eligible for deployment again.                       |
| .undeploying | Placed by the deployment scanner service to indicate that it has noticed a .deployed file has been deleted and the content is being undeployed. This marker file will be deleted, when the undeployment process completes.                        |
| .undeployed  | Placed by the deployment scanner service to indicate that the given content has been undeployed from the runtime. If this marker file is deleted by the user, it has no impact.                       |

## Basic workflows

All examples assume variable $AS points to the root of the appserver.io AS distribution.

Windows users: the examples below use UNIX shell commands; see the [Windows Notes](#windows-notes) below.

1. Add new zipped (.phar) content and deploy it:

```
$ cp target/example.phar $AS/deploy
$ touch $AS/deploy/example.phar.dodeploy
```

2. Undeploy currently deployed zipped (.phar) content:

```
$ rm $AS/deploy/example.phar.deployed
```

3. Replace currently deployed zipped (.phar) content with a new version and redeploy it:

```
$ cp target/example.phar $AS/deploy
$ mv $AS/deploy/example.phar.deployed $AS/deploy/example.phar.dodeploy
```

## Windows Notes

The above examples use UNIX shell commands. Windows equivalents are:

| UNIX           | Windows                 |
|:---------------|:------------------------|
| cp src dest    | xcopy /y src dest       |
| cp -r src dest | xcopy /e /s /y src dest |
| rm afile       | del afile               |
| touch afile    | echo >> afile           |

Note that the behavior of ```touch``` and ```echo``` are different, but the differences are not relevant to the usages.

# Uninstall

Before uninstalling, you should [stop all the services](#start-and-stop-scripts), which are still running (rpm-based packages will see to that themselves), otherwise there might be problems with existing pid-files on Linux and Mac for the next time you install it.

To uninstall the appserver on Linux, you might rely on your package management system.
On Windows you can use the normal uninstall process provided by the operating system.

Under Mac OS X you can simply delete the `/opt/appserver` folder that containers all installed files.

# External Links

* All about appserver.io at [appserver.io](http://www.appserver.io)
