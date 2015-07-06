---
layout: docs_1_1
title: Dependency Injection
meta_title: Dependency Injection - appserver.io
meta_description: Dependency Injection enables developers to write cleaner, reusable and maintainable code with less coupling by injecting necessary instances at runtime.
position: 70
group: Docs
subNav:
  - title: What can be injected
    href: what-can-be-injected
  - title: How to inject an instance
    href: how-to-inject-an-instance
permalink: /get-started/documentation/1.1/dependency-injection.html
---
As we use Dependency Injection (DI) to inject instances of [Server-Side Component Types](<{{ "/get-started/documentation/persistence-container.html#server-side-component-types" | prepend: site.baseurl }}>) this section gives you a brief introduction of how DI works in the `Persistence-Container` context.

DI enables developers to write cleaner, reusable and maintainable code with less coupling by injecting necessary instances at runtime instead of instantiating them in the class itself. Within the application server, each application has its scope, and, therefore, an  own dependency injection container. This prevents your application from fatal errors like `Cannot redeclare class ...`.

Since DI in combination with application state is a complex issue, it is worthwhile to have a closer look at the most important elements.

## Injection elements

The application server does not use DI. Instead, it provides DI as a service for the applications running in apperserver.io. All session and message-driven beans, the application instance and all managers can be injected.  But, before the DI container can inject an instance to your class, you have to register it. Registering beans is either done by annotations or a deployment descriptor.

The following example shows you how to annotate an `SLSB` and make it available for DI.

```php
<?php

namespace AppserverIo\Example\SessionBeans;

/**
 * @Stateless(name="AStatelessSessionBean")
 */
class AStatelessSessionBean
{

  /**
   * Creates and returns a new md5 hash for the passed password.
   *
   * @param string $password The password we want to hash
   *
   * @return string The md5 hash representation of the password
   */
  public function hashPassword($password)
  {
    return md5($password);
  }
}
```

After having registered your beans, a pretty straigthforward process when using annotations, you are ready to inject them.

## How to inject an instance

DI can be a manual process, where you `inject` an instance needed by another class by passing it to the constructor, for example. Inside the `Persistence-Container`, the injection is a process, which can be hard to follow. Thus, it seems like magic happening behind the scenes. So, instead of manually passing the necessary instances to a class' constructor, the DI container will do that for you.

A developer simply has to tell the DI container which instance needs to be injected at runtime. The following section describes the two available options.

### Property Injection

The first option is to annotate a class property using the `@EnterpriseBean` annotation. The annotation accepts a `name` attribute, which allows you to specify the name of a registered bean, like the one we registered above. The following code snippet shows you how to annotate a class property and initiate appserver.io to inject the instance of `AStatelessSessionBean` at runtime.

```php
<?php

namespace AppserverIo\Example\SessionBeans;

/**
 * @Stateful
 */
class AStatefulSessionBean
{

  /**
   * The SessionBean instance we want to have injected.
   *
   * @var \AppserverIo\Example\SessionBeans\AStatelessSessionBean
   * @EnterpriseBean(name="AStatelessSessionBean")
   */
  protected $aStatelessSessionBean;

  /**
   * Encrypts and stores a password.
   *
   * @param string $password The password to be encrypted and stored
   *
   * @return void
   */
  public function savePassword($password)
  {

    // encrypt password by calling the SLSB
    $encryptedPassword = $this->aStatelessSessionBean->hashPassword($password);

    /*
     * Implement functionality to store password to database here
     */
  }
}
```

As the `@EnterpriseBean` annotation with the `name` attribute is not the only option to inject instances, a more detailed description about the available annotations will follow later.

> Property injection is the recommended method, because it allows for greater performance.
>

### Setter Injection

The second possibility to inject an instance is through setter injection. Setter injection allows developers to inject instances by using methods.

```php
<?php

namespace AppserverIo\Example\SessionBeans;

/**
 * @Stateful
 */
class AStatefulSessionBean
{

  /**
   * The SessionBean instance we want to have injected.
   *
   * @var \AppserverIo\Example\SessionBeans\AStatelessSessionBean
   */
  protected $aStatelessSessionBean;

  /**
   * Injects the stateless session bean.
   *
   * @param \AppserverIo\Example\SessionBeans\AStatelessSessionBean $aStatelessSessionBean
   *     The stateless session to be injected
   *
   * @return void
   * @EnterpriseBean(name="AStatelessSessionBean")
   */
  public function injectAStatelessSessionBean($aStatelessSessionBean)
  {
    $this->aStatelessSessionBean = $aStatelessSessionBean;
  }

  /**
   * Encrypts and stores a password.
   *
   * @param string $password The password to be encrypted and stored
   *
   * @return void
   */
  public function savePassword($password)
  {

    // encrypt password by calling the SLSB
    $encryptedPassword = $this->aStatelessSessionBean->hashPassword($password);

    /*
     * Implement functionality to store password to database here
     */
  }
}
```

In the above example, the container injects an instance of `AStatelessSessionBean` at runtime by invoking the `injectAStatelessSessionBean` method passing the instance as an argument.

> Method injection only works on methods that have exactly one argument. As described above, the container does not inject a real instance of the bean. Instead, it injects a proxy. That proxy does not extend the class or, if given, does not implement the interfaces of your bean. So, do NOT type hint the argument with the class or an interface name.
