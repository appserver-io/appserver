# appserver.io, a PHP application server

[![Gitter](https://badges.gitter.im/Join Chat.svg)](https://gitter.im/appserver-io/appserver?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![Build Status](https://travis-ci.org/appserver-io/appserver.png)](https://travis-ci.org/appserver-io/appserver) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/appserver-io/appserver/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/appserver-io/appserver/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/appserver-io/appserver/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/appserver-io/appserver/?branch=master)

This is the main repository for the [appserver.io](http://www.appserver.io/) project.

The objective of the project is to develop a multi-threaded application server for PHP, written 
in PHP. Yes, pure PHP! You think we aren't serious? Maybe! But we think, in order to enable as 
many developers in our great community, this will be the one and only way. So with your help we 
hopefully establish a solution as the standard for enterprise applications in PHP environments.

# Table of Contents

**[Runtime Environment](#runtime-environment)**
**[Installation](#installation)** 
**[Uninstall](#uninstall)** 
**[Configuration](#configuration)** 
**[Basic Usage](#basic-usage)** 
**[HTTP Server](#webserver)** 
**[Servlet-Engine](#servlet-engine)** 
**[Persistence-Container](#persistence-container)** 
**[Message-Queue](#message-queue)** 
**[Deployment](#deployment)** 

# Runtime Environment

The runtime environment appserver.io is using is delivered by the package [runtime](<https://github.com/appserver-io-php/runtime>).
This package  provides the appserver runtime which is system independent and encloses a thread-safe
compiled PHP environment. Besides the most recent PHP 5.5.x version the package came with installed
extensions:

* [pthreads](http://github.com/appserver-io-php/pthreads)
* [appserver](https://github.com/appserver-io/php-ext-appserver) (contains some replacement functions
  which behave badly in a multithreaded environment)

Additionally the PECL extensions [XDebug](http://pecl.php.net/package/xdebug) and [ev](http://pecl.php.net/package/ev) 
are compiled as a shared modules. XDebug is necessary to render detailed code coverage reports when 
running unit and integration tests. ev will be used to integrate a timer service in one of the future
versions.

# Installation

Besides supporting several operating systems and their specific ways of installing software, we 
also support several ways of getting this software. So to get your appserver.io package you might
do any of the following:

* Download one of our [**releases**](<https://github.com/appserver-io/appserver/releases>) 
  right from this repository which provide tested install packages

* Grab any of our [**nightlies**](<http://builds.appserver.io/>) from our project page to get 
  bleeding edge install packages which still might have some bugs

* Build your own package using [ant](<http://ant.apache.org/>)! To do so clone the [runtime](<https://github.com/appserver-io-php/runtime>) 
  first. Then update at least the `os.family` and `os.distribution` build properties according to 
  your environment and build the appserver with the ant target appropriate for your installer 
  (e.g. `create-pkg` for Mac or `create-deb` for Debian based systems).

The package will install with these basic default characteristics:

* Install dir: `/opt/appserver`
* Autostart after installation, no autostart on reboot
* Reachable under pre-configured ports as described [here](#basic-usage)

For OS specific steps and characteristics see below for tested environments.

## Mac OS X

* Tested versions: 10.8.x +
* Ant build: 
    - `os.family` = mac 
    - target `create-pkg`

## Windows

* Tested versions: 7 +
* Ant build: 
    - `os.family` = win
    - target `WIN-create-jar`

As we deliver the Windows appserver as a .jar file, a installed Java Runtime Environment (or JDK 
that is) is a vital requirement for using it. If the JRE/JDK is not installed you have to do so 
first. You might get it from [Oracle's download page](<http://www.oracle.com/technetwork/java/javase/downloads/jre7-downloads-1880261.html>).
If this requirement is met you can start the installation by simply double-clicking the .jar archive.

## Debian

* Tested versions: Squeeze +
* Ant build: 
    - `os.family` = linux
    - `os.distribution` = debian 
    - target `create-deb`

If you're on a Debian system you might also try our .deb repository:

```
root@debian:~# echo "deb http://deb.appserver.io/ wheezy main" > /etc/apt/sources.list.d/appserver.list
root@debian:~# wget http://deb.appserver.io/appserver.gpg -O - | apt-key add -
root@debian:~# aptitude update
root@debian:~# aptitude install appserver
```

## Fedora

* Tested versions: 20
* Ant build: 
    - `os.family` = linux
    - `os.distribution` = fedora 
    - target `create-rpm`
    

## CentOS

* Tested versions: 6.5
* Ant build: 
    - `os.family` = linux
    - `os.distribution` = centos 
    - target `create-rpm`

Installation and basic usage is the same as on Fedora **but** CentOS requires additional repositories
like [remi](<http://rpms.famillecollet.com/>) or [EPEL](<http://fedoraproject.org/wiki/EPEL>) to 
satisfy additional dependencies.

## Raspbian
As an experiment we offer Raspbian and brought the appserver to an ARM environment. What should 
we say, it worked! :D With `os.distribution` = raspbian you might give it a try to build it 
yourself (plan at least 5 hours) as we currently do not offer prepared install packages.

# Uninstall

Before uninstalling you should stop all services which are still running, otherwise there might
be problems with existing pid-files on Linux and Mac for the next time you install it. You can 
have a look how to do so [here](#start-and-stop-scripts).

To uninstall the appserver on Linux you might rely on your package management system. 
On Windows you can use the normal uninstall process provided by the operating system.

Under Mac OS X you can simply delete the `/opt/appserver` folder and delete the configuration
files for the launch daemons. These are files are located in folder `/Library/LaunchDaemons` and 
named `io.appserver.<DAEMON>.plist`.

# Configuration

We believe that the appserver should be highly configurable, so anyone interested can fiddle 
around with it. Therefor we provide a central configuration file located at `/opt/appserver/etc/appserver.xml`.

This file contains the complete [architecture](#the-architecture) as an XML structure.

So if you want to change used components, introduce new services or scale the system by adding
additional servers you can do so with some lines of XML.You might have a look at a basic 
`appserver.xml`.

## The Architecture

In this example we have a shortened piece of the `apserver.xml` file to understand how the 
architecture is driven by configuration.

```xml
<container
    name="webserver"
    type="TechDivision\WebContainer\Container">
    <description>
        <![CDATA[This is an example of a webserver container that handles http requests in common way]]>
    </description>
    <deployment type="TechDivision\WebContainer\WebContainerDeployment" />
    <host
        name="localhost"
        appBase="/webapps"
        serverAdmin="info@appserver.io"
        serverSoftware="appserver/${appserver.version} (${os.family}) PHP/${appserver.php.version}" />
    <servers>
        <server
            type="\TechDivision\WebSocketServer\Servers\AsyncServer"
            socket="\TechDivision\WebSocketServer\Sockets\AsyncSocket"
            serverContext="\TechDivision\Server\ServerContext"
            loggerName="System">
            <params>
                <param name="transport" type="string">tcp</param>
                <param name="address" type="string">0.0.0.0</param>
                <param name="port" type="integer">8589</param>
                <param name="workerNumber" type="integer">64</param>
                        
                <!-- configure the server as you would like -->
                        
            </params>

            <connectionHandlers>
                <connectionHandler type="\TechDivision\WebSocketProtocol\WebSocketConnectionHandler" />
            </connectionHandlers>
        </server>

        <!-- Here, additional servers might be added -->
    
    </servers>
</container>
``` 

In the above example you can see three important components of the appserver architecture being 
used. The [*container*](<architecture.md#container>), [*server*](<architecture.md#server>) and a 
[*protocol*](<architecture.md#protocol>) (if you did not read about our basic [architecture](<architecture.md>) 
you should now). We are basically building up a container which holds a server using the websocket 
protocol to handle incomming requests.

### Container configuration

A *container* is created by using the `container` element within the `containers` collection 
of the `appserver` document element. Two things make this element in a specific container 
being built up by the system on startup:

* The `type` attribute states a class extending our `AbstractContainerThread` which makes a 
  container into a certain kind of container.

* The `deployment` element states a class containing preparations for starting up the container. 
  It can be considered a hook which will be invoked before the container will be available.

That is basically everything there is to do to create a new container. To make use of it, it has 
to contain at least one *server* within its `servers` collection.

### Server configuration

The *servers* contained by our *container* can also be losely drafted by the XML configuration and 
will be instantiated on container bootup. To enable a *server* you have to mention three basic 
attributes of the element:

* The `type` specifies a class implementing the `ServerInterface` which implements the basic 
  behaviour of the server on receiving a connection and how it will handle it.

* The `socket` attribute specifies the type of socket the server should open. E.g. a stream or 
  asynchonious socket
* The `serverContext` specifies the server's soure of configuration and container for runtime 
  information e.g. ServerVariables like `DOCUMENT_ROOT`

So we have our specific server which will open a certain port and operate in a defined context. But
to make the server handle a certain type of requests it needs to know which *protocol* to speak.

This can be done using the `connectionHandler` element. Certain server wrappers can handle certain
protocols. Therefor we can use the protocols which a server wrapper, e.g. `WebServer` supports in 
form of connection handlers. [WebServer](<https://github.com/techdivision/TechDivision_WebServer>)
offers a `HttpConnectionHandler` class. By using it, the server is able to understand the HTTP 
protocol. 

# Basic Usage

The appserver will automatically start after your installation wizard (or package manager) finishes
the setup. You can use it without limitations from now on.

Below you can find basic instructions on how to make use of the appserver. After the installation
you might want to have a look and some of the bundled apps. Two of are interesting in particular:

* **Example** shows basic usage of services. You can reach it at `http://127.0.0.1:9080/example`

* **Admin** appserver and app management `http://127.0.0.1:9080/admin`

Start your favorite browser and have a look at what we can do. :) To pass the password barriers use
the default login `appserver/appserver.i0`.

You will see that we provide basic frontend implementations of services the appserver runtime
provides. If you want to use these services yourself you should have a look into the code of our 
apps and read about [app development](<../basics/webapp-basics/app-deployment.md>).

You might be curious about the different port we use. Per default the appserver will open several 
ports at which it's services are available. As we do not want to block (or be blocked by) other 
services we use ports of a higher range.

As a default we use the following ports:

* WebContainer
    - Http-Server: `9080`
    - Https-Server: `9443`
    - WebSocketServer: `8589`  
    
* Persistence-MQ-Container
    - PersistenceServer: `8585`
    - PersistenceServer: `8587`

You can change this default port mapping by using the [configuration file](<../basics/appserver-basics/the-appserver_xml-configuration-file.md>).
If you are interested in our naming, you can see our container->server pattern, you might want to 
have a deeper look into our [architecture](<../basics/appserver-basics/architecture.md>)

## Start and Stop Scripts

Together with the appserver we deliver several standalone processes which we need for proper 
functioning of different features.

For these processes we provide start and stop scripts for all *nix like operating systems.
These work the way they normally would on the regarding system. They are:

* `appserver`: The main process which will start the appserver itself

* `appserver-php5-fpm`: php-fpm + appserver configuration. Our default FastCGI backend. Others might
  be added the same way

* `appserver-watcher`: A watchdog which monitors filesystem changes and manages appserver restarts

On a normal system all three of these processes should run to enable the full feature set. To 
ultimately run the appserver only the appserver process is needed but you will miss simple on-the-fly 
deployment (`appserver-watcher`) and might have problems with legacy applications.

Depending on the FastCGI Backend you want to use you might ditch `appserver-php5-fpm` for other 
processes e.g. supplying you with a hhvm backend.

Currently we support three different types of init scripts which support the commands `start`, `stop`,
`status` and `restart` (additional commands migth be available on other systems).

**Mac OS X (LAUNCHD)**
The LAUNCHD launch daemons are located within the appserver installation at `/opt/appserver/sbin`.
They can be used with the schema `/opt/appserver/sbin/<DAEMON> <COMMAND>`

**Debian, Raspbian, CentOS, ...(SystemV)**
Commonly known and located in `/etc/init.d/` they too support the commands mentioned above  provided 
in the form `/etc/init.d/<DAEMON> <COMMAND>`.

**Fedora, ... (systemd)**
systemd init scripts can be used using the `systemctl` command with the syntax `systemctl <COMMAND> <DAEMON>`.

**Windows**

On Windows we sadly do not offer any of these scripts. After the installation you can start the 
Application Server with the ``server.bat`` file located within the root directory of your installation.
Best thing to do would be starting a command prompt as an administrator snd run the following commands
(assuming default installation path):

```
C:\Windows\system32>cd "C:\Program Files\appserver"
C:\Program Files\appserver>server.bat
```

# HTTP Server

The configuration itself is highly self-explanatory so just have a look to the preferred config
file and try to change settings. A detailed overview of all configuration settings will follow ...

## Configure a Virtual Host

Using virtual hosts you can extend the default server configuration and produce a host specific
environment for your app to run.

You can do so by adding a virtual host configuration to your global server configuration file. See
the example for a XML based configuration below:

```xml
<virtualHost name="example.local">
    <params>
        <param name="admin" type="string">admin@appserver.io</param>
        <param name="documentRoot" type="string">/opt/appserver/webapps/example</param>
    </params>
</virtualHost>
```

The above configuration sits within the server element and opens up the virtual host `example.local`
which has a different document root than the global configuration has. The virtual host is born. :-)

The `virtualHost` element can hold params, rewrite rules or environment variables which are only 
available for the host specifically.

## Configure Environment Variables

You can set environment variables using either the global or the virtual host based configuration.
The example below shows a basic usage of environment variables in XML format.

```xml
<environmentVariables>
    <environmentVariable condition="" definition="EXAMPLE_VAR=example" />
    <environmentVariable condition="Apple@$HTTP_USER_AGENT" definition="USER_HAS_APPLE=true" />
</environmentVariables>
```

There are several ways in which this feature is used. You can get a rough idea when having a look 
at Apache modules [mod_env](<http://httpd.apache.org/docs/2.2/mod/mod_env.html>) and [mod_setenvif](<http://httpd.apache.org/docs/2.2/mod/mod_setenvif.html>)
which we adopted.

You can make definitions of environment variables dependent on REGEX based conditions which will
be performed on so called backreferences. These backreferences are request related server variables
like `HTTP_USER_AGENT`.

A condition has the format `<REGEX_CONDITION>@$<BACKREFERENCE>`. If the condition is empty the 
environment variable will be set every time.

The definition you can use has the form `<NAME_OF_VAR>=<THE_VALUE_TO_SET>`. The definition has some
specialities too:

- Setting a var to `null` will unset the variable if it existed before
- You can use backreferences for the value you want to set as well. But those are limited to environment 
  variables of the PHP process
- Values will be treated as strings

## Modules

The web server comes with a package of default modules. The functionality that allows us to configure
a virtual host or environment variables, for example, is also provided by two, maybe the most important,
modules.

### Rewrite Module

#### Usage

The module can be used according to the `\AppserverIo\WebServer\Interfaces\HttpModuleInterface` interface.
It needs an initial call of the `init` method and will process any request offered to the `process` method.
The module is best used within the [`webserver`](<https://github.com/appserver-io/webserver>)
project as it offers all needed infrastructure.

#### Rules

Most important part of the module is the way in which it can perform rewrites. All rewrites are 
based on rewrite rules which consist of three important parts:

- *condition string* : Conditions which have to be met in order for the rule to take effect. 
  See more [down here](#condition-syntax)

- *target string* : The target to rewrite the requested URI to. Within this string you can use 
  backreferences similar
  to the Apache mod_rewrite module with the difference that you have to use the `$ syntax`
  (instead of the `$/%/%{} syntax` of Apache).
  
  Backreferences are parts of the matching rule conditions which you specifically pick out via regex.

  *Simple example* : A condition like `(.+)@$X_REQUEST_URI` would produce a back reference `$1` 
  with the value `/index` for a requested URI `/index`. The target string `$1/welcome.html` would
  therefore result in a rewrite to `/index/welcome.html`

- *flag string* : You can use flags similar to mod_rewrite which are used to make rules react in a 
  certain way or influence further processing. See more [down here](#flags)

#### Condition Syntax

The Syntax of possible conditions is roughly based on the possibilities of Apache's RewriteCondition 
and RewriteRule combined.

To make use of such a combination you can chain conditions together using the `{OR}` symbol for 
OR-combined, and the `{AND}` symbol for AND-combined conditions.

Please be aware that AND takes precedence over OR! Conditions can either be PCRE regex or certain fixed 
expressions. So a condition string of `([A-Z]+\.txt){OR}^/([0-9]+){AND}-f` would match only real files 
(through `-f`) which either begin with numbers or end with capital letters and the extension .txt.

As you might have noticed: Backslashes do **not have to be escaped**.

You might also be curious of the `-f` condition. This is a direct copy of Apaches -f RewriteCondition.
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
any operand you like using the `@` symbol. All conditions within a rule will use the next operand to 
their right and if none is given the requested URI. For example:

- *`([A-Z]+\.txt){OR}^/([0-9]+)`* Will take the requested URI for both conditions (note the `{OR}` symbol)
- *`([A-Z]+\.txt){OR}^/([0-9]+)@$DOCUMENT_ROOT`* Will test both conditions against the document root
- *`([A-Z]+\.txt)@$DOCUMENT_ROOT{OR}^/([0-9]+)`* Will only test the first one against the document root 
  and the second against the requested URI

You might have noted the `$` symbol before `DOCUMENT_ROOT` and remembered it from the backreference 
syntax. That's because all Apache common server vars can be explicitly used as backreferences too!

That does not work for you? Need the exact opposite? No problem!

All conditions, weather regex or expression based can be negated using the `!` symbol in front of 
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

#### Usage

The module can be used according to the `\AppserverIo\WebServer\Interfaces\HttpModuleInterface`
interface. It needs an initial call of the `init` method and will process any request offered to 
the `process` method. The module is best used within the [webserver](<https://github.com/appserver-io/webserver>)
project as it offers all needed infrastructure.

#### Examples

The following examples should help you to configure your application with default settings usually
provided with the applications .htaccess files.

If you want to configure virtual hosts a virtual hosts node is mandatory and should look like the 
following.

```xml
<virtualHosts>
    <virtualHost name="name.local">
        Please put in your Hosts configuration here. For Examples see below 
    </virtualHost>
</virtualHosts>
```

##### Magento:

```xml
<virtualHost name="magento-real-vhost.dev magento-real-vhost.local">
    <params>
        <param name="admin" type="string">info@appserver.io</param>
        <param name="documentRoot" type="string">webapps</param>
    </params>
    <rewrites>
        <rewrite condition="-d{OR}-f{OR}-l" target="" flag="L" />
        <rewrite condition="(.*)" target="index.php/$1" flag="L" />
    </rewrites>
    <accesses>
        <access type="allow">
            <params>
                <param name="X_REQUEST_URI" type="string">^\/([^\/]+\/)?(media|skin|js|index\.php).*
                </param>
            </params>
        </access>
    </accesses>
</virtualHost>
```

##### TYPO3 Neos

```xml
<virtualHost name="neos.local">
    <params>
        <param name="admin" type="string">info@appserver.io</param>
        <param name="documentRoot" type="string">webapps/neos-1.0.2/Web
        </param>
    </params>
    <rewrites>
        <rewrite
            condition="^/(_Resources/Packages/|robots\.txt|favicon\.ico){OR}-d{OR}-f{OR}-l"
            target="" flag="L" />
        <rewrite
            condition="^/(_Resources/Persistent/[a-z0-9]+/(.+/)?[a-f0-9]{40})/.+(\..+)"
            target="$1$3" flag="L" />
        <rewrite condition="^/(_Resources/Persistent/.{40})/.+(\..+)"
            target="$1$2" flag="L" />
        <rewrite condition="^/_Resources/.*" target="" flag="L" />
        <rewrite condition="(.*)" target="index.php" flag="L" />
    </rewrites>
    <environmentVariables>
        <environmentVariable condition=""
            definition="FLOW_REWRITEURLS=1" />
        <environmentVariable condition=""
            definition="FLOW_CONTEXT=Production" />
        <environmentVariable condition="Basic ([a-zA-Z0-9\+/=]+)@$Authorization"
            definition="REMOTE_AUTHORIZATION=$1" />
    </environmentVariables>
</virtualHost>
```

##### ORO CRM

```xml
<virtualHost name="oro-crm.local">
    <params>
        <param name="admin" type="string">info@appserver.io</param>
        <param name="documentRoot" type="string">webapps/crm-application/web
        </param>
    </params>
    <rewrites>
        <rewrite condition="-f" target="" flag="L" />
        <rewrite condition="^/(.*)$" target="app.php" flag="L" />
    </rewrites>
</virtualHost>
```

##### Wordpress

```xml
<virtualHost name="wordpress.local">
    <params>
        <param name="admin" type="string">info@appserver.io</param>
        <param name="documentRoot" type="string">webapps/wordpress</param>
    </params>
</virtualHost>
```

# Servlet-Engine

# Persistence-Container

As described in the introduction the application is designed inside a runtime environment like
an application server as appserver.io is. The following example gives you a short introdction 
how you can create a stateful session bean and the way you can invoke it's method on client side.

First thing you've to do is to create your SessionBean. What is a SessionBean? It's not simple
to describe it in only a few words, but i'll try. A SessionBean basically is plain PHP class.
You MUST not instanciate it directly, because the application server takes care of its complete
lifecycle. Therefore, if you need an instance of a SessionBean, you'll ask the application server 
to give you a instance. This can be done by a [client](<https://github.com/techdivision/persistencecontainerclient>).

The persistence container client will give you a proxy to the session bean that allows you to
invoke all methods the SessionBean provides as you can do if you would have real instance. But
the proxy also allows you to call this method over a network as remote method call. Using the 
persistence container client makes it obvious for you if your SessionBean is on the same 
application server instance or on another one in your network. This gives you the possibilty
to distribute the components of your application over your network what includes a great and
seemless scalabilty.

You have to tell the persistence container of the type the SessionBean should have. This MUST 
be done by simply add an annotation to the class doc block. The possible annotations therefore 
are

* @Singleton
* @Stateless
* @Stateful

The SessionBean types are self explained i think.

## @Singleton SessionBean

A SessionBean with a @Singleton annotation will be created only one time for each application.
This means, whenever you'll request an instance, you'll receive the same one. If you set a
variable in the SessionBean, it'll be available until you'll overwrite it, or the application
server has been restarted.

## @Stateless SessionBean

In opposite to a singleton session bean, a SessionBean with a @Stateless annotation will always
be instanciated when you request it. It has NO state, only for the time you invoke a method on
it.

## @Stateful SessionBean

The @Stateful SessionBean is something between the other types. It is stateful for the session
with the ID you pass to the client when you request the instance. A stateful SessionBean is 
useful if you want to implement something like a shopping cart. If you declare the shopping cart 
instance a class member of your SessionBean makes it persistent for your session lifetime.

## Example

The following example shows you a really simple implementation of a stateful SessionBean providing
a counter that'll be raised whenever you call the raiseMe() method.

```php

namespace Namespace\Module;

/**
 * This is demo implementation of stateful session bean.
 *
 * @Stateful
 */
class MyStatefulSessionBean
{

    /**
     * Stateful counter that exists as long as your session exists.
     *
     * @var integer
     */
    protected $counter = 0;

    /**
     * Passes a reference to the application context to our session bean.
     *
     * @param \TechDivision\Application\Interface\ApplicationInterface $application The application instance
     */
    public function __construct(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * Example method that raises the counter by one each time you'll invoke it.
     *
     * @return void
     */
    public function raiseMe()
    {
        $this->counter++;
    }
}
```

As described above, you MUST not instanciate it directly. The request an instance of the SessionBean
you MUST use the persistence container client. With the lookup() method you'll receive a proxy to
your SessionBean, on that you can invoke the methods as you can do with a real instance.

```php

// initialize the connection and the session
$connection = ConnectionFactory::createContextConnection('your-application-name');
$contextSession = $connection->createContextSession();

// set the session ID of the actual request (necessary for SessionBeans declared as @Stateful)
$contextSession->setSessionId('your-session-id');

// create an return the proxy instance and call a method, raiseMe() in this example
$proxyInstance = $contextSession->createInitialContext()->lookup('Namespace\Module\MyStatefulSessionBean');
$proxyIntance->raiseMe();

```

# Message-Queue

# Deployment

The deploy directory in the appserver.io Application Server distribution is the location end users can place their 
deployment content (e. g. phar files) to have it deployed into the server runtime.

Users, particularly those running production systems, are encouraged to use the appserver.io AS management APIs to 
upload and deploy deployment content.

## Deployment Modes

The scanner actually only suports manual deployment mode which means that you have to restart the server to process 
deployment of your content. In this mode, the scanner will not attempt to directly monitor the deployment content and 
decide if or when the end user wishes the content to be deployed or undeployed. Instead, the scanner relies on a system 
of marker files, with the user's addition or removal of a marker file serving as a sort of command telling the scanner 
to deploy, undeploy or redeploy content.

It is also possible to copy your unzipped content directly into the webapps folder. After restarting the webserver
your content will then be deployed without having any impact on the deployment scanner, because only zipped (.phar)
content will be recognized.

## Marker Files

The marker files always have the same name as the deployment content to which they relate, but with an additional file 
suffix appended. For example, the marker file to indicate the example.phar file should be deployed is named 
example.phar.dodeploy. Different marker file suffixes have different meanings.

The relevant marker file types are:

| Marker       | Description                                                     |
|:-------------|:----------------------------------------------------------------|
| .dodeploy    | Placed by the user to indicate that the given content should be deployed or redeployed into the runtime.                     |
| .deploying   | Placed by the deployment scanner service to indicate that it has noticed a .dodeploy file and is in the process of deploying the content. This marker file will be deleted when the deployment process completes.                                   |
| .deployed    | Placed by the deployment scanner service to indicate that the given content has been deployed into the runtime. If an end user deletes this file and no other marker is available, the content will be undeployed.                                     |
| .failed      | Placed by the deployment scanner service to indicate that the given content failed to deploy into the runtime. The content of the file will include some information about the cause of the failure. Note that, removing this file will make the deployment eligible for deployment again.                       |
| .undeploying | Placed by the deployment scanner service to indicate that it has noticed a .deployed file has been deleted and the content is being undeployed. This marker file will be deleted when the undeployment process completes.                        |
| .undeployed  | Placed by the deployment scanner service to indicate that the given content has been undeployed from the runtime. If an end content is being undeployed. This marker file will be deleted user deletes this file, it has no impact.                       |

## Basic workflows

All examples assume variable $AS points to the root of the appserver.io AS distribution.

Windows users: the examples below use UNIX shell commands; see the [Windows Notes](#windows-notes) below.

1. Add new zipped (.phar) content and deploy it:

```
$ cp target/example.phar $AS/deploy
$ touch $AS/deploy/example.phar.dodeploy
```

2. Undeploy currently deployed zipped (.phar) content:

```
$ rm $AS/deploy/example.phar.deployed
```

3. Replace currently deployed zipped (.phar) content with a new version and redeploy it:

```
$ cp target/example.phar $AS/deploy
$ mv $AS/deploy/example.phar.deployed $AS/deploy/example.phar.dodeploy
```

## Windows Notes

The above examples use UNIX shell commands. Windows equivalents are:

| UNIX           | Windows                 |
|:---------------|:------------------------|
| cp src dest    | xcopy /y src dest       |
| cp -r src dest | xcopy /e /s /y src dest |
| rm afile       | del afile               |
| touch afile    | echo >> afile           |

Note that the behavior of ```touch``` and ```echo``` are different but thedifferences are not relevant to the usages 

# External Links

* Documentation at [appserver.io](http://docs.appserver.io)
* Documentation on [GitHub](https://github.com/techdivision/TechDivision_AppserverDocumentation)
* [Getting started](https://github.com/techdivision/TechDivision_AppserverDocumentation/tree/master/docs/getting-started)
* [Appserver basics](https://github.com/techdivision/TechDivision_AppserverDocumentation/tree/master/docs/basics/appserver-basics)
