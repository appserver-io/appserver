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
  - title: Application Configuration
    href: application-configuration
  - title: Module Configuration
    href: module-configuration
  - title: Configuration Defaults
    href: configuration-defaults
  - title: Optional Configuration
    href: optional-configuration
  - title: CRON
    href: cron
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

In the above example, you can see three important components of the appserver architecture in
use. The [*container*](#container-configuration), [*server*](#server-configuration) and some
[*modules*](#module-configuration). We are basically building up a container, which holds a server that uses different modules
to process incoming HTTP (have a look at the `connectionHandler`) requests.

When looking at the configuration file of a current installation, it will become visible that certain structures are handled differently on a live system.
The most obvious is the usage of the separation of different aspects of the configuration.

The `appserver.xml` configuration supports the [XInclude](http://en.wikipedia.org/wiki/XInclude) mechanism to allow for re-usability.
The following example (which is actually used) shows how the virtual host configuration is separated into an extra file.

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

The *servers* contained by our *container* can also be loosely drafted by the XML configuration and
will be instantiated on container boot-up. To enable a *server* you have to mention three basic
attributes of the element:

* The `type` specifies a class implementing the `ServerInterface` which implements the basic
  behavior of the server on receiving a connection and how it will handle it.
* The `socket` attribute specifies the type of socket the server should open. E.g. a stream or
  asynchronous socket
* The `serverContext` specifies the server's source of configuration and container for runtime
  information e.g. ServerVariables like `DOCUMENT_ROOT`

So we have the specific server, which will open a certain port and operate in a defined context. But,
to make the server handle a certain type of requests, it needs to know which protocol to speak.

This can be done using the `connectionHandler` element. Certain server wrappers can handle certain
protocols. Therefore, we can use the protocols, which a server wrapper, e.g. [`WebServer`]({{ "/get-started/documentation/webserver.html" | prepend: site.baseurl }}) supports in
form of connection handlers. [WebServer](<https://github.com/appserver-io/webserver>)
offers an `HttpConnectionHandler` class. By using it, the server is able to understand the HTTP
protocol.

The server configuration makes heavy use of the `param` element, which is used to apply some of the most important configuration values to a server instance.
An example of the params a server can take can be found in the example below.

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

Some of these params do speak for themselves, but others don't. You can find a complete list of their meaning below:

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
* The value of the `certPath` attribute has to be the relative path to the certificate that should be bound, assumed the base directory is the appserver's root directory

> Do not forget to restart the server after adding the certificates.

## Application Configuration

In addition to the Container and Server configurations, it is also possible to configure the applications. 

### Environment

As you might need an environment switch for your application, to handle things differently e.g. turn of authentication in development mode 
or use different database connections, you can specify as many environments as you like by following this naming convention:

`META-INF/context.production.xml` or `META-INF/context.development.xml` would cause your application to have 2 different environments 
`production` and `development` which you can use to switch configurations as you wish.

In your application code you can check which environment is currently active by injecting the application and checking its environment:

```php
/**
 * @var \AppserverIo\Appserver\Application\Application
 * @Resource(name="ApplicationInterface")
 */
protected $application;

public function doSomething()
{
    if ($this->application->getEnvironmentName() === 'production') {
        // do something different
    }
}
```  

To specify the variable, set it in a `build.properties` file which resides in your [application's root directory](webapps.md#structure):

```ini
appserver.webapp.environment = development
```

This will result in a preference for all XML configuration containing the `.development.xml` suffix over their non-suffixed counterpart.
If no suffixed file exists, the default file will be loaded instead.

### Context

Each application
can have its own autoloaders and managers. By default, each application found in the application
server's webapp directory `/opt/appserver/webapps` will be initialized with the defaults, defined
in `/opt/appserver/etc/appserver/conf.d/context.xml`

```xml
<?xml version="1.0" encoding="UTF-8"?>
<context
  type="AppserverIo\Appserver\Application\Application">

  <classLoaders>

    <!-- necessary to load files from the vendor directory of your application -->
    <classLoader
      name="ComposerClassLoader"
      interface="ClassLoaderInterface"
      type="AppserverIo\Appserver\Core\ComposerClassLoader"
      factory="AppserverIo\Appserver\Core\ComposerClassLoaderFactory">
      <directories>
        <directory>/vendor</directory>
      </directories>
    </classLoader>

    <!-- necessary to load files from WEB-INF/classes and META-INF/classes, also -->
    <!-- provides the functionality for Design-by-Contract and AOP               -->
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

  <managers>

    <!-- provides services necessary for DI -->
    <manager
      name="Provider"
      beanInterface="ProviderInterface"
      type="AppserverIo\Appserver\DependencyInjectionContainer\Provider"
      factory="AppserverIo\Appserver\DependencyInjectionContainer\ProviderFactory"/>

    <!-- provides the services necessary to handle Session- and MessageBeans -->
    <manager
      name="BeanManager"
      beanInterface="BeanContextInterface"
      type="AppserverIo\Appserver\PersistenceContainer\BeanManager"
      factory="AppserverIo\Appserver\PersistenceContainer\BeanManagerFactory">
      <!-- params>
        <param name="lifetime" type="integer">1440</param>
        <param name="garbageCollectionProbability" type="float">0.1</param>
      </params -->
    </manager>

    <!-- provides the functionality to define and run a Queue -->
    <manager
      name="QueueManager"
      beanInterface="QueueContextInterface"
      type="AppserverIo\Appserver\MessageQueue\QueueManager"
      factory="AppserverIo\Appserver\MessageQueue\QueueManagerFactory"/>

    <!-- provides the functionality to define Servlets handling HTTP request -->
    <manager
      name="ServletManager"
      beanInterface="ServletContextInterface"
      type="AppserverIo\Appserver\ServletEngine\ServletManager"
      factory="AppserverIo\Appserver\ServletEngine\ServletManagerFactory">
      <directories>
        <directory enforced="true">/WEB-INF/classes</directory>
        <directory enforced="true">/vendor/appserver-io/routlt/src</directory>
      </directories>
    </manager>

    <!-- provides functionality to handle HTTP sessions -->
    <manager
      name="StandardSessionManager"
      beanInterface="SessionManagerInterface"
      type="AppserverIo\Appserver\ServletEngine\StandardSessionManager"
      factory="AppserverIo\Appserver\ServletEngine\StandardSessionManagerFactory"/>

    <!-- provides functionality to handle Timers -->
    <manager
      name="TimerServiceRegistry"
      beanInterface="TimerServiceContextInterface"
      type="AppserverIo\Appserver\PersistenceContainer\TimerServiceRegistry"
      factory="AppserverIo\Appserver\PersistenceContainer\TimerServiceRegistryFactory"/>

    <!-- provides functionality to handle HTTP basic/digest authentication -->
    <manager
      name="StandardAuthenticationManager"
      beanInterface="AuthenticationManagerInterface"
      type="AppserverIo\Appserver\ServletEngine\Authentication\StandardAuthenticationManager"
      factory="AppserverIo\Appserver\ServletEngine\Authentication\StandardAuthenticationManagerFactory"/>

    <!-- provides functionality to preload Advices found in WEB-INF/classes or META-INF/classes -->
    <manager
      name="AspectManager"
      beanInterface="AspectManagerInterface"
      type="AppserverIo\Appserver\AspectContainer\AspectManager"
      factory="AppserverIo\Appserver\AspectContainer\AspectManagerFactory"/>

  </managers>

</context>
```

If your application does not use any of the defined class loaders or managers, or you want to implement
your own managers, you can define them in a `context.xml` file, that you have to deliver with your
application. Your own, customized file has to be stored in `META-INF/context.xml`. When the application
server starts, this file will be parsed and your application will be initialized with the defined class loaders
and managers.

> Please be aware, that the default class loaders and managers provide most of the functionality
> described above. If you remove them from the `context.xml` you have to anticipate unexpected behavior.

### Create/Override/Extend Server Configuration

Since version 1.1 you have the possibility to create a new server as well as override or extend parts of the 
existing server configuration, assumed you have activated that functionality. This functionality will be
activated by default. If not, you can set the `param` with the name `allowApplicationConfiguration` 

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

If that flag is activated, you can deliver a completely separate container configuration with servers, virtual hosts
and all allowed configuration parameters. The configuration file has to be located in the `META-INF` directory of
your application and named `containers.xml`.

For example, if you want to deliver your own virtual host configuration with your application, your configuration
file would look like this.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<containers xmlns="http://www.appserver.io/appserver">
    <container name="combined-appserver">
        <servers>
            <server name="http">
                <virtualHosts>
                    <virtualHost name="reports.local www.reports.local">
                        <params>
                            <param name="admin" type="string">info@appserver.io</param>
                            <param name="documentRoot" type="string">webapps/example</param>
                        </params>
                        <rewrites>
                            <rewrite condition="-d{OR}-f{OR}-l" target="" flag="L" />
                        </rewrites>
                        <accesses>
                            <access type="allow">
                                <params>
                                    <param name="X_REQUEST_URI" type="string">^.*
                                    </param>
                                </params>
                            </access>
                        </accesses>
                    </virtualHost>
                </virtualHosts>
            </server>
        </servers>
    </container>
</containers>
```

> In order to extend or override an existing configuration, it is necessary to use the sames names of the container or 
> server you wish to extend. If you do not use the same container or server names, you will end up creating a **NEW** 
> container or server, which is probably not the outcome you expected. To find the container or server names, please 
> refer to the application server's default configuration in `etc/appserver/appserver.xml`.

## Module Configuration

The web server comes with a package of default modules. The functionality that allows you to configure
a virtual host or environment variables, for example, is also provided by two of, probably the most, important
modules.

### Rewrite Module

This module can be used according to the `\AppserverIo\WebServer\Interfaces\HttpModuleInterface` interface.
It needs an initial call of the `init` method and will process any request offered to the `process` method.
The module is best used within the [`webserver`](<https://github.com/appserver-io/webserver>)
project, as it offers all needed infrastructure.

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

## CRON

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
