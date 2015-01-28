---
layout: post
title:  Naming-Directory Basics
date:   2015-01-28 12:00:00
author: wagnert
version: 1.0.0beta4
categories: [Naming-Directory]
---

Every container running in the application server has a internal registry, we call it Naming Directory. In Java this is call `Enterprise Naming Context` or `ENC`, in short. The naming directory is something like an object store, the container registers references to its resources. Resources can be beans or contexts provided by an application. All that resources are registered in the `Naming Directory` which allows you the access them if needed.

### Register Resources
***

When the application server starts, it parses the `META-INF/classes` and `WEB-INF/classes` folders by default to find classes with supported annotations. If a class is found, the class will be registered in the application servers naming directory under the name you specify in the annotations `name` Attribute, in following example `AStatelessSessionBean`. As the `name` attribute is optional, the bean will be registered in the naming directory with the short class name, if not specified.

When you want to inject that bean later, you have to know the name it has been registered with. In the following example, the bean will be registered in the naming directory under `php:global/example/AStatelessSessionBean`, whereas `example` is the name of the application. When using annotations to inject components, you don't have to know the fully qualified name, because the application server knows the actual context, tries to lookup the bean and injects it.

### Annotations
***




### Deployment Descriptor
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
          <injection-target-class>AppserverIo\Apps\Example\Services\SampleProcessor</injection-target-class>
          <injection-target-property>userProcessor</injection-target-property>
        </injection-target>
      </epb-ref>
      <res-ref>
        <description>Reference to the application</description>
        <res-ref-name>ApplicationInterface</res-ref-name>
        <injection-target>
          <injection-target-class>AppserverIo\Apps\Example\Services\AbstractProcessor</injection-target-class>
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
