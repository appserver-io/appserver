---
layout: docs
title: Design by Contract
meta_title: appserver.io design by contract (DbC)
meta_description: DbC extends the ordinary definition of classes, abstract classes and interfaces by adding pre-/postconditions and invariants referred to as contracts
position: 120
group: Docs
subNav:
  - title: What can be contracted
    href: what-can-be-contracted
  - title: How it works
    href: how-it-works
  - title: Usage
    href: usage
  - title: Configuration
    href: configuration
permalink: /get-started/documentation/design-by-contract.html
---

Besides AOP, [Design-by-Contract](http://en.wikipedia.org/wiki/Design_by_contract) is another
interesting architectural approach we support out-of-the-box.

First introduced by Bertram Meyer in connection with his design of the [Eiffel programming language](https://en.wikipedia.org/wiki/Eiffel_%28programming_language%29),
Design by Contract allows you to define formal, precise and verifiable interface specifications of
software components.

Design by Contract extends the ordinary definition of classes and interfaces by allowing the possibility of defining so called `contracts` (hence the name).
These contracts allow every public method to specify `preconditions` which they need to function and a `post-condition` as a result they assure for their proper execution.
Additionally one can state so called `invariants` which define an atomic state of integrity for the structure itself.
Such contract elements are checked during program runtime and allow for true domain logic [fail-fast](https://en.wikipedia.org/wiki/Fail-fast) programming.

## What can be contracted?

The most basic type of contract is one used all the time and is already integrated into most IDEs: annotation based type hinting.
If following [common syntax](http://phpdoc.org/docs/latest/guides/types.html) we can make use of these annotations to enforce strong typing at method call and return.

> We currently support the `@param` and `@return` annotations as type hints (scalar and class/interface
> based), including special features like `typed arrays` using e. g. `\stdClass[]`



- Specify complex method contracts in PHP syntax using `@requires` as precondition and `@Ensures` as 
  postcondition
- Specify a state of validity for your classes (e.g. `$this->attribute !== null`) which should be true
  all times using `@Invariant`
- The above can be (not enabled by default) inherited by every child structure, strengthening your
  object hierarchies
- The library will warn you (exception or log message) on violation of these contracts

## How it works

We use a system of autoloading and code creation to ensure our annotations will get enforced.
This features a 4 step process:

- Autoloader : Handles autoloading and will know if contract enforcement is needed for a certain file.
  If so (and the cache is empty) the call will be directed to the Generator/Parser Combo
- Parser : Will parse the needed file using [`Tokenizer`](<http://www.php.net/manual/en/book.tokenizer.php>)
  and provide information for further handling.
- Generator : Will use stream filters to create a new file structure definition containing configured enforcement
- Cache : Will allow us to omit Parser and Generator for future calls, to speed up usage significantly.

## Usage

Supposed, we want to make sure, that the counter in our stateful SessionBean will always be an integer, we can 
define a simple contract therefor

```php
<?php

namespace Namespace\Module;

/**
 * This is demo implementation of stateful session bean.
 *
 * @Stateful
 * @Invariant("is_integer($this->counter)")
 */
class MyStatefulSessionBean
{

  /**
   * Stateful counter that exists as long as your session exists.
   *
   * @var integer
   */
  protected $counter = 0;

  /**
   * Example method that raises the counter by one each time you'll invoke it.
   *
   * @return integer The raised counter
   */
  public function raiseMe()
  {
    return $this->counter++;
  }
}
```

Depending on your [configuration](#configuration), if a method would try to set a string on the counter variable, the
Design-by-Contract implementation would either throw an exception or write an error message to our 
log file under `/opt/appserver/var/log/appserver-errors.log`.

## Configuration

```xml
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
```


