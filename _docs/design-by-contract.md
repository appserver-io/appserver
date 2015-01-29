---
layout: docs
title: Design by Contract
position: 120
group: Docs
subDocs:
  - title: What can be done
    href: what-can-be-done
  - title: How does it work
    href: how-does-it-work
  - title: Usage
    href: usage
permalink: /documentation/design-by-contract.html
---

Beside AOP, [Design-by-Contract](http://en.wikipedia.org/wiki/Design_by_contract) is another
interesting approach we support out-of-the-box when you think about the architecture of your
software.

First introduced by Bertram Meyer in connection with his design of the Eiffel programming language
Design-by-Contract allows you to define formal, precise and verifiable interface specifications of
software components.

Design-by-Contract extends the ordinary definition of classes, abstract classes and interfaces by
adding pre-/postconditions and invariants referred to as `contracts`. As Design-by-Contract is, as
AOP, is not part of the PHP core, we also use annotations to specify these contracts.

## What can be done?

As stated above this library aims to bring you the power of Design by Contract, an approach to make
your applications more robust and easier to debug. This contains basic features as:

- Use your basic `DocBlock` annotations `@param` and `@return` as type hints (scalar and class/interface
  based), including special features like `typed arrays` using e. g. `array<int>` (collections for
  complex types only yet)
- Specify complex method contracts in PHP syntax using `@requires` as precondition and `@ensures` as 
  postcondition
- Specify a state of validity for your classes (e.g. `$this->attribute !== null`) which will be true
  all times using `@invariant`
- The above (not including type safety) will be inherited by every child structure, strengthening your
  object hierarchies
- The library will warn you (exception or log message) on violation of these contracts

## How does it work?

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
 * @invariant is_integer($this->counter)
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

Depending on your configuration, if a method would try to set a string on the counter variable, the
Design-by-Contract implementation would either throw an exception or write an error message to our 
log file under `/opt/appserver/var/log/appserver-errors.log`.
