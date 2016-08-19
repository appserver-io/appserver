# Upgrade from 1.1.2 to 1.1.3

## Configuration for Doctrine Entity Manager

### Auto Generation for Proxy Classes

So far, in context of appserver.io, running Doctrine in production mode requires manually proxy generation by using the doctrine commandline tool. Up with version 1.1.3, it is possible to configure auto generation for proxy classes, which makes thinks more easy to use. To enable proxy autogeneration, simple set the param `autoGenerateProxyClasses` in your application's `META-INF/persistence.xml` file.

Additionally it's possible to specify the namespace for the generated proxy classes. Therefor pass a valid namespace as value for the parameter `proxyNamespace`.

### Use variables for path concatenation

Up to version 1.1.3, the directories specified for the nodes `<metadataConfiguration/>` and the `<annotationRegistries/>` have been prefixed with the path to the webapp directory. This has been changed now, because it should be possible to specify a path outside the application's webapp directory to. To specify a relative path, use the configuration variables that are available since version [1.1.1](https://github.com/appserver-io/appserver/releases/tag/1.1.1).

### Cache Configuration

Another configuration option allows to specify a separate cache implementation for each available Doctrine cache type, which are

* the metadata cache
* the result cache
* the query cache

The cache can be specified by the apropriate cache factory, for example

```xml
`<queryCacheConfiguration factory="AppserverIo\Appserver\PersistenceContainer\Doctrine\CacheFactory\ApcCacheFactory"/>
```

if you want to use the APC cache to cache the queries. An extended example can be found below.

### Example Doctrine Configuration

The following example represents a fully configured Doctrine entity manager that uses the APC cache for all available caches. Additionally the Entity Manager runs in production mode and the automaticly generated proxy classes are stored in the application's temporary cache directory that will be cleand up, when the application server starts.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<persistence xmlns="http://www.appserver.io/appserver">

    <persistenceUnits>

        <persistenceUnit name="YourEntityManager"
            interface="Doctrine\ORM\EntityManagerInterface"
            type="Doctrine\ORM\EntityManager"
            factory="AppserverIo\Appserver\PersistenceContainer\Doctrine\EntityManagerFactory">

            <metadataConfiguration type="xml">
                <directories>
                    <directory>${webapp.dir}/META-INF/conf</directory>
                </directories>
                <params>
                    <param name="isDevMode" type="boolean">false</param>
                    <param name="autoGenerateProxyClasses" type="boolean">true</param>
                    <param name="useSimpleAnnotationReader" type="boolean">false</param>
                    <param name="proxyDir" type="string">${webapp.cache.dir}</param>
                    <param name="proxyNamespace" type="string">Importer\Csv</param>
                </params>
            </metadataConfiguration>

            <queryCacheConfiguration factory="AppserverIo\Appserver\PersistenceContainer\Doctrine\CacheFactory\ApcCacheFactory" />
            <resultCacheConfiguration factory="AppserverIo\Appserver\PersistenceContainer\Doctrine\CacheFactory\ApcCacheFactory" />
            <metadataCacheConfiguration factory="AppserverIo\Appserver\PersistenceContainer\Doctrine\CacheFactory\ApcCacheFactory" />

            <datasource name="csv-importer-application"/>

            <annotationRegistries>
                <annotationRegistry namespace="JMS\Serializer\Annotation">
                    <directories>
                        <directory>${webapp.dir}/vendor/jms/serializer/src</directory>
                    </directories>
                </annotationRegistry>
            </annotationRegistries>

        </persistenceUnit>

    </persistenceUnits>

</persistence>
```