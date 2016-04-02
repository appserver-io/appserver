---
layout: docs_1_1
title: DNS server
meta_title: appserver.io DNS server
meta_description: appserver.io comes with a integrated DNS server that uses the virtual host configuration to resolve DNS requests.
position: 45
group: Docs
subNav:
  - title: Configuration
    href: configuration
  - title: Activation
    href: activation
permalink: /get-started/documentation/1.1/dnsserver.html
---

The appserver.io DNS server is **NOT** a fully featured DNS server that can be used in production mode, it has been designed and implemented to make development a bit more comfortable. In the context of appserver.io, by default it uses the virtual host configuration to resolve the DNS names without the need to add each of them to the `/etc/hosts` file. So, whenever a new virtual host configuration will be added to the `etc/appserver/conf.d/virtual-hosts.xml` or in a application's `META-INF/containers.xml` file, the DNS name can be resolved by the DNS server, after it has been [restarted]({{ "/get-started/documentation/basic-usage.html#start-and-stop-scripts" | prepend: site.baseurl }}).

## Configuration

The DNS server is built and configured like any other server component using our [multithreaded server framework](<https://github.com/appserver-io/server>). Let's have a look at the main configuration of the DNS server component in `etc/appserver/appserver.xml` itself

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

The only difference to the HTTP server configuration is the different worker, as a DNS server is **NOT** using the TCP, but the UDP protocol.

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

### Server Parameters

In the following section, the server params used to configure the DNS server are described. The DNS server node in  `etc/appserver/appserver.xml` needs the following params.

```xml
<params>
    <param name="admin" type="string">info@appserver.io</param>
    <param name="software" type="string">appserver/1.1.1 (darwin) PHP/5.6.17</param>
    <param name="transport" type="string">tcp</param>
    <param name="address" type="string">127.0.0.1</param>
    <param name="port" type="integer">9053</param>
    <param name="flags" type="string">STREAM_SERVER_BIND</param>
    <param name="documentRoot" type="string">var</param>
    <param name="workerNumber" type="integer">64</param>
    <param name="workerAcceptMin" type="integer">3</param>
    <param name="workerAcceptMax" type="integer">8</param>
</params>
```

They are used to define several key/value pairs for the DNS server implementation. Beside the `flags` param, all of them are common to all servers. Their descriptions can be found [within the server configuration documentation]({{ "/get-started/documentation/configuration.html#server-configuration" | prepend: site.baseurl }})

The description for the DNS server specific param is available below.

| Param             | Type     | Description                                                    |
| ----------------- | ---------| ---------------------------------------------------------------|
| `flags`           | string   | Flag, that overrides the default flags, and is necessary to create an UDP connection. |.

### Connection Handler

As we want to handle requests based on a specific protocol, the server needs a mechanism to understand and manage those requests properly.

For our DNS server, we use the `\AppserverIo\DnsServer\ConnectionHandlers\DnsConnectionHandler` implementation, which implements the `\AppserverIo\Server\Interfaces\ConnectionHandlerInterface` and follows the [DNS specification](<https://www.ietf.org/rfc/rfc1035.txt>).

The connection handler can be configured in the `etc/appserver/appserver.xml` in the DNS server node like

```xml
<connectionHandlers>
   <connectionHandler type="\AppserverIo\DnsServer\ConnectionHandlers\DnsConnectionHandler" />
</connectionHandlers>
```

### Server Modules

As mentioned in the beginning, we use our [multithreaded server framework](<https://github.com/appserver-io/server>). to build the DNS server on. It allows you to provide modules for request and response processing. In contrast to the HTTP server, the DNS server actually doesn't need and provide any hooks. If you want to write you own module, e. g. to load DNS records from a different source (although the storage provider would be the right place for that), the modules would be processed in the given order.

As the DNS server actually only need exactly one module, the `\AppserverIo\DnsServer\Modules\CoreModule`, there is not much to describe how it has to be configured. So the module configuration in the DNS server node looks like

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

Addionally the DNS server package provides an interface called `\AppserverIo\DnsServer\Interfaces\DnsModuleInterface`, which has to be implemented by every module providing DNS related functionality.

The only module available is described in the table below.

### Storage Providers

The core module tries to resolve DNS request by loading the available DNS records with the configured storage provider.

The storage provider is not configured directly. Instead a factory class, implementing the `AppserverIo\DnsServer\Interfaces\ResolverFactoryInterface`, has to be given. By default, in the appserver.io context the class `\AppserverIo\Appserver\Core\Modules\StorageProvider\SystemConfigurationResolverFactory` initializes a storage provider that uses the virtual from the system configuration as source for the DNS records. 

Another possibility will be a JSON file, that can provide complete DNS records instead of the **A** or **AAAA** types only. The JSON storage provider is part of the [DNS server](<https://github.com/appserver-io/dnsserver>) package and  can be configured by using the apropriate storage provider, like

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

whereas the file with DNS records has to be available in the directory `/opt/appserver/etc/dns_record.json`, assuming appserver.io is running on a Linux or a Mac OS X installation.

## Activation

Why do i have to activate the DNS server? By default, a DNS server has to listen to port `53`. As we don't know a DNS server like [dnsmasq](http://www.thekelleys.org.uk/dnsmasq/doc.html) has already been installed on the target system, we decided to let our DNS server listen to port 9053 by default. This means, that the DNS server is running indeed, but it will not be used to resolving DNS queries.

To activate the appserver.io internal DNS server, the following steps are necessary

1. Switch the port from `9053` to `53` in `etc/appserver/appserver.xml` by changing the `port` [server parameter](#server-parameters)
2. Restart appserver.io with the default [start/stop scripts]({{ "/get-started/documentation/basic-usage.html#start-and-stop-scripts" | prepend: site.baseurl }})

The DNS server should now listen to port `53` and should be ready to resolve DNS queries. To make sure everything works invoke the following command

```sh
dig @127.0.0.1 example.dev
```

trying to resolve the DNS entry for the appserver.io example application from the DNS server. The result should look like

```sh
; <<>> DiG 9.8.3-P1 <<>> @127.0.0.1 example.dev
; (1 server found)
;; global options: +cmd
;; Got answer:
;; ->>HEADER<<- opcode: QUERY, status: NOERROR, id: 42150
;; flags: qr rd; QUERY: 1, ANSWER: 1, AUTHORITY: 0, ADDITIONAL: 0
;; WARNING: recursion requested but not available

;; QUESTION SECTION:
;example.dev.           IN  A

;; ANSWER SECTION:
example.dev.        300 IN  A   127.0.0.1

;; Query time: 6 msec
;; SERVER: 127.0.0.1#53(127.0.0.1)
;; WHEN: Sat Apr  2 11:49:31 2016
;; MSG SIZE  rcvd: 56
```

The DNS server is now completely set up. Finally it is necessary that the OS is aware of the nameserver. So, how to configure a nameserver on the various operating systems? Each OS has it's own solution, so it's not possible to list them all here. To give you an idea, we described a solution for Mac OS X here.

Mac OS X provides a [resolver](https://developer.apple.com/library/mac/documentation/Darwin/Reference/ManPages/man5/resolver.5.html), that can be configured either by a file `/etc/resolve.conf` or by specifying additional DNS nameserver in files below the directory `/etc/resolver`. The seconde approach is the preferred one, because the `/etc/resolv.conf` file is autogenerated and will be overwritten when someone makes changes in the Mac OS X network preferences.

To let the appserver.io DNS server resolve all domain names with the suffix `.dev`, e. g. `example.dev` a file `/etc/resolver/dev` with the following content

```sh
nameserver 127.0.0.1
```

has to be created. This can be done by invoking the following command on the console

```sh
$ sudo sh -c " echo 'nameserver 127.0.0.1' >> /etc/resolver/dev"
```

Vòila, pointing the browser to `http://example.dev:9080` should now open the appserver.io example application!