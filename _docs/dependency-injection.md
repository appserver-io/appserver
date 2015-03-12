---
layout: docs
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
permalink: /get-started/documentation/dependency-injection.html
---
As we probably use DI to inject instances of [Server-Side Component Types](<{{ "/get-started/documentation/persistence-container.html#server-side-component-types" | prepend: site.baseurl }}>) this section gives you a brief introduction of how DI works in the `Persistence-Container` context. 

Dependency Injection, furthermore DI, enables developers to write cleaner, reusable and maintainable code with less coupling by injecting necessary instances at runtime instead of instantiating them in the class itself. Within the application server, each application has it's own scope and therefore a  own dependency injection container. This prevents your application from fatal errors like `Cannot redeclare class ...`.

DI can be a complicated subject, escpecially if it come together with application state! Let's try to explain the most important things in short. 

## What can be injected

The application server itself doesn't use DI, instead it provides DI as a service for the applications running within. Actually all session and message driven beans, the application instance and all managers can be injected.  But, before you can let the DI container inject an instance to your class, you have to register it. Registering beans can either be done by annotations or a deployment descriptor.

The following example shows you how to annotated a `SLSB` and make it available for DI.

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

After register your beans, what is pretty simple when using annotations, you're ready to inject them!

## How to inject an instance

Basically DI can be a manual process where you `inject` an instance, needed by another class by passing it to the constructor for example. Inside the `Persistence-Container`, the injection is an process you can't see, it's more some kind of magic which happens behind the scenes. So instead of manually pass the necessary instances to a classes constructor, the DI container will do that for you.

A developer simply has to tell the DI container what instance has to be injected at runtime. So let's have a look at the options he has.

### Property Injection

The first possibility we have is to annotate a class property using the `@EnterpriseBean` annotation. The annotation accepts a `name` attribute which allows you to specify the name of a bean you've registered before. The following example shows you how to annotate a class property and let the application server inject an instance of `AStatelessSessionBean` at runtime.

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

> Property injection is preferred, because of massive performance improvements.

### Setter Injection

The second possibility to inject an instance is the setter injection. Setter injection allows developers to inject instances by using methods. 

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

In that example the container will inject an instance of `AStatelessSessionBean` at runtime by invoking the `injectAStatelessSessionBean` method passing the instance as argument.

> Method injection only works on methods with exactly one argument. As described above, the container don't inject a real instance of the bean, instead it injects a proxy. That proxy actually not extends the class or, if given, implements the interfaces of your bean. So do NOT type hint the argument with the class or a interface name!

