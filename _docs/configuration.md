---
layout: docs
title: Configuration
meta_title: appserver.io configuration
meta_description: appserver.io is highly configurable giving you the flexibility you need. Therefore we provide a central and very powerful configuration file.
position: 130
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
  - title: Configuration Defaults
    href: configuration-defaults
  - title: Optional Configuration
    href: optional-configuration
permalink: /get-started/documentation/configuration.html
---

We fancy that appserver should be highly configurable, so that anyone who is interested can give it a shot. Therefore we provide a central configuration file located at `/opt/appserver/etc/appserver.xml`.

This file contains the complete [architecture](#the-architecture) as an XML structure.

To change used components, introduce new services or scale the system by adding additional servers you can do so with some lines of XML. Look at a basic 
`appserver.xml`.

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
        serverSoftware="appserver/1.0.0-beta (mac) PHP/5.5.16" />

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
                appserver/1.0.0.0 (darwin) PHP/5.5.16
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
                    /opt/appserver/webapps/example
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
                type="\AppserverIo\Appserver\ServletEngine\ServletEngine" />
              <!-- RESPONSE_PRE hook -->
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

In the above example, you can see three important components of the appserver architecture being 
used. The [*container*](#container-configuration), [*server*](#server-configuration) and a
[*protocol*](docs/docs/architecture.md#protocol>) (if you have not read our [*basic architecture*](#basic-architecture)
you should do so now). We are basically building up a container which holds a server using the WebSocket 
protocol to handle incoming requests.

## Container Configuration

A *container* is created by using the `container` element within the `containers` collection 
of the `appserver` document element. Two things make this element in a specific container 
being built up by the system on startup:

* The `type` attribute states a class extending our `AbstractContainerThread` which makes a 
  container into a certain kind of container.

* The `deployment` element states a class containing preparations for starting up the container. 
  It can be considered a hook which will be invoked before the container will be available.

That is basically everything to create a new container. To make use of it, it has to contain at least one *server* within its `servers` collection.

## Server Configuration

The *servers* contained by our *container* can also be loosely drafted by the XML configuration and 
will be instantiated on container bootup. To enable a *server* you have to mention three basic 
attributes of the element:

* The `type` specifies a class implementing the `ServerInterface` which implements the basic 
  behavior of the server on receiving a connection and how it will handle it.
* The `socket` attribute specifies the type of socket the server should open. E.g. a stream or 
  asynchronous socket
* The `serverContext` specifies the servers source of configuration and container for runtime 
  information e.g. ServerVariables like `DOCUMENT_ROOT`

So we have the specific server which will open a certain port and operate in a defined context. But
to make the server handle a certain type of requests it needs to know which *protocol* to speak.

This can be done using the `connectionHandler` element. Certain server wrappers can handle certain
protocols. Therefor we can use the protocols which a server wrapper, e.g. `WebServer` supports in 
form of connection handlers. [WebServer](<https://github.com/appserver-io/webserver>)
offers a `HttpConnectionHandler` class. By using it, the server is able to understand the HTTP 
protocol.

## Application Configuration

Beside Container and Server, it is also possible to configure the Application. Each Application
can have its own autoloaders and managers. By default, each Application found in the application
servers webapp directory `/opt/appserver/webapps` will be initialized with the defaults, defined
in `/opt/appserver/etc/appserver/conf.d/context.xml`

```xml
<?xml version="1.0" encoding="UTF-8"?>
<context 
  type="AppserverIo\Appserver\Application\Application">

  <classLoaders>

    <!-- necessary to load files from the vendor directory of your application -->
    <classLoader
      name="composer"
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
      name="doppelgaenger"
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
      beanInterface="BeanContext"
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
      beanInterface="QueueContext"
      type="AppserverIo\Appserver\MessageQueue\QueueManager"
      factory="AppserverIo\Appserver\MessageQueue\QueueManagerFactory"/>

    <!-- provides the functionality to define Servlets handling HTTP request -->
    <manager 
      name="ServletManager"
      beanInterface="ServletContext"
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
      beanInterface="SessionManager"
      type="AppserverIo\Appserver\ServletEngine\StandardSessionManager"
      factory="AppserverIo\Appserver\ServletEngine\StandardSessionManagerFactory"/>

    <!-- provides functionality to handle Timers -->
    <manager 
      name="TimerServiceRegistry"
      beanInterface="TimerServiceContext"
      type="AppserverIo\Appserver\PersistenceContainer\TimerServiceRegistry"
      factory="AppserverIo\Appserver\PersistenceContainer\TimerServiceRegistryFactory"/>

    <!-- provides functionality to handle HTTP basic/digest authentication -->
    <manager 
      name="StandardAuthenticationManager"
      beanInterface="AuthenticationManager"
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
application. Your own, customized file, has to be stored in `META-INF/context.xml`. When the application
server starts, this file will be parsed and your application will be initialized with the defined class loaders
and managers.

> Please be aware, that the default class loaders and managers provide most of the functionality
> described above. If you remove them from the `context.xml` you have to anticipate unexpected behavior.

## Module Configuration

The web server comes with a package of default modules. The functionality that allows to configure
a virtual host or environment variables, for example, is also provided by two, maybe the most important,
modules.

### Rewrite Module

The module can be used according to the `\AppserverIo\WebServer\Interfaces\HttpModuleInterface` interface.
It needs an initial call of the `init` method and will process any request offered to the `process` method.
The module is best used within the [`webserver`](<https://github.com/appserver-io/webserver>)
project as it offers all needed infrastructure.

#### Rules

Most important part of the module is the way it can perform rewrites. All rewrites are based on rewrite rules which consist of three important parts:

- *condition string* : Conditions to be met in order for the rule to take effect. 
  See more [down here](#condition-syntax)

- *target string* : The target to rewrite the requested URI to. Within this string, you can use 
  backreferences similar
  to the Apache mod_rewrite module with the difference that you have to use the `$ syntax`
  (instead of the `$/%/%{} syntax` of Apache).
  
  Matching rule conditions to pick out specifically via regex are also part of available backreferences
  as well as server and environment variables.

  *Simple example* : A condition like `(.+)@$X_REQUEST_URI` would produce a back reference `$1` 
  with the value `/index` for a requested URI `/index`. The target string `$1/welcome.html` would
  therefore, result in a rewrite to `/index/welcome.html`

- *flag string* : Use flags similar to mod_rewrite which are used to make rules react in a 
  certain way or influence further processing. See more [down here](#flags)

#### Condition Syntax

The Syntax of possible conditions is roughly based on the possibilities of Apache's RewriteCondition 
and RewriteRule combined.

To make use of such a combination, you can chain conditions together using the `{OR}` symbol for 
OR-combined and the `{AND}` symbol for AND-combined conditions.

Please be aware that AND takes precedence over OR! Conditions can either be PCRE regex or certain fixed 
expressions. So a condition string of `([A-Z]+\.txt){OR}^/([0-9]+){AND}-f` would match only real files 
(through `-f`) which either begins with numbers or end with capital letters and the extension .txt.

As you might have noticed: Backslashes do **not have to be escaped**.

You might also be curious about the `-f` condition. This is a direct copy of Apaches -f RewriteCondition.
We also support several other expressions to regex based conditions which are:

 - *<<COMPARE_STRING>* : Is the operand lexically preceding `<COMPARE_STRING>`?
 - *><COMPARE_STRING>* : Is the operand lexically following `<COMPARE_STRING>`?
 - *=<COMPARE_STRING>* : Is the operand lexically equal to `<COMPARE_STRING>`?
 - *-d* : Is the operand a directory?
 - *-f* : Is the operand a real file?
 - *-s* : Is the operand a real file of a size greater than 0?
 - *-l* : Is the operand a symbolic link?
 - *-x* : Is the operand an executable file?

If you are wondering what the `operand` might be: it is **whatever you want it to be**! You can specify
any operand you like using the `@` symbol. All conditions of a rule will use the next operand to 
their right and if none is given the requested URI. For example:

- *`([A-Z]+\.txt){OR}^/([0-9]+)`* Will take the requested URI for both conditions (note the `{OR}` symbol)
- *`([A-Z]+\.txt){OR}^/([0-9]+)@$DOCUMENT_ROOT`* Will test both conditions against the document root
- *`([A-Z]+\.txt)@$DOCUMENT_ROOT{OR}^/([0-9]+)`* Will only test the first one against the document root 
  and the second against the requested URI

You might have noted the `$` symbol before `DOCUMENT_ROOT` and remembered it from the backreference 
syntax. That is because all Apache common server vars can be explicitly used as backreferences too!

That does not work for you? Need the exact opposite? No problem!

All conditions, regex or expression based can be negated using the `!` symbol in front of 
them! So `!^([0-9]+)` would match all strings which do NOT begin with a number and `!-d` would match
all non-directories.

#### Flags

Flags are used to further influence processing. You can specify as many flags per rewrite as you like,
but be aware of their impact! Syntax for several flags is simple: just separate them with a `,` symbol.
Flags which might accept a parameter can be assigned one by using the `=` symbol. Currently supported
flags are:

- *L* : As rules are normally processed one after the other, the `L` flag will make the flagged rule 
  the last one processed if matched.

- *R* : If this flag is set we will redirect the client to the URL specified in the `target string`. 
   If this is just an URI we will redirect to the same host. You might also specify a custom status 
   code between 300 and 399 to indicate the reason for/kind of the redirect. Default is `301` aka 
   `permanent`

- *M* : Stands for map. Using this flag you can specify an external source (have a look at the Injector
  classes of the WebServer project) of a target map. With `M=<MY_BACKREFERENCE>` you can specify what 
  the map's index has to match. This matching is done **only** if the rewrite condition matches and will 
  behave as another condition

### Virtual-Host Module

The module can be used according to the `\AppserverIo\WebServer\Interfaces\HttpModuleInterface`
interface. It needs an initial call of the `init` method and will process any request offered to 
the `process` method. The module is best used within the [webserver](<https://github.com/appserver-io/webserver>)
project as it offers all needed infrastructure.

If you need to configure a virtual host, it should look like the 
following example, that would enable a Magento installation under `http://magento.dev:9080`.

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

You will see that we provide basic frontend implementations of services the appserver runtime
provides. If you want to use these services yourself you should have a look at the code of our 
apps and read about [app development](#deployment).

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

You can change this default port mapping by using the [configuration file](#the-architecture).
If you are interested in our naming, you can see our container->server pattern, you might want to 
have a deeper look into our [architecture](docs/docs/architecture.md)

## Optional Configuration

Simplicity has always been in our main focus. Therefore we do provide several [configuration defaults](#configuration-defaults) which are not even shown in the configuration file, 
as their default setup works very well out of the box.
You might change these values and we do not want to stand in your way.
So following are some configurable components which are already configured implicitly but can be explicitly set up in the configuration files.

### Extractors

Extractors are used to process any form of archive in an ETL like manner.
The example shown below is used to un-pack webapps which are provided as `phar` archives upon deployment. 

```xml
<extractors>
  <extractor name="phar" type="AppserverIo\Appserver\Core\Extractors\PharExtractor" createBackups="false" restoreBackups="false" />
</extractors>
```

### Initial Context

The initial context is the configurational heart of the running appserver instance and manages instance creation on a low level.
If really needed for any custom core functionality you are able to change its composition here.

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
You might want to use this feature to tightly integrate a deployment scanner like used in the `appserver-watcher` process using the first example configuration below, or
add a scanner to restart the appserver upon changes to your webapps code as in the second example.
Implementing your own scanners is possible as well.

```xml
<scanners>
  <scanner name="deployment" type="AppserverIo\Appserver\Core\Scanner\DeploymentScanner">
    <params>
      <param name="interval" type="integer">1</param>
      <param name="extensionsToWatch" type="string">dodeploy, deployed</param>
    </params>
    <directories>
      <directory>deploy</directory>
    </directories>
  </scanner>
  <scanner name="webapps" type="AppserverIo\Appserver\Core\Scanner\RecursiveDirectoryScanner">
    <params>
       <param name="interval" type="integer">1</param>
       <param name="extensionsToWatch" type="string">php</param>
    </params>
    <directories>
      <directory>webapps</directory>
    </directories>
  </scanner>
  <scanner name="logrotate" type="AppserverIo\Appserver\Core\Scanner\LogrotateScanner">
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
