# Upgrade from 1.0.0 to 1.0.1

## Configuration

Given you've a custom `META-INF/context.xml` file in your application, and your application makes
use of components like `Servlets` or `Beans`, you **MUST** update that file.

To update the file, the values for the <descriptor> nodes have to be changed from

```xml
...
<managers>
    <manager 
        name="ObjectManagerInterface" 
        type="AppserverIo\Appserver\DependencyInjectionContainer\ObjectManager" 
        factory="AppserverIo\Appserver\DependencyInjectionContainer\ObjectManagerFactory">
        <descriptors>
            <descriptor>AppserverIo\Appserver\DependencyInjectionContainer\Description\ServletDescriptor</descriptor>
            <descriptor>AppserverIo\Appserver\DependencyInjectionContainer\Description\SingletonSessionBeanDescriptor</descriptor>
            <descriptor>AppserverIo\Appserver\DependencyInjectionContainer\Description\StatefulSessionBeanDescriptor</descriptor>
            <descriptor>AppserverIo\Appserver\DependencyInjectionContainer\Description\StatelessSessionBeanDescriptor</descriptor>
            <descriptor>AppserverIo\Appserver\DependencyInjectionContainer\Description\MessageDrivenBeanDescriptor</descriptor>
        </descriptors>
    </manager>
    ...
</managers>
```

to the following values

```xml
<managers>
    <manager 
        name="ObjectManagerInterface" 
        type="AppserverIo\Appserver\DependencyInjectionContainer\ObjectManager" 
        factory="AppserverIo\Appserver\DependencyInjectionContainer\ObjectManagerFactory">
        <descriptors>
            <descriptor>AppserverIo\Description\ServletDescriptor</descriptor>
            <descriptor>AppserverIo\Description\MessageDrivenBeanDescriptor</descriptor>
            <descriptor>AppserverIo\Description\StatefulSessionBeanDescriptor</descriptor>
            <descriptor>AppserverIo\Description\SingletonSessionBeanDescriptor</descriptor>
            <descriptor>AppserverIo\Description\StatelessSessionBeanDescriptor</descriptor>
        </descriptors>
    </manager>
...
</managers>
```

This is necessary, because we moved the desriptors to a separate package `appserver-io/description`
to enable developers writing loose coupled frameworks.