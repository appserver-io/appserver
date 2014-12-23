---
layout: post
title:  Servlet-Engine Basics
date:   2014-12-22 18:00:00
author: wagnert
version: 1.0.0beta2
categories: [Servlet-Engine]
---

As announced in our last blog post, we want to give you an overview of the services and functions an 
application server will provide. Let's start with the Servlet-Engine, because we think that this will
be a good start for all developers who never get in contact with an application server before.

Reading this post, you'll get the feeling, that a [Servlet-Engine](https://github.com/appserver-io/appserver/wiki/05.-Servlet-Engine) is something like a framework.
This is not completely wrong. A Servlet-Engine is not a framework, but it is a part in a framework. So you have
to see it as the controller part of a MVC framework. This is the reason why you'll read stuff about routing,
request methods and other like that. As the framworks out there are actually not implemented for running in
an  application server like appserver.io, they provide stuff like a HTTP foundation library by themselves. 
We hope, that someday all of the frameworks recognize the advantages an application server provide and will
be ported to run on top of [appserver.io](http://www.appserver.io) by using the infrastructure with all
functionality it provides, instead of implementing it again and again in each of them. For sure this will be 
a long way, but i think i'llbe worth it.

### Problems without Servlet-Engine
***

Some of you, who are familiar with a Java Servlet-Engine will wonder: a Servlet-Engine in PHP? Does 
that make sense because of all the great frameworks out there. We think yes, because one of the big
issues with the frameworks is, that they'll get huge during the last years and bootstrapping became
a big meaning meanwhile. As PHP is used as a scripting language, the problem of the bootstrapping is,
that is has be done at every request and therefore it leads to a massive loss of performance. One 
solution can be caching, but that entails many other problems that you may have faced in your projects.

> A Servlet-Engine is not a solution for all the problems you'll face nor for bad code you'll probably
> write. But it gives you new possiblities because it's stateful and that is something all other frameworks
> can not provide actually, they try to simulate that by introducing cache solutions!

### How can a Servlet-Engine help
***

One solution can be using a Servlet-Engine, like we integrated in our application server. Imagine a
`Servlet` as a class that implements the `Servlet` interface, part of our PSR's, that provides some
kind of MVC pattern controller functionality by implementing some methods that will be invoked when
a request came in, nothing more, nothing less. So to implement your first `Servlet`, you have to think
about two things. First, which requests should our `Servlet` dispatch, the second is what functionality
it should provide.

As in many other frameworks do, our Servlet-Engine use a URL path to map a request to a controller, in
our case this will be a `Servlet`. You can write as many servlets as you want, but you dont't need to
write any configuration therefor. Let's have a look at how you can map an URL path to a `Servlet`

```php
namespace AppserverIo\Examples\Servlets;

use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\Http\HttpServletRequest;
use AppserverIo\Psr\Servlet\Http\HttpServletResponse;

/**
 * This is the famous 'Hello World' as servlet implementation.
 *
 * @Route(name="helloWorld", urlPattern={"/helloWorld.do", "/helloWorld.do*"})
 */
class HelloWorldServlet extends HttpServlet
{

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
    $servletResponse->appendBodyStream('Hello World!');
  }
}
```

To map a `Servlet` to a URL, you can simply use the `@Route` annotation. With the `name` attribute you have
specify a name that has to be unique in your application scope, the attribute `urlPattern` allows you to
specify a list of URL patterns you want to map the `Servlet` to. In our example, we want to map the 
`HelloWorldServlet` to the URL's like `http://127.0.0.1:9080/examples/helloWorld.do`, whatever parameters
are appended.

Last but not least, we've to implement the `doGet()` method, that'll be invoked, when a `GET` request,
has been sent, and therefore is the main entry point to handle the request by implementing the functionality
we want to provide. For our first example, we only want to add the `Hello World!` that should be rendered.

That is pretty simple, we think! So, given you've downloaded and installed the latest version of the application
server, create a folder `examples/WEB-INF/classes/AppserverIo/Example/Servlets` under the `webapps` folder of 
your installation. In the folder, create a new file named `HelloWorldServlet.php`, copy the code from above and
save it. After [restarting](https://github.com/appserver-io/appserver/wiki/02.-Basic-Usage#start-and-stop-scripts)
the application server, open the URL `http://127.0.0.1:9080/examples/helloWorld.do` in your favorite browser.
You should see the text `Hello World`. Congratulations, you have written your first `Servlet`!

> Simplicity is one of our main targets, because we want you to write your applications with a minimum of 
> configuration, actually NULL. So to start write an application that perfectly works in the application 
> server, you only have to download and install it, create some folders and write your code!

### Bootstrapping a Servlet

As described before, bootstrapping a framework with every request is a very expensive procedure if have be done
again and again. Using an application server with a Servlet-Engine can be a great help here. Beside the fact,
that parsing configuration like the `@Route` annotation, will be done only once when the application server
starts, You additionally have the possiblity to do all that expensive stuff in an `ìnit()` method that'll be
invoked by the application server when the `Servlet` is instanciated and initialized at startup. Let's extend
our previous example

```php
namespace AppserverIo\Examples\Servlets;

use AppserverIo\Psr\Servlet\ServletConfig;
use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\Http\HttpServletRequest;
use AppserverIo\Psr\Servlet\Http\HttpServletResponse;

/**
 * This is the famous 'Hello World' as servlet implementation.
 *
 * @Route(name="helloWorld", urlPattern={"/helloWorld.do", "/helloWorld.do*"})
 */
class HelloWorldServlet extends HttpServlet
{

  /**
   * Resources parsed from a INI file.
   *
   * @var array
   */
  protected $resources;

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
    
    // parse the application resources from an INI file
    $this->resources = parse_ini_file($config->getWebappPath() . '/WEB-INF/resources.ini');
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
    $servletResponse->appendBodyStream($this->resources['hello-world.en_US.key']);
  }
}
```

We've extended the example by reading the translated `Hello World!` from a resource file, when the application
server starts. When we handle the request later, we only need to resolve the translation from the array with
the resources by its key.

> You can get major performance improvements, letting the application server do CPU expensive functionality
> during startup. Keep in mind, that you get a copy of the `Servlet` when the `doGet()` method is invoked.
> Therefor it doesn't make sense to write data to members there, because it'll be not available in the next
> request!

### Passing data from configuration
***

In some cases, it'll be necessary, that you need to pass data to the `init()` method, e. g. configuration
values. You can also do this with the `@Route` annotation. So imagine, we want to make the path to the file
with the resources configurable.

```php
namespace AppserverIo\Examples\Servlets;

use AppserverIo\Psr\Servlet\ServletConfig;
use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\Http\HttpServletRequest;
use AppserverIo\Psr\Servlet\Http\HttpServletResponse;

/**
 * This is the famous 'Hello World' as servlet implementation.
 *
 * @Route(name="helloWorld",
 *        urlPattern={"/helloWorld.do", "/helloWorld.do*"}
 *        initParams={"resourceFile", "WEB-INF/resources.ini"})
 */
class HelloWorldServlet extends HttpServlet
{

  /**
   * Resources parsed from a INI file.
   *
   * @var array
   */
  protected $resources;

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
    
    // parse the application resources from an INI file
    $this->resources = parse_ini_file(
       $config->getWebappPath() . '/' . $config->getInitParam('resourceFile')
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
    $servletResponse->appendBodyStream($this->resources['hello-world.en_US.key']);
  }
}
```

With the `ìnitParams` attribute of the `@Route` annotation you can specify a list of parameters that'll
available later by the `$config` instance passed to the `ìnit()` method. You can specify a random number
of key/value pair whereas the first value will be the key you later can load the value with. In our example
we register a the path to our resources file `WEB-INF/resources.ini` under the key `resourceFile` in our
servlet configuration. In the `ìnit()` method we can then load the path from the servlet configuration.

> You maybe think, that it doesn't make to much sense specifying such values in an annotation. That can be
> true, but keep in mind, that you can overwrite these values later in a XML configuration. So you can see
> the values specified in the annotation as some kind of default value. We'll see an example of how we can
> overwrite these values in a XML configuration later.

### Starting a Session
***

Starting a session is one of the things you'll need in nearly every application. Start a new session is
quite simple. So let's see how we can integrate session handling in our application.

```php
namespace AppserverIo\Examples\Servlets;

use AppserverIo\Psr\Servlet\ServletConfig;
use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\Http\HttpServletRequest;
use AppserverIo\Psr\Servlet\Http\HttpServletResponse;

/**
 * This is the famous 'Hello World' as servlet implementation.
 *
 * @Route(name="helloWorld",
 *        urlPattern={"/helloWorld.do", "/helloWorld.do*"}
 *        initParams={"resourceFile", "WEB-INF/resources.ini"})
 */
class HelloWorldServlet extends HttpServlet
{

  /**
   * Resources parsed from a INI file.
   *
   * @var array
   */
  protected $resources;

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
    
    // parse the application resources from an INI file
    $this->resources = parse_ini_file(
      $config->getWebappPath() . '/' . $config->getInitParameter('resourceFile')
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
  
    // try to load the username from the session
    if ($session = $servletRequest->getSession()) {
      $username = $session->getData('username');
    } else { // username not available
      $username = 'Unknown';
    }
    
    // prepare the hello world string, this should look like 'Hello World %s!'
    $helloWorld = sprintf($this->resources['hello-world.en_US.key'], $username);
    
    // append the prepared hello world to the response
    $servletResponse->appendBodyStream($helloWorld);
  }
  
  /**
   * Handles a HTTP POST request.
   *
   * This is a very simple example that shows how to start a new session, adding
   * the username passed with the POST data to the session.
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
    
    // store the username found in the POST data
    $session->putData('username', $servletRequest->getParameter('username'));
  }
}
```

This is a very simple example of how you can start a session and add some data to it. Session handling is a
complicated thing and we tried to break it down to be as simple as we can imagine. By default you don't have
to configure anything, but you still have to option to configure everything in a XML configuration file that
has to be stored in you applications `WEB-INF` folder as `web.xml`.

> Other as a simple web server, we've the possiblity to hold a number of sessions persistent in the application
> servers memory. This ensures great performance on the one hand, but came with great responsibility for the
> developer on the other. By writing an application that should be runned in an application server, you have to
> be aware of what you are doing and have a look at the memory footprint of your application.

### Optional XML Configuration
***

As described before, we thought, that it have to be very simple, to write a `Servlet`. Therefore we provide
annotations that gives you the power to configure the basics. For sure, for many things we deliver a good
default configuration, but you need the power to overwrite that.

You can overwrite the default configuration values in a simple XML file called `web.xml` that you've to 
store in your applications `WEB-INF` folder. In that file you can configure Servlets, overwrite values you've
specified in annotations, change the default session settings and give or deny users access to resources with
HTTP basic or digest authentication.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<web-app version="2.4">

  <!-- application meta information -->
  <display-name>appserver.io example application</display-name>
  <description>Simple example application.</description>

  <!-- default session configuration -->
  <session-config>
    <session-name>PHPSESSID</session-name>
    <session-file-prefix></session-file-prefix>
    <!-- defaults to application specifc temporary directory 
         e. g. /opt/appserver/var/tmp/example/session -->
    <session-save-path></session-save-path>
    <!-- default configuration for session/cookie lifetime and gc -->
    <session-maximum-age>0</session-maximum-age>
    <session-inactivity-timeout>1440</session-inactivity-timeout>
    <garbage-collection-probability>0.1</garbage-collection-probability>
    <!-- cookie configuration -->
    <session-cookie-lifetime>86400</session-cookie-lifetime>
    <session-cookie-domain>localhost</session-cookie-domain>
    <session-cookie-path>/</session-cookie-path>
    <session-cookie-secure>false</session-cookie-secure>
    <session-http-only>false</session-http-only>
  </session-config>
  
  <!-- application context initialization parameter -->
  <context-param>
    <param-name>applicationProperties</param-name>
    <param-value>WEB-INF/application.properties</param-value>
  </context-param>

  <!-- define the hello world servlet -->
  <servlet>
    <description>The hello world as servlet implementation.</description>
    <display-name>Hello World</display-name>
    <servlet-name>helloWorld</servlet-name>
    <servlet-class>AppserverIo\Examples\Servlets\HelloWorldServlet</servlet-class>
    <!-- servlet specific application parameter -->
    <init-param>
      <param-name>resourceFile</param-name>
      <param-value>WEB-INF/resources.ini</param-value>
    </init-param>
  </servlet>

  <!-- map it to an URL path -->
  <servlet-mapping>
    <servlet-name>helloWorld</servlet-name>
    <url-pattern>/helloWorld.do</url-pattern>
  </servlet-mapping>

  <!-- allow access to known users only -->
  <security>
    <url-pattern>/helloWorld.do*</url-pattern>
    <auth>
      <auth_type>Basic</auth_type>
      <realm>test</realm>
      <adapter_type>htpasswd</adapter_type>
      <options>
        <file>WEB-INF/htpasswd</file>
      </options>
    </auth>
  </security>

</web-app>
```

The XML configuration seems to be a bit complicated for the start, right? So we will go through it, node by 
node and give you a brief introduction what you can configure with it.

#### Meta-Data Configuration
***

##### `/web-app/display-name` *string*
This node actually doesn't has any functionality. Actually you can use it to giver your application a name.
In later versions, this name will be displayed in admin UI where all applications are listed. 

##### `/web-app/description` *string*
As `/web-app/display-name`, this node has also no functionality. You can add a short description about your
application functionality. In later versions this description will be displayed in application details in
admin UI.

#### Session Configuration
***

By default, you'll not have to change the session configuration.

##### `/web-app/session-config/session-name` *string*
In some cases, e. g. if you want to specify a indivdual cookie name for your session, you can do that. To change
the name of the session cookie, customize the value of this node to your choice. Please be aware that you can only
use chars that are defined in [RFC2616 - Section 2.2](http://tools.ietf.org/html/rfc2616#section-2.2).

##### `/web-app/session-config/session-file-prefix` *string* 
As sessions are persisted to the filesystem after the configured inactivity timeout, by default 1.440 seconds,
you can also specify a prefix for the filename used to store the session data. To specify a custom prefix, change
the value for node . As for the cookie name, be aware of the restrictions for filenames, that'll depend on the OS
you run the application server on. Also keep in mind, that you can only customize the prefix and the session-ID
will always we added as suffix. For example, if you specify `foo_` as value for `/web-app/session-config/session-file-prefix`, the session files will result in something like `foo_au1ctio31v10lm9jlhipdlurn1`.

##### `/web-app/session-config/session-save-path` *string*
If you want to change the default folder, the application server stores the session files, you can specify the
absolute path as value of node . This will be necessary if you want to use a shared folder to store the session
files, e. g. on a cluster file system.

##### `/web-app/session-config/session-maximum-age` *integer*
The value of this node specifies the maximum age of the session. By default this value is `0`, what means that
the session would never expire, except it'll be destroyed by your application, e. g. when a user logs out and
you invoke

```php
/**
 * Handles a HTTP POST request, destroys the session and logs the user out.
 *
 * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequest  $servletRequest  
 *   The request instance
 * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponse $servletResponse 
 *   The response instance
 *
 * @return void
 */
public function doPost(HttpServletRequest $servletRequest, HttpServletResponse $servletResponse)
{

    // destroy the session and reset the cookie
    if ($session = $servletRequest->getSession()) {
        $session->destroy('Explicit logout requested ');
    }
}
```

in a `doPost()` of your `Servlet` for example.

> Actually the session maximum age only depends on the sessions creation time. This means, independent how often
> a users changes session data, the maximum age of the session will NOT be extended. After the maximum age is
> reached the session will be destroyed and the user has to create a new one, by re-login for example. If you want
> to implement something like a sticky login functionality, you MUST set the value for `session-maximum-age` to
> `0` and the value for the [session-cookie-lifetime](#web-appsession-configsession-cookie-lifetime) to a value that > points far in the future.

##### `/web-app/session-config/session-inactivity-timeout` *integer*
This node allows you to specify a timeout in seconds that marks the session as inactive. This lets the application
server remove the session from memory and persists it to the configurated persistence layer. By default, we persist
sessions to the file system.

> Actually we only have a filesystem persistence manager as part of our standard session manager. By registering
> your own session manager, you are able to implement your own persistence manager that allows you to persist
> sessions in cache systems like Redis for example.

##### `/web-app/session-config/garbage-collection-probability` *float*
Allows you to set a value, how often the garbage collector will be invoked. You can specify a value between `100`
and `0`. As higher the value, as higher is the probability that the garbage collector will be invoked. With the
number ob decimals you extend the range, and therefor the probability that the GC will be invoked. By default the
value for this node is set to `0.1`.

##### `/web-app/session-config/session-cookie-lifetime` *integer*
Independent from the [session-maximum-age](#web-appsession-configsession-maximum-age-integer) value, you can
specify a lifetime for the session cookie in seconds, that'll let the browser cookie expire and invalidates
the session therefor.

##### `/web-app/session-config/session-cookie-domain` *string*
The value of this node specifies the domain to set in the session cookie. Default is `localhost` which results in
the host name of the server which generated the cookie according to cookies specification.

##### `/web-app/session-config/session-cookie-path` *string*
With this value of this node, you specify the path to set in the session cookie, which defaults to `/`. The path
tells the browser to use the cookie only when requesting pages contains the path you specify. If you use the 
default value, the cookie will be valid for all paths in your application.

##### `/web-app/session-config/session-cookie-secure` *boolean*
The value for this node specifies whether cookies should only be sent over secure connections. By default we've
set this value to `false`, which means that cookies will always be set.

##### `/web-app/session-config/session-http-only` *boolean*
These configuration node allows you to mark the cookie as accessible only through the HTTP protocol. Setting this
value to `true` makes the cookie inaccessible by scripting languages, such as JavaScript. This will effectively
reduce identity theft through XSS attacks. Keep in mind, that although it is not supported by all browsers. By
default, this value is set to `false`.

#### Global Initialization Parameters
Something you actually can not configure with annotations are context parameters. You can and should use context 
parameters when you want to specify and pass values to your application, you would need to bootstrap your servlets,
e. g. the path to a application specific configuration file.

##### `/web-app/context-param` *string*
You can specify a random number of context parameters that you can load from the servlet context. For example, if
we want to load the path to the `applicationProperties`, defined as context parameter in our [example](#optional-xml-configuration) XML configuration file

```xml
<context-param>
  <param-name>applicationProperties</param-name>
  <param-value>WEB-INF/application.properties</param-value>
</context-param>
```

We can do this, by the following code, implemented in the `init()` method of a `Servlet`

```php
/**
 * Initializes the servlet with the application properties.
 *
 * @param \AppserverIo\Psr\Servlet\ServletConfig $servletConfig 
 *   The configuration to initialize the servlet with
 *
 * @throws \AppserverIo\Psr\Servlet\ServletException
 *   Is thrown if the configuration has errors
 * @return void
 * @see \AppserverIo\Psr\Servlet\GenericServlet::init()
 */
public function init(ServletConfig $config)
{

    // load the servlet context
    $context = $config->getServletContext();

    // load path to application and to properties  
    $webappPath = $context->getWebappPath();                 
    $pathToProperties = $context->getInitParameter('applicationProperties')

    // load and initialize the application properties
    $this->applicationProperties = new Properties()
    $this->applicationProperites->load($webappPath . DIRECTORY_SEPARATOR . $pathToProperties);
}
```

> Context parameters enables you to load data from configuration files, databases, webservices on application
> server startup. In the end, this means that this is the best place to bootstrap your servlet or your 
> application.

#### Servlet Configurations
As this post is all about our [Servlet-Engine](https://github.com/appserver-io/appserver/wiki/05.-Servlet-Engine),
maybe the most important thing is, how you can define the servlets, or override annotations you defined in the servlets itself, which will be parsed when the application server starts.

##### `/web-app/servlet` *string*
In many cases, it'll be the easiest way to use annotations to define your sevlets and map them to a request URL.
Sometimes it'll be necessary that you define servlets in the `web.xml` file. As the order, the servlets will be
loaded, is relevant for matching the URL when the request will be handled by the Servlet-Engine, it could be
necessary that you have to manually change it in this file. You can define a servlet by add the following snippet
to your configuration file

```xml
<servlet>
  <description>The Routlt controller servlet implementation.</description>
  <display-name>The Routlt controller servlet</display-name>
  <servlet-name>routlt</servlet-name>
  <servlet-class>\AppserverIo\Routlt\ControllerServlet</servlet-class>
  <init-param>
    <param-name>configurationFile</param-name>
    <param-value>WEB-INF/routes.json</param-value>
  </init-param>
</servlet>
```

You can access this value by invoking the `$this->getInitParameter()` method like

```php
/**
 * Initializes the servlet with the path to the configuration file.
 *
 * @param \AppserverIo\Psr\Servlet\ServletConfig $servletConfig 
 *   The configuration to initialize the servlet with
 *
 * @throws \AppserverIo\Psr\Servlet\ServletException
 *   Is thrown if the configuration has errors
 * @return void
 * @see \AppserverIo\Psr\Servlet\GenericServlet::init()
 */
public function init(ServletConfig $config)
{

  // call parent method
  parent::init($config);

  // load path to the applications configuration file       
  $this->pathToConfigurationFile = $this->getInitParameter('configurationFile')
}
```

> A good example is the [Routlt](https://github.com/appserver-io/routlt) library. This library provides a simple
> [controller implementation](https://github.com/appserver-io/routlt/blob/master/src/AppserverIo/Routlt/ControllerServlet.php), but is actually missing the possibility to map the actions to the request path info
> by annotations, we need a configuration file. This configuration file will be parsed by the controller servlet
> and pre-loads the action classes when the application server starts.

#### Servlet Mappings
Finally it is necessary to map the `Servlet` we've configured before, to a URL path. As the 
[Servlet-Engine](https://github.com/appserver-io/appserver/wiki/05.-Servlet-Engine) is a webserver module, by
default it is bound to the file extension `.do`. You can change this in the `appserver.xml` confguration file
in directory `etc/appserver/appserver.xml`.

##### `/web-app/servlet-mapping` *string*
To stay with our Routlt example, the `ControllerServlet` has to be mapped to the URL patterns `/` and `/*` like

```xml
<servlet-mapping>
  <servlet-name>helloWorld</servlet-name>
  <url-pattern>/helloWorld.do</url-pattern>
</servlet-mapping>
<servlet-mapping>
  <servlet-name>helloWorld</servlet-name>
  <url-pattern>/helloWorld.do*</url-pattern>
</servlet-mapping>
<servlet-mapping>
  <servlet-name>routlt</servlet-name>
  <url-pattern>/</url-pattern>
</servlet-mapping>
<servlet-mapping>
  <servlet-name>routlt</servlet-name>
  <url-pattern>/*</url-pattern>
</servlet-mapping>
```

This is necessary, because the Routlt `ControllerServlet::service()` method has to be invoked either when you open
`http://127.0.0.1:9080` or something like `http://127.0.0.1:9080/index.do?test=test`. You can understand it as a
catch all `Servlet` that will be invoked if no other matches. In the example before, we've add a URL mapping for
the `HelloWorld` servlet. So if you enter the URL `http:://127.0.0.1:9080/helloWorld.do` the URL mapping for this
`Servlet` matches before and the apropriate method will be invoked.

> Keep in mind, if you want to write a servlet, in general you should map it to a path with a `.do` file extension,
> as long as you not change the default configuration for that. An exception is the default servlet, because this
> should catch all requests that will not match by any other servlets. To match a `Servlet` on a URL path, we're
> actually use the PHP [fnmatch](http://php.net/fnmatch) method.

#### HTTP Basic and Digest Authentication

##### `/web-app/security` *string*


