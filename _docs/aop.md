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

AOP is often realized using so called [`Proxy Classes`](https://en.wikipedia.org/wiki/Proxy_pattern) which wrap around actual classes and can be used to invoke code hooks on certain points in the program flow. Solutions injecting calls to a managing component which handles the execution of cross cutting logic are also possible.
At appserver.io we are concerned with two things: good performance and accessibility for the community. To accommodate these concerns we implemented everything in PHP and tried to do as much work during bootstrapping as possible.
This leaves us with the solution of building highly specialized proxy classes which get individually built for their respective AOP configuration.
What this configuration is will be described a little further down, for now we just have to know that there are alternative classes which wrap around the original classes and executed cross cutting logic.
As we do not want to force a certain way in which objects are instantiated we have to ensure that the proxy and the original implementation can be swapped externally but still without the use of any factory or entity manager which a programmer has to use.
That leaves us with working with the original definition of a structure (class, trait or in other context interface).
As we still want to switch between original and proxy we simply generate another implementation of the original class, lets call it `ExampleClass`, which contains method stubs which wrap around the original methods. As proxy generation is a very costly thing we will store these new definitions in the file system for later re-use.

To make an example, every AOP usage for a class of the structure

```php
<?php

class ExampleClass
{
    public function aMethod()
    {
        // here comes the method logic
    }
}
```

will result in a new definition of the form

```php
<?php

class ExampleClass
{
    public function aMethod()
    {
        // here might be AOP code hooks
        $dbResult = $this->aMethodDOPPELGAENGEROriginal();
        // here might be AOP code hooks
    }
    
    public function aMethodDOPPELGAENGEROriginal()
    {
        // here comes the method logic
    }
}
```

As you might notice, this does not fit the proxy pattern a 100%, as we do not deal with instances here but rather structure definitions, but it fulfills the same purpose and does not require a lot logic during program runtime. 

To enable switching between the altered and the original definition we utilize the autoloading concept of PHP. For this purpose we have the autoloader class `\AppserverIo\Appserver\Core\DgClassLoader` which will preferably load the altered definition unless configured otherwise.
The `DgClassLoader` is the default class loader for all applications so AOP as well as Design by Contract are enabled by default within the appserver.io environment.
If you want to change this for whatever reason you can do so be registering your own classloader within the `context.xml` configuration files.
You might do so on application level by placing a file called `context.xml` in your webapp's `META-INF` directory or by editing the system wide `context.xml` (which we do not encourage by any means).

The classloader configuration might look something like this then:

```xml
<classLoaders>
    <!-- other loaders if desired -->
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
    <!-- other loaders if desired -->
</classLoaders>
```

## Important terms

The concepts described [above](#how-does-it-work) can be used in very complex and powerful ways. To better understand all aspects of these concepts, the AOP approach has some terms which are widely
used within its field.
These terms will be used throughout our code and the following documentation. To make them understandable we will describe them further below.

You might additionally have a look at [this](https://en.wikipedia.org/wiki/Aspect-oriented_programming) and [this](http://eclipse.org/aspectj/doc/released/progguide/starting-aspectj.html) link for explanations. 

### Join-points
It is already clear, that AOP is about the insertion (one speaks of weaving) of code (logic of our `cross cutting concerns`) into defined points within a program's flow. It is also obvious that we have to describe this point and which concern we want to insert.

`Join points` are used to describe a certain event within the actual flow of a running program at which insertion should take place. 

> So rather than describing a place within our source code join points describe something that is *happening* at a certain place in your application.
 
 We currently support two specific join points, one standalone and the other as addition for other join points:
 
 | Keyword      | Type       | Description                                                     |
 |:-------------|:-----------|:----------------------------------------------------------------|
 | call         | standalone | The call of a method whether being static or dynamic.          |
 | execute      | addition   | An addition to other join points. Describes that a certain join point has to be in the context of an execution stack. E.g. a call to `methodB` might only be interesting if it was made from within `methodA` |

### Pointcuts
A pointcut is used to define an explicit event in the flow of a running program by combining a `join point` with a point within your application's code such as a method of a certain class.
You can specify these explicit events using something called a [join-point model](https://en.wikipedia.org/wiki/Aspect-oriented_programming#Join_point_models). This model explains how a pointcut
sets `advices` and join points in relation to build an actual execution of aspect code.

In the join point model we use pointcuts only define the relation of a join point and a piece of code and can be actively referenced by advices.
Pseudocode might be: `call to methodB while executing methodA`.

As pseudocode does not make much sense for us we mostly (see other possibilities [below](#usage)) use `annotations` to describe a pointcut.
Such an annotation would look like this:

```php
<?php

/**
 * @Pointcut("call(\Namespace\Module\Class->targetMethod()) && execute(\Namespace\Module\Class->callingMethod*())")
 */
public function certainMethodCallStack()
{}
```

As you can see, pointcuts can be specified using [logical connectives](https://en.wikipedia.org/wiki/Logical_connective#Common_logical_connectives) to express a very specific description of an event. Keep in mind that this description only narrows the specified `call` join point.

> All pointcut specifications MUST contain a single `call` join point

The basic connectives the doppelgaenger library supports are:

 | Connective  | Symbol     | Description                                                     |
 |:------------|:-----------|:----------------------------------------------------------------|
 | and         | &&         | Both parts of the pointcut have to evaluate to `true` that the weaving will take effect.  |
 | or          | \|\|       | *At least one* part on both sides of the or connective have to be true that the weaving takes place. |
 | if...then   | if(...)    | The boolean result of the condition within the brackets can be used to determine if weaving will take place. The condition will share the scope of the method(s) specified within the narrowed `call` join point |

As also seen above we can use very simple [bash wildcard patterns](https://www.shell-tips.com/2006/11/04/using-bash-wildcards/) to make the selection of code even more dynamic!

### Advices

Advices are used to implement logic of cross cutting concerns which can be woven into our application's code if a join point is reached. Advices are logically gathered together in so called `aspects`. Aspects are a group of logic concern actions just as a class is the grouping of actions an object can perform on itself or others. You will find this fitting as aspects are simply classes annotated in a certain way in our AOp implementation.
A commonly used example for an aspect is a class providing several methods for logging, e.g. for different severity levels.

As described above an advice can reference a `pointcut` to specify the `join point` and piece of program code it will get woven into.
To do so we also prefer the usage of annotation.
A basic example would look like the following:

```php
<?php

/**
 * ...
 *
 * @Before("pointcut(certainMethodCallStack())")
 */
public static function someAdvice(MethodInvocationInterface $methodInvocation)
{
    // cross cutting concern logic here
}
```

As visible we use the term `pointcut` to make clear what we are referencing and simply specify the name of the method our pointcut's annotation was made over. 
Please note that we can also use [bash wildcard patterns](https://www.shell-tips.com/2006/11/04/using-bash-wildcards/) here.

What you will also notice is the name of the annotation: `Before`.
This describes the type of the advice and specifies exactly *how the woven advice will be positioned in relation to the specified piece of code*. This is a very important piece if information as it determines in which context the advice will be executed and what kind of influence it will have over the targeted code.
We differentiate between five types of advices:

 | Type           | Description                                                     |
 |:---------------|:----------------------------------------------------------------|
 | Before         | Will get executed *before* the actual logic of the targeted method. The advices has access to the parameters of the original method call and can change them. |
 | AfterReturning | Will be executed *after the method returned a value* and will therefore further narrow the join point as any thrown exception will make the program flow omit this advice. Within the advice we can access the result of the original method logic in a read-only way. |
 | AfterThrowing  | Behaves similar to the `AfterReturning` advice as it is only reached if the original method logic throws an exception. The advice has read-only access to the thrown exception object. |
 | After          | The after advice is a logical `and` combination of the `AfterReturning` and the `AfterThrowing` advice as it gets always executed and has read-only access to either any thrown exception or the regular result of the original method logic. |
 | Around         | Around advices wrap around the actual method logic and combine the possibilities of the other advices as they can freely implemented the call to the original method body or even omit it entirely |
 
 Each advice type is identified by the name of its type.

## Usage


> AOP is a very powerful instrument to enrich your application with functionality with coupling.
> But as in most cases, great power comes together with great responsibility. So it is really 
> necessary to keep in mind, where your Aspect classes are and what they do. If not, someone
> will wonder what happens and maybe need a long time to figure out problems. To avoid this, we'll
> provide a XML based advice declaration in future versions.


### Generic annotations within aspects

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

### Generic configuration in XML 

### Direct annotation aka interceptors

## How to add an Advice

Integrating AOP in your app can be done in two ways. The first one is to define the pointcuts (and also
advices if you like) in the same class they will get woven into, the second one is to separate them. Here we want to describe 
the second approach.

Let's say we simply want to log all GET requests on our HelloWorldServlet without adding any
code to the servlet itself. To do this, we first have to create an Aspect class like



Store the class in `/opt/appserver/myapp/META-INF/classes/Namespace/Module/LoggerAspect` and
[restart](#start-and-stop-scripts) the application server.

To see the the log message, open the console (Linux/Mac OS X) and enter

```bash
$ tail -f /opt/appserver/var/log/appserver-errors.log
```

Then open `http://127.0.0.1:9080/myapp/helloWorld.do` in your favorite browser, and have a look
at the console.


