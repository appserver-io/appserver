---
layout: docs
title: HTTP(S) Server
position: 40
group: Docs
subDocs:
  - title: Configure the HTTP(S) server
    href: configure-the-http-s-server
  - title: Configure a Virtual Host
    href: configure-a-virtual-host
  - title: Configure your Environment Variables
    href: configure-your-environment-variables
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

They are used to define several key/value pairs for the http(s) server implementation to react on
which are described in the following table.

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

A detailed overview of all configuration settings will follow ...

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
