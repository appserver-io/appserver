---
layout: docs
title: Dependency Injection
position: 70
group: Docs
permalink: /documentation/dependency-injection.html
---

> [What can be injected](#what-can-be-injected)
> [How to inject an instance](#how-to-inject-an-instance)   

Dependency Injection, furthermore DI, enables developers to write cleaner, reusable and maintainable
code with less coupling by injecting necessary instances at runtime instead of instantiating them in
the class itself. Within the application server, each application has it's own scope and therefore a 
own dependency injection container. This prevents your application from fatal errors like `Cannot redeclare class ...`.

## What can be injected

Generally everything! The application server itself doesn't use DI, instead it provides DI as a
service for the applications running within. But, before you can let the DI container inject an
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
passing it to the constructor. Inside the application server, the injection is an process you can't
see, it's more a kind of magic which happens behind the scenes. So instead of manually pass the
necessary instances to a classes constructor, the DI container will do that for you. 

You simple has to tell the DI container what you need, let's have a look at the details.

### Property Injection

The first possibility we have is to annotate a class property

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

With the `name` attribute of the `@EnterpriseBean`annotation you have the possibility to specify the
name of the bean, you registered before by annotating it. A more detailed description about the 
available annotations will be part of the [Persistence-Container](#persistence-container).

### Setter Injection

The second possibility to inject an instance is setter injection.

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

> This method is the preferred one, because it'll be refactored not to use reflection in further
> versions.