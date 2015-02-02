---
layout: docs
title: AOP
position: 110
group: Docs
subNav:
  - title: How to add an Advice
    href: how-to-add-an-advice
permalink: /documentation/aop.html
---

AOP, or [Aspect-oriented programming](http://en.wikipedia.org/wiki/Aspect-oriented_programming) is the concept of decoupling so called *cross-cutting concerns*, logic which is logic duplicated throughout the complete  codebase, and implemented them at a central point.
These cross-cutting concerns are logical patterns which are needed in a manifold of places but has a simple implementation. Examples would be security/authentication or logging.
Implementing logging at several places is either a huge duplication mess or results in dependencies to your logging infrastructure littered all over your application.
With AOP logging gets implemented once and you can centrally (or at the actual place of use if you like) define where to use it.
This allows for very easy reactions to changes within your infrastructure.

Meanwhile, AOP is more than a buzzword. Many of the PHP frameworks out there are supporting AOP for some years, in other languages like Java it's available for a long time. Currently there is
no stable PECL extension nor is AOP part of the PHP core. For this reason we implement our own AOP solution which is completely written in PHP and can be found [in this repository](https://github.com/appserver-io/doppelgaenger). Besides AOP this library also supports [Design by Contract](https://en.wikipedia.org/wiki/Design_by_contract) and can be used separate from the appserver as well.
Within the appserver environment, this solution is enabled by default and can be used in every webapp from the first start on.

## How does it work

AOP is often realized using so called `Proxy Classes` which wrap around actual classes and can be used to invoke code hooks on certain points in the program flow. Solutions injecting calls to a managing component which handles the execution of cross cutting logic are also possible.
At appserver.io we are concerned with two things: good performance and accessibility for the community. To accommodate these concerns we implemented everything in PHP and tried to do as much work during bootstrapping as possible.

## Join-points


## Advices

## Pointcuts
A pointcut is basically a definition of a certain point within the flow of the application code. This might e.g. be the execution of a certain method or a method of a certain kind throwing an exception.
You can specify these certain points using something called a [join-point model](https://en.wikipedia.org/wiki/Aspect-oriented_programming#Join_point_models). This model explains how a pointcut
sets advices and join-points in relation to build an actual execution of aspect code.

Pseudocode might be `execute AdviceA at call to MethodB`.
 
Pointcuts can be specified in three different ways:

### Direct annotation aka interceptors

### Generic annotations within aspects

### Generic configuration in XML 

## How to add an Advice

Integrating AOP in your app can be done in two ways. The first one is to define the pointcuts (and also
advices if you like) in the same class they will get woven into, the second one is to separate them. Here we want to describe 
the second approach.

Let's say we simply want to log all GET requests on our HelloWorldServlet without adding any
code to the servlet itself. To do this, we first have to create an Aspect class like

```php
<?php

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
