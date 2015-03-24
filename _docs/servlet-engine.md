---
layout: docs
title: Servlet Engine
meta_title: appserver.io servlet engine
meta_description: Originally Servlets are the Java counterpart to other dynamic web technologies like PHP or the Microsoft .NET plattform.
position: 50
group: Docs
subNav:
  - title: How can a Servlet Engine help
    href: how-can-a-servlet-engine-help
  - title: Bootstrapping a Servlet
    href: bootstrapping-a-servlet
  - title: Passing data from configuration
    href: passing-data-from-configuration
  - title: Starting a Session
    href: starting-a-session
  - title: Optional XML Configuration
    href: optional-xml-configuration
permalink: /get-started/documentation/servlet-engine.html
---

Originally Servlets are the Java counterpart to other dynamic web technologies like PHP or the
Microsoft .NET platform. In contrast to PHP, a Servlet written in Java is not a script that is interpreted per request. It is rather a class instantiated when the Servlet Engine starts a
process requests by invoking one of its methods.

> In most cases, this is a major advantage of the common PHP way to load the script on each
> request again. Since PHP applications, mostly based on frameworks like Yii or Symfony, have been growing
> tremendously during the last years, reloading all script files required by the application again
> and again slows down performance critically. This is why meanwhile caching
> is a major part of nearly all frameworks. On the one hand, caching ensures the application to respond
> to the request within an acceptable timeframe. On the other hand, it is the
> origin of many problems, such as how to invalidate parts of the cache during an application's
> runtime.

By using a Servlet Engine, you can implement your application logic as you are used to, without taking care of the expensive bootstrapping process, which is combined with the common legacy frameworks. 
A Servlet is a very fast and simple way to implement an entry point to handle HTTP requests. It allows you to
execute all performance critical tasks, like bootstrapping, in a method called `init()`, when
the Servlet Engine starts.

## Benefits of a Servlet Engine

One solution is using a Servlet Engine as it is intigrated in appserver.io. Imagine a servlet as a class that implements the servlet interface, part of our PSR's. It provides a kind of MVC pattern controller functionality by invoking methods when a request comes in. Two things have to be considered when implementing the first servlet: Which requests need to be dispatched by the servlet and what functionality is to be provided.

As many other frameworks, our Servlet Engine uses a URL path to map a request to a controller. In our case this is a servlet. You can write as many servlets as you want, but you do not need to provide any configuration. The following section demonstrates how to map a URL path to a servlet.

```php
<?php

namespace AppserverIo\Example\Servlets;

use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\Http\HttpServletRequest;
use AppserverIo\Psr\Servlet\Http\HttpServletResponse;

/**
 * This is the famous 'Hello World' as servlet implementation.
 *
 * @Route(name="helloWorld",
 *        urlPattern={"/helloWorld.do", "/helloWorld.do*"})
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

To map a servlet to a URL, you can simply use the `@Route` annotation. With the `name` attribute you
specify a unique name in your application scope. The attribute `urlPattern` allows you to
specify a list of URL patterns you want to map the servlet to. In our example, we want to map the
`HelloWorldServlet` to the URL's like `http://127.0.0.1:9080/examples/helloWorld.do`, regardless of the parameters appended.

Last but not least, we have to implement the `doGet()` method, that is invoked, when a `GET` request,
is sent. Therefore, it is the main entry point of handling the request by implementing the functionality
we want to provide. For our first example, we only want to add the `Hello World!` that needs to be rendered.

That is a simple procedure. Given you have downloaded and installed the latest version of the appserver.io, create a folder `examples/WEB-INF/classes/AppserverIo/Example/Servlets` in the `webapps` folder of
your installation. In this folder, create a new file named `HelloWorldServlet.php`, copy the code from above and
save it. After [restarting]({{ "/get-started/documentation/basic-usage.html#start-and-stop-scripts" | prepend: site.baseurl }})
the application server, open the URL `http://127.0.0.1:9080/examples/helloWorld.do` in your favorite browser.
You should see the text `Hello World`. Congratulations, you have written your first servlet.

> Simplicity is one of our main targets because we want you to write your applications with a minimum of
> configuration efforts, none actually. To write an application that perfectly works with appserver.io,
> you only have to download and install it, create some folders and write your code.

## Bootstrapping a Servlet

As described before, bootstrapping a framework with every request is a very expensive procedure when doing it
again and again. Using an application server with a Servlet Engine has major advantages. First, parsing configuration like the `@Route` annotation is done only once when the application server
starts. Secondly, you have the possibility to do all expensive steps in an `ìnit()` method that will be
invoked by appserver.io when the servlet is instanciated and initialized at startup. The next section extends our previous example.

```php
<?php

namespace AppserverIo\Example\Servlets;

use AppserverIo\Psr\Servlet\ServletConfig;
use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\Http\HttpServletRequest;
use AppserverIo\Psr\Servlet\Http\HttpServletResponse;

/**
 * This is the famous 'Hello World' as servlet implementation.
 *
 * @Route(name="helloWorld",
 *        urlPattern={"/helloWorld.do", "/helloWorld.do*"})
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
        $config->getWebappPath() . '/WEB-INF/resources.ini'
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

We extended the example by reading the translated `Hello World!` from a resource file, when the application
server starts. When we handle the request later, we only need to resolve the translation from the array with
the resources by its key.

> You can get major performance improvements by letting appserver.io do CPU expensive functionality
> during startup. Keep in mind, that you get a copy of the servlet when the `doGet()` method is invoked.
> Therefore, it does not make sense to write data to members because it will be not available in the next
> request.

## Passing data from a configuration

In some cases, it will be necessary to pass data to the `init()` method, e. g. configuration
values. You can also do this with the `@Route` annotation. Imagine, we want to make the path to the file
with the resources configurable.

```php
<?php

namespace AppserverIo\Example\Servlets;

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

With the `ìnitParams` attribute of the `@Route` annotation, you can specify a list of parameters. This list will be available later in the `$config` instance passed to the `ìnit()` method. You can specify a random number
of key/value pair whereas the first value will be the key you load the value with, later. In our example
we register a the path to our resources file `WEB-INF/resources.ini` with the key `resourceFile` in our
servlet configuration. Afterwards, we load the path from the servlet configuration in the `ìnit()` method.

> You might think it does not make sense specifying such values in an annotation. Keep in mind that you can 
> overwrite these values in an XML configuration. So, the values specified in the annotation are some kind of 
> default values. We will see an example of how we can overwrite these values in an XML configuration, later.

## Starting a Session

Starting a session is one of the things needed in nearly every application. This is why we show how to integrate session handling in the application.

```php
<?php

namespace AppserverIo\Example\Servlets;

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

The simple example above demonstrates how a session is started and how data is added to it. Since session handling is a complex topic, we will break it down into single steps for better understanding. By default, you do not have
to configure anything, but you have the option to do it in an XML configuration file that
is stored in you applications `WEB-INF` folder as `web.xml`.

> In contrast to a simple webserver, we have the possibility to hold a number of sessions persistent in the 
> application server's memory. This guarantees excellent performance but comes along with great responsibility
> for the developer. By writing an application that runs on an application server, you have to
> have a look at the memory footprint of your application.

## Optional XML Configuration

As described before, writing a servlet is easy. Therefore, we provide annotations that enable you to configure the basics. For sure, we deliver sound default configurations for many application areas. However, you still need the power to overwrite it.

You can overwrite the default configuration values in a simple XML file called `web.xml`, which is stored in your application's `WEB-INF` folder. In this file, you can configure servlets and overwrite values you have
specified in annotations, change the default session settings and give or deny users access to resources with
HTTP basic or digest authentication.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<web-app version="1.0">

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
    <servlet-class>AppserverIo\Example\Servlets\HelloWorldServlet</servlet-class>
    <!-- servlet specific application parameter -->
    <init-param>
      <param-name>servletProperties</param-name>
      <param-value>WEB-INF/hello-world.properties</param-value>
    </init-param>
  </servlet>

  <!-- map it to an URL path -->
  <servlet-mapping>
    <servlet-name>helloWorld</servlet-name>
    <url-pattern>/helloWorld.do</url-pattern>
  </servlet-mapping>
  <servlet-mapping>
    <servlet-name>helloWorld</servlet-name>
    <url-pattern>/helloWorld.do*</url-pattern>
  </servlet-mapping>

  <!-- allow access to known users only -->
  <security>
    <url-pattern>/helloWorld.do*</url-pattern>
    <auth>
      <auth_type>Basic</auth_type>
      <realm>Basic Authentication Test</realm>
      <adapter_type>htpasswd</adapter_type>
      <options>
        <file>WEB-INF/htpasswd</file>
      </options>
    </auth>
  </security>

</web-app>
```

At first sight, the XML configuration might seem complicated. This is why we go it through node by node and give a brief introduction to the configuration opportunities. 

### Meta-Data Configuration
| Configuration | Data Type | Description |
| ----------| ----------- | ----------- |
|`/web-app/display-name` | *string* | This node does not have a functionality. You can use it to give your application a name. In later versions, this name will be displayed in admin UI where all applications are listed.|
|`/web-app/description` | *string* | This node does not have a functionality. You can add a short description about your application functionality. In later versions, this description will be displayed in application details in admin UI. |

### Session Configuration

By default, you do not have to change the session configuration.

| Configuration | Data Type | Description |
| ----------| ----------- | ----------- |
|`/web-app/session-config/session-name` | *string* | In some cases, for example, if you want to specify an individual cookie name for your session, you can do so. To change the name of the session cookie, customize the value of the node. Please be aware that you can only use chars that are defined in [RFC2616 - Section 2.2](http://tools.ietf.org/html/rfc2616#section-2.2). |
|`/web-app/session-config/session-file-prefix` | *string* | As sessions are persisted to the file system after the configured inactivity timeout, by default 1.440 seconds, you can also specify a prefix for the filename used to store the session data. To specify a custom prefix, change the value for node. As for the cookie name, be aware of the restrictions for filenames, which depend on the OS you run appserver.io on. Also, keep in mind that you can only customize the prefix, and the session-ID will always be added as a suffix. For example, if you specify `foo_` as value for `/web-app/session-config/session-file-prefix`, the session files result in something like `foo_au1ctio31v10lm9jlhipdlurn1`. |
| `/web-app/session-config/session-save-path` | *string* | If you want to change the default folder, the application server stores the session files, you can specify the absolute path value of node . This will be necessary if you want to use a shared folder to store the session files, for example, on a cluster file system. |
| `/web-app/session-config/session-maximum-age` | *integer* | The value of this node specifies the maximum age of the session. By default, this value is `0`, what means that the session would never expire except it is destroyed by your application. The session maximum age only depends on the sessions creation time. This means, independent how often a user changes session data, the maximum age of the session will not be extended. After the maximum age is reached the session is destroyed and the user has to create a new one by re-login, for example. If you want to implement something like a sticky login functionality, you must set the value for `session-maximum-age` to `0`. The value for the [session-cookie-lifetime](#web-appsession-configsession-cookie-lifetime) has to be set to a value that points far in the future. |
| `/web-app/session-config/session-inactivity-timeout` | *integer* | This node allows you to quickly specify a timeout that marks the session as inactive. This enables the application server to remove the session from the memory and persists it to the configurated persistence layer. By default, we persist sessions to the file system.  We only have a file system persistence manager as part of our standard session manager. By registering your session manager, you can implement your persistence manager that enables persisting sessions in cache systems like Redis, for example. |
| `/web-app/session-config/garbage-collection-probability` | *float* | It allows you to set a value, how often the garbage collector is invoked. You can specify a value between `100` and `0`. The higher the value, the higher is the probability that the garbage collector will be invoked. With the number of decimals, you extend the range. Therefore, the probability that the GC is invoked is higher. By default the value for this node is set to `0.1`.
| `/web-app/session-config/session-cookie-lifetime` | *integer* | Independent of the [session-maximum-age](#web-appsession-configsession-maximum-age-integer) value, you can quickly specify a lifetime for the session cookie that enables the browser cookie to expire and invalidates the session. |
| `/web-app/session-config/session-cookie-domain` | *string* | The value of this node specifies the domain to set in the session cookie. The default is `localhost`. It results in the hostname of the server, which generated the cookie according to cookie's specification. |
| `/web-app/session-config/session-cookie-path` | *string* | With the value of this node, you specify the path to set in the session cookie, which defaults to `/`. The path tells the browser to use the cookie only when requesting pages contains the path you specify. If you use the default value, the cookie will be valid for all paths in your application. |
| `/web-app/session-config/session-cookie-secure` | *boolean* | The value for this node specifies whether cookies should only be sent over secure connections. By default, we have set this value to `false`, which means that cookies will always be set. |
| `/web-app/session-config/session-http-only` | *boolean* | This configuration node allows you to mark the cookie as accessible only through the HTTP protocol. Setting this value to `true` makes the cookie inaccessible by scripting languages, such as JavaScript. This will effectively reduce identity theft through XSS attacks. Keep in mind, that although it is not supported by all browsers. By default, this value is set to `false`. |

### Global Initialization Parameters
Something you can not configure with annotations are context parameters. You should use context
parameters when you want to specify and pass values to your application, you would need to bootstrap your servlets,
for example, the path to an application specific configuration file.

| Configuration | Data Type | Description |
| ----------| ----------- | ----------- |
| `/web-app/context-param` | *string* | You can specify a random number of context parameters that you can load from the servlet context. For example, if we want to load the path to the `applicationProperties`, defined as context parameter in our [example](#optional-xml-configuration) XML configuration file. |

```xml
<context-param>
  <param-name>applicationProperties</param-name>
  <param-value>WEB-INF/application.properties</param-value>
</context-param>
```

We can do this, by adding the following code, implemented in the `init()` method to a servlet

```php
<?php

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

  // call parent method
  parent::init($config);

  // load the servlet context
  $context = $config->getServletContext();

  // load path to application and to properties
  $webappPath = $context->getWebappPath();
  $pathToProperties = $context->getInitParameter('applicationProperties')

  // load and initialize the application properties
  $this->applicationProperties = new AppserverIo\Properties\Properties()
  $this->applicationProperites->load($webappPath . DIRECTORY_SEPARATOR . $pathToProperties);
}
```

> Context parameters enables you to load data from configuration files, databases, webservices on application
> server startup. In the end, this means that this is the best place to bootstrap your servlet or your
> application.

### Servlet Configuration
The following section demonstrates how to define the servlets and how to override annotations you have defined in the servlets, which are parsed when appserver.io starts.

| Configuration | Data Type | Description |
| ----------| ----------- | ----------- |
| `/web-app/servlet` | *string* | Often, it is the easiest way to use annotations to define your servlets and map them to a request URL. Sometimes is necessary to define servlets in the `web.xml` file. As the order, in which the servlets are loaded, is relevant for matching the URL, it might be necessary to change it manually in this file. You can define a servlet by adding the following snippet to your configuration file. |

```xml
<servlet>
  <description>The hello world as servlet implementation.</description>
  <display-name>Hello World</display-name>
  <servlet-name>helloWorld</servlet-name>
  <servlet-class>AppserverIo\Examples\Servlets\HelloWorldServlet</servlet-class>
  <init-param>
    <param-name>servletProperties</param-name>
    <param-value>WEB-INF/hello-world.properties</param-value>
  </init-param>
</servlet> 
``` 
| Configuration | Data Type | Description |
| ----------| ----------- | ----------- |
|`/web-app/servlet/description` | *string* | You can specify a short description of the servlet here. The description has no usage. In later versions, this description is displayed in the servlet details in admin UI. |
| `/web-app/servlet/display-name` | *string* | This node does not have a functionality. You can use it to give your servlet a name. In later versions, this name will be displayed in admin UI where all servlets are listed. |
| `/web-app/servlet/servlet-name` | *string* | You must specify a name, unique in your application, for the servlet here. This name is used to [map](#servlet-mapping) your servlet to a request URL later. |
|`/web-app/servlet/servlet-class` | *string* | The Servlet Engine needs to know, which class has to be instantiated when initializing the servlet. You have to specify the fully qualified name of your servlet here. |
| `/web-app/servlet/init-param` | *string* |You can specify a random number of initialization parameters here. The parameters are parsed when the application's start und you can load them from the servlet configuration. |
| `/web-app/servlet/init-param/param-name` | *string* | This represents the parameter's key. You should only use US-ASCII chars (octets 0 - 127) for the key. |
| `/web-app/servlet/init-param/param-value` | *string* |This nodes value is the parameters value. Here you can specify anything that is allowed to specify in a XML file. |

You can access a servlet's initialization parameters by invoking the `$this->getInitParameter()` method as follows.

```php
<?php

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

  // load the servlet context
  $context = $config->getServletContext();

  // load path to servlet and to properties
  $webappPath = $context->getWebappPath();
  $pathToProperties = $this->getInitParameter('servletProperties')

  // load and initialize the application properties
  $this->servletProperties = new AppserverIo\Properties\Properties()
  $this->servletProperties->load($webappPath . DIRECTORY_SEPARATOR . $pathToProperties);
}
```

> A good example is the [Routlt](https://github.com/appserver-io/routlt) library. The library provides a simple
> [controller implementation](https://github.com/appserver-io/routlt/blob/master/src/AppserverIo/Routlt/ControllerServlet.php), but is lacking the possibility to map the actions to the request path info
> by annotations. This configuration file will be parsed by the controller servlet
> and pre-loads the action classes when the application server starts.

### Servlet Mapping
Finally, it is necessary to map the servlet we have configured before, to a URL path. As the Servlet Engine is a webserver module, it is bound to the file extension `.do`. You can change this in the `appserver.xml` confguration file in directory `etc/appserver/appserver.xml`.

| Configuration | Data Type | Description |
| ----------| ----------- | ----------- |
| `/web-app/servlet-mapping` | *string* | You can specify as many servlet mappings as you need. The mapping maps a `servlet-name` to a `url-pattern`. The mapping has to be specified by the following subnodes. |
| `/web-app/servlet-mapping/servlet-name` | *string* | This node has to contain the `servlet-name` you have specified in a `/web-app/servlet/servlet-name` node before. |
| `/web-app/servlet-mapping/url-pattern` | *string* | To stick to our example, the `HelloWorldServlet` with `servlet-name` `helloWorld`, has to be mapped to the URL patterns `/helloWorld.do` and `/helloWorld.do*` as displayed in the following. This is necessary because the `HttpServlet::service()` method has to be invoked either when you open `http://127.0.0.1:9080/example/helloWorld.do` or anything like `http://127.0.0.1:9080/example/helloWorld.do/my/path/info?test=test`. You can understand the URL mapping, containing the `*` as a catch all. |

```xml
<servlet-mapping>
  <servlet-name>helloWorld</servlet-name>
  <url-pattern>/helloWorld.do</url-pattern>
</servlet-mapping>
<servlet-mapping>
  <servlet-name>routlt</servlet-name>
  <url-pattern>/helloWorld.do*</url-pattern>
</servlet-mapping>
```

> If you want to write a servlet, you should map it to a path with a `.do` file extension,
> as long as you do not change the default configuration for that. An exception is the default servlet because this
> should catch all requests that will not match any other servlets. To match a servlet on a URL path, we
> use the PHP [fnmatch](http://php.net/fnmatch) method.

#### HTTP Basic and Digest Authentication
Security is a very important topic when writing applications, especially web applications. You have the
possibility to secure your servlets with HTTP basic or digest authentication as described in [RFC2617](http://tools.ietf.org/html/rfc2617).

| Configuration | Data Type | Description |
| ----------| ----------- | ----------- |
| `/web-app/security` | *string* |  Configuration is done by defining a URL pattern you want to secure and, depending on the authentication type, the parameters against which you want to authenticate. If we want to secure our `helloWorld` servlet using basic authentication, the following snipped is used. |

```xml
<security>
  <url-pattern>/helloWorld.do*</url-pattern>
  <auth>
    <auth_type>Basic</auth_type>
    <realm>Basic Authentication Test</realm>
    <adapter_type>htpasswd</adapter_type>
    <options>
      <file>WEB-INF/htpasswd</file>
    </options>
  </auth>
</security>
```

This protects access when someone tries to open the URL `http://127.0.0.1:9080/example/helloWorld.do` by open the
browsers dialog and request a username and a password.

You can define user credentials with the tool [htpasswd](http://httpd.apache.org/docs/2.2/programs/htpasswd.html)
that will be available on all supported OS, except Windows. On Windows there are optional tools available, for
example you can use [.Htaccesstools](http://www.htaccesstools.com/htpasswd-generator-windows/) online to create a
file.

To create a file for HTTP digest authentication, you can use the tool [htdigest](http://httpd.apache.org/docs/2.2/programs/htdigest.html).
Again, there is an online [website](http://jesin.tk/tools/htdigest-generator-tool/) you can generate a file
that will work on Windows also.

| Configuration | Data Type | Description |
| ----------| ----------- | ----------- |
| `/web-app/security/url-pattern` | *string* | The value of this node allows you to specify an URL pattern. If a request has to be handled, the Servlet Engine again uses the PHP [fnmatch](http://php.net/fnmatch) method to match the URL against the pattern. |
| `/web-app/security/auth/auth_type` | *string* | The value of this node defines the authentication type you want to use. `Basic` enables HTTP basic authentication, `Digest` enables HTTP digest authentication. Depending on the value you have entered, you have to add the appropriate options as described below. |
| `/web-app/security/auth/realm` | *string* | This value defines the text the browser dialogue renders after `The server says:`. If you can specify a short message to the user, he will remember his credentials easily. In our example we specify `Basic Authentication Type`. |
| `/web-app/security/auth/adapter` | *string* | The value for this node defines the adapter used to validate the credentials the user has entered in the browsers dialog. We have `htpasswd` for HTTP basic authentication and `htdigest`for HTTP digest authentication. In later releases, we will provide other adapters, for example, a LDAP implementation you can use for HTTP basic authentication. |
| `/web-app/security/auth/options/file` | *string* | Based on the value for `/web-app/security/auth/auth_type`, you have to enter the relative path to the file containing the `.htpasswd` or `.htdigest` file with the allowed users. |
