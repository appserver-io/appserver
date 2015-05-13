---
layout: docs
title: AOP
meta_title: appserver.io AOP – aspect-oriented programming
meta_description: AOP is the concept of decoupling cross-cutting concerns, a logic which is duplicated throughout the complete codebase, and implements and coordinates those cross-cutting concerns in a more centralized manner.
position: 120
group: Docs
subNav:
  - title: How it works
    href: how-it-works

  - title: Important terms
    href: important-terms

  - title: Usage
    href: usage
permalink: /get-started/documentation/aop.html
---

[Aspect-oriented programming](http://en.wikipedia.org/wiki/Aspect-oriented_programming) (AOP) is the concept of decoupling so-called *cross-cutting concerns*, a logic which is duplicated throughout the complete codebase, and implements and coordinates those cross-cutting concerns in a more centralized manner.
These cross-cutting concerns are logical patterns, which are needed in multiple places, but mostly have a simple implementation. Examples are security/authentication or logging.

Implementing logging in several places is either a huge duplication mess or results in dependencies to your logging infrastructure scattered all over your application.
With AOP, logging is implemented once and you can centrally (or at the actual place of usage) define where to use it. This allows for easy reactions to changes within your infrastructure.

AOP is more than a buzzword. Many of the PHP frameworks have been supporting AOP for some years. In other languages, like Java, it has been used and is commonplace for even longer. Currently, there is neither a stable PECL extension nor is AOP part of the PHP core. For this reason, we implemented our AOP solution, which is completely written in PHP, and can be found [in this repository](https://github.com/appserver-io/doppelgaenger).

In addition to AOP, this library also supports [Design by Contract](https://en.wikipedia.org/wiki/Design_by_contract) and can be used separately from appserver.io.

Within the application server's environment, this solution is enabled by default and can be used in every webapp from the start.

## How it works

AOP is often accomplished using [`Proxy Classes`](https://en.wikipedia.org/wiki/Proxy_pattern) that wrap around actual classes and can be used to invoke code hooks on certain points in the program flow. Solutions injecting calls to a managing component, which handles the execution of cross-cutting logic are also possible.

With appserver.io, we have two things in mind: good performance and accessibility for the community.
To accommodate these concerns, we implemented everything in PHP and did as much work during bootstrapping as possible.

This leaves us with the solution of building highly specialized proxy classes, which get individually built for their respective AOP configuration.

The configuration's details are described further below. For now, it is important to know that there are alternative classes that wrap around the original classes and execute cross-cutting logic.

What we don't want to do is force a certain way to instantiate objects. We have to ensure that the proxy and the original implementation can be swapped externally, without using a factory or entity manager. Therefore, we are working with the original definition of a structure (class, trait, or in another context interface).

As we still want to switch between original and proxy, we simply generate another implementation of the original class, we call it `ExampleClass`. It contains method stubs that wrap around the original methods. Since proxy generation is very expensive, these new definitions will be stored in the file system for later re-use.

As an example, every AOP usage for a class of the following structure

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

As you might notice, this does not fit the proxy pattern completely, as we do not deal with instances directly, but rather with structure definitions. Nevertheless, it fulfils the same purpose and does not require a lot of logic during program runtime.

To switch between the altered and the original definition, we utilise the autoloading concept of PHP. For this purpose, we have the autoloader class `\AppserverIo\Appserver\Core\DgClassLoader`, which preferably loads the altered definition, unless configured otherwise.

The `DgClassLoader` is the default class loader for all applications. So, AOP as well as Design by Contract, are enabled by default within the appserver.io environment.

If you want to change this, you can do so by registering your own classloader within the `context.xml` configuration files.
You might do so at application level, by placing a file called `context.xml` in your webapp's `META-INF` directory or by editing the system-wide `context.xml` (which we highly recommend you should not do!).

The classloader configuration might look like the following.

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

The concepts we have described [above](#how-does-it-work) can be used in very complex and powerful ways.

## Important terms

Since there are a number of specialized terms used in the AOP approach, which you'll need to learn in order to understand the AOP methodologies, we've described them in some detail below.

You might additionally want to have a look at [Aspect Oriented Programming](https://en.wikipedia.org/wiki/Aspect-oriented_programming) and [Starting AspectJ](http://eclipse.org/aspectj/doc/released/progguide/starting-aspectj.html) for further information.

### Aspects

An aspect, in an AOP designed program, is an abstract concept and represents a feature linked to many other parts of the program, but which is not necessarily related directly to the program's primary function. It is what is know as a cross-cutting concern. Examples of such aspects in programming are logging and database persistence. In some AOP circles, aspects are not only considered the cross cutting concerns (additional features), but also the core concerns or business logic (main features) and weaving them together is considered the whole art of Aspect Oriented Programming. In normal OOP terms, this weaving is considered [composition](http://en.wikipedia.org/wiki/Composition_over_inheritance).

There is an analogy useful for understanding AOP and it involves a prism. With a prism, you can dissect a ray of light into its individual aspects (different colors), and you can use another prism to bring those colors back to the ray of light. The same effect is what AOP offers, as a programming paradigm. You dissect your problem into its singular aspects and can weave them back together to form the solution to the whole problem.

### Join-points
It is hopefully clear to you now that AOP is about the insertion (one speaks of weaving) of code (logic of our `cross-cutting concerns`) into defined points within a program's flow. It should also be obvious that we have to describe these points and which concerns we want to insert.

`Join points` are used to describe a certain event within the actual flow of a running program, at which weaving should take place.

> So, rather than describing a place within the source code, join points describe something that is *happening* at a specific place in your application.

 We currently support two join point types.

| Keyword      | Type       | Description                                                     |
| -------------| -----------| ----------------------------------------------------------------|
| `call`       | standalone | The call of a method being static or dynamic.                   |
| `execute`    | addition   | An addition to other join points. Describes that a certain join point has to be in the context of an execution stack. E.g. when a call to methodB is made from within methodA, then the execute join point is a match and the woven advice will be carried out. |

### Pointcuts
A pointcut is used to define an explicit event in the flow of a running program by combining a `join point` with a point within your application's code, such as a method of a certain class.
You can specify these explicit events using something called a [join-point model](https://en.wikipedia.org/wiki/Aspect-oriented_programming#Join_point_models). This model explains how a pointcut sets `advices` and join points in relation to each other to build an actual execution of an aspect's code.

In the join point model, we use pointcuts to define the relation of a join point and a piece of code that can be actively referenced by advices.

Pseudocode might be: `call to methodB while executing methodA`.

As pseudocode does not make much sense for us, we mostly use `annotations` to describe a pointcut (see other possibilities [below](#usage)). Such an annotation could look like the following.

```php
<?php

/**
 * @Pointcut("call(\Namespace\Module\Class->targetMethod()) && execute(\Namespace\Module\Class->callingMethod*())")
 */
public function certainMethodCallStack()
{}
```

As you can see, pointcuts can be specified using [logical connectives](https://en.wikipedia.org/wiki/Logical_connective#Common_logical_connectives) to express a very specific description of an event. Keep in mind that this description only narrows the necessary `call` join point.

> All pointcut specifications MUST contain a single `call` join point.

The basic connectives the doppelgaenger library supports are the following.

| Connective  | Symbol      | Description                                                                                          |
| ------------| ------------| -----------------------------------------------------------------------------------------------------|
| `and`         | &&          | Both parts of the pointcut have to return `true` for the weaving to take effect.             |
| `or`          | &#124;&#124;| *At least one* part on both sides of the connection have to be true for the weaving to take place. |
| `if...then`   | if(...)     | The boolean result of the condition within the brackets can be used to determine if weaving will take place. The condition will share the scope of the method(s) specified within the narrowed `call` join point. |

As you can see, you do not have to specify the full name of the targeted code as we support [bash wildcard patterns](https://www.shell-tips.com/2006/11/04/using-bash-wildcards/) within so called `Signature Pointcuts`.

> We can use very simple bash wildcard patterns to make the selection of code even more dynamic.

### Advices

Advices are used to implement the logic of cross-cutting concerns, which will be woven into our application's code, if a join point is reached.

Advices are logically gathered together in so-called `aspects`. Aspects are a group of logic concern actions just as a class is the grouping of actions an object can perform for itself or others. This sounds reasonable, because aspects are simply classes annotated in a certain way in our AOP implementation.

A commonly used example of an aspect is a class providing several methods for logging, e.g. for different severity levels.

As described above, an advice can reference a `pointcut` to specify the `join point` and a piece of program code it will get woven into.
To do so, we also prefer the usage of an annotation.

A basic example looks like the following.

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

As shown we use a `pointcut` to make clear what we are referencing to and simply specify the name of the method.
Please note that we can also use [bash wildcard patterns](https://www.shell-tips.com/2006/11/04/using-bash-wildcards/) here.

You might notice the name of the annotation: `Before`.
This describes the type of the advice and specifies exactly *how the woven advice will be positioned in relation to the specified piece of code*. This is a very important piece of information, as it determines in which context the advice will be executed and what kind of influence it will have on the targeted code.

There are five types of advices:

| Type            | Description                                                                                                                                                   |
| ----------------| --------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `Before`        | Will get executed *before* the actual logic of the targeted method. The advice has access to the parameters of the original method call and can change them. |
| `AfterReturning`| Will be executed *after the method returned a value* and will further narrow the join point, as any thrown exception will make the program flow omit this advice. Within the advice, we can access the result of the original method logic in a read-only way. |
| `AfterThrowing` | Behaves simliar to the `AfterReturning` advice as it is only reached if the original method logic *throws an exception*. The advice has read-only access to the object of the thrown exception. |
| `After`         | The `After` advice is a logical `and` combination of the `AfterReturning` and the `AfterThrowing` advice, as it gets always executed and has read-only access to either any thrown exception or the regular result of the original method logic. |
| `Around`        | Around advices wrap around the actual method logic and combine the possibilities of the other advices as they can freely implement the call to the original method body, or even omit it entirely. Around advices can be stacked in an onion-like manner onto a target method to form a so-called `advice chain`. |

 To identify the type of the advice we use the annotation, which also references the targeted pointcut.

 > Each advice is identified by the name of its type.

## Usage

This section will describe how to make use of the concepts mentioned above.

When utilizing your application with AOP techniques please keep in mind:

> AOP is a very powerful instrument to enrich your application with functionality and coupling.
> However, great power comes with great responsibility. So it is
> necessary to keep in mind where your aspect classes are and what they do. If this isn't done, you
> will often wonder what is happening and might need a more time than usual to figure out the source of your problem.

Within the application server's environment, we differentiate three ways of using AOP functionality. No matter which way is chosen, the results in the code will stay the same. However, there are advantages and disadvantages to each choice. The usage of the aspects as a collection of advices mentioned above will always stay imminent, but the way of using pointcuts and join points are different.
An aspect is simply a class identified by the `@Aspect` annotation as seen in the example below:

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

> Please note that the constructor of aspect classes MUST NOT require parameters.

### Generic annotations within aspects

The first usage possibility is solely based on aspect classes and annotations.
Within these class definitions, we can define pointcuts and advices for reference as mentioned [above](#important-terms).

In the example below you can see the pointcut `allDoGet`, which defines a call to any method called `doGet`, within any class in the `\Namespace\Module` namespace.
The `Before` advice `logInfoAdvice` references the `allDoGet` pointcut and will, therefore, be woven into any `doGet` method right before (hence the `Before` type) the original method logic.

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

* Pointcuts and advices are defined in the same place for a better overview of references. This leads to better control of weaving
* Everything is managed decentrally and the target code does not have to be touched, as all implementations needed can be made within the aspect class
* Pointcuts can be re-used or referenced within several advices

### Generic configuration in XML

Additionally, there is the possibility to configure pointcuts within a central XML file.
This file MUST be called `pointcuts.xml` and MUST be placed within the `META-INF` directory of your application.

This is shown in the following example.

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

The configuration describes a pointcut, which specifies all `call` join points of all `doPost` methods within the `\Namespace\Module` namespace. This is very similar to the annotation example above and is referenced by the same advice. Therefore, the advice does not have to be changed.

This method of using XML has the same advantages as annotating everything within the aspect class but serves another purpose:

> By using XML, the configuration allows for easy manipulation through external tools.

### Direct annotation

For some use cases, implementing explicit pointcuts can be considered an overhead, as they might be referenced only once or reference only one point within your application's code.
For these cases, it can make sense to use a more direct approach that also makes the AOP weaving known to the target code.

This direct approach can be used for directly annotating a target method with the type and qualified name of the advice(s), which have to be woven. This is demonstrated in the following example.

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

We can derive several important points from this example:

* We can use the same complex building blocks of [logical connectives](https://en.wikipedia.org/wiki/Logical_connective#Common_logical_connectives) and join points, as we do for normal pointcuts.
* We do NOT need the `call` join point as we will implicitly make the call to the annotated method of our target event.
* The annotation defines the type of advice, and therefore, the actual point of weaving within the target code. This SHOULD be used to define an advice's actual type.
* When annotating directly, we can use additional expressions instead of join points and logical connectives.

The additional expressions mentioned above are used to define the weaved code.
We differ from two possible expressions:

| Keyword      | Type       | Description                                                                                                           |
| -------------| -----------| ----------------------------------------------------------------------------------------------------------------------|
| `advise`     | standalone | The referenced advice will be woven into the target method. The advice's type SHOULD be the used annotation.           |
| `weave`      | standalone | Weave in arbitrary code at a certain point. This code will be run in the scope of the target method, as if it was implemented within it. |

An example, the `weave` expression looks like the following.

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

This is technically the same as if the call to the logger would have been implemented within the `logIfTrue` except for two exceptions:

* The weaving can be switched off externally, by not using the AOP features for the annotated files.
* The woven code does not count as part of the method's original logic and is therefore not wrapped by other advices.

### Advices and the method invocation object

Advices can be dynamically woven into several points within application code, but to have a value they have to know about the target code and be able to influence it.

To achieve this, we will pass an object containing context information to every advice.

> Every advice MUST accept exactly one parameter of the type `\AppserverIo\Doppelgaenger\Interfaces\MethodInvocationInterface`.

The passed instance of the type `MethodInvocationInterface` fulfills three important purposes:

* It contains information about the original function call such as passed parameters, possible results, structure and method name and allows changes to some of them.
* It contains the context of the original call, meaning the instance of the called class.
* It contains the advice chain and allows to iterate manually through layered "Around" advices.

In the example below, an advice uses the passed context instance to retrieve the system's logger and other information about the method call.

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

As another example, see the `Around` advice below. It filters parameters and formats the result of the original method logic.

```php
<?php

/**
 * Advice adjusting parameters and result
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

As you can see, we can safely sanitize in- and output of any method without touching the original code. This allows for powerful solutions without littering any code with copy-and-paste snippets.

