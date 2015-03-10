# Upgrade from 1.0.0 to 1.0.1

## Development Mode

After an update, all files will be reset to the original ownership and rights. So if you've switched to [Development-Mode](http://appserver.io/get-started/documentation/basic-usage.html#setup-script), you need to run the setup script again to re-activate the development mode again.

To do so, open a console and type the following

```sh
sudo /opt/appserver/server.php -s dev
# Should return: Setup for mode 'dev' done successfully!
```

## Updating Mac OS X

When updating a Mac OS X installation, we actually do **NOT** take care about changes in your configuration files. Please **BACKUP** all customized files, especially `etc/appserver/appserver.xml` and `etc/appserver/conf.d/virtual-hosts.xml` to not loose your changes.

## Configuration

We've made minor changes in some configuration files. This changes **SHOULD NOT** affect you in most cases.

### Custom Directories in `etc/appserver/appserver.xml`

In version 1.0.0 we had only one `tmp` directory that points to `var/tmp`. As this directory has also been used to store temporary files of uploads for example, we decided to add a real `tmp` directory under `tmp` relative to appserver.io root directory. If you've changed the directories in `etc/appserver/appserver.xml` you've to customize the attribute names from

```xml
<appserver>
    <params>
        <!--
        <param name="log.dir" type="string">/var/log</param>
        <param name="run.dir" type="string">/var/run</param>
        <param name="tmp.dir" type="string">/var/tmp</param>
        <param name="deploy.dir" type="string">/deploy</param>
        <param name="webapps.dir" type="string">/webapps</param>
        <param name="conf.dir" type="string">/etc/appserver</param>
        <param name="confd.dir" type="string">/etc/appserver/conf.d</param>
        -->
        <param name="user" type="string"><?php echo Setup::getValue(SetupKeys::USER) ?></param>
        <param name="group" type="string"><?php echo Setup::getValue(SetupKeys::GROUP) ?></param>
        <param name="umask" type="string"><?php echo Setup::getValue(SetupKeys::UMASK) ?></param>
    </params>
    ...
</appserver>
```

to the following

```xml
<appserver>
    <params>
        <!-- this is NEW with 1.0.1
        <param name="tmp.dir" type="string">/tmp</param>
        -->
        <!-- this HAS been changed with 1.0.1
        <param name="var.log.dir" type="string">/var/log</param>
        <param name="var.run.dir" type="string">/var/run</param>
        <param name="var.tmp.dir" type="string">/var/tmp</param>
        -->
        <!-- this HAS NOT been changed
        <param name="deploy.dir" type="string">/deploy</param>
        <param name="webapps.dir" type="string">/webapps</param>
        -->
        <!-- this HAS been changed with 1.0.1
        <param name="etc.appserver.dir" type="string">/etc/appserver</param>
        <param name="etc.appserver.confd.dir" type="string">/etc/appserver/conf.d</param>
        -->
        <param name="user" type="string"><?php echo Setup::getValue(SetupKeys::USER) ?></param>
        <param name="group" type="string"><?php echo Setup::getValue(SetupKeys::GROUP) ?></param>
        <param name="umask" type="string"><?php echo Setup::getValue(SetupKeys::UMASK) ?></param>
    </params>
    ...
</appserver>
```

### Custom Directories for ObjectManager in `META-INF/context.xml`

Given you've a custom `META-INF/context.xml` file in your application, and your application makes use of components like `Servlets` or `Beans`, you **MUST** update that file.

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

This is necessary, because we moved the descriptors to a separate package `appserver-io/description`
to enable developers writing loose coupled frameworks.
