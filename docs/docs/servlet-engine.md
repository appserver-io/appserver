# Servlet-Engine

Originally Servlets are the Java counterpart to other dynamic web technologies like PHP or the 
Microsoft .NET plattform. In contrast to PHP, a Servlet written in Java is not a script that'll
be interpreted per request, instead it is a class instantiated when the Servlet Engine starts up
process requests be invoking one of its methods.

> In most cases, this is a major advantage against the common PHP way to load the script on each
> request again. Sinces PHP applications, mostly based on frameworks like Yii or Symfony growed
> immensly during the last years, reload all the script filest, required by the application again
> and again slows down performance in a critical manner. This is one of the reasons, why caching 
> is meanwhile a major part of nearly all frameworks. On the one hand, caching takes care, that 
> the application responds to the request in an acceptable time, on the other hand it is the 
> origin of many problems, such as how to invalidate parts of the cache during a applications
> runtime.

Using a Servlet Engine and, as a consequence of that, Servlets enables you to implement your
application logic as you are used to, without the need to take care about the expensive 
bootstrapping process that came together with common legacy frameworks. A Servlet is a super
fast and simple way to implement an entry point to handle HTTP requests that allows you to
execute all performance critical tasks, like bootstrapping, in a method called `init()`, when
the Servlet Engine starts up.

## What is a Servlet

A Servlet is a simple class, that has to extend from `AppserverIo\Psr\Servlet\Http\HttpServlet`.
Your application logic can then be implemented by overwriting the `service()` method or better
by overwriting the request specific methods like `doGet()` if you want to handle a GET request.

## Create a simple Servlet

Let's write a simple example and start with a famous `Hello World` servlet

```php

namespace Namespace\Module;

use AppserverIo\Psr\Servlet\ServletConfig;
use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\Http\HttpServletRequest;
use AppserverIo\Psr\Servlet\Http\HttpServletResponse;

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

    // prepare the text here
    $this->helloWorld = 'Hello World!';

    // @todo Do all the bootstrapping here, because this method will
    //       be invoked only once when the Servlet Engines starts up
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
    $servletResponse->appendBodyStream($this->helloWorld);
  }
}
```

and save it as `/opt/appserver/webapps/myapp/WEB-INF/classes/Namespace/Module/HelloWorldServlet.php`.

Is that all? Yes! [Restart](#start-and-stop-scripts) the application server and open 
`http://127.0.0.1:9080/myapp/helloWorld.do` in your favorite browser, and ... vóila :)

> A restart is always required since you changed code in your Servlet, because the Servlet
> will be loaded and initialized when the the application server starts. Without a restart
> the application server doesn't know anything about your changes.

## Configuration

How to configure the my web application? You can also register servlets with a simple XML file

```xml
<?xml version="1.0" encoding="UTF-8"?>
<web-app version="1.0">

  <display-name>appserver.io example application</display-name>
  <description>
    This is the example application for the appserver.io servlet engine.
  </description>

  <servlet>
    <description>The hello world as servlet implementation.</description>
    <display-name>The famous 'Hello World' example</display-name>
    <servlet-name>helloWorld</servlet-name>
    <servlet-class>\Namespace\Module\HelloWorldServlet</servlet-class>
  </servlet>

  <servlet-mapping>
    <servlet-name>helloWorld</servlet-name>
    <url-pattern>/helloWorld.do</url-pattern>
  </servlet-mapping>

</web-app>
```
