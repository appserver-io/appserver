---
layout: docs
title: AOP
position: 110
permalink: /docs/aop.html
---

> [How to add an Advice](#how-to-add-an-advice)

Meanwhile, AOP is more than a buzzword. Many of the PHP frameworks out there are supporting AOP for
some years, in other languages like Java it's available for a long time. As there is actually
no stable PECL extension nor is AOP part of the PHP core, performance is a big problem,
because of its nature, AOP needs to be deeply weaved into your code. Most of the solutions
available for PHP solve that by generating so called `proxy classes` that wrap the original
methods and allow to weave the advices before, after or around the original implementation.

As we're in a multithreaded environment, and performance is one of our main goals, we were not 
able to use on of the available solutions. As we also need to generate proxy classes, we decided
to do that triggered by the autoloader. As the autoloader is part of the appserver.io distribution, you
don't have to configure anything to use AOP in your code.

## How to add an Advice

Integrating AOP in your app can be done in two ways. The first one is to define the pointcuts (and also
advices if you like) in the same class they will get woven into, the second one is to separate them. Here we want to describe 
the second approach.

Let's say we simply want to log all GET requests on our HelloWorldServlet without adding any
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
   * @param \AppserverIo\Doppelgaenger\Entities\MethodInvocation $methodInvocation 
   *   Initially invoked method
   *
   * @return null
   * @Before("pointcut(allIndexActions())")
   */
  public function logInfoAdvice(MethodInvocation $methodInvocation)
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

> AOP is a very powerful instrument to enrich your application with functionality with coupling.
> But as in most cases, great power comes together with great responsibility. So it is really 
> necessary to keep in mind, where your Aspect classes are and what they do. If not, someone
> will wonder what happens and maybe need a long time to figure out problems. To avoid this, we'll
> provide a XML based advice declaration in future versions.