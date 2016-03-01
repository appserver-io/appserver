---
layout: docs_1_1
title: DNS server
meta_title: appserver.io DNS server
meta_description: appserver.io comes with a integrated DNS server that uses the virtual host configuration to resolve DNS requests.
position: 45
group: Docs
subNav:
  - title: Server Parameters
    href: server-parameters
  - title: Connection Handler
    href: connection-handler
  - title: Server Modules
    href: server-modules
  - title: Storage Providers
    href: storage-providers
permalink: /get-started/documentation/1.1/dnsserver.html
---

The DNS server is built and configured like any other server component using our [multithreaded server framework]
(<https://github.com/appserver-io/server>). Let's have a look at the main configuration of the server component 
itself.

```xml
<server
  name="dns"
  type="\AppserverIo\Server\Servers\MultiThreadedServer"
  worker="\AppserverIo\DnsServer\Workers\UdpThreadWorker"
  socket="\AppserverIo\Server\Sockets\StreamSocket"
  serverContext="\AppserverIo\Server\Contexts\ServerContext"
  requestContext="\AppserverIo\Server\Contexts\RequestContext"
  loggerName="System">
```

There are several attributes to configure for a server component, which are described in the following table.

| Attributes        | Description |
| ----------------- | ----------- |
| `name`            | The name of the server component used for reference and logging purposes. |
| `type`            | The server type implementation classname based on `AppserverIo\Server\Interfaces\ServerInterface`. It provides the main daemon like logic of the server. |
| `worker`          | The worker queue implementation classname based on `\AppserverIo\Server\Interfaces\WorkerInterface`. It introduces a common worker queue logic for the server with the ability to process many requests at the same time. This could be either a classic event loop or a threaded, forked mechanism. |
| `socket`          | The socket implementation classname based on `AppserverIo\Psr\Socket\SocketInterface`. It provides common socket functionality. As we have our [psr for sockets](<https://github.com/appserver-io-psr/socket>) you might want to have a look at it. |
| `serverContext`   | The server context implementation classname based on `\AppserverIo\Server\Interfaces\ServerContextInterface`. It represents the server context while running as a daemon and holds the configuration, loggers and an optional injectable container object, which can be used to connect several server components. |
| `requestContext`  | The request context implementation classname based on `\AppserverIo\Server\Interfaces\RequestContextInterface`. It holds all vars needed (server, environment and module vars), which can be processed and modified by the defined server-module-chain. After the request was pre-processed by internal server-modules, the request context provides the information for specific file-handlers being able to process the request in a common way. |
| `loggerName`      | The logger instance to use in the server's context. |

## Server Parameters

In the following section, the server params used to configure the DNS server are described. The DNS server
node in  `etc/appserver/appserver.xml` needs the following params.

```xml
<params>
    <param name="admin" type="string">info@appserver.io</param>
    <param name="software" type="string">appserver/1.1.1-119+alpha6 (darwin) PHP/5.6.17</param>
    <param name="transport" type="string">tcp</param>
    <param name="address" type="string">127.0.0.1</param>
    <param name="port" type="integer">53</param>
    <param name="flags" type="string">STREAM_SERVER_BIND</param>
    <param name="workerNumber" type="integer">64</param>
    <param name="workerAcceptMin" type="integer">3</param>
    <param name="workerAcceptMax" type="integer">8</param>
</params>
```

They are used to define several key/value pairs for the DNS server implementation. Beside the `flags` param, all of 
them are common to all servers. Their descriptions can be found [within the server configuration documentation]
({{ "/get-started/documentation/configuration.html#server-configuration" | prepend: site.baseurl }})

The description for the DNS server specific param is available below.

| Param             | Type     | Description                                                    |
| ----------------- | ---------| ---------------------------------------------------------------|
| `flags`           | string   | Flag, that overrides the default flags, and is necessary to create an UDP connection. |.

## Connection Handler

As we want to handle requests based on a specific protocol, the server needs a mechanism to understand and manage
those requests properly.

For our DNS server, we use the `\AppserverIo\DnsServer\ConnectionHandlers\DnsConnectionHandler` implementation,
which implements the `\AppserverIo\Server\Interfaces\ConnectionHandlerInterface` and follows the [DNS specification]
(<https://www.ietf.org/rfc/rfc1035.txt>).

The connection handler can be configured in the `etc/appserver/appserver.xml` in the DNS server node like

```xml
<connectionHandlers>
   <connectionHandler type="\AppserverIo\DnsServer\ConnectionHandlers\DnsConnectionHandler" />
</connectionHandlers>
```

## Server Modules

As mentioned in the beginning, we use our [multithreaded server framework](<https://github.com/appserver-io/server>).
to build the DNS server on. It allows you to provide modules for request and response processing. In contrast to the 
HTTP server, the DNS server actually doesn't need and provide any hooks. If you want to write you own module, e. g. 
to load DNS records from a different source (although the storage provider would be the right place for that), the 
modules would be processed in the given order.

As the DNS server actually only need exactly one module, the `\AppserverIo\DnsServer\Modules\CoreModule`, there is not 
much to describe how it has to be configured. So the module configuration in the DNS server node looks like

```xml
<modules>
  <module type="\AppserverIo\DnsServer\Modules\CoreModule">
    <params>
        <param name="resolverFactory" type="string">\AppserverIo\Appserver\Core\Modules\StorageProvider\SystemConfigurationResolverFactory</param>
        <param name="defaultTTL" type="integer">300</param>
    </params>
  </module>
</modules>
```

The possible module params, depending on the configured [storage provider](#storage-providers), are described below.

| Param                      | Type    | Description                                                                                                |
| -------------------------- | ------- | ---------------------------------------------------------------------------------------------------------- |
| `resolverFactory`          | string  | The class name of the factory that initializes the storage provider.                                       |
| `recordFile`               | string  | Beside using the configured virtual host, a JSON file can be used to load the DNS records from (needs the JSON storage provider). |
| `defaultTTL`               | integer | The default TTL for a DNS record to be cached.                                                             |

Addionally the DNS server package provides an interface called `\AppserverIo\DnsServer\Interfaces\DnsModuleInterface`, 
which has to be implemented by every module providing DNS related functionality.

The only module available is described in the table below.

## Storage Providers

The core module tries to resolve DNS request by loading the available DNS records with the configured storage provider.

The storage provider is not configured directly. Instead a factory class, implementing the 
`AppserverIo\DnsServer\Interfaces\ResolverFactoryInterface`, has to be given. By default, in the appserver.io context
the class `\AppserverIo\Appserver\Core\Modules\StorageProvider\SystemConfigurationResolverFactory` initializes a storage 
provider that uses the virtual from the system configuration as source for the DNS records. 

Another possibility will be a JSON file, that can provide complete DNS records instead of the **A** or **AAAA** types 
only. The JSON storage provider is part of the [DNS server](<https://github.com/appserver-io/dnsserver>) package and 
can be configured by using the apropriate storage provider, like

```xml
<modules>
  <module type="\AppserverIo\DnsServer\Modules\CoreModule">
    <params>
        <param name="resolverFactory" type="string">\AppserverIo\DnsServer\StorageProvider\StandardResolverFactory</param>
        <param name="recordFile" type="string">etc/dns_record.json</param>
        <param name="defaultTTL" type="integer">300</param>
    </params>
  </module>
</modules>
```

whereas the file with DNS records has to be available in the directory `/opt/appserver/etc/dns_record.json`, assuming
appserver.io is running on a Linux or a Mac OS X installation.