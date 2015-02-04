---
layout: post
title:  Naming-Directory Basics
date:   2015-02-04 12:00:00
author: wagnert
version: 1.0.0-rc1
categories: [naming-directory]
---

Every container running in the application server has a internal registry, we call it Naming Directory. In Java this is called `Enterprise Naming Context` or in short `ENC`. The naming directory is something like an object store, the container registers references to its resources. Resources can be beans or contexts provided by an application. All that resources are registered in the `Naming-Directory` which allows you the access them if needed.

## Configure directories to be parsed
***

When the application server starts, by default, it parses the `META-INF/classes` and `WEB-INF/classes` folders of your application to find components with supported annotations.

What directories are parsed to locate annotated components can be configured in your applications configuration file. If you don't bundle a specific configuration file with your application, the default configuration will be used. The default application configuration is located under `etc/appserver/conf.d/context.xml` and should **NEVER** be edited. The nodes `/context/managers/manager[@name="BeanContextInterface" or @name="ServletContextInterface"]` are responsible for parsing and initializing the components.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<context 
  name="globalBaseContext"
  type="AppserverIo\Appserver\Application\Application"
  xmlns="http://www.appserver.io/appserver">
  ...
  <managers>
    ...
    <manager
      name="BeanContextInterface"
      type="AppserverIo\Appserver\PersistenceContainer\BeanManager"
      factory="AppserverIo\Appserver\PersistenceContainer\BeanManagerFactory">
      <directories>
        <directory>/META-INF/classes</directory>
      </directories>
    </manager>
    <manager
      name="ServletContextInterface"
      type="AppserverIo\Appserver\ServletEngine\ServletManager"
      factory="AppserverIo\Appserver\ServletEngine\ServletManagerFactory">
      <directories>
        <directory>/WEB-INF/classes</directory>
      </directories>
    </manager>
    ...
  </managers>
</context>
```

You can bundle your application with its own, customized `context.xml` file. This **MUST** be placed in your applications `META-INF` directory. The file **MUST NOT** be a full copy of the default one, it allows you to override the nodes you want to customize or extend. To add an additional directory like `common/classes` for example, your `context.xml` file could simply look like this

```xml
<?xml version="1.0" encoding="UTF-8"?>
<context 
  name="globalBaseContext"
  type="AppserverIo\Appserver\Application\Application"
  xmlns="http://www.appserver.io/appserver">
  <managers>
    <manager
      name="BeanContextInterface"
      type="AppserverIo\Appserver\PersistenceContainer\BeanManager"
      factory="AppserverIo\Appserver\PersistenceContainer\BeanManagerFactory">
      <directories>
        <directory>/common/classes</directory>
        <directory>/META-INF/classes</directory>
      </directories>
    </manager>
  </managers>
</context>
```

> Keep in mind, that the directory MUST be relative to your applications root directory and start with a `/`.

More detailed information about the how to configure an application can be found in the section [Application Configuration](<{{ "/get-started/documentation/configuration.html#application-configuration" | prepend: site.baseurl }}>) of the documentation.

## Register Resources
***

If a class is found, the class will be registered in the application servers naming directory under the name you specify in the annotations `name` attribut. As the `name` attribute is optional, the bean will be registered in the naming directory with the short class name, if not specified.

When you want to inject a bean later, you have to know the name it has been registered with. In the following example, the bean will be registered in the naming directory under 

* `php:global/example/AStatelessSessionBean`

whereas `example` is the name of the application.

> The name of your application is *ALWAYS* the directory it'll be deployed to. As the document root is by default `webapps`, which, for example on a Linux system, will result in `/opt/appserver/webapps`, the name of your application will be `example` and will be located under `/opt/appserver/webapps/example`.

When using annotations to inject components, you don't have to know the fully qualified name, because the application server knows the context you're in, tries to lookup the bean and injects it.

```php
<?php

namespace AppserverIo\Example\SessionBeans;

/**
 * @Stateless
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
  
  /* Creates a new user, hashes the password before.
   *
   * @param string $username The username of the user to create
   * @param string $password The password bound to the user
   *
   * @return void
   */
  public function createUser($username, $password)
  {
    
    // hash the password
    $hashedPassword = $this->hashPassword($password);
    
    /*
     * Implement functionality to create user in DB
     */
  }
}
```

## Annotations
***

Using annotations to configure your components will probably be the easiest way. We provide several annotations that allows you to register and configure your components on the one side and inject them during runtime on the other.

### Stateless Session Bean (@Stateless)

The `@Stateless` annotation defines a component als `Stateless` session bean. The annotation only supports the optional `name` attribute. If the `name` attribute is specified, the given value will be used to register the component in the `Naming-Directory` instead of the short class name.

### Stateful Session Bean (@Stateful)

The `@Stateful` annotation defines a component als `Stateful` session bean. The annotation only supports the optional `name` attribute. If the `name` attribute is specified, the given value will be used to register the component in the `Naming-Directory` instead of the short class name. The annotation has to be set at the classes DocBlock.

### Singleton Session Bean (@Singleton)

The `@Singleton` annotation defines a component als `Singleton` session bean. The annotation only supports the optional `name` attribute. If the `name` attribute is specified, the given value will be used to register the component in the `Naming-Directory` instead of the short class name. The annotation has to be set at the classes DocBlock.

### Message Driven Bean (@MessageDriven)

The `@MessageDriven` annotation defines a component als `Message Driven` bean. The annotation only supports the optional `name` attribute. If the `name` attribute is specified, the given value will be used to register the component in the `Naming-Directory` instead of the short class name. The annotation has to be set at the classes DocBlock.

### Explicit Startup (@Startup)

The `@Startup` annotation configures a `Singleton` session bean to be initialized on application startup and can **explicitly** be used on `Singleton` session beans. The annotation doesn't accept any attributes and has to be set at the classes DocBlock.

### Post-Construct Callback (@PostConstruct)

This annotation marks a method as `post-construct` lifecycle callback and has to be set at the methods DocBlock. The annotation can be used on all [Server-Side Component Types](<{{ "/persistence-container/2015/01/30/persistence-container-basics.html#server-side-component-types" | prepend: site.baseurl }}>) and doesn't accept any attributes.

### Pre-Destroy Callback (@PreDestroy)

This annotation marks a method as `pre-destroy` lifecycle callback and has to be set at the methods DocBlock. The annotation can be used on all [Server-Side Component Types](<{{ "/persistence-container/2015/01/30/persistence-container-basics.html#server-side-component-types" | prepend: site.baseurl }}>) and doesn't accept any attributes.

### Enterprise Beans (@EnterpriseBean)

This annotation is used to inject [components](<{{ "/persistence-container/2015/01/30/persistence-container-basics.html#server-side-component-types" | prepend: site.baseurl }}>) into other components.

The `@EnterpriseBean` annotation can be used in two scopes. First scope is in the DocBlock of a components class member, second of a class method. In both cases, the member or the method is marked as target for `Dependency Injection`.

In the simplest case **NO** attribute is needed. If so, the member or parameter name **MUST** exactly match the components `name` that should be injected. Otherwise you have to specify the `name` attribute and optionally the `beanName` and `beanInterface` or `lookup` attribute.

| Node Name                   | Type        | Description                                                          |
| --------------------------- | ----------- | -------------------------------------------------------------------- |
| `description`               | `string`    | Short description for the created reference.                         |
| `name`                      | `string`    | Name the reference will be registered in the `Naming-Directory`.     |
| `beanName`                  | `string`    | The `name` of the component we want to reference.                    |
| `beanInterface`             | `string`    | The business interface we want to reference. This has to be the `name`, suffixed with either one of `Local` or `Remote`. |
| `lookup`                    | `string`    | The fully qualified name the component that has to be referenced has been registered in the `Naming-Directory`. |

### Resources (@Resource)

This annotation is used to inject resources into components.

As resources are classes, that are initialized during application server startup to handle the main application functionality, they are not accessed by a proxy. When adding a `@Resource` annotation to inject a resource, a simple reference to the resource, using a callback, will be registered in the `Naming-Directory`.

The `@Resource` annotation can be used in two scopes. First scope is in the DocBlock of a components class member, second of a class method. In both cases, the member or the method is marked as target for `Dependency Injection`.

In the simplest case **NO** attribute is needed. If so, the member or parameter name **MUST** exactly match the resource `name` that should be injected. Otherwise you have to specify the `name` attribute and the `type` attribute.

| Node Name                   | Type        | Description                                                          |
| --------------------------- | ----------- | -------------------------------------------------------------------- |
| `description`               | `string`    | Short description for the created reference.                         |
| `name`                      | `string`    | Name the reference will be registered in the `Naming-Directory`.     |
| `type`                      | `string`    | The `name` of the resource we want to reference.                     |

### Example

The following example implementation of a `Singleton` session bean contains all available annotations and demonstrates how they can be used.

```php
<?php

namespace AppserverIo\Example\SessionBeans;

/**
 * Example implementation of a singleton session bean using that'll be initialized
 * on application startup, uses post-construct and pre-destroy lifecycle callbacks
 * and dependency injection.
 *
 * @Singleton
 * @Startup
 */
class ASingletonSessionBean
{

  /**
   * The application instance that provides the entity manager.
   *
   * @var \AppserverIo\Psr\Application\ApplicationInterface
   * @Resource(name="ApplicationInterface")
   */
  protected $application;

  /**
   * A stateless session bean instance that using property injection.
   *
   * @var \AppserverIo\Example\SessionBeans\AStatelessSessionBean
   * @EnterpriseBean
   */
  protected $aStatelessSessionBean;

  /**
   * A stateful session bean instance injected by method injection.
   *
   * @var \AppserverIo\Example\SessionBeans\AStatefulSessionBean
   */
  protected $aStatefulSessionBean;
  
  /**
   * Injects a stateful session bean instance.
   *
   * @param \AppserverIo\Example\SessionBeans\AStatefulSessionBean
   *
   * @return void
   * @EnterpriseBean
   */
  public function injectAStatelessSessionBean($aStatefulSessionBean)
  {
    $this->aStatefulSessionBean = $aStatefulSessionBean;
  }
  
  /**
   * Post-Construct lifecycle callback implementation.
   *
   * @return void
   * @PostConstruct
   */
  public function postConstruct()
  {
    // to something after initialization here
  }
  
  /**
   * Pre-Destroy lifecycle callback implementation.
   *
   * @return void
   * @PreDestroy
   */
  public function preDestroy()
  {
    // to something before destruction here
  }
}
```

## Deployment Descriptor
***

Beside the possibility to configure nearly everything by annotations, it is also possible to resign annotations and use a XML based deployment descriptor called `epb.xml`. As we think that annotations are the way, that most developers will prefer, we'll only give a short overview of a deployment descriptors structure here.

The following example is a simplyfied copy of the deployment descriptor of our [example](https://github.com/appserver-io-apps/example) application and provides a brief overview of the structure.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<epb xmlns="http://www.appserver.io/appserver">
  <enterprise-beans>
    <session>
      <session-type>Singleton</session-type>
      <epb-name>ASingletonProcessor</epb-name>
      <epb-class>AppserverIo\Apps\Example\Services\ASingletonProcessor</epb-class>
      <init-on-startup>true</init-on-startup>
      <post-construct>
        <lifecycle-callback-method>initialize</lifecycle-callback-method>
      </post-construct>
    </session>
    <session>
      <session-type>Stateful</session-type>
      <epb-name>UserProcessor</epb-name>
      <epb-class>AppserverIo\Apps\Example\Services\UserProcessor</epb-class>
      <pre-destroy>
        <lifecycle-callback-method>destroy</lifecycle-callback-method>
      </pre-destroy>
    </session>
    <session>
      <session-type>Stateless</session-type>
      <epb-name>SampleProcessor</epb-name>
      <epb-class>AppserverIo\Apps\Example\Services\SampleProcessor</epb-class>
      <post-construct>
        <lifecycle-callback-method>initialize</lifecycle-callback-method>
      </post-construct>
      <pre-destroy>
        <lifecycle-callback-method>destroy</lifecycle-callback-method>
      </pre-destroy>
      <epb-ref>
        <epb-ref-name>UserProcessor</epb-ref-name>
        <lookup-name>php:global/example/UserProcessor</lookup-name>
        <injection-target>
          <injection-target-class>
	   AppserverIo\Apps\Example\Services\SampleProcessor
          </injection-target-class>
          <injection-target-property>userProcessor</injection-target-property>
        </injection-target>
      </epb-ref>
      <res-ref>
        <description>Reference to the application</description>
        <res-ref-name>ApplicationInterface</res-ref-name>
        <injection-target>
          <injection-target-class>
            AppserverIo\Apps\Example\Services\AbstractProcessor
          </injection-target-class>
          <injection-target-method>injectApplication</injection-target-method>
        </injection-target>
      </res-ref>
    </session>
  </enterprise-beans>
</epb>
```

The structure should be self-explanatory, as it nearly reflects the annotation `@EnterpriseBean` and `Resource` annotations. The following table describes all possible nodes and gives a short description about meaning and options.

`/epb/enterprise-beans/session`

Either defines a `SLSB`, `SFSB` or a `SSB`.

| Node Name                   | Type        | Description                                                          |
| --------------------------- | ----------- | -------------------------------------------------------------------- |
| `session-type`              | `string`    | Can be one of `Stateless`, `Stateful` or `Singleton`.                |
| `epb-name`                  | `string`    | Short name of the component used for registration in naming directory. |
| `epb-class`                 | `string`    | Fully qualified class name of the components class.                  |
| `init-on-startup`           | `boolean`   | `true` if the component should be instanciated on application startup. This can only be set to `true` if `session-type` is `Singleton`. |

`/epb/enterprise-beans/message-driven`

Defines a `MDB`.

| Node Name                   | Type        | Description                                                          |
| --------------------------- | ----------- | -------------------------------------------------------------------- |
| `epb-name`                  | `string`    | Short name of the component used for registration in naming directory. |
| `epb-class`                 | `string`    | Fully qualified class name of the components class.                  |

`/epb/enterprise-beans/[session or message-driven]/post-construct`

Adds a `post-construct` lifecycle callback to the component.

| Node Name                   | Type        | Description                                                          |
| --------------------------- | ----------- | -------------------------------------------------------------------- |
| `lifecycle-callback-method` | `string`    | Name of a class method that will be invoked after the component been initialized. |

`/epb/enterprise-beans/[session or message-driven]/pre-destroy`

Adds a `pre-destroy` lifecycle callback to the component.

| Node Name                   | Type        | Description                                                          |
| --------------------------- | ----------- | -------------------------------------------------------------------- |
| `lifecycle-callback-method` | `string`    | Name of a class methode that will be invoked before the class will be destroyed. |

`/epb/enterprise-beans/[session or message-driven]/epb-ref`

Creates a reference to the remote or local business interface of the defined session bean in the naming directory under `php:global/example/env/[epb-ref-name][Local or Remote]`. This reference can be used by other components or for DI purposes.

| Node Name                   | Type        | Description                                                          |
| --------------------------- | ----------- | -------------------------------------------------------------------- |
| `description`               | `string`    | A short description of the reference that will be created.           |
| `epb-ref-name`              | `string`    | The name of the reference created in the naming directory.           |
| `epb-link`                  | `string`    | Name of referenced component. This name is by default the short class name or can be overwritten by the `name` attribute of the `Stateless`, `Stateful` or `Singleton` annotations. |
| `lookup-name`               | `string`    | Optionally to the `epb-link` this value contains the fully qualified name of the referenced component. |
| `remote`                    | `boolean`   | If a value has been specified, a reference to the remote proxy will be created instead of a local one. |

`/epb/enterprise-beans/[session or message-driven]/res-ref`

Creates a reference the defined resource in the naming directory under `php:global/example/env/[res-ref-name]`. This reference can be used by other components or for DI purposes.

| Node Name                   | Type        | Description                                                          |
| --------------------------- | ----------- | -------------------------------------------------------------------- |
| `description`               | `string`    | A short description of the reference that will be created.           |
| `res-ref-name`              | `string`    | The name of the reference created in the naming directory.           |
| `res-ref-type`              | `string`    | The type of the reference resource.                                  |

`/epb/enterprise-beans/[session or message-driven]/[ebp-ref or res-ref]/injection-target`

Injects the reference by either using the method or property defined. The class name will be of interest, if there exists a hierarchy and the target class has to be specified explictly.

| Node Name                   | Type        | Description                                                          |
| --------------------------- | ----------- | -------------------------------------------------------------------- |
| `injection-target-class`    | `string`    | The class we want to inject the reference.                           |
| `injection-target-method`   | `string`    | Use this method to inject the reference on runtime.                  |
| `injection-target-property` | `string`    | Inject the reference to this property, whereas either this node or `injection-target-method` can be specified. |

> Annotations can be seen as default values, whereas a deployment descriptor enables a developer or a system administrator to override values specified in annotations. So keep in mind, that a deployment descriptor will always override the values specified by annotations. 

## Summary
***

