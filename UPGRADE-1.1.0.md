# Upgrade from 1.0.x to 1.1.0

## Provisioning

The provisioning functionality changed in a massive way. Up from 1.1.0-beta1, as result of closing [issue 760](https://github.com/appserver-io/appserver/issues/760), provisioning is part of application deployment and not of thecontainer start-up process. This results in the possiblity to use classes, that are part of your application, because the applications autoloader are now loaded prior provisioning.

In case, that your application is distributed with a `provision.xml`, either in the `WEB-INF` or `META-INF` folder, you have to change the `provision.xml` distributed with your application from

```xml
<?xml version="1.0" encoding="UTF-8"?>
<provision xmlns="http://www.appserver.io/appserver">
    <datasource name="appserver.io-example-application"/>
    <installation>
        <steps>
            <step type="AppserverIo\Appserver\Core\Provisioning\CreateDatabaseStep">
                <params>
                    <param name="pathToEntities" type="string">common/classes/AppserverIo/Apps/Example/Entities</param>
                </params>
            </step>
        </steps>
    </installation>
</provision>
```

to

```xml
<?xml version="1.0" encoding="UTF-8"?>
<provision xmlns="http://www.appserver.io/appserver">
    <datasource name="appserver.io-example-application"/>
    <installation>
        <steps>
            <step type="AppserverIo\Appserver\Provisioning\Steps\CreateDatabaseStep">
                <params>
                    <param name="pathToEntities" type="string">common/classes/AppserverIo/Apps/Example/Entities</param>
                </params>
            </step>
        </steps>
    </installation>
</provision>
```

Only change is the type declaration for the provisioning steps, that changed from `AppserverIo\Appserver\Core\Provisioning` to `AppserverIo\Appserver\Provisioning\Steps\CreateDatabaseStep`.