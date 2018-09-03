# Upgrade from 1.1.13 to 1.1.14

## Application Context

Up from version 1.1.14 we've strictly separated parsing classes and deployment descriptors into `common`, `META-INF` and `WEB-INF` directory.

The application configuration `WEB-INF/context.xml` has been extended to allow configuration of the descriptors as well as the parsers and 
directories to be parsed. This is possible for each manager. Manager configuration will **NOT** be merged, instead a manager configuration
in you appliction's `WEB-INF` directory will override the default one.

If, for example you project should support console commands, the object manager configuration has to be extended with the path to the 
`appserver-io/console` library that supports the necessary functionality

```xml
...
    <manager name="ObjectManagerInterface" type="AppserverIo\Appserver\DependencyInjectionContainer\ObjectManager" factory="AppserverIo\Appserver\DependencyInjectionContainer\ObjectManagerFactory">
        <objectDescription>
            <descriptors>
                <descriptor>AppserverIo\Description\PreferenceDescriptor</descriptor>
                <descriptor>AppserverIo\Description\BeanDescriptor</descriptor>
            </descriptors>
            <parsers>
                <parser name="directory" type="AppserverIo\Appserver\DependencyInjectionContainer\DirectoryParser" factory="AppserverIo\Appserver\DependencyInjectionContainer\ParserFactory">
                    <directories>
                        <directory>${webapp.dir}/common/classes</directory>
                        <directory>${webapp.dir}/WEB-INF/classes</directory>
                        <directory>${webapp.dir}/META-INF/classes</directory>
                    </directories>
                </parser>
                <parser name="deploymentDescriptor" type="AppserverIo\Appserver\DependencyInjectionContainer\DeploymentDescriptorParser" factory="AppserverIo\Appserver\DependencyInjectionContainer\ParserFactory" descriptorName="di">
                    <directories>
                        <directory>${webapp.dir}/common</directory>
                        <directory>${webapp.dir}/WEB-INF</directory>
                        <directory>${webapp.dir}/META-INF</directory>
                        <directory>${webapp.dir}/vendor/appserver-io/console/etc/appserver/conf.d</directory>
                    </directories>
                </parser>
            </parsers>
        </objectDescription>
    </manager>
...
```

> The configuration parses only files in it's default directory. For the object manager, this are `common`, `META-INF` and `WEB-INF`  The bean
> and message queue manager parses the `META-INF` directory whereas the servlet manager parses the `WEB-INF` direcotory for annotations and the 
> deployment descriptor.