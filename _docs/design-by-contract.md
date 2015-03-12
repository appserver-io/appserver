---
layout: docs
title: Design by Contract
meta_title: appserver.io design by contract (DbC)
meta_description: DbC extends the ordinary definition of classes, abstract classes and interfaces by adding pre-/postconditions and invariants referred to as contracts
position: 140
group: Docs
subNav:
  - title: What can be contracted?
    href: what-can-be-contracted
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

> Besides fail-fast behaviour, which would be an exception, our implementation offers [PSR-3](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md) compatible logging as a reaction on contract break

## What can be contracted?

### Type hints
The most basic type of contract is one used all the time, and is already integrated into most IDEs: annotation based type hinting.
If following [common syntax](http://phpdoc.org/docs/latest/guides/types.html) we can make use of these annotations to enforce strong typing at method call and return point.

> We currently support the `@param` and `@return` annotations as type hints (scalar and class/interface
> based), including special features like `typed arrays` using e. g. `\stdClass[]`

A basic example would be the following

```php
<?php

/**
 * Add string to storage. Will return resulting storage size
 *
 * @param string $string String to add
 *
 * @return integer
 */
public function addString($string)
{
    // ...
}
```

As stated in the comments this example method will add a string to a storage and return the resulting storage size. Using Design by Contract both the parameter type and the return type will be enforced.

This is possible for scalar types as well as complex ones and offers an addition for `typed arrays`.
An example might be:

```php
<?php

/**
 * Will return an array of all strings currently stored
 *
 * @return string[]
 */
public function getStrings()
{
    // ...
}
```

### Execution constraints

A more complex use case is writing out execution constraints within contracts.
This can be done using the mentioned condition clauses.

An example expanding on the former `addString` code piece would be:

```php
<?php

/**
 * Add string to storage. Will return resulting storage size
 *
 * @param string $string String to add
 *
 * @return integer
 *
 * @Ensures("$this->stringExists($string)")
 */
public function addString($string)
{
    // ...
}
```

The resulting behaviour would not just check for basic parameter and return type, but would also assure that the passed string variable did actually get added 
to the storage (assuming the existence of a `stringExists` method).

This constraint on the execution of the `addString` method is by now solely base on its implementation and can therefore not be changed during runtime.
But we can also make on parameters:

```php
<?php

/**
 * Add a string shorter than six characters to storage. Will return resulting storage size
 *
 * @param string $string Short string to add
 *
 * @return integer
 *
 * @Requires("strlen($string) <= 6")
 *
 * @Ensures("$this->stringExists($string)")
 */
public function addShortString($string)
{
    // ...
}
```

Assuming that our storage can only handle strings equally long or shorter than six characters we can constraint the length of our input parameter.
The precondition constraint guards our storage by enforcing proper string length and at the same time gives the assurance that the string will be stored if having the proper length.

A constraint based contract for the execution of a method is born.

### State validity

As the Design by Contract principle is based on the concept of contracts within the business world we have to consider another element.
The already mentioned `invariant` as a global constraint to possibilities of interaction.

In the financial market this would be laws about taxes and contract clauses, in the programming world this is a valid state an object has to maintain during its lifecycle.

> The invariant describes a valid state an object must have at defined moments in its lifetime

For our implementation these defined moments are:

* After its construction (leaving the `__construct` method)
* Before any read/write access to a public property
* After any write access to a public property
* Before and after any call to a public method

These invariants are stated using the @Invariant annotation.
A basic example following former examples would be:

```php
<?php

/**
 * Class which is used to store string literals
 *
 * @Invariant("$this->onlyContainsStrings()")
 */
class StringStorage
{
    // ...
}
```

The annotation above would result in a constraint for the content of the storage (given `onlyContainsStrings` exists) which checks that there are only strings within the storage on every relevant public access to an object of the class `StringStorage`.

## Usage

The examples above already give a glimpse of how to use Design by Contract within your code.
Here, some rules and guidelines will be mentioned.

### Annotation syntax

First of all, syntax.
The syntax of an annotation is very simple and follows `Doctrine` syntax and is composed as follows:

```
@<AnnotationName>("<Constraint>")
```

The constraint itself MUST be a valid conditional PHP expression which evaluates to the boolean `true` or `false` or any value castable to a boolean.

### Scope

The code within the constraints can make use of variables and properties of the containing structures based on their specific scope.
These constraint scopes are listed below:

| Annotation   | Scope       | Description                                                                         |
| -------------| ------------| ------------------------------------------------------------------------------------|
| `@Requires`  | method      | Shares the scope of the method the annotation belongs to                            |
| `@Ensures`   | method      | Shares the scope of the method the annotation belongs to                            |
| `@Invariant` | structure   | Consider as called from a parameter-less private method within the structure itself |

We do offer two more variables which are available to our constraints, additionally to the variables and properties visible within the original structure definition.

| Variable name | Usable within | Description                                                                                                        |
| --------------| --------------| -------------------------------------------------------------------------------------------------------------------|
| `$dgResult`   | `@Ensures`    | Contains the result of the method the annotation belongs to. Can be used to constrain the actual method result     |
| `$dgOld`      | `@Ensures`    | Contains a copy of the object instance before method execution, can be used to test changes to the object's state  |

## Configuration

### Where to change the configuration

Design by Contract is fully integrated into the appserver.io infrastructure's autoloading capabilities. This is due to the 
heavy modifications it requires for structure definitions and the applied caching in the file system.

> To change any Design by Contract behaviour the change therefore has to be made within the class loader configuration

The class loader configuration can be found within any `context.xml` file relevant to the application changes should apply to.
These might be:

* The global file at `etc/appserver/conf.d/context.xml` which is relevant to all applications
* The webapp specific file located within the webapp's `META-INF` directory

So choose wisely where to make changes to the configuration.

### Configuration options

Below is an example of the configuration appserver.io ships with.
The attributes of the `classLoader` element are mandatory and need not be changed for any configuration changes.
One only might change them to use alternative implementations or bootstrapping processes.

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

Most important are the `param` elements.
See their meaning and options as listed below.

| Param name         | Options                     | Description                                                     |
| -------------------| ----------------------------| ----------------------------------------------------------------|
| `environment`      | production&#124;development | Whether or not contracted definitions get cached. Recommended is production, as an appserver restart will clear the cache by force, therefore the cache does not hinder development   |
| `enforcementLevel` | [ 1 - 7 ]                   | A bitmask similar to the Linux user right notation. This will be used to switch on (or off) enforcement features. The bitmask can be written as `invariants post-conditions preconditions`, so the default `7` results in all contract elements being enforced whereas `1` would only enforce preconditions |
| `typeSafety`       | 0&#124;1                    | Whether or not annotation based type hints get enforced as described [above](#what-can-be-contracted) |
| `processing`       | logging&#124;exception      | Which kind of reaction a contract or type safety breach triggers. `logging` will result a message within the system logger. `exception` will result in an exception of the type `\AppserverIo\Psr\MetaobjectProtocol\Dbc\ContractExceptionInterface`. Throw point is the method in which the contract got breached |

Adding a `directory` element we can specify which directory should be loaded by our class loader.
This is common configuration for every class loader type.
Special for our class loader is that for every one of these directories enforcement of Design by Contract can be toggled.


If the attribute `enforced` is set to FALSE,  no enforcement will take place for the mentioned directory. 
