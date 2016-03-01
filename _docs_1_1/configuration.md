---
layout: docs_1_1
title: Configuration
meta_title: appserver.io configuration
meta_description: appserver.io is a highly configurable php application server, giving you the flexibility you need. Therefore we provide a central and very powerful configuration file.
position: 150
group: Docs
subNav:
  - title: Basic Architecture
    href: basic-architecture
  - title: Container Configuration
    href: container-configuration
  - title: Server Configuration
    href: server-configuration
  - title: CRON
    href: cron
  - title: Application Configuration
    href: application-configuration
  - title: Module Configuration
    href: module-configuration
  - title: Configuration Defaults
    href: configuration-defaults
  - title: Optional Configuration
    href: optional-configuration
permalink: /get-started/documentation/1.1/configuration.html
---

We fancy the fact that we made appserver highly configurable. We've provided a centralized configuration file located at `/opt/appserver/etc/appserver/appserver.xml`.

This file contains the complete architecture as an XML structure.

To change used components, introduce new services or scale the system by adding additional servers, you can do so with some lines of XML.

## Basic Architecture

In this example, we use a shortened piece of the `appserver.xml` file to understand how the architecture is driven by configuration.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<appserver xmlns="http://www.appserver.io/appserver">

  <!-- define user, group and default umask applied when creating directories and files -->
  <params>
    <param name="user" type="string">_www</param>
    <param name="group" type="string">staff</param>
    <param name="umask" type="string">0002</param>
  </params>

  <containers>

    <!-- by default we combine all servers in one container -->
    <container name="combined-appserver" type="AppserverIo\Core\GenericContainer">
      <description>
        <![CDATA[
          This is an example of a webserver container
          that handles http requests in common way
        ]]>
      </description>
      <deployment type="AppserverIo\Appserver\Core\GenericDeployment" />
      <host
        name="localhost"
        appBase="/webapps"
        serverAdmin="info@appserver.io"
        serverSoftware="appserver/1.0.0.0 (darwin) PHP/5.5.21" />

        <!-- This is an example of how to configure upstream backends for proxy usage. -->
        <!--
        <upstreams>
            <upstream name="exampleBackend" type="\AppserverIo\WebServer\Upstreams\DefaultUpstream">
                <servers xmlns="">
                    <server name="local-apache" type="\AppserverIo\WebServer\Upstreams\Servers\DefaultServer">
                        <params xmlns="http://www.appserver.io/appserver">
                            <param name="address" type="string">127.0.0.1</param>
                            <param name="port" type="integer">80</param>
                            <param name="weight" type="integer">1</param>
                            <param name="maxFails" type="integer">10</param>
                            <param name="failTimeout" type="integer">30</param>
                            <param name="maxConns" type="integer">64</param>
                            <param name="backup" type="boolean">false</param>
                            <param name="down" type="boolean">false</param>
                            <param name="resolve" type="boolean">false</param>
                        </params>
                    </server>
                    <server name="local-nginx" type="\AppserverIo\WebServer\Upstreams\Servers\DefaultServer">
                        <params xmlns="http://www.appserver.io/appserver">
                            <param name="address" type="string">127.0.0.1</param>
                            <param name="port" type="integer">8080</param>
                            <param name="weight" type="integer">1</param>
                            <param name="maxFails" type="integer">10</param>
                            <param name="failTimeout" type="integer">30</param>
                            <param name="maxConns" type="integer">64</param>
                            <param name="backup" type="boolean">false</param>
                            <param name="down" type="boolean">false</param>
                            <param name="resolve" type="boolean">false</param>
                        </params>
                    </server>
                </servers>
            </upstream>
        </upstreams>
        -->

        <servers>

          <!-- this is the default configuration for the HTTP server -->
          <server
            name="http"
            type="\AppserverIo\Server\Servers\MultiThreadedServer"
            worker="\AppserverIo\Server\Workers\ThreadWorker"
            socket="\AppserverIo\Server\Sockets\StreamSocket"
            serverContext="\AppserverIo\Server\Contexts\ServerContext"
            requestContext="\AppserverIo\Server\Contexts\RequestContext"
            loggerName="System">

            <!-- define the parameters to configure the server instance -->
            <params>
              <param name="admin" type="string">info@appserver.io</param>
              <param name="software" type="string">
                appserver/1.0.0.0 (darwin) PHP/5.5.21
              </param>
                <param name="transport" type="string">tcp</param>
                <param name="address" type="string">127.0.0.1</param>
                <param name="port" type="integer">9080</param>
                <param name="workerNumber" type="integer">64</param>
                <param name="workerAcceptMin" type="integer">3</param>
                <param name="workerAcceptMax" type="integer">8</param>
                <param name="documentRoot" type="string">webapps</param>
                <param name="directoryIndex" type="string">
                    index.do index.php index.html index.htm
                </param>
                <param name="keepAliveMax" type="integer">64</param>
                <param name="keepAliveTimeout" type="integer">5</param>
                <param name="errorsPageTemplatePath" type="string">
                    var/www/errors/error.phtml
                </param>
            </params>

            <!-- An example how to modify response headers -->
            <!--
            <headers>
                <header type="response" name="Server" value="My Own Server" override="true"/>
                <header type="response" name="X-Powered-By" value="appserver"/>
            </headers>
            -->

            <!-- An example to activate a the auto index functionality on a custom directory -->
            <!--
            <locations>
                <location condition="^\/example\/META-INF\/.*">
                    <headers>
                        <header type="response" name="X-Powered-By" value="autoIndex" append="true"/>
                    </headers>
                    <params>
                        <param name="autoIndex" type="boolean">true</param>
                    </params>
                </location>
            </locations>
            -->

            <!-- An example how to activate the proxy module to use an upstream backend -->
            <!--
            <locations>
                <location condition="\/test\/.*">
                    <fileHandlers>
                        <fileHandler name="proxy" extension=".*">
                            <params>
                                <param name="transport" type="string">tcp</param>
                                <param name="upstream" type="string">exampleBackend</param>
                            </params>
                        </fileHandler>
                    </fileHandlers>
                </location>
            </locations>
            -->

            <!-- define the environment variables -->
            <environmentVariables>
              <environmentVariable
                condition="" definition="LOGGER_ACCESS=Access" />
            </environmentVariables>

            <!-- define the connection handler(s) -->
            <connectionHandlers>
              <connectionHandler
                type="\AppserverIo\WebServer\ConnectionHandlers\HttpConnectionHandler" />
            </connectionHandlers>

            <!-- define authentication basic/digest -->
            <authentications>
              <authentication uri="^\/admin.*">
                <params>
                  <param name="type" type="string">
                    \AppserverIo\WebServer\Authentication\BasicAuthentication
                  </param>
                  <param name="realm" type="string">
                    appserver.io Basic Authentication System
                  </param>
                  <param name="hash" type="string">
                    YXBwc2VydmVyOmFwcHNlcnZlci5pMA==
                  </param>
                </params>
              </authentication>
            </authentications>

            <!-- allow access to everything -->
            <accesses>
              <access type="allow">
                <params>
                  <param name="X_REQUEST_URI" type="string">.*</param>
                </params>
              </access>
            </accesses>

            <!-- define a virtual host -->
            <virtualHosts>
              <virtualHost name="example.local">
                <params>
                  <param name="admin" type="string">
                    admin@appserver.io
                  </param>
                  <param name="documentRoot" type="string">
                    webapps/example
                  </param>
                </params>
              </virtualHost>
            </virtualHosts>

            <!-- the webserver modules we want to load -->
            <modules>
              <!-- REQUEST_POST hook -->
              <module
                type="\AppserverIo\WebServer\Modules\VirtualHostModule"/>
              <module
                type="\AppserverIo\WebServer\Modules\AuthenticationModule"/>
              <module
                type="\AppserverIo\WebServer\Modules\EnvironmentVariableModule" />
              <module
                type="\AppserverIo\WebServer\Modules\RewriteModule"/>
              <module
                type="\AppserverIo\WebServer\Modules\DirectoryModule"/>
              <module
                type="\AppserverIo\WebServer\Modules\AccessModule"/>
              <module
                type="\AppserverIo\WebServer\Modules\CoreModule"/>
              <module
                type="\AppserverIo\WebServer\Modules\PhpModule"/>
              <module
                type="\AppserverIo\WebServer\Modules\FastCgiModule"/>
              <module
                type="\AppserverIo\WebServer\Modules\ProxyModule" />
              <module
                type="\AppserverIo\Appserver\ServletEngine\ServletEngine" />
              <!-- RESPONSE_PRE hook -->
              <module
                type="\AppserverIo\WebServer\Modules\HeaderModule" />
              <module
                type="\AppserverIo\WebServer\Modules\DeflateModule"/>
              <!-- RESPONSE_POST hook -->
              <module
                type="\AppserverIo\Appserver\Core\Modules\ProfileModule"/>
            </modules>

            <!-- bound the file extensions to the responsible module -->
            <fileHandlers>
              <fileHandler name="servlet" extension=".do" />
              <fileHandler name="fastcgi" extension=".php">
                <params>
                  <param name="host" type="string">127.0.0.1</param>
                  <param name="port" type="integer">9010</param>
                </params>
              </fileHandler>
            </fileHandlers>

        </server>

        <!-- Here, additional servers might be added -->

      </servers>
    </container>
  </containers>
</appserver>
```

In the above example, you can see three important components of the appserver architecture in use. The  [*container*](#container-configuration), [*server*](#server-configuration) and some [*modules*](#module-configuration). We are basically building up a container, which holds a server that uses different modules to process incoming HTTP (have a look at the `connectionHandler`) requests.

When looking at the configuration file of a current installation, it will become visible that certain structures are handled differently on a live system. The most obvious is the usage of the separation of different aspects of the configuration.

The `appserver.xml` configuration supports the [XInclude](http://en.wikipedia.org/wiki/XInclude) mechanism to allow for re-usability. The following example (which is actually used) shows how the virtual host configuration is separated into an extra file.

```xml
<!-- include of virtual host configurations -->
<xi:include href="conf.d/virtual-hosts.xml"/>
```

This makes virtual hosts re-usable within several servers with just one line within the XML configuration.

## Container Configuration

A *container* is created by using the `container` element within the `containers` collection
of the `appserver` document element. Two things make this element into a specific container
being built up by the system on startup:

* The `type` attribute states a class extending our `AbstractContainerThread` which makes a
  container into a certain kind of container.

* The `deployment` element states a class containing preparations for starting up the container.
  It can be considered a hook which will be invoked before the container will be available.

That is basically everything to create a new container. To make use of it, it has to contain at least one *server* within its `servers` collection.

## Server Configuration

The *servers* contained by our *container* can also be loosely drafted by the XML configuration and will be instantiated on container boot-up. To enable a *server* you have to mention three basic attributes of the element:

* The `type` specifies a class implementing the `ServerInterface` which implements the basic
  behavior of the server on receiving a connection and how it will handle it.
* The `socket` attribute specifies the type of socket the server should open. E.g. a stream or
  asynchronous socket
* The `serverContext` specifies the server's source of configuration and container for runtime
  information e.g. ServerVariables like `DOCUMENT_ROOT`

So we have the specific server, which will open a certain port and operate in a defined context. But, to make the server handle a certain type of requests, it needs to know which protocol to speak.

This can be done using the `connectionHandler` element. Certain server wrappers can handle certain protocols. Therefore, we can use the protocols, which a server wrapper, e.g. [`WebServer`]({{ "/get-started/documentation/webserver.html" | prepend: site.baseurl }}) supports in form of connection handlers. [WebServer](<https://github.com/appserver-io/webserver>) offers an `HttpConnectionHandler` class. By using it, the server is able to understand the HTTP protocol.

The server configuration makes heavy use of the `param` element, which is used to apply some of the most important configuration values to a server instance. An example of the params a server can take can be found in the example below.

```xml
<params>
    <param name="admin" type="string">info@appserver.io</param>
    <param name="software" type="string">
        appserver/1.0.0 (darwin) PHP/5.5.21
    </param>
    <param name="transport" type="string">tcp</param>
    <param name="address" type="string">127.0.0.1</param>
    <param name="port" type="integer">9080</param>
    <param name="workerNumber" type="integer">64</param>
    <param name="workerAcceptMin" type="integer">3</param>
    <param name="workerAcceptMax" type="integer">8</param>
    <!-- ... -->
</params>
```

Some of these params do speak for themselves, but others don't. You can find a complete list of their meaning 
below:

| Param name           | Type     | Description                                                    |
| ---------------------| ---------| ---------------------------------------------------------------|
| `admin`              | string   | The email address of the administrator who is responsible for the server. |
| `software`           | string   | The software signature, as shown in the response header for example. |
| `transport`          | string   | The transport layer. In ssl mode `ssl` will be used instead of plain `tcp`. |
| `address`            | string   | The address the server-socket should bind and listen to. If you want to allow only connection on local loopback define `127.0.0.1` as in the example above shown. This will be good enough for local development and testing purpose. If you want to allow connections to your external ethernet interfaces, just define `0.0.0.0` or if you want to allow connection only on a specific interface, just define the ip of your interface `192.168.1.100`. |
| `port`               | integer  | The port for the server-socket to accept connections to. This can be any [common port number](http://en.wikipedia.org/wiki/Port_%28computer_networking%29#Common_port_numbers). Make sure there is no other server installed blocking the default ports.|
| `workerNumber`       | integer  | Defines the number of worker-queues to be started waiting for requests to process. |
| `workerAcceptMin`    | integer  | Describes the minimum number of requests for the worker to be accepted to randomize its lifetime. |
| `workerAcceptMax`    | integer  | Describes the maximum number of requests for the worker to be accepted tor randomize its lifetime. |

All params listed above are common to servers using the `HttpConnectionHandler`.

> The param composition may vary depending on the server implementation.

Since version 1.1 you've the possibility to define multiple SSL certificates. Multiple certificates can be enabled on server level by adding a `<certificates/>` node containing a `<certificate/>` node for each certificate you want to add. For example, if you want to add a wildcard certificate for `appserver.local` and `appserver.dev`, the following configuration will be appropriate

```xml
<certificates>
    <certificate domain="*.appserver.local" certPath="etc/appserver/appserver-local.pem" />
    <certificate domain="*.appserver.dev" certPath="etc/appserver/appserver-dev.pem" />
</certificates>
```

The `<certificate/>` node has two attributes that has to be specified:

* The value of the `domain` attribute has to be the fully qualified domain name (FQDN)
* The value of the `certPath` attribute has to be the relative path to the certificate that should be bound, 
  assumed the base directory is the appserver's root directory

> Do not forget to restart the server after adding the certificates.

## System CRON

Since version 1.1 appserver.io also provides a real CRON implementation that can replace your system's CRON daemon. The jobs can be configured in a separate XML configuration file, located under `etc/appserver/conf.d/cron.xml`.

The following example shows the configuration for a simple CRON job that writes the application servers PHP version to the `var/log/php_errors.log` file every minute.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<cron xmlns="http://www.appserver.io/appserver">
    <jobs>
        <job name="test-02">
            <schedule>0 * * * * *</schedule>
            <execute directory="/opt/appserver" script="bin/php">
                <args>
                    <arg type="string">-v</arg>
                </args>
            </execute>
        </job>
    </jobs>
</cron>
```

The configuration of a job will need the `name` attribute.

* The `name` attribute has to contain a unique job name, as well as `<schedule/>` and `<execute/>` subnodes

The `<schedule/>` node's value must be a valid [CRON expression](https://en.wikipedia.org/wiki/Cron), whereas the `<execute/>` node has the two attributes `directory` and `script`.

* `directory` has to contain the working directory the job will be executed in
* `script` the name of the script or binary that has to be executed

Both values can contain an absolute or a relativ path. If the path is relative, the CRON job assumes that the root is the application server's base directory. Optionally, the `<execute/>` node can have a subnode `<args/>` that can have numerous `<arg/>` nodes containing the parameters that has to be passed to the script, when it'll be executed.

## Application Configuration

In addition to the Container and Server configurations, it is also possible to configure the applications. 

As each application is running in a separat thread, which is necessary to avoid the unavoidable `Can't redeclare class ...` errors.

### The META-INF and WEB-INF Directories

The application itself has provides many configuration options. To make things more comfortable, we provide a default configuration, that should fit most of the common requirements. These default options can be overwritten, but **NOT** removed, in the application specific configuration files that resides in the `META-INF` and `WEB-INF` directories. These directories are intended to be the default directories for the application specific configuration and it's classes.

### Configuration Variables

To make the application configuration as generic as possible, it is possible to use variables, that will be populated with the real values at runtime. This will help, to write an application specific configuration, that'll work system independent in nearly every appserver.io installation.

#### Available Variables

The following variables are available and can be used in most of the application specific configuration files

| Variable name             | Type     | Description                                                    |
| ------------------------- | ---------| ---------------------------------------------------------------|
| `base.dir`                | string   | The installation directory, `/opt/appserver` on Linux and Mac OS X. |
| `var.log.dir`             | string   | The directory containing the log files, defaults to `/opt/appserver/var/log`. |
| `etc.dir`                 | string   | The configuration base directory, defaults to `/opt/appserver/etc`. |
| `etc.appserver.dir`       | string   | The directory that contains the appserver.io main configuration file, defaults to `/opt/appserver/etc`. |
| `etc.appserver.confd.dir` | string   | Directory that contains additional appserver.io specific configuration files, defaults to `/opt/appserver/etc/appserver/conf.d`. |
| `tmp.dir`                 | string   | The temporary directory used by PHP and is configured in `php.ini` and `php-fpm-fcgi.ini` as `upload_tmp_dir`. |
| `webapps.dir`             | string   | Contains the absolute path to the container specific directory with the deployed web applications, defaults to `/opt/appserver/webapps`. |
| `host.appBase.dir`        | string   | Contains the path, relative to `base.dir` with the container specific directory with the deployed web applications, defaults to `webapps`. |
| `host.tmpBase.dir`        | string   | Contains the path, relative to `base.dir` with the container specific directory temporary directory, defaults to `webapps`. |
| `host.deployBase.dir`     | string   | Contains the path, relative to `base.dir` with the container specific deploy with the PHAR archives to be deployed, defaults to `webapps`. |
| `container.name`          | string   | The name of the container the application has been deployed in, defaults to `combined-appserver`. |
| `webapp.name`             | string   | The name of the deployed web application, defaults to the application directory, e. g. `example` for the example application |

As it some of the configuration are, by definition, system independent the variables can be used in the following configuration files

| Configuration filename       | Type     | Description                                                                       |
| ---------------------------- | ---------| --------------------------------------------------------------------------------- |
| `META-INF/cron.xml`          | string   | The application specific CRON configuration.                                      |
| `META-INF/*-ds.xml`          | string   | One or more datasources that will be part of an application.                      |
| `META-INF/context.xml`       | string   | The application's main configuration file.                                        |
| `META-INF/provision.xml`     | string   | Provisioning configuration for the application.                                   |
| `META-INF/containers.xml`    | string   | Override, extend or replace appserver.io's container and/or server configuration. |
| `META-INF/persistence.xml`   | string   | Configuration of the application's Doctrine entity manager(s).                    |
| `META-INF/message-queues.xml`| string   | The application's message queue configuration.                                    |

You can't use it in

| Configuration filename       | Type     | Description                                                                       |
| ---------------------------- | ---------| --------------------------------------------------------------------------------- |
| `META-INF/epb.xml`           | string   | The application's session and message bean configuration.                         |
| `META-INF/pointcuts.xml`     | string   | The AOP configuration of the application's session and message beans.             |
| `WEB-INF/web.xml`            | string   | The main web application configuration.                                           |
| `WEB-INF/pointcuts.xml`      | string   | The AOP configuration of the web appliation.                                      |

as these configuration files, are by definitiion, system independent and there is no need to use variables.

#### Using Variables

The variables can be referenced with the default properties file notation `${VARIABLE-NAME}`. A appserver.io independend virtual host configuration `META-INF/containers.xml` for your web application, that makes heavy usage of variables, would look like the following example.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<containers xmlns="http://www.appserver.io/appserver">
  <container name="${container.name}">
    <servers>
      <server name="http*">
        <virtualHosts>
          <virtualHost name="${webapp.name}.dev www.${webapp.name}.dev">
            <params>
              <param name="admin" type="string">info@appserver.io</param>
              <param name="documentRoot" type="string">${host.appBase.dir}/${webapp.name}</param>
            </params>
          </virtualHost>
        </virtualHosts>
      </server>
    </servers>
  </container>
</containers>
```

This example also uses the wildcard pattern `http*` for the server name, that will be described in chapter [Create/Override/Extends Server Configuration](#create-override-extend-server-configuration) later on.

### Context

Each application can have its own classloaders, loggers and managers. As mentioned before, each application, found a the container's webapp directory will be initialized with the defaults, defined in `etc/appserver/conf.d/context.xml`. This files  has the following content

```xml
<?xml version="1.0" encoding="UTF-8"?>
<context 
  name="globalBaseContext" 
  factory="AppserverIo\Appserver\Application\ApplicationFactory" 
  type="AppserverIo\Appserver\Application\Application" 
  xmlns="http://www.appserver.io/appserver">

  <classLoaders>
    <classLoader
      name="ComposerClassLoader"
      interface="ClassLoaderInterface"
      type="AppserverIo\Appserver\Core\ComposerClassLoader"
      factory="AppserverIo\Appserver\Core\ComposerClassLoaderFactory">
      <directories>
        <directory>/vendor</directory>
      </directories>
    </classLoader>
    <classLoader
      name="DgClassLoader"
      interface="ClassLoaderInterface"
      type="AppserverIo\Appserver\Core\DgClassLoader"
      factory="AppserverIo\Appserver\Core\DgClassLoaderFactory">
      <params>
        <param name="environment" type="string">production</param>
        <param name="enforcementLevel" type="integer">7</param>
        <param name="typeSafety" type="boolean">1</param>
        <param name="processing" type="string">logging</param>
      </params>
      <directories>
        <directory enforced="true">/common/classes</directory>
        <directory enforced="true">/WEB-INF/classes</directory>
        <directory enforced="true">/META-INF/classes</directory>
      </directories>
    </classLoader>
  </classLoaders>

  <loggers>
    <logger channelName="system" name="System" type="\AppserverIo\Logger\Logger">
      <handlers>
        <handler type="\AppserverIo\Logger\Handlers\CustomFileHandler">
          <formatter type="\AppserverIo\Logger\Formatters\StandardFormatter"/>
          <params>
            <param name="logFile" type="string">var/log/${webapp.name}-errors.log</param>
            <param name="logLevel" type="string">info</param>
          </params>
        </handler>
      </handlers>
    </logger>
    <logger channelName="access" name="Access" type="\AppserverIo\Logger\Logger">
      <handlers>
        <handler type="\AppserverIo\Logger\Handlers\CustomFileHandler">
          <formatter type="\AppserverIo\Logger\Formatters\StandardFormatter">
            <params>
              <param name="format" type="string">%4$s</param>
            </params>
          </formatter>
          <params>
            <param name="logFile" type="string">var/log/${webapp.name}-access.log</param>
            <param name="logLevel" type="string">info</param>
          </params>
        </handler>
      </handlers>
    </logger>
  </loggers>

  <managers>
    <manager name="ObjectManagerInterface" type="AppserverIo\Appserver\DependencyInjectionContainer\ObjectManager" factory="AppserverIo\Appserver\DependencyInjectionContainer\ObjectManagerFactory">
      <descriptors>
        <descriptor>AppserverIo\Description\ServletDescriptor</descriptor>
        <descriptor>AppserverIo\Description\MessageDrivenBeanDescriptor</descriptor>
        <descriptor>AppserverIo\Description\StatefulSessionBeanDescriptor</descriptor>
        <descriptor>AppserverIo\Description\SingletonSessionBeanDescriptor</descriptor>
        <descriptor>AppserverIo\Description\StatelessSessionBeanDescriptor</descriptor>
      </descriptors>
    </manager>
    <manager name="ProviderInterface" type="AppserverIo\Appserver\DependencyInjectionContainer\Provider" factory="AppserverIo\Appserver\DependencyInjectionContainer\ProviderFactory"/>
    <manager name="PersistenceContextInterface" type="AppserverIo\Appserver\PersistenceContainer\PersistenceManager" factory="AppserverIo\Appserver\PersistenceContainer\PersistenceManagerFactory"/>
    <manager name="BeanContextInterface" type="AppserverIo\Appserver\PersistenceContainer\BeanManager" factory="AppserverIo\Appserver\PersistenceContainer\BeanManagerFactory">
      <directories>
        <directory>/META-INF/classes</directory>
      </directories>
    </manager>
    <manager name="QueueContextInterface" type="AppserverIo\Appserver\MessageQueue\QueueManager" factory="AppserverIo\Appserver\MessageQueue\QueueManagerFactory">
      <params>
        <param name="maximumJobsToProcess" type="integer">200</param>
      </params>
    </manager>
    <manager name="ServletContextInterface" type="AppserverIo\Appserver\ServletEngine\ServletManager" factory="AppserverIo\Appserver\ServletEngine\ServletManagerFactory">
      <directories>
        <directory>/WEB-INF/classes</directory>
      </directories>
    </manager>
    <manager name="SessionManagerInterface" type="AppserverIo\Appserver\ServletEngine\StandardSessionManager" factory="AppserverIo\Appserver\ServletEngine\StandardSessionManagerFactory">
      <sessionHandlers>
        <sessionHandler name="filesystem" type="AppserverIo\Appserver\ServletEngine\Session\FilesystemSessionHandler" factory="AppserverIo\Appserver\ServletEngine\Session\SessionHandlerFactory"/>
      </sessionHandlers>
    </manager>
    <manager name="TimerServiceContextInterface" type="AppserverIo\Appserver\PersistenceContainer\TimerServiceRegistry" factory="AppserverIo\Appserver\PersistenceContainer\TimerServiceRegistryFactory"/>
    <manager name="AuthenticationManagerInterface" type="AppserverIo\Appserver\ServletEngine\Security\StandardAuthenticationManager" factory="AppserverIo\Appserver\ServletEngine\Security\StandardAuthenticationManagerFactory">
      <authenticators>
        <authenticator name="Form" type="AppserverIo\Appserver\ServletEngine\Authenticator\FormAuthenticator" />
        <authenticator name="Basic" type="AppserverIo\Appserver\ServletEngine\Authenticator\BasicAuthenticator" />
        <authenticator name="Digest" type="AppserverIo\Appserver\ServletEngine\Authenticator\DigestAuthenticator" />
      </authenticators>
    </manager>
    <manager name="AspectManagerInterface" type="AppserverIo\Appserver\AspectContainer\AspectManager" factory="AppserverIo\Appserver\AspectContainer\AspectManagerFactory"/>
  </managers>

  <provisioners>
    <provisioner name="standard" factory="AppserverIo\Appserver\Provisioning\StandardProvisionerFactory" type="AppserverIo\Appserver\Provisioning\StandardProvisioner" />
  </provisioners>

</context>
```

If the application does not make use of any of the defined classloaders, loggers or managers, or addtional managers are necessary, it is possible to define them in an application specific file, that has to be delivered with the application itself. This customized file has to be stored in `META-INF/context.xml`. When the application server starts, it will be parsed and the application will be initialized with the defined classloaders, loggers and managers.

> Please be aware, that the default classloaders, loggers and managers provides most of the functionality a web applications makes use of. They can **NOT** simply be removed by commenting them in the application's `META-INF/context.xml`, because the values in the template will be used instead. To remove them completely, they've also be commented in the template `etc/appserver/conf.d/context.xml`, which may, with a high probability, result in an unexpected behavior.

#### Classloaders

The default application configuration above defines two application sepcific classloaders. The first classloader is responsible to load the classes of the composer libraries, delivered in the application's vendor directory. The second one is responsible for the application specific classes that resides below the `common/classes`, `META-INF/classes` and `WEB-IN/classes` directories. These higher priorized classloader, namely `AppserverIo\Appserver\Core\DgClassLoader` provides additional functionality, like generating the class stubs, that are necessary for the [AOP]({{ "/get-started/documentation/aop.html" | prepend: site.baseurl }}) and [Design-by-Contract]({{ "/get-started/documentation/design-by-contract.html" | prepend: site.baseurl }}) functionality. So these classloader **MUST NOT** be removed or replaced.

#### Loggers

By default, an application comes with two registered loggers, an access and a system logger. The access logger can be used to have a separate access log for an application, the system logger to have an application specific log file for debugging purposes. The access logger is not used by default. To activate it, an environment variable has to be set in the application's virtual host configuration, which can be done in the `META-INF/containers.xml`, and will be described in the next chapter. The application's system logger can be used wherever an application instance is available or by loading it from the [Naming Directory]({{ "/get-started/documentation/naming directory.html" | prepend: site.baseurl }}). For example in a servlet, the application's system logger can be accessed by

```php
<?php

namespace AppserverIo\Example\Servlets;

use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;

/**
 * This is the famous 'Hello World' as servlet implementation.
 *
 * @Route(name="helloWorld",
 *        urlPattern={"/helloWorld.do", "/helloWorld.do*"})
 */
class HelloWorldServlet extends HttpServlet
{

  /**
   * The application instance that provides the entity manager.
   *
   * @var \AppserverIo\Psr\Application\ApplicationInterface
   * @Resource(name="ApplicationInterface")
   */
  protected $application;

  /**
   * Handles a HTTP GET request.
   *
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest
   *   The request instance
   * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse
   *   The response instance
   *
   * @return void
   * @see \AppserverIo\Psr\Servlet\Http\HttpServlet::doGet()
   */
  public function doGet(
    HttpServletRequestInterface $servletRequest,
    HttpServletResponseInterface $servletResponse)
  {
    // first log 'Hello World!' to the application's system logger
    $this->application->getLogger()->info($message = 'Hello World!');
    // then append it to the response body
    $servletResponse->appendBodyStream($message);
  }
}
```

#### Managers

The managers implements the main part of the functionality the appserver.io provides when writing applications.
  
The manager itself provides the functionality, in most cases, in combination with the infrastructure. For example, the Servlet, the Session and the Authentication Manager needs the Servlet Engine to work properly. The functionality of the Bean Manager, the Message Queue, the Timer Service and the Persistence Manager are strongly coupled with the Persistence Container instead.

For an overview of the possible configuration options for the managers, have a look at the `etc/appserver/conf.d/context.xml` template file. Below is a short description for the available managers and the configuration options for the most important one's.

##### Object Manager

Holds the object descriptions for the application's servlets and beans. The object descriptions are necessary for object creation and dependency injection. The object manager also allows the configuration of additional descriptor implementations, e. g. if someone what's to implement a new framework and appserver.io needs knowledge about it's classes for DI purposes.

For example, the Rout.Lt framework add's another descriptor `AppserverIo\Routlt\Description\PathDescriptor` to the Object Manager by extending the configuration with

```xml
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
        <descriptor>AppserverIo\Routlt\Description\PathDescriptor</descriptor>
  </descriptors>
</manager>
```

The example above is copied from the [example](<https://github.com/appserver-io-apps/example>) application package.

##### Dependency Injection Provider

Handles the dependency injection for servlets and beans and needs the Object Manager therefore.

##### Bean Manager

Provides configuration, initialization and lookup functionality for Session and Message Driven Beans.

##### Timer Service

Allows the scheduled execution of methods on Singleton and Stateless Session Beans as well as Message Driven Beans.
  
##### Persistence Manager

The Persistence Manager handles the information about the application's Doctrine Entity Manager instances.

##### Message Queue Manager

Handles the Message Queues provided by the application.

##### Servlet Manager

Provides configuration, initialization and lookup functionality for Servlets.

##### Session Manager

Handles servlet session configuration and persistence. The session manager configuration allows several session handlers that are responsible to persist the user sessions to a persistence layer implementation.

##### Authentication Manager

Handles servlet authentication and authorization. The authentication manager initializes the authenticators and maps them to the incoming requests, to autenthicate it against the login modules configured for the security domain.

The following conifiguration is an example configuration and shows, how the autentication manager can be configured to use the `DatabasePDOLoginModule` to authenticate incoming requests against a database that has to be defined by a datasource named `appserver.io-example-application`.

```xml
<manager 
  name="AuthenticationManagerInterface" 
  type="AppserverIo\Appserver\ServletEngine\Security\StandardAuthenticationManager" 
  factory="AppserverIo\Appserver\ServletEngine\Security\StandardAuthenticationManagerFactory">
  <securityDomains>
    <securityDomain name="example-realm">
      <authConfig>
        <loginModules>
          <loginModule type="AppserverIo\Appserver\ServletEngine\Security\Auth\Spi\DatabasePDOLoginModule" flag="required">
            <params>
              <param name="lookupName" type="string">php:env/${container.name}/ds/appserver.io-example-application</param>
              <param name="principalsQuery" type="string">select password from user where username = ?</param>
              <param name="rolesQuery" type="string">select r.name, 'Roles' from role r inner join user p on r.userIdFk = p.userId where p.username = ?</param>
              <param name="hashAlgorithm" type="string">SHA-512</param>
              <param name="hashEncoding" type="string">hex</param>
              <param name="password-stacking" type="string">useFirstPass</param>
            </params>
          </loginModule>
        </loginModules>
      </authConfig>
    </securityDomain>
  </securityDomains>
</manager>
```

The matching datasource, that has to be deployed either globally or by the application itself, could to look like the following example

```xml
<?xml version="1.0" encoding="UTF-8"?>
<datasources xmlns="http://www.appserver.io/appserver">
    <datasource name="appserver.io-example-application">
        <database>
            <driver>pdo_sqlite</driver>
            <path>META-INF/data/appserver_ApplicationServer.sqlite</path>
            <memory>false</memory>
        </database>
    </datasource>
</datasources>
```

and has also been copied from our [example](<https://github.com/appserver-io-apps/example>) application package.

#### Provisioner

An application can have it's own provisioning implementation. The provisioning can be configured in a configuration file `META-INF/provision.xml` and allows the definition of steps. Each step can define a type, which reflects a class name, that will be instanciated and executed during the application server's startup. Beside the steps, the configuration allows the specification of a datasource, which can be used within a step , e. g. to access the data.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<provision xmlns="http://www.appserver.io/appserver">
  <datasource name="appserver.io-example-application"/>
  <installation>
    <steps>
      <step type="AppserverIo\Apps\Example\Provisioning\PrepareDatabaseStep" />
    </steps>
  </installation>
</provision>
```

The provisioning process itself and each step will be executed in a separate thread, in a sychronous manner. The step `AppserverIo\Apps\Example\Provisioning\PrepareDatabaseStep`, which is part of our [example](<https://github.com/appserver-io-apps/example>) application uses a Stateless Session Bean to create an empty database as well as default credentials and products.

> Actual, the provisioning process has no mechanism to query whether or not the application state, e. g. if this is the first installation or an update. This functionality can depend for each application and therefore has to be implemented by application vendor itself.

### Create/Override/Extend Server Configuration

Since version 1.1 you have the possiblity to create a new server as well as override or extend parts of the existing server configuration, assumed you have activated that functionality. This functionality will be activated by default. If not, you can set the `param` with the name `allowApplicationConfiguration` 

```xml
<?xml version="1.0" encoding="UTF-8"?>
<appserver xmlns="http://www.appserver.io/appserver"  xmlns:xi="http://www.w3.org/2001/XInclude">
  <params>
    <param name="user" type="string">_www</param>
    <param name="group" type="string">staff</param>
    <param name="umask" type="string">0002</param>
    <param name="allowApplicationConfiguration" type="boolean">true</param>
  </params>
  ...
```

in `etc/appserver/appserver.xml` to `true`.

If that flag is activated, you can deliver a completely separate container configuration with servers, virtual hosts and all allowed configuration parameters. The configuration file has to be located in the `META-INF` directory of your application and named `containers.xml`.

For example, if you want to deliver your own virtual host configuration with your application, your configuration file would look like this.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<containers xmlns="http://www.appserver.io/appserver">
  <container name="${container.name}">
    <servers>
      <server name="http*">
        <virtualHosts>
          <virtualHost name="${webapp.name}.dev www.${webapp.name}.dev">
            <params>
              <param name="admin" type="string">info@appserver.io</param>
              <param name="documentRoot" type="string">${host.appBase.dir}/${webapp.name}</param>
            </params>
            <rewrites>
              <rewrite condition="-d{OR}-f{OR}-l" target="" flag="L" />
            </rewrites>
            <accesses>
              <access type="allow">
                <params>
                  <param name="X_REQUEST_URI" type="string">^.*</param>
                </params>
              </access>
            </accesses>
            <environmentVariables>
              <environmentVariable condition="" definition="LOGGER_ACCESS=${container.name}/${webapp.name}/Access" />
            </environmentVariables>
          </virtualHost>
        </virtualHosts>
      </server>
    </servers>
  </container>
</containers>
```

The example above also defines an environment variable, that activates an access log for the application. This results in a separate access log file that can be configured in the logger configuration of the application's `META-INF/context.xml` file. As the access logger will be looked up by using the Naming Directory, the name specified in the `LOGGER_ACCESS` environment variable has to be prefixed with the container and the web application name.

To avoid writing virtual host configurations twice, one for the `http` and one for the `https` server, also wildcards can be used. Instead of using `http` or `https` as server name, `http*` can be specified. When the configuration file will be parsed on the application server's startup, the PHP `fnmatch()` method will be used to resolve the matching servers and apply the virtual host configuration to them.

> In order to extend or override an existing configuration, it is necessary either to use the same names of the container or server you wish to extend, variables or wildcards (for container/server names only). If you do not use the same or matching names, you will end up creating a **NEW**  container or server, which is probably not the outcome you expected. To find the container or server names, please refer to the application server's default configuration in `etc/appserver/appserver.xml`.

### Application Specific CRON

Beside an application specific container and server configuration, it is also possible to deliver an application specific CRON configuration. The application specific CRON configuration has the same file structure as the global one and has to be located in `META-INF/cron.xml`. As well as in the other application specific configuration files, the usage of variables is supported and strongly recommended to make the configuration system independent.

The following example shows a valid Magento 2 CRON configuration

```xml
<?xml version="1.0" encoding="UTF-8"?>
<cron xmlns="http://www.appserver.io/appserver">
  <jobs>
    <job name="${webapp.name}-default-cron">
      <schedule>* * * * * *</schedule>
      <execute directory="${webapp.dir}" script="${base.dir}/bin/php">
        <args>
          <arg type="string">bin/magento</arg>
          <arg type="string">cron:run</arg>
        </args>
      </execute>
    </job>
    <job name="${webapp.name}-setup-cron">
      <schedule>* * * * * *</schedule>
      <execute directory="${webapp.dir}" script="${base.dir}/bin/php">
        <args>
          <arg type="string">bin/magento</arg>
          <arg type="string">setup:cron:run</arg>
        </args>
      </execute>
    </job>
  </jobs>
</cron>
```

For more information about the CRON configuration options, have a look at the [System CRON](#system-cron) chapter above.

## Module Configuration

The web server comes with a package of default modules. The functionality that allows you to configure a virtual host or environment variables, for example, is also provided by two of, probably the most, important modules.

### Rewrite Module

This module can be used according to the `\AppserverIo\WebServer\Interfaces\HttpModuleInterface` interface. It needs an initial call of the `init` method and will process any request offered to the `process` method. The module is best used within the [`webserver`](<https://github.com/appserver-io/webserver>) project, as it offers all needed infrastructure.

#### Rules

One of the most important parts of the module is the way it can perform rewrites. All rewrites are based on rewrite rules, which consist of three important parts:

- *condition string* : Conditions to be met in order for the rule to take effect.
  See more [down here](#condition-syntax)

- *target string* : The target to rewrite the requested URI to. Within this string, you can use
  backreferences similar
  to the Apache mod_rewrite module with the difference that you have to use the `$ syntax`
  (instead of the `$/%/%{} syntax` of Apache).

  Matching rule conditions via regex are also part of available backreferences,
  as well as server and environment variables.

  *Simple example* : A condition like `(.+)@$X_REQUEST_URI` would produce a back reference `$1`
  with the value `/index` for a requested URI `/index`. The target string `$1/welcome.html` would,
  therefore, result in a rewrite to `/index/welcome.html`

- *flag string* : Use flags, similar to mod_rewrite, to make rules, which react in a
  certain way or influence further processing. Learn more [about flags below](#flags).

#### Condition Syntax

The Syntax of conditions is roughly based on the combination of Apache's RewriteCondition
and RewriteRule syntax.

To make use of such a combination, you can chain conditions together using the `{OR}` symbol for
OR-combined and the `{AND}` symbol for AND-combined conditions.

Please be aware that AND takes precedence over OR! Conditions can either be PCRE regex or certain fixed
expressions. So a condition string of `([A-Z]+\.txt){OR}^/([0-9]+){AND}-f` would match only real files
(through `-f`), which either begins with numbers or end with capital letters and the extension .txt.

As you might have noticed: Backslashes do **not have to be escaped**.

You might also be curious about the `-f` condition. This is a direct copy of Apaches -f RewriteCondition.
We also support several other expressions of regex based conditions which are:

 - *<<COMPARE_STRING>* : Is the operand lexically preceding `<COMPARE_STRING>`?
 - *><COMPARE_STRING>* : Is the operand lexically following `<COMPARE_STRING>`?
 - *=<COMPARE_STRING>* : Is the operand lexically equal to `<COMPARE_STRING>`?
 - *-d* : Is the operand a directory?
 - *-f* : Is the operand a real file?
 - *-s* : Is the operand a real file of a size greater than 0?
 - *-l* : Is the operand a symbolic link?
 - *-x* : Is the operand an executable file?

If you are wondering what the `operand` might be: it is **whatever you want it to be**! You can specify
any operand you'd like using the `@` symbol. All conditions of a rule will use the next operand to
their right, and if no operand is given, the module will simply use the requested URI. For example:

- *`([A-Z]+\.txt){OR}^/([0-9]+)`* Will take the requested URI for both conditions (note the `{OR}` symbol)
- *`([A-Z]+\.txt){OR}^/([0-9]+)@$DOCUMENT_ROOT`* Will test both conditions against the document root
- *`([A-Z]+\.txt)@$DOCUMENT_ROOT{OR}^/([0-9]+)`* Will only test the first one against the document root
  and the second against the requested URI

You might have noted the `$` symbol before `DOCUMENT_ROOT` and remembered it from the backreference
syntax. That is because all Apache common server vars can be explicitly used as backreferences too!

This doesn't work for you? Need the exact opposite? No problem!

All conditions, regex or expression based, can be negated using the `!` symbol in front of
them! So `!^([0-9]+)` would match all strings which do NOT begin with a number and `!-d` would match
all non-directories.

#### Flags

Flags are used to further influence processing. You can specify as many flags per rewrite as you'd like,
but be aware of their impact! Syntax for several flags is simple: just separate them with a `,` symbol.
Flags, which might accept a parameter, can be assigned one by using the `=` symbol. Currently supported
flags are:

- *L* : As rules are normally processed one after the other, the `L` flag will make the flagged rule
  the last one processed, if matched.

- *R* : If this flag is set, we will redirect the client to the URL specified in the `target string`.
   If this is just a URI, we will redirect to the same host. You might also specify a custom status
   code between 300 and 399, to indicate the reason for or the kind of the redirect. Default is `301` aka
   `permanent`

- *M* : Stands for map. Using this flag you can specify an external source (have a look at the Injector
  classes of the WebServer project) of a target map. With `M=<MY_BACKREFERENCE>` you can specify what
  the map's index has to match. This matching is done **only** if the rewrite condition matches and will
  behave as another condition.

### Virtual-Host Module

This module can be used according to the `\AppserverIo\WebServer\Interfaces\HttpModuleInterface`
interface. It needs an initial call of the `init` method and will process any request offered to
the `process` method. The module is best used within the [webserver](https://github.com/appserver-io/webserver)
project, as it offers all the needed infrastructure.

If you need to configure a virtual host, it should look like the
following example, which would enable a Magento installation under `http://magento.dev:9080`.

```xml
<virtualHosts>
  <virtualHost name="magento.dev">
    <params>
      <param name="admin" type="string">info@appserver.io</param>
      <param name="documentRoot" type="string">webapps/magento</param>
    </params>
    <rewrites>
      <rewrite condition="-d{OR}-f{OR}-l" target="" flag="L" />
      <rewrite condition="(.*)" target="index.php/$1" flag="L" />
    </rewrites>
    <accesses>
      <access type="allow">
        <params>
          <param name="X_REQUEST_URI" type="string">
            ^\/([^\/]+\/)?(media|skin|js|index\.php).*
          </param>
        </params>
      </access>
    </accesses>
  </virtualHost>
</virtualHosts>
```

## Configuration Defaults

You might be curious about the different ports we use. Per default the appserver will open several
ports where its services are available. As we do not want to block (or be blocked by) other
services we use ports of a higher range.

As a default we use the following ports:

* WebContainer

    - HTTP Server: `9080`
    - HTTPS Server: `9443`

* Persistence-MQ-Container

    - Persistence-Container: `8585`
    - Message-Queue: `8587`

You can change this default port mapping by using the [server configuration](#server-configuration).

## Optional Configuration

Simplicity has always been in our main focus. Therefore we do provide several configuration defaults, which are not even shown in the configuration file,
as their default setup works very well out of the box.
You might change these values and we do not want to stand in your way.
So following are some configurable components, which are already configured implicitly, but can be explicitly set up in the configuration files.

### Extractors

Extractors are used to process any form of archive in an ETL like manner.
The example shown below is used to un-pack webapps, which are provided as `phar` archives upon deployment.

```xml
<extractors>
  <extractor name="phar" type="AppserverIo\Appserver\Core\Extractors\PharExtractor" createBackups="false" restoreBackups="false" />
</extractors>
```

### Initial Context

The initial context is the configurational heart of the running appserver instance and manages instance creation at a low level.
If really needed for any custom core functionality, you are able to change its composition here.

```xml
<initialContext type="AppserverIo\Appserver\Core\InitialContext">
  <description><![CDATA[The initial context configuration.]]></description>
  <classLoader name="default" type="AppserverIo\Appserver\Core\SplClassLoader" />
  <storage type="AppserverIo\Storage\StackableStorage" />
</initialContext>
```

### Loggers

Loggers can be used to log specific types of messages in a specific way. This is highly configurable and by default there are three loggers:

- system logger -> appserver-errors.log
- access logger -> appserver-access.log
- error logger -> php_errors.log

Loggers are configured within the `appserver` node:

```xml
<loggers>
    <logger channelName="system" name="System" type="\AppserverIo\Logger\Logger">
        <handlers>
            <handler type="\AppserverIo\Logger\Handlers\CustomFileHandler">
                <formatter type="\AppserverIo\Logger\Formatters\StandardFormatter"/>
                <params>
                    <param name="logFile" type="string">var/log/appserver-errors.log</param>
                    <param name="logLevel" type="string">info</param>
                </params>
            </handler>
        </handlers>
    </logger>
    <logger channelName="access" name="Access" type="\AppserverIo\Logger\Logger">
        <handlers>
            <handler type="\AppserverIo\Logger\Handlers\CustomFileHandler">
                <formatter type="\AppserverIo\Logger\Formatters\StandardFormatter">
                    <params>
                        <param name="format" type="string">%4$s</param>
                    </params>
                </formatter>
                <params>
                    <param name="logFile" type="string">var/log/appserver-access.log</param>
                    <param name="logLevel" type="string">info</param>
                </params>
            </handler>
        </handlers>
    </logger>
    <logger channelName="profile" name="Profile" type="\AppserverIo\Logger\Logger">
        <processors>
            <processor type="\AppserverIo\Logger\Processors\MemoryProcessor"/>
            <processor type="\AppserverIo\Logger\Processors\SysloadProcessor"/>
            <processor type="\AppserverIo\Logger\Processors\ThreadContextProcessor"/>
        </processors>
        <handlers>
            <handler type="\AppserverIo\Logger\Handlers\LogstashHandler">
                <params>
                    <param name="host" type="string">127.0.0.1</param>
                    <param name="port" type="integer">9514</param>
                    <param name="logLevel" type="string">debug</param>
                </params>
            </handler>
            <handler type="\AppserverIo\Logger\Handlers\CustomFileHandler">
                <params>
                    <param name="logFile" type="string">var/log/appserver-profile.log</param>
                    <param name="logLevel" type="string">debug</param>
                </params>
            </handler>
        </handlers>
    </logger>
</loggers>
```

### Provisioners

Provisioners can be used to automatically setup webapps upon their deployment. You might integrate your own, using provided steps, or completely code new ones.
The shown example creates data sources configured within the application.

```xml
<provisioners>
  <provisioner name="datasource" type="AppserverIo\Appserver\Core\DatasourceProvisioner" />
  <provisioner name="standard" type="AppserverIo\Appserver\Core\StandardProvisioner" />
</provisioners>
```

### Scanners

Scanners are classes reacting to file system changes and can be configured within the `appserver` node.
You might want to use this feature to tightly integrate a deployment scanner, like used in the `appserver-watcher` process using the first example configuration below, or
add a scanner to restart the appserver upon changes to your webapp's code, as in the second example.
Implementing your own scanners is possible as well.

```xml
<scanners>
  <scanner 
    name="deployment" 
    type="AppserverIo\Appserver\Core\Scanner\DeploymentScanner"
    factory="AppserverIo\Appserver\Core\Scanner\DirectoryScannerFactory">
    <params>
      <param name="interval" type="integer">1</param>
      <param name="extensionsToWatch" type="string">dodeploy, deployed</param>
    </params>
    <directories>
      <directory>deploy</directory>
    </directories>
  </scanner>
  <scanner 
    name="webapps" 
    type="AppserverIo\Appserver\Core\Scanner\RecursiveDirectoryScanner"
    factory="AppserverIo\Appserver\Core\Scanner\DirectoryScannerFactory">
    <params>
       <param name="interval" type="integer">1</param>
       <param name="extensionsToWatch" type="string">php</param>
    </params>
    <directories>
      <directory>webapps</directory>
    </directories>
  </scanner>
  <scanner 
    name="logrotate" 
    type="AppserverIo\Appserver\Core\Scanner\LogrotateScanner"
    factory="AppserverIo\Appserver\Core\Scanner\StandardScannerFactory">
    <params>
      <param name="interval" type="integer">1</param>
      <param name="extensionsToWatch" type="string">log</param>
      <param name="maxFiles" type="integer">10</param>
      <param name="maxSize" type="integer">1048576</param>
    </params>
    <directories>
      <directory>var/log</directory>
    </directories>
  </scanner>
</scanners>
```

### Persistence-Container (Remote)

The [Persistence-Container](<{{ "/get-started/documentation/persistence-container.html" | prepend: site.baseurl }}>) can also be used remotely. This allows you to distribute the components of your application across a network. Therefore, you need to configure a dedicated server thread for the Persistence-Container, which allows it connect over a streaming socket.

```xml
<server
  name="persistence-container"
  type="\AppserverIo\Server\Servers\MultiThreadedServer"
  worker="\AppserverIo\Server\Workers\ThreadWorker"
  socket="\AppserverIo\Server\Sockets\StreamSocket"
  requestContext="\AppserverIo\Server\Contexts\RequestContext"
  serverContext="\AppserverIo\Server\Contexts\ServerContext"
  loggerName="System">

  <params>
    <param name="admin" type="string">info@appserver.io</param>
    <param name="transport" type="string">tcp</param>
    <param name="address" type="string">127.0.0.1</param>
    <param name="port" type="integer">8585</param>
    <param name="workerNumber" type="integer">8</param>
    <param name="workerAcceptMin" type="integer">3</param>
    <param name="workerAcceptMax" type="integer">8</param>
    <param name="documentRoot" type="string">webapps</param>
    <param name="directoryIndex" type="string">index.pc</param>
    <param name="keepAliveMax" type="integer">64</param>
    <param name="keepAliveTimeout" type="integer">5</param>
    <param name="errorsPageTemplatePath" type="string">var/www/errors/error.phtml</param>
  </params>

  <environmentVariables>
    <environmentVariable condition="" definition="LOGGER_ACCESS=Access" />
  </environmentVariables>

  <connectionHandlers>
    <connectionHandler type="\AppserverIo\WebServer\ConnectionHandlers\HttpConnectionHandler" />
  </connectionHandlers>

  <accesses>
    <!-- per default allow everything -->
    <access type="allow">
      <params>
        <param name="X_REQUEST_URI" type="string">.*</param>
      </params>
    </access>
  </accesses>

  <!-- include of virtual host configurations -->
  <xi:include href="conf.d/virtual-hosts.xml"/>

  <modules>
    <!-- REQUEST_POST hook -->
    <module type="\AppserverIo\WebServer\Modules\AuthenticationModule"/>
    <module type="\AppserverIo\WebServer\Modules\VirtualHostModule"/>
    <module type="\AppserverIo\WebServer\Modules\EnvironmentVariableModule" />
    <module type="\AppserverIo\WebServer\Modules\RewriteModule"/>
    <module type="\AppserverIo\WebServer\Modules\DirectoryModule"/>
    <module type="\AppserverIo\WebServer\Modules\AccessModule"/>
    <module type="\AppserverIo\WebServer\Modules\CoreModule"/>
    <module type="\AppserverIo\Appserver\PersistenceContainer\PersistenceContainerModule" />
    <!-- RESPONSE_PRE hook -->
    <module type="\AppserverIo\WebServer\Modules\DeflateModule"/>
    <!-- RESPONSE_POST hook -->
    <module type="\AppserverIo\Appserver\Core\Modules\ProfileModule"/>
  </modules>

  <fileHandlers>
    <fileHandler name="persistence-container" extension=".pc" />
  </fileHandlers>

</server>
```
