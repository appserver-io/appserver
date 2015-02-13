---
layout: docs
title: AOP
position: 110
group: Docs
subNav:
  - title: How does it work
    href: how-does-it-work
    
  - title: Important terms
    href: important-terms
    
  - title: Usage
    href: usage
permalink: /get-started/documentation/aop.html
---

AOP, or [Aspect-oriented programming](http://en.wikipedia.org/wiki/Aspect-oriented_programming) is the concept of decoupling so called *cross-cutting concerns*, logic which is duplicated throughout the complete  codebase, and implement them at a central point.
These cross-cutting concerns are logical patterns which are needed in a manifold of places but mostly have a simple implementation. Examples would be security/authentication or logging.

Implementing logging at several places is either a huge duplication mess or results in dependencies to your logging infrastructure littered all over your application.
With AOP, logging gets implemented once and you can centrally (or at the actual place of use if you like) define where to use it.
This allows for very easy reactions to changes within your infrastructure.

AOP is more than a buzzword. Many of the PHP frameworks out there are supporting AOP for some years, in other languages like Java it's available for a long time. Currently there is
no stable PECL extension nor is AOP part of the PHP core. For this reason we implement our own AOP solution which is completely written in PHP and can be found [in this repository](https://github.com/appserver-io/doppelgaenger). 

Besides AOP this library also supports [Design by Contract](https://en.wikipedia.org/wiki/Design_by_contract) and can be used separate from the appserver as well.
Within the appserver environment, this solution is enabled by default and can be used in every webapp from the first start on.

## How does it work

AOP is often done using [`Proxy Classes`](https://en.wikipedia.org/wiki/Proxy_pattern) which wrap around actual classes and can be used to invoke code hooks on certain points in the program flow. Solutions injecting calls to a managing component which handles the execution of cross cutting logic are also possible.

At appserver.io we are concerned with two things: good performance and accessibility to the community. 
To accommodate these concerns we implemented everything in PHP and tried to do as much work during bootstrapping as possible.

This leaves us with the solution of building highly specialized proxy classes which get individually built for their respective AOP configuration.
What this configuration is will be described a little further down, for now we just have to know that there are alternative classes which wrap around the original classes and executed cross cutting logic.

We do not want to force a certain way in which objects are instantiated and we have to ensure that the proxy and the original implementation can be swapped externally but still without the use of any factory or entity manager which a programmer has to use.
Therefore we are working with the original definition of a structure (class, trait or in other context interface).

As we still want to switch between original and proxy we simply generate another implementation of the original class, lets call it `ExampleClass`, which contains method stubs which wrap around the original methods. As proxy generation is a very costly thing we will store these new definitions in the file system for later re-use.

To make an example, every AOP usage for a class of the structure:

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

will result in a new definition of the form:

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

The `DgClassLoader` is the default class loader for all applications, so AOP as well as Design by Contract are enabled by default within the appserver.io environment.
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

The concepts described [above](#how-does-it-work) can be used in very complex and powerful ways. The AOP approach has some terms which are widely used within its field to better understand all aspects of its concepts.
These terms will be used throughout our code and the following documentation. To make them understandable we will describe them further below.

You might additionally have a look at [this](https://en.wikipedia.org/wiki/Aspect-oriented_programming) and [this](http://eclipse.org/aspectj/doc/released/progguide/starting-aspectj.html) link for further explanations. 

### Join-points
It is already clear, that AOP is about the insertion (one speaks of weaving) of code (logic of our `cross cutting concerns`) into defined points within a program's flow. It is also obvious that we have to describe this point and which concern we want to insert.

`Join points` are used to describe a certain event within the actual flow of a running program at which weaving should take place. 

> So rather than describing a place within our source code, join points describe something that is *happening* at a certain place in your application.
 
 We currently support two specific join points, one standalone and the other as addition for other join points:
 
| Keyword      | Type       | Description                                                     |
| -------------| -----------| ----------------------------------------------------------------|
| `call`       | standalone | The call of a method whether being static or dynamic.           |
| `execute`    | addition   | An addition to other join points. Describes that a certain join point has to be in the context of an execution stack. E.g. a call to `methodB` might only be interesting if it was made from within `methodA` |

### Pointcuts
A pointcut is used to define an explicit event in the flow of a running program by combining a `join point` with a point within your application's code such as a method of a certain class.
You can specify these explicit events using something called a [join-point model](https://en.wikipedia.org/wiki/Aspect-oriented_programming#Join_point_models). This model explains how a pointcut
sets `advices` and join points in relation to build an actual execution of aspect code.

In the join point model we use, pointcuts only define the relation of a join point and a piece of code and can be actively referenced by advices.

Pseudocode might be: `call to methodB while executing methodA`.

As pseudocode does not make much sense for us, we mostly (see other possibilities [below](#usage)) use `annotations` to describe a pointcut.
Such an annotation would look like this:

```php
<?php

/**
 * @Pointcut("call(\Namespace\Module\Class->targetMethod()) && execute(\Namespace\Module\Class->callingMethod*())")
 */
public function certainMethodCallStack()
{}
```

As you can see, pointcuts can be specified using [logical connectives](https://en.wikipedia.org/wiki/Logical_connective#Common_logical_connectives) to express a very specific description of an event. Keep in mind that this description only narrows the necessary `call` join point.


> All pointcut specifications MUST contain a single `call` join point

The basic connectives the doppelgaenger library supports are:

| Connective  | Symbol      | Description                                                                                          |
| ------------| ------------| -----------------------------------------------------------------------------------------------------|
| `and`         | &&          | Both parts of the pointcut have to evaluate to `true` that the weaving will take effect.             |
| `or`          | &#124;&#124;| *At least one* part on both sides of the or connective have to be true that the weaving takes place. |
| `if...then`   | if(...)     | The boolean result of the condition within the brackets can be used to determine if weaving will take place. The condition will share the scope of the method(s) specified within the narrowed `call` join point |

As you can also see, you do not have to specify the full name of the targeted code as we support [bash wildcard patterns](https://www.shell-tips.com/2006/11/04/using-bash-wildcards/) within so called `Signature Pointcuts`.

> We can use very simple bash wildcard patterns to make the selection of code even more dynamic!

### Advices

Advices are used to implement logic of cross cutting concerns which will be woven into our application's code if a join point is reached.

Advices are logically gathered together in so called `aspects`. Aspects are a group of logic concern actions just as a class is the grouping of actions an object can perform on itself or others. You will find this fitting as aspects are simply classes annotated in a certain way in our AOP implementation.
A commonly used example for an aspect is a class providing several methods for logging, e.g. for different severity levels.

As described above an advice can reference a `pointcut` to specify the `join point` and piece of program code it will get woven into.
To do so we also prefer the usage of annotation.

A basic example would look like the following:

```php
<?php

/**
 * Empty dummy "Before" advice
 *
 * @param \AppserverIo\Doppelgaenger\Interfaces\MethodInvocationInterface $methodInvocation Initially invoked method
 * @return null
 *
 * @Before("pointcut(certainMethodCallStack())")
 */
public static function someAdvice(MethodInvocationInterface $methodInvocation)
{
    // cross cutting concern logic here
}
```

As visible we use `pointcut` pointcut to make clear what we are referencing and simply specify the name of the method our pointcut's annotation was made over. 
Please note that we can also use [bash wildcard patterns](https://www.shell-tips.com/2006/11/04/using-bash-wildcards/) here.

What you will also notice is the name of the annotation: `Before`.
This describes the type of the advice and specifies exactly *how the woven advice will be positioned in relation to the specified piece of code*. This is a very important piece iof information, as it determines in which context the advice will be executed and what kind of influence it will have over the targeted code.

We differentiate between five types of advices:

| Type            | Description                                                                                                                                                   |
| ----------------| --------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `Before`        | Will get executed *before* the actual logic of the targeted method. The advice has access to the parameters of the original method call and can change them. |
| `AfterReturning`| Will be executed *after the method returned a value* and will therefore further narrow the join point, as any thrown exception will make the program flow omit this advice. Within the advice we can access the result of the original method logic in a read-only way.                                                                                            |
| `AfterThrowing` | Behaves similar to the `AfterReturning` advice as it is only reached if the original method logic *throws an exception*. The advice has read-only access to the object of the thrown exception. |
| `After`         | The after advice is a logical `and` combination of the `AfterReturning` and the `AfterThrowing` advice, as it gets always executed and has read-only access to either any thrown exception or the regular result of the original method logic (whichever happened). |
| `Around`        | Around advices wrap around the actual method logic and combine the possibilities of the other advices as they can freely implement the call to the original method body, or even omit it entirely. Around advices can be stacked in an onion-like manner onto a target method to form a so called `advice chain`. |
 
 To identify the type of the advice we use the annotation which also references the targeted pointcut.
 
 > Each advice is identified by the name of its type.

## Usage

This section will describe how to make use of the concepts mentioned above.

When utilizing your application with AOP techniques please keep one thing in mind:

> AOP is a very powerful instrument to enrich your application with functionality and coupling.
> But as in most cases, great power comes with great responsibility. So it is really 
> necessary to keep in mind where your aspect classes are, and what they do. If not, someone
> will wonder what happens and maybe need a long time to figure out problems.

Within the appserver environment, we can differentiate three ways in which AOP functionality can be used. No matter which way is chosen, the results in the code will stay the same, but you might have different problems or advantages coming with your choice. The usage of said aspects as collection of advices will always stay imminent, but the way in which pointcuts and join points are used differs.
An aspect is simply a class identified with the `@Aspect` annotation as seen in the example below:

```php
<?php

/**
 * ...
 *
 * @Aspect
 */
class AnExampleAspect
{
  // add advices here
}
```

Aspect classes have to be implemented within your webapp, namely within the `META-INF`, `WEB-INF` or `common` directory.

### Generic annotations within aspects

The first usage possibility is solely based on the on aspect classes and annotations.
Within these class definitions we can define pointcuts and advices referencing them as already hinted [above](#important-terms). 

In the example below you can see the pointcut `allDoGet` which defines a call to any method called `doGet` within any class in the `\Namespace\Module` namespace.
 The `Before` advice `logInfoAdvice` does reference the `allDoGet` pointcut and will therefore be woven into any `doGet` method right before (hence the `Before` type) the original method logic. 

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
   * @param \AppserverIo\Doppelgaenger\Interfaces\MethodInvocationInterface $methodInvocation Initially invoked method
   *
   * @return null
   * @Before("pointcut(allDoGet())")
   */
  public function logInfoAdvice(MethodInvocationInterface $methodInvocation)
  {

    // ... do something here
  }
}
```

This technique has several advantages:

- Pointcuts and advices are defined in the same place which allows for better overview of what is referenced where. This leads to better control of weaving
- Everything is managed de-centrally and target code does not have to be touched, as all needed implementations can be made within the aspect class
- Pointcuts can be re-used or referenced within several advices

### Generic configuration in XML 

Additionally there is the possibility to configure pointcuts, and them being referenced by advices, within a central XML file.
This file MUST be called `pointcuts.xml` and MUST be placed within the `META-INF` directory of your application.

An example can be seen below:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<pointcuts xmlns="http://www.appserver.io/appserver">
  <pointcut>
    <pointcut-name>allDoPost</pointcut-name>
    <pointcut-pattern>call(\Namespace\Module\*->doPost())</pointcut-pattern>
  </pointcut>
 
  <advice>
    <advice-aspect>\Namespace\Module\LoggerAspect</advice-aspect>
    <advice-name>logInfoAdvice</advice-name>
    <advice-type>Before</advice-type>
    <advice-pointcuts>
      <pointcut-name>allDoPost</pointcut-name>
    </advice-pointcuts>
  </advice>
</pointcuts>
```

The configuration describes a pointcut which specifies all `call` join points of all `doPost` methods within the `\Namespace\Module` namespace. This is very similar to the annotation example above and gets referenced by the same advice. To do so the advice does not have to be changed in any way.

This method of using XML has the same advantages as annotating everything within the aspect class, but serves another purpose:

> By using XML the configuration allows for easy manipulation through external tools.

A thought to keep in mind when configuring pointcuts.

### Direct annotation

For some use cases it can be considered an overhead to implement explicit pointcuts, as they might be referenced only once or reference only one point within your application's code themselves.
For these cases it can make sense to use a more direct approach which also makes the AOP weaving known to the target code.

To use such an direct approach it is possible to directly annotate a target method with the type and qualified name of the advice(s) which have to be woven.

An example can be seen below:

```php
<?php

/**
 * ...
 *
 * @Before("if($param === true) && advise(\Namespace\Module\LoggerAspect->logInfoAdvice())")
 */
public function logIfTrue($param)
{
    // ... custom logic
}
```

We can take several pieces of information from this example:

- We can use the same complex building blocks of [logical connectives](https://en.wikipedia.org/wiki/Logical_connective#Common_logical_connectives) and join points as for normal pointcuts
- We do NOT need the `call` join point, as we will implicitly make the call to the annotated method our target event
- The annotation defines the type of advice and therefor the actual point of weaving within the target code. This SHOULD be the used advice's actual type
- When annotating directly we can use additional expressions instead of join points and logical connectives

The additional expressions mentioned above are used to define the weaved code.
We differ between two possible expressions:

| Keyword      | Type       | Description                                                                                                           |
| -------------| -----------| ----------------------------------------------------------------------------------------------------------------------|
| `advise`     | standalone | The referenced advice will be woven into the target method. The advice's type SHOULD be the used annotation           |
| `weave`      | standalone | Weave in arbitrary code at a certain point. This code will be run n the scope of the target method as if implemented within it |

An example for the `weave` expression would look as follows:

```php
<?php

/**
 * ...
 *
 * @Before("if($param === true) && weave($this->logger->log('Now executing: ' . __METHOD__))")
 */
public function logIfTrue($param)
{
    // ... custom logic
}
```

This is technically the same as if the call to the logger would have been implemented within the `logIfTrue` with two exceptions:

- The weaving can be switched off externally by not using the AOP features for the annotated files
- The woven code does not count into the method's original logic and is therefor not wrapped by other advices

### Advices and the method invocation object

Advices can be dynamically woven into several points within application code, but to really have a value they have to know about the target code and be able to influence it.
To achieve this, we will pass an object containing context information to every advice.

> Every advice MUST accept exactly one parameter of the type `\AppserverIo\Doppelgaenger\Interfaces\MethodInvocationInterface`.

The passed instance of the type `MethodInvocationInterface` fulfills three important purposes:

- It contains information about the original function call such as passed parameters, possible results, structure and method name and allows changes to some of them
- It contains the context of the original call, meaning the instance of the called class
- It contains the advice chain and allows to manually iterate through layered around advices

Below is an example where an advice uses the passed context instance to retrieve the system's logger and other information about the method call:

```php
<?php

/**
 * Advice used to log the call to any advised method.
 *
 * @param \AppserverIo\Doppelgaenger\Interfaces\MethodInvocationInterface $methodInvocation Initially invoked method
 *
 * @return null
 * @Before
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
    ->info(sprintf('The method %s::%s is about to be called', className, methodName));
}
```

As an example to take influence you can see an `Around` advice below. It filters parameters and formats the result of the original method logic.

```php
<?php

/**
 * Advice tampering with parameters and result
 *
 * @param \AppserverIo\Doppelgaenger\Interfaces\MethodInvocationInterface $methodInvocation Initially invoked method
 *
 * @return null
 * @Around
 */
public function runThroughFilter(MethodInvocationInterface $methodInvocation)
{

  $filteredParameters = filter_var_array($methodInvocation->getParameters(), FILTER_SANITIZE_STRING);
  $methodInvocation->setParameters($filteredParameters);
 
  // proceed to the next callback in the chain
  $result = $methodInvocation->proceed();
  
  return filter_var($result, FILTER_SANITIZE_STRING);
}
```

As you can see we can safely sanitize in- and output of any method without touching the original code. This allows for pretty powerful solutions without littering any code with copy-and-paste snippets.

