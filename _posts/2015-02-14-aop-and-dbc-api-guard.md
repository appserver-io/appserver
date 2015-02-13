---
layout: post
title:  AOP and DbC - your API guards
meta_title: appserver.io aop and dbc api guard blog post
meta_description: A blog post explaining how AOP and Design by Contract can be used to protect a RESTful API
date:   2015-02-14 00:05:00
author: wick-ed
version: 1.0.0-rc3
categories: [design-by-contract]
---

This blog post will give an introduction to the appserver.io implementation of techniques such as [Design by Contract](https://en.wikipedia.org/wiki/Design_by_contract) and [AOP](https://en.wikipedia.org/wiki/Aspect-oriented_programming) and how they can be used.
 As an example we will make a simple and self validating JSON `REST` webservice.

Our webservice in question exposes a basic `CRUD` like JSON API and can be used to store object representations of user accounts.
What we will focus on is the handling of invalid data and how to respond to malformed requests without much (nearly none) effort from the developer side.

appserver.io utilizes the [appserver-io/doppelgaenger](https://github.com/appserver-io/doppelgaenger) library and allows to use its functionality within any webapp using annotations and XML configuration.
This example makes use of this as a showcase example to further expand on.

## How it works

To make our API self validation we have to meet two requirements:

* We have to define valid/invalid states and transitions
* We have to define a reaction to both

### Defining valid/invalid states

To define how our API is used in a valid or invalid way we will make use of the Design by Contract (DbC for short) feature of appserver.io.
Using it can be done by placing well designed constraints into our applications.
These constraints define so called contracts, doublets of `pre-` and `post-condition` and a coherent state, the `invariant`, which marks the valid state and transitions our app 
can handle.

You can define these contracts using [`Doctrine`-style](http://doctrine-common.readthedocs.org/en/latest/reference/annotations.html) annotations containing **valid PHP code conditions resulting in the boolean TRUE or FALSE**.

Any breach of a contract will result in an exception of the type `AppserverIo\Psr\MetaobjectProtocol\Dbc\ContractExceptionInterface` which we can later handle centrally.

> Preconditions can be annotated using the `@Requires` annotation, whereas post-conditions are defined using the `@Ensures` annotation.

So to explain how we use these concepts, we will have to have a look at the flow of data through our application.

If a request hits any of the actions we defined (more on that later) the request content will be passed to a connector class (JSON in our example) which tries to extract the needed information from the request message.
So basically turning JSON into objects of a certain type.

But as we do so, we want to also validate every step on the way.
Have a look how we do it:

```php
<?php
/**
 * Will create an instance of the $targetEntity class based on the data given as 
 * raw string
 *
 * @param string $rawData      Data string to generate the instance from
 * @param string $targetEntity The fully qualified class name to create an instance from
 *
 * @return object
 *
 * @Requires("is_object(json_decode($rawData))")
 * @Requires("class_exists($targetEntity)")
 *
 * @Ensures("$dgResult instanceof $targetEntity")
 */
public function instanceFromString($rawData, $targetEntity)
{
    // ...
}
```

As you can see in the example above we have used several annotations defining the following:

* Any data coming in has to be valid JSON
* Any object we are told to create from the JSON must be of a known class
* The resulting object must be an instance of the specified class

This is great news! We can pass a JSON string which is validated and will be transformed into an object of our choice!

Next thing we would like to do is making sure that the resulting object itself has a coherent state.

An example would be that any user email we want to safe must be a valid email address:.
We do that defining a contract for the used class setter method `setEmail`:

```php
<?php
/**
 * Sets the value for the class member email.
 *
 * @param string $email Holds the value for the class member email
 *
 * @return void
 *
 * @Ensures("is_string(filter_var($this->email, FILTER_VALIDATE_EMAIL))")
 */
public function setEmail($email)
{
    $this->email = $email;
}
```

This `@Ensures` annotations defines that the property `email` will contain a valid email address after calling `setEmail`.

> This technique can be used to test for password length, username syntax and a million other things.


Finally we want to collect our user entities and make sure nothing corrupts our storage.

Therefor we define an `invariant` which is a state of validity which is checked on every public access (read or write) on an object.
This is done using the `@Invariant` annotation within the header 

```php
<?php
/**
 * A singleton session bean implementation that handles the
 * data by using Doctrine ORM.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io-apps/api-guard
 * @link      http://www.appserver.io
 *
 * @Invariant("$this->isConsistent()")
 */
abstract class AbstractProcessor extends \Stackable implements ProcessorInterface
{
    // ...
}
```

Any class inheriting from our `AbstractProcessor` must implement the `isConsistent` method which is used to check for the storage consistency at every access.
Atomic consistency checks out of the box.

So we got:

* JSON validation
* Creation of coherent entities
* Continued check of storage integrity

And everything with just some small annotations!

### Defining reactions to contract breaches

So we got our contracts as briefly described [above](#defining-valid/invalid-states) which will turn every faulty request into an exception-hell.

> To make sense of these exceptions we will use the power of AOP.

To understand how we will have a look at our actions which have been mentioned before.

Actions are used to expose URL endpoints which can be requested by a client. They have a simple (yet configurable) URL-class/method mapping (have a look at [the routlt library we use here](https://github.com/appserver-io/routlt)).

The action used for our examples is the `create` action which is implemented as seen below:

```php
<?php
/**
 * Action to create an instance
 *
 * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  
 *          The request instance
 * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse 
 *          The response instance
 *
 * @return null
 */
public function createAction(
    HttpServletRequestInterface $servletRequest, 
    HttpServletResponseInterface $servletResponse
) {
    $instance = $this->connector->instanceFromString(
        $servletRequest->getBodyStream(), 
        static::TARGET_ENTITY
    );
    $proxy = $this->getProxy($servletRequest);
    $proxy->create($instance);
}
```

It is implemented within the abstract `\AppserverIo\Apps\ApiGuard\Actions\AbstractCrudAction` class and as inherited by the `UsersAction` class, results in the URL `http://127.0.0.1:9080/api-guard/index.do/users/create` being exposed to `GET` and `POST` requests.

As you can see the method makes use of all three examples made [above](#defining-valid/invalid-states):

* Passing the request body to our JSON connector
* Creating the entity object
* Passing the entity to our storage (over a proxy class)

So every exception resulting from a DbC contract breach will pass this method!

To not litter exception handling over all actions we might need in our API service (our example only has four but use your imagination) we will use AOP to centralize exception handling.

We will do so within our AOP `Aspect` class `\AppserverIo\Apps\ApiGuard\Aspects\ExceptionHandlingAspect`:

```php
<?php
/**
 * Aspect which is used to catch webservice DbC errors and respond to 
 * the client automatically
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io-apps/api-guard
 * @link      http://www.appserver.io/
 *
 * @Aspect
 */
class ExceptionHandlingAspect
{

    /**
     * Pointcut specifying all actions within any action class we have
     *
     * @return null
     *
     * @Pointcut("call(\AppserverIo\Apps\ApiGuard\Actions\*->*Action())")
     */
    public function allActionMethods()
    {
    }

    /**
     * Advice used to proceed a method but catch all Design by Contract 
     * related exceptions.
     * Will react on any caught exception with an error response to the client
     *
     * @param \AppserverIo\Psr\MetaobjectProtocol\Aop\MethodInvocationInterface 
     *          $methodInvocation Initially invoked method
     *
     * @return mixed
     *
     * @Around("pointcut(allActionMethods())")
     */
    public function actionExceptionToError(MethodInvocationInterface $methodInvocation)
    {
        try {
            return $methodInvocation->proceed();

        } catch (ContractExceptionInterface $e) {
            // build up the right format for our response message
            $messageObject = new \stdClass();
            $messageObject->error = new \stdClass();
            $messageObject->error->code = 400;
            $messageObject->error->message = 'Invalid request data';
            $message = $methodInvocation->getContext()
                ->getConnector()
                ->stringFromObject($messageObject);

            // get the servlet response and set the error message
            $parameters = $methodInvocation->getParameters();
            $servletResponse = $parameters['servletResponse'];
            $servletResponse->appendBodyStream($message);
            $servletResponse->setStatusCode($messageObject->error->code);
        }
    }
}
```

You can see two things:

* The body-less method `allActionMethods` acting as a [pointcut](https://en.wikipedia.org/wiki/Aspect-oriented_programming#Terminology) specifying all action methods in all action classes
* The method `actionExceptionToError` action as [Around advice](https://en.wikipedia.org/wiki/Aspect-oriented_programming#Terminology) handling the exception

First things first, the pointcut:
 
> Pointcuts are used to specify certain events at certain places within your applications flow.

The annotation `@Pointcut("call(\AppserverIo\Apps\ApiGuard\Actions\*->*Action())")` allows us to have a code hook at every call to every action method of every action class we have.
Let me repeat that: **every call to every action method of every action class we have!** :)


The advice references the pointcut using the annotation `@Around("pointcut(allActionMethods())")` and wraps around (hence the name `Around`) all specified methods.
That allows us to catch every DbC exception within any action and handle it in a central way.

> Using the automatically passed `$methodInvocation` instance we are able to access the action instance in question and therefore using the connector to append an automatic response.

Tadaa: Automatic validation of client requests using annotations!

## Try it yourself!

Got a taste for AOP? Build a contracted API yourself?
Have a look at [the api-guard app](https://github.com/appserver-io-apps/api-guard) which contains all examples and the working code to build your own webservice.

To do this, we assume you have installed appserver.io in a version of at least 1.0.0-rc3.

You also need the sources of this repository. So clone it, open a command line, change into your 
working  directory, which can be `/tmp` for example, and enter:

```
$ git clone https://github.com/appserver-io-apps/api-guard
```

Now you can change into the newly created `api-guard` directory and use our `ant` app deployment by executing:

```
$ ant composer-init
$ ant deploy
```

## Testing it

You can test the example's guarding functionality using simple `CURL` commands.
Using our default example of the `User` entity you might try the following:

Executing any of: 
```
$ curl -X POST --data '{"password": "test", "username": "tester", "email": "mail"}' http://127.0.0.1:9080/api-guard/index.do/users/create
$ curl -X POST --data '{"username": "tester", "email": "mail@mail.me"}' http://127.0.0.1:9080/api-guard/index.do/users/create
$ curl -X POST --data '{"password": "test", "email": "mail@mail.me"}' http://127.0.0.1:9080/api-guard/index.do/users/create
```

Will return the message:
```json
{
    "error": {
        "code": 400,
        "message": "Invalid request data"
    }
}
```

as at least one of our constraints has been broken.

Sending a request as such:
```
$ curl -X POST --data '{"password": "test", "username": "tester", "email": "mail@mail.me"}' http://127.0.0.1:9080/api-guard/index.do/users/create
$ curl -X POST --data '{"id": "f6e8f4c5073ee49d73e4a05b703c93de0e7bc321"}' http://127.0.0.1:9080/api-guard/index.do/users/get
```

will return a response status `200` as all constraints were met and a JSON representation of the user entity was stored in our `Singleton Session Bean`
(yes, this is a built-in in-memory data storage).

## A final note

The discussed techniques allow for a pretty powerful validation mechanism but there are still things we will expand on.
Next thing on our roadmap will be a better mapping of exceptions, allowing for more specific response messages!

So stay tuned! :)
