---
layout: docs_1_1
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
permalink: /get-started/documentation/1.1/servlet-engine.html
---

Originally Servlets are the Java counterpart to other dynamic web technologies like PHP or the Microsoft .NET platform. In contrast to PHP, a Servlet written in Java is not a script that is interpreted per request. It is, rather, a class instantiated when the Servlet Engine starts a process request by invoking one of its methods.

> In most cases, this is a major advantage of the common PHP method of loading the script on each request again. Since PHP applications, mostly based on frameworks like Yii or Symfony, have been growing tremendously during the last years, repetitiously reloading all script files required by the application slows down performance critically. This is why caching is a major part of nearly all frameworks currently. Caching ensures the application will respond to the request within an acceptable timeframe. However, it is the origin of many problems, such as how to invalidate parts of the cache during an application's runtime.

By using a Servlet Engine, you can implement your application logic as usual, but without the expensive bootstrapping process, which is a part of every modern framework. A Servlet is a very fast and simple way to implement an entry point to handle HTTP requests. It allows you to execute all performance critical tasks, like bootstrapping, in a method called `init()`, when the Servlet Engine starts. In other words, the bootstrapping process is loaded once into memory and it stays there, until the appserver is stopped.

## Benefits of a Servlet Engine

Let's have a look at how the Servlet Engine is implemented in appserver. Imagine a servlet as a class that implements the servlet interface, part of our PSR's. So each class (or servlet) provides an MVC pattern controller kind of functionality, by invoking methods, when a request is made to the server. Two things have to be considered when implementing the first servlet: Which requests need to be operated on by the servlet and what functionality is to be provided.

As with many other frameworks, our Servlet Engine uses a URL path to map a request to a controller. Within the Servlet Enginer, this is simply a single servlet. You can write as many servlets as you want, but you do not need to provide any configuration. The following section demonstrates how to map a URL path to a servlet.

```php
<?php

namespace AppserverIo\Example\Servlets;

use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;

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
    $servletResponse->appendBodyStream('Hello World!');
  }
}
```

To map a URL to a servlet, you can simply use the `@Route` annotation. With the `name` attribute you specify a unique name in your application's scope. The attribute `urlPattern` allows you to specify a list of URL patterns you want to map to the servlet. In our example, we want to map the URL's like `http://127.0.0.1:9080/examples/helloWorld.do` to the `HelloWorldServlet`, 

Last but not least, we have to implement the `doGet()` method, which is invoked, when a `GET` request, is sent. Therefore, the `doGet()` method of the `HelloWorldServlet`, and the functionality we provide in the method, is the main entry point of handling the request. In our example above, we only added the `Hello World!` text, in order to render it in the response.

That is a simple procedure. Given you have downloaded and installed the latest version of the appserver.io, create a folder `examples/WEB-INF/classes/AppserverIo/Example/Servlets` in the `webapps` folder of your installation. In this folder, create a new file named `HelloWorldServlet.php`, copy the code from above and save it. After [restarting]({{ "/get-started/documentation/basic-usage.html#start-and-stop-scripts" | prepend: site.baseurl }}) the application server, open the URL `http://127.0.0.1:9080/examples/helloWorld.do` in your favorite browser. You should see the text `Hello World`. Congratulations, you have written your first servlet!

> Simplicity is one of our main goals, because we want you to write your applications with a minimum of configuration effort, none actually. To write an application that perfectly works with appserver.io, you only have to download and install it, create some folders and write your code.

## Bootstrapping a Servlet

As described before, bootstrapping a framework with every request is a very expensive procedure due to its repetition. Using an application server with a Servlet Engine has major advantages. First, parsing configuration like the `@Route` annotation is done only once when the application server starts. Secondly, you have the possibility to do all other expensive steps in an `ìnit()` method, which will be invoked by appserver.io, when the servlet is instantiated and initialized at startup. The next section extends our previous example.

```php
<?php

namespace AppserverIo\Example\Servlets;

use AppserverIo\Psr\Servlet\ServletConfig;
use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;

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
    $servletResponse->appendBodyStream($this->resources['hello-world.en_US.key']);
  }
}
```

We extended the example by reading the translated `Hello World!` from a resource file, when the application server starts. When we handle the request later, we only need to resolve the translation from the array with the resources by its key.

> You can get major performance improvements by letting appserver.io do CPU expensive functionality during startup. Keep in mind, that you get a copy of the servlet when, the `doGet()` method is invoked. Therefore, it does not make sense to write data to members, because it will be not available in the next request.

## Passing data from a configuration

In some cases, it will be necessary to pass data to the `init()` method, e. g. configuration values. You can also do this with the `@Route` annotation. Imagine, we want to make the path to the file with the resources configurable.

```php
<?php

namespace AppserverIo\Example\Servlets;

use AppserverIo\Psr\Servlet\ServletConfig;
use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;

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
    $servletResponse->appendBodyStream($this->resources['hello-world.en_US.key']);
  }
}
```

With the `ìnitParams` attribute of the `@Route` annotation, you can specify a list of parameters. This list will be available later in the `$config` instance passed to the `ìnit()` method. You can specify a random number of key/value pairs, whereas the first value will be the key you will use to load the value with later. In our example, we register a path to our resources file `WEB-INF/resources.ini` with the key `resourceFile` in our servlet configuration. Afterwards, we load the path from the servlet configuration in the `ìnit()` method.

> You might think it does not make sense specifying such values in an annotation. Keep in mind that you can override these values in an XML configuration. So, the values specified in the annotation are only default values. We will see an example of how we can override these values in an XML configuration later.

## Starting a Session

Starting a session is one of the things needed in nearly every application. Below is an example, which demonstrates how to implement a session within appserver.

```php
<?php

namespace AppserverIo\Example\Servlets;

use AppserverIo\Psr\Servlet\ServletConfig;
use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;

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
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest
   *   The request instance
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse
   *   The response instance
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

    // store the username found in the POST data
    $session->putData('username', $servletRequest->getParameter('username'));
  }
}
```

The simple example above demonstrates how a session is started and how data is added to it. Since session handling is a complex topic, we will break it down into single steps for better understanding. By default, you do not have to configure anything, but you have the option to do it in an XML configuration file that is stored in you applications `WEB-INF` folder as `web.xml`.

> In contrast to a simple webserver, we have the possibility to hold a number of sessions persistent in the application server's memory. This guarantees excellent performance, but comes along with great responsibility for the developer. When writing an application that runs on an application server, you do always have keep an eye on the memory footprint of your application.

## Optional XML Configuration

As described earlier, writing a servlet is easy, appserver already provides annotations, which enable you to configure some basics out-of-the-box. Appserver covers a good portion of application configuration scenarios. However, you will still need the ability override the defaults.

You can override the default configuration values in a simple XML file called `web.xml`, which is stored in your application's `WEB-INF` folder. In this file, you can configure servlets and override values you have specified in annotations, change the default session settings and give or deny users access to resources with HTTP basic or digest authentication. Here is an example of such a file.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<web-app xmlns="http://www.appserver.io/appserver">

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
    <url-pattern>/helloWorld.do*</url-pattern>
  </servlet-mapping>

  <!-- define the Login Configuration for this application -->
  <login-config>
    <auth-method>Form</auth-method>
      <realm-name>example-realm</realm-name>
      <form-login-config>
        <form-login-page>/dhtml/login.dhtml</form-login-page>
        <form-error-page>/dhtml/login.dhtml</form-error-page>
      </form-login-config>
  </login-config>

  <!-- security roles referenced by this web application -->
  <security-role>
    <role-name>Administrator</role-name>
    <description>The Administrator role</description>
  </security-role>

  <!-- define a Security Constraint on this application -->
  <security-constraint>
    <display-name>The hello world servlet</display-name>
    <web-resource-collection>
      <web-resource-name>Protect the hello world servlet</web-resource-name>
      <url-pattern>/helloWorld*</url-pattern>
      <http-method>POST</http-method>
      <http-method>GET</http-method>
    </web-resource-collection>
    <auth-constraint>
      <description>Enable access for the Administrator role</description>
      <role-name>Administrator</role-name>
    </auth-constraint>
  </security-constraint>

  <!-- define an optional error page -->
  <error-page>
    <error-code-pattern>404</error-code-pattern>
    <error-location>/dhtml/error.dhtml</error-location>
  </error-page>

</web-app>
```

At first glance, the XML configuration might seem a bit complicated. No worries, we'll go through it node by node and give a brief introduction to the configuration opportunities.

### Meta-Data Configuration
| Configuration | Data Type | Description |
| ----------| ----------- | ----------- |
|`/web-app/display-name` | *string* | This node does not have any functionality. You can use it to give your application a name. In later versions, this name will be displayed in the admin UI, where all applications are listed.|
|`/web-app/description` | *string* | This node does not have any functionality. You can add a short description about your application's functionality. In later versions, this description will be displayed in application details in the admin UI. |

### Session Configuration

By default, you do not have to change the session configuration.

| Configuration | Data Type | Description |
| --------------| --------- | ----------- |
|`/web-app/session-config/session-name` | *string* | In some cases, for example, if you want to specify an individual cookie name for your session, you can do so here. To change the name of the session cookie, customize the value of the node. Please be aware that you can only use chars as defined in [RFC2616 - Section 2.2](http://tools.ietf.org/html/rfc2616#section-2.2). |
|`/web-app/session-config/session-file-prefix` | *string* | As sessions are persisted to the file system after the configured inactivity timeout, by default 1.440 seconds, you can also specify a prefix for the filename used to store the session data. To specify a custom prefix, change the value for this node. As for the cookie name, be aware of the restrictions for filenames, which depend on the OS you run appserver.io on. Also, keep in mind that you can only customize the prefix, and the session-ID will always be added as a suffix. For example, if you specify `foo_` as value for `/web-app/session-config/session-file-prefix`, the session files result in something like `foo_au1ctio31v10lm9jlhipdlurn1`. |
| `/web-app/session-config/session-save-path` | *string* | If you want to change the default folder, where the application server stores the session files, you can specify the absolute path value in this node. This will be necessary if you want to use a shared folder to store the session files, for example, on a cluster file system. |
| `/web-app/session-config/session-maximum-age` | *integer* | The value of this node specifies the maximum age of the session. By default, this value is `0`, which means the session would never expire, except when it is destroyed by your application. The session maximum age only depends on the sessions creation time. This means, independent of how often a user changes session data, the maximum age of the session will not be extended. After the maximum age is reached the session is destroyed and the user has to create a new one by logging in again, for example. If you want to implement something like a sticky login functionality, you must set the value for `session-maximum-age` to `0`. Also, the value for the session-cookie-lifetime should be set to a value very far in the future. |
| `/web-app/session-config/session-inactivity-timeout` | *integer* | This node allows you to quickly specify a timeout that marks the session as inactive. This enables the application server to remove the session from the memory and persists it to the configurated persistence layer. By default, we persist sessions to the file system.  We only have a file system persistence manager as part of our standard session manager. By registering your session manager, you can implement your persistence manager that enables persisting sessions in cache systems like Redis, for example. |
| `/web-app/session-config/garbage-collection-probability` | *float* | This node allows you to set a value, which sets how often the garbage collector should be invoked. You can specify a value between `100` and `0`. The higher the value, the higher is the probability that the garbage collector will be invoked. With the number of decimals, you extend the range. Therefore, the probability that the GC is invoked is higher. The default value for this node is `0.1`.
| `/web-app/session-config/session-cookie-lifetime` | *integer* | Independent of the session-maximum-age value, you can quickly specify a lifetime for the session cookie, which enables the browser cookie to expire and invalidates the session. |
| `/web-app/session-config/session-cookie-domain` | *string* | The value of this node specifies the domain to set in the session cookie. The default is `localhost`. It results in the hostname of the server, which generated the cookie according to cookie's specification. |
| `/web-app/session-config/session-cookie-path` | *string* | With the value of this node, you specify the path to set in the session cookie, which defaults to `/`. The path tells the browser to use the cookie only when requesting pages containing the path you specify. If you use the default value, the cookie will be valid for all paths in your application. |
| `/web-app/session-config/session-cookie-secure` | *boolean* | The value for this node specifies whether cookies should only be sent over secure connections. By default, we have set this value to `false`, which means that cookies will always be sent. |
| `/web-app/session-config/session-http-only` | *boolean* | This configuration node allows you to mark the cookie as accessible only through the HTTP protocol. Setting this value to `true` makes the cookie inaccessible by scripting languages, such as JavaScript. This will effectively reduce identity theft through XSS attacks. Keep in mind, that although it is not supported by all browsers. By default, this value is set to `false`. |

### Global Initialization Parameters

Something you can not configure with annotations are context parameters. You should use context parameters, when you want to specify and pass values to your application. To do this, you would need to bootstrap your servlets, for example, with the path to an application specific configuration file.

| Configuration | Data Type | Description |
| --------------| --------- | ----------- |
| `/web-app/context-param` | *string* | You can specify a random number of context parameters, which you can load from the servlet context. For example, if we want to load the path to the `applicationProperties`, defined as context parameters in our [example](#optional-xml-configuration) XML configuration file. |

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

> Context parameters enables you to load data from configuration files, databases, webservices, etc., during application server startup. In the end, this means that this is the best place to bootstrap your servlet or your application.

### Servlet Configuration

The following section demonstrates how to define the servlets and how to override annotations you have defined in the servlets, which are parsed when appserver starts.

| Configuration | Data Type | Description |
| --------------| --------- | ----------- |
| `/web-app/servlet` | *string* | Often, the easiest way is to use annotations to define your servlets and map request URLs to them. Sometimes it is necessary to define servlets in the `web.xml` file. As the order in which the servlets are loaded is relevant for matching the URL, it might be necessary to change it manually in this file. You can define a servlet by adding the following snippet to your configuration file. |

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
| --------------| --------- | ----------- |
|`/web-app/servlet/description` | *string* | You can specify a short description of the servlet here. The description has no usage. In later versions, this description will be displayed in the servlet details in an admin UI. |
| `/web-app/servlet/display-name` | *string* | This node does not have a functionality. You can use it to give your servlet a name. In later versions, this name will be displayed in  an admin UI, where all servlets are listed. |
| `/web-app/servlet/servlet-name` | *string* | You must specify a name, unique to your application, for the servlet here. This name is used to [map](#servlet-mapping) your servlet to a request URL later. |
|`/web-app/servlet/servlet-class` | *string* | The Servlet Engine needs to know which class has to be instantiated, when initializing the servlet. You have to specify the fully qualified name of your servlet here. |
| `/web-app/servlet/init-param` | *string* |You can specify a random number of initialization parameters here. The parameters are parsed, when the application starts und you can load them from the servlet configuration. |
| `/web-app/servlet/init-param/param-name` | *string* | This represents the parameter's key. You should only use US-ASCII chars (octets 0 - 127) for the key. |
| `/web-app/servlet/init-param/param-value` | *string* |This nodes value is the parameter's value. Here you can specify anything that is allowed to specify in an XML file. |

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

> A good example is the [Routlt](https://github.com/appserver-io/routlt) library. The library provides a basic
> [router/controller implementation](https://github.com/appserver-io/routlt/blob/master/src/AppserverIo/Routlt/ControllerServlet.php). The new annotation feature allows for /route/controller(servlet) mapping in annotations, which will be pre-loaded, when the application server starts.

### Servlet Mapping

Finally, it is necessary to map a URL path to the servlet we had configured earlier. As the Servlet Engine is a webserver module, it is bound to the file extension `.do`. You can change this in the `appserver.xml` configuration file in directory `etc/appserver/appserver.xml`.

| Configuration | Data Type | Description |
| --------------| --------- | ----------- |
| `/web-app/servlet-mapping` | *string* | You can specify as many servlet mappings as you need. The mapping maps a `servlet-name` to a `url-pattern`. The mapping has to be specified by the following subnodes. |
| `/web-app/servlet-mapping/servlet-name` | *string* | This node has to contain the `servlet-name` you had specified in `/web-app/servlet/servlet-name` node. |
| `/web-app/servlet-mapping/url-pattern` | *string* | To stick to our example, the URL patterns `/helloWorld.do` and `/helloWorld.do*` have to be mapped to the `HelloWorldServlet` with `servlet-name` `helloWorld`, as displayed in the following. This is necessary because the `HttpServlet::service()` method has to be invoked either when you open `http://127.0.0.1:9080/example/helloWorld.do` or anything like `http://127.0.0.1:9080/example/helloWorld.do/my/path/info?test=test`. You can use a URL mapping containing the `*` as a catch all too, for example. |

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

> If you want to write a servlet, you should map a path with a `.do` file extension to it, as long as you do not change the default configuration. An exception is the default servlet because this should catch all requests that will not match any other servlets. To match a servlet on a URL path, we use the PHP [fnmatch](http://php.net/fnmatch) method.

### Authentication and Authorization

Security is a very important topic when writing applications, especially web applications. You have the possibility to secure your servlets with HTTP basic or digest as described in [RFC2617](http://tools.ietf.org/html/rfc2617) as well as HTML form based authentication.

#### Removed old Security Configuration

Up with version 1.1.1 we've replaced the possibility to specify the HTTP authentication type with the following configuration option

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

If you still want to restrict access to a file or directory, please have a look at the webserver's [authentication]({{ "/get-started/documentation/webserver.html#authentications" | prepend: site.baseurl }}) configuration. The replacement functionality is quite more complex, but also much more powerful.

#### Login Configuration

To restrict access to a complete or only parts of an application, the first thing to do is, to definition the login configuration. The login configuration specify's how a user will be requested to login to the application as well what happens if the login attempt failed.

```xml
<login-config>
  <auth-method>Form</auth-method>
  <realm-name>example-realm</realm-name>
  <form-login-config>
    <form-login-page>/dhtml/login.dhtml</form-login-page>
    <form-error-page>/dhtml/login.dhtml</form-error-page>
  </form-login-config>
</login-config>
```

The Login Configuration allows the following parameters.

| Configuration | Data Type | Description |
| --------------| --------- | ----------- |
| `/login-config/type` | *string* | The login type can either be `basic`, `digest` or `form`, whereas only the `form` type makes usage of the `form-login-config` options. The types `basic` and `digest` uses the browsers default login dialog to handle login and failed login attempts. |
| `/login-config/realm-name` | *string* | The name of the realm that has to be used to lookup the user that wants to login. |
| `/login-config/form-login-config/form-login-page` | *string* | If the `form` type has been specified, the value specifies the servlet, that has to render the login dialog itself. |
| `/login-config/form-login-config/form-login-page` | *string* | If the `form` type has been specified, the value specifies the servlet, that has to render a dialog for a failed login attempt. |

The configured `<realm-name>example-realm</realm-name>` **MUST** match exactly match a `<securityDomain name="example-realm">` node `name` attribute configured by the [Authentication Manager's]({{ "/get-started/documentation/configuration.html#authentication-manager" | prepend: site.baseurl }}) realm configured in the `META-INF/context.xml` file.

When a user attempt's to login to a protected web resource, the security subsytem will load the matching realm and tries to authenticate the user aginst the realm's login modules. Depending on the value of the login modules `flag` attributes, the credentials **MUST** be accepted at least by one login module or by all.

> Be aware, that the Login Configuration itself does **NOT** provide any authentication functionality. It only specifies which dialogues has to be used for authentication!

The login form defined by the `/login-config/form-login-config/form-login-page` value **MUST** have the following structure 

```html
<form action="p_security_check.do" method="post">
    <input type="text" name="p_username">
    <input type="password" name="p_password">
    <button type="submit">Sign in</button>
</form>
```

whereas the action **MUST** be `p_security_check.do`, the name of the username field `p_username` and the password field `p_password`. This is necessary, as the security subsystem have to work without any application specific classes and needs unique form/field names that can be definitely identified. It is possible to change that behaviour by implementing a custom authenticator and override the default one it in the application's Authentication Manager configuration.

> For a working example have a look at the [example](<https://github.com/appserver-io-apps/example>) application package.

#### Roles

The next step to do is to configure the roles that has to be available in the application. The roles will be available when defining the security constraints described in the next chapter.

````xml
<security-role>
    <role-name>Guest</role-name>
    <description>The Guest role</description>
</security-role>
<security-role>
    <role-name>Customer</role-name>
    <description>The Customer role</description>
</security-role>
<security-role>
    <role-name>Administrator</role-name>
    <description>The Administrator role</description>
</security-role>
```

The Role Configuration allows the following parameters. An application can have as many Roles as necessary.

| Configuration | Data Type | Description |
| --------------| --------- | ----------- |
| `/security-role/role-name` | *string* | The unique name of the role. |
| `/security-role/description` | *string* | A short description of the role. |

The roles that have been defined in the `WEB-INF/web.xml` file **MUST** correlate with the ones, available and loaded by the login modules, configured by the [Authentication Manager's]({{ "/get-started/documentation/configuration.html#authentication-manager" | prepend: site.baseurl }}) realm.

#### Security Constraints

The last step to do is to define which resources and how they should be protected.

```xml
<security-constraint>
  <display-name>The user profile</display-name>
  <web-resource-collection>
    <web-resource-name>Protect the user profile</web-resource-name>
    <url-pattern>/index.do/user*</url-pattern>
    <http-method>POST</http-method>
    <http-method>GET</http-method>
  </web-resource-collection>
  <auth-constraint>
   <description>Enable profile access for the customer role</description>
   <role-name>Customer</role-name>
  </auth-constraint>
</security-constraint>
<security-constraint>
  <display-name>All other resources</display-name>
  <web-resource-collection>
    <web-resource-name>All other resources</web-resource-name>
    <url-pattern>/*</url-pattern>
  </web-resource-collection>
</security-constraint>
```

The Security Constraint Configuration allows the following parameters, whereas an application can define as many Security Constraints as necessary.

| Configuration | Data Type | Description |
| --------------| --------- | ----------- |
| `/security-constraint/display-name` | *string* | The name that'll be displayed in the admin GUI (once it will be available). |
| `/security-constraint/web-resource-colection/web-resource-name` | *string* | A short description of the resource that has to be protected. |
| `/security-constraint/web-resource-colection/url-pattern` | *string* | The URL pattern that matches the resources. |
| `/security-constraint/web-resource-colection/http-method` | *string* | The HTTP method that needs a user to be logged into the system. |
| `/security-constraint/web-resource-colection/http-method-omission` | *string* | The HTTP method that are **NOT** protected. |
| `/security-constraint/auth-constraint/description` | *string* | A short description of the authentication constraint. |
| `/security-constraint/auth-constraint/role-name` | *string* | The role name a user **MUST** have to get access to the requested web resource. |

> It is very important, that the order the Security Constraints have been defined makes sense, as the first matching Security Constraint will be used to authorize the request. This means, if a child filder that has no constraint will be defined **AFTER** the parent, a user needs to login to access the folder, which may not be the inteded behaviour!

### Error Pages

By default the Servlet Engine uses the template `var/www/dhtml/error.dhtml` to render the error messages for all HTTP requests with a response code > 399. This behaviour can be customized by overriding it with a HTTP code specific error page.

```xml
<error-page>
  <error-code-pattern>404</error-code-pattern>
  <error-location>/dhtml/error.dhtml</error-location>
</error-page>
```

The Error Pages Configuration allows the following parameters, whereas an error page for each error code can be defined.

| Configuration | Data Type | Description |
| --------------| --------- | ----------- |
| `/error-page/error-code-pattern` | *string* | The pattern to match the error code. |
| `/error-page/error-location` | *string* | The path to the Servlet that should be rendered when the error code matches. |

> The Servlet Engine also supports wildcards, that can be handled by PHP's fnmatch() method, which can be used to match the HTTP response code.