---
layout: docs
title: HTTP(S) Server
position: 40
group: Docs
subDocs:
  - title: Configure the HTTP(S) server
    href: configure-the-http-s-server
  - title: The connection handler
    href: the-connection-handler
  - title: Server modules
    href: server-modules
  - title: Configure a Virtual Host
    href: configure-a-virtual-host
  - title: Configure your Environment Variables
    href: configure-your-environment-variables
  - title: Configure authentications
    href: configure-authentications
permalink: /documentation/http-s-server.html
---

## Configure the HTTP(S) server

The http(s) server is configured like any other server component using our
[multithreaded server framework](<https://github.com/appserver-io/server>). Let's have a look at the main configuration
of the http server component.

```xml
<server
  name="http"
  type="\AppserverIo\Server\Servers\MultiThreadedServer"
  worker="\AppserverIo\Server\Workers\ThreadWorker"
  socket="\AppserverIo\Server\Sockets\StreamSocket"
  serverContext="\AppserverIo\Server\Contexts\ServerContext"
  requestContext="\AppserverIo\Server\Contexts\RequestContext"
  loggerName="System">
```

There are several attributes to configure for a server component which are described in the following
table.

| Attributes        | Description |
| ----------------- | ----------- |
| `name`            | The name of the server component used for reference and logging purpose. |
| `type`            | The server type implementation classname based on `AppserverIo\Server\Interfaces\ServerInterface`. It provides the main daemon like logic of the server. |
| `worker`          | The worker queue implementation classname based on `\AppserverIo\Server\Interfaces\WorkerInterface`. It will introduce a common worker queue logic for the server being able processing many requests at the same time. This could be either a classic eventloop or a threaded, forked mechanism |
| `socket`          | The socket implementation classname based on `AppserverIo\Psr\Socket\SocketInterface`. It provides common socket functionality. As we have our [psr for sockets](<https://github.com/appserver-io-psr/socket>) now you might have a look at it. |
| `serverContext`   | The server context implementation classname based on `\AppserverIo\Server\Interfaces\ServerContextInterface`. It represents the server context while running as daemon and holds the configuration, loggers and an optional injectable container object which can be used to connect several server components. |
| `requestContext`  | The request context implementation classname based on `\AppserverIo\Server\Interfaces\RequestContextInterface`. It holds all vars needed (server, environment and module vars) which can be processed and modified by the server-module-chain defined. After the request was pre processed by internal server-modules the request context will provide those information for specific file-handlers being able to process the request in a common way. |
| `loggerName`      | The logger instance to use in the server's context. |

Next thing we'll have a look at are server params.

```xml
<params>
    <param name="admin" type="string">info@appserver.io</param>
    <param name="software" type="string">appserver/1.0.0-beta4.19 (linux) PHP/5.5.19</param>
    <param name="transport" type="string">tcp</param>
    <param name="address" type="string">127.0.0.1</param>
    <param name="port" type="integer">9080</param>
    <param name="workerNumber" type="integer">64</param>
    <param name="workerAcceptMin" type="integer">3</param>
    <param name="workerAcceptMax" type="integer">8</param>
    <param name="documentRoot" type="string">webapps</param>
    <param name="directoryIndex" type="string">index.do index.php index.html index.htm</param>
    <param name="keepAliveMax" type="integer">64</param>
    <param name="keepAliveTimeout" type="integer">5</param>
    <param name="errorsPageTemplatePath" type="string">var/www/errors/error.phtml</param>
</params>
```

They are used to define several key/value pairs for the HTTP(S) server implementation to react on. Find the param
descriptions below.

| Param                    | Description |
| ------------------------ | ----------- |
| `admin`                  | The email address of the administrator who is responsible for. |
| `software`               | The software signature as showen in the response header for example. |
| `transport`              | The transport layer. In ssl mode `ssl` will be used instead of plain `tcp`. |
| `address`                | The address the server-socket should be bind and listen to. If you want to allow only connection on local loopback define ´127.0.0.1´ as in the example above shown. This will be good enough for local development and testing purpos. If you want to allow connections to your external ethernet interfaces just define `0.0.0.0` or if you want to allow connection only on a specific interface just define the ip of your interface `192.168.1.100`. |
| `port`                   | The port for the server-socket to accept connections to. Default setting is `9080` and `9443` for ssl. If you want to serve through default http(s) ports just define `80` and for https `443`. Make sure there is no other webserver installed blocking the default ports.|
| `workerNumber`           | Defines the number of worker-queues to be started waiting for requests to process. |
| `workerAcceptMin`        | Describes the minimum number of requests for the worker to be accepted for randomize its lifetime. |
| `workerAcceptMax`        | Describes the maximum number of requests for the worker to be accepted for randomize its lifetime. |
| `documentRoot`           | Defines the root directory for the server to append the uri with and search for the requested file or directory. The document root path will be relative to the servers root directory if there is no beginning slash "/" |
| `directoryIndex`         | Defines the index resources to lookup for the requested directory. The server will return the first one that it finds. If none of the resources exist, the server will respond with a 404 Not Found. |
| `keepAliveMax`           | The number of requests allowed per connection when keep-alive is on. If it is set to 0 keep-alive feature will be deactivated. |
| `keepAliveTimeout`       | The number of seconds waiting for a subsequent request while in keep-alive loop before closing the connection. |
| `errorsPageTemplatePath` | The path to the errors page template. The path will be relative to the servers root directory if there is no beginning slash "/". |

If you want to setup a HTTPS server you have to configure 2 more params.

| Param         | Description |
| --------------| ----------- |
| `certPath`    | The path to your certificate file which as to be a combined PEM file of private key and certificate. The path will be relative to the servers root directory if there is no beginning slash "/". |
| `passphrase`  | The passphrase you have created your SSL private key file with. Can be optional. |

## The connection handler

As we wanted to handle requests based on a specific protocol, the server needs a mechanism to understand and handle
those requests in a proper way.

For our http(s) server we use `\AppserverIo\WebServer\ConnectionHandlers\HttpConnectionHandler`
which implements the `\AppserverIo\Server\Interfaces\ConnectionHandlerInterface` and follows the HTTP/1.1 specification,
which can be found [here](<http://tools.ietf.org/html/rfc7230>) using our [http library](<https://github.com/appserver-io/http>).

```xml
<connectionHandlers>
    <connectionHandler type="\AppserverIo\WebServer\ConnectionHandlers\HttpConnectionHandler" />
</connectionHandlers>
```

> There is the possibility to provide more than one connection handler, but in most cases it does not make sense because
> you will have to handle another protocol which might not be compatible with the modules you provided in the same server
> configuration. In certain circumstances it will make sense but it's not best practise to do this.

## Server modules

As mentioned at the beginning we're using our [multithreaded server framework](<https://github.com/appserver-io/server>)
which allows you to provide modules for request and response processing triggered from several hooks.

Let's get an overview of those hooks which can also be found in the corresponding dictionary class `\AppserverIo\Server\Dictionaries\ModuleHooks`

| Hook             | Description |
| -----------------| ----------- |
| `REQUEST_PRE`    | The request pre hook should be used to do something before the request will be parsed. So if there is a keep-alive loop going on this will be triggered every request loop. |
| `REQUEST_POST`   | The request post hook should be used to do something after the request has been parsed. Most modules such as CoreModule will use this hook to do their job. |
| `RESPONSE_PRE`   | The response pre hook will be triggered at the point before the response will be prepared for sending it to the to the connection endpoint. |
| `RESPONSE_POST`  | The response post hook is the last hook triggered within a keep-alive loop and will execute the modules logic when the response is well prepared and ready to dispatch. |
| `SHUTDOWN`       | The shutdown hook is called whenever a php fatal error will shutdown the current worker process. In this case current filehandler module will be called to process the shutdown hook. This enables the module the possibility to react on fatal error's by it's own in some cases. If it does not react on this shutdown hook, the default error handling response dispatcher logic will be used. If the module reacts on the shutdown hook and set's the response state to be dispatched no other error handling shutdown logic will be called to fill up the response. |

Now let's dig into the modules list provided for the HTTP(S) server by default.

```xml
<modules>
    <!-- REQUEST_POST hook -->
    <module type="\AppserverIo\WebServer\Modules\VirtualHostModule"/>
    <module type="\AppserverIo\WebServer\Modules\AuthenticationModule"/>
    <module type="\AppserverIo\WebServer\Modules\EnvironmentVariableModule" />
    <module type="\AppserverIo\WebServer\Modules\RewriteModule"/>
    <module type="\AppserverIo\WebServer\Modules\DirectoryModule"/>
    <module type="\AppserverIo\WebServer\Modules\AccessModule"/>
    <module type="\AppserverIo\WebServer\Modules\CoreModule"/>
    <module type="\AppserverIo\WebServer\Modules\PhpModule"/>
    <module type="\AppserverIo\WebServer\Modules\FastCgiModule"/>
    <module type="\AppserverIo\Appserver\ServletEngine\ServletEngine" />
    <!-- RESPONSE_PRE hook -->
    <module type="\AppserverIo\WebServer\Modules\DeflateModule"/>
    <!-- RESPONSE_POST hook -->
    <module type="\AppserverIo\Appserver\Core\Modules\ProfileModule"/>
</modules>
```

For every hook all modules are processed in the same order as they are listed in the xml configuration.
> The order of the modules provided by the default configuration is intended and should not be changed. For example if you change the
> order of AccessModule to come before the RewriteModule it would be possible to lever an access rule by any rewrite rule.

Our webserver provides an interface called `\AppserverIo\WebServer\Interfaces\HttpModuleInterface` that every module has
to implement.

Find an overview of all modules below ...

| Module                      | Description |
| ----------------------------| ----------- |
| `VirtualHostModule`         | Provides virtual host functionality that allows you to run more than one hostname (such as yourname.example.com and othername.example.com) on the same server while having different params and configurations. |
| `AuthenticationModule`      | Offers the possibility to secure resources using basic or digest authentication based on request uri with regular expression support. |
| `EnvironmentVariableModule` | This module let you manipulate server environment variables. These can be conditionally set, unset and copied in form of an OS context. |
| `RewriteModule`             | A simple rewrite module for PHP based web servers which uses a self made structure for usable rules. It can be used similar to Apaches mod_rewrite and provides rewriting and redirecting capabilities. |
| `DirectoryModule`           | Provides for "trailing slash" redirects and serving directory index files. |
| `AccessModule`              | Allows a http header based access management with regular expression support. |
| `CoreModule`                | HTTP server features that are always available such as serving static resources and finding defined file handlers. |
| `PhpModule`                 | Acts like a classic php webserver module (such as `mod_php` for apache) which calls and runs your requested php scripts in an isolated context with all globals (such as `$_SERVER`, `$_GET`, `$_POST` etc.) prepared in the common way. |
| `FastCgiModule`             | The Module allows you to connect several fastcgi backends (such as `php-fpm` or `hhvm`) based on configured file-handlers. |
| `ServletEngine`             | The ServletEngine introduces a super fast and simple way to implement an entry point to handle HTTP requests that allows you to execute all performance critical tasks. Please see [Servlet Engine](<{{ "/documentation/servlet-engine.html" | prepend: site.baseurl }}>) for full documentation. |
| `DeflateModule`             | It provides the `deflate` output filter that allows output from your server to be compressed before being sent to the client over the network. |
| `ProfileModule`             | Allows request based realtime profiling using external tools like logstash and kibana. |

## Configure a Virtual Host

Using virtual hosts you can extend the default server configuration and produce a host specific
environment for your app to run.

You can do so by adding a virtual host configuration to your global server configuration file. See
the example for a XML based configuration below:

```xml
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
```

The above configuration sits within the server element and opens up the virtual host `example.local`
which has a different document root than the global configuration has. The virtual host is born. :-)

The `virtualHost` element can hold params, rewrite rules or environment variables which are only 
available for the host specifically.

## Configure your Environment Variables

You can set environment variables using either the global or the virtual host based configuration.
The example below shows a basic usage of environment variables in XML format.

```xml
<environmentVariables>
  <environmentVariable 
    condition="" 
    definition="EXAMPLE_VAR=example" />
  <environmentVariable 
    condition="Apple@$HTTP_USER_AGENT" 
    definition="USER_HAS_APPLE=true" />
</environmentVariables>
```

There are several ways in which this feature is used. You can get a rough idea when having a 
look at Apache modules [mod_env](<http://httpd.apache.org/docs/2.2/mod/mod_env.html>) and 
[mod_setenvif](<http://httpd.apache.org/docs/2.2/mod/mod_setenvif.html>) which we adopted.

You can make definitions of environment variables dependent on REGEX based conditions which will
be performed on so called backreferences. These backreferences are request related server variables
like `HTTP_USER_AGENT`.

A condition has the format `<REGEX_CONDITION>@$<BACKREFERENCE>`. If the condition is empty the 
environment variable will be set every time.

The definition you can use has the form `<NAME_OF_VAR>=<THE_VALUE_TO_SET>`. The definition has 
some specialities too:

- Setting a var to `null` will unset the variable if it existed before
- You can use backreferences for the value you want to set as well. But those are limited to 
  environment variables of the PHP process
- Values will be treated as strings

## Configure authentications

You can setup request uri based basic or digest authentication based on the  with regular expression support.