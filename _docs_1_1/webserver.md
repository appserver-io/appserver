---
layout: docs_1_1
title: Webserver
meta_title: appserver.io webserver
meta_description: appserver.io comes with a integrated webserver. This is build and configured like any other server component using our multithreaded server framework.
position: 40
group: Docs
subNav:
  - title: Connection Handler
    href: connection-handler
  - title: Server Modules
    href: server-modules
  - title: Virtual Hosts
    href: virtual-hosts
  - title: Environment Variables
    href: environment-variables
  - title: Headers
    href: headers
  - title: Authentications
    href: authentications
  - title: Accesses
    href: accesses
  - title: File Handlers
    href: file-handlers
  - title: Locations
    href: locations
  - title: Rewrites
    href: rewrites
  - title: Auto Index
    href: auto-index
  - title: Proxy
    href: proxy
permalink: /get-started/documentation/1.1/webserver.html
---

The Webserver is built and configured like any other server component using our
[multithreaded server framework](<https://github.com/appserver-io/server>). Let's have a look at the main configuration of the server component itself.

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

In the following section, the server params are discussed.

```xml
<params>
    <param name="admin" type="string">info@appserver.io</param>
    <param name="software" type="string">appserver/1.1.1 (linux) PHP/5.5.19</param>
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
    <param name="welcomePageTemplatePath" type="string">resources/templates/www/welcome.phtml</param>
</params>
```

They are used to define several key/value pairs for the Webserver implementation. Some of them are common to all HTTP servers. Their descriptions can be found [within the server configuration documentation]({{ "/get-started/documentation/configuration.html#server-configuration" | prepend: site.baseurl }})

Descriptions for webserver specific params are available below.

| Param                     | Type     | Description                                                    |
| ------------------------- | ---------| ---------------------------------------------------------------|
| `documentRoot`            | string   | Defines the root directory for the server to append the URI to and search for the requested file or directory. The document root path is relative to the servers root directory, if there is no beginning slash "/" |
| `directoryIndex`          | string   | Whitespace separated list of index resources to look up the requested directory. The server will return the first one that is found. If none of the resources exist, the server will respond with a 404 Not Found. |
| `keepAliveMax`            | integer  | The number of requests allowed per connection when keep-alive is on. If it is set to 0 keep-alive feature will be deactivated. |
| `keepAliveTimeout`        | integer  | The number of seconds waiting for a subsequent request while in keep-alive loop, before closing the connection. |
| `errorsPageTemplatePath`  | string   | The path to the errors page template. The path is relative to the server's root directory if there is no beginning slash "/". |
| `welcomePageTemplatePath` | string   | The path to the welcome page template. The path is relative to the server's root directory if there is no beginning slash "/". |

If you want to setup a HTTPS Webserver, you have to configure some more params.

| Param                | Type     | Description |
| -------------------- | ---------| ------------|
| `certPath`           | string   | The absolute/relative (to the server's root directory) path to your certificate file, which has to be a combined PEM file of private key and certificate. |
| `privateKeyPath`     | string   | The absolute/relative (to the server's root directory) path to the SSL certificate's private key file. |
| `dhParamPath`        | string   | The absolute/relative (to the server's root directory) path to the Diffie Hellmann param file. |
| `passphrase`         | string   | The passphrase you have created your SSL private key file with. It can be optional. |
| `cryptoMethod`       | string   | The crypto method(s) to use., default's to TLS 1.0, 1.1 + 1.2 |
| `peerName`           | string   | Peer name to be used. If this value is not set, then the name is guessed based on the hostname used when opening the stream. |
| `verifyPeer`         | boolean  | Flag to enable/disable peer verification, default's to `FALSE`, because appserver.io is coming with a self signed SSL certificate. |
| `verifyPeerName`     | boolean  | The flag to enable/disable peer name verification, default's to `FALSE`, because appserver.io is coming with a self signed SSL certificate. |
| `allowSelfSigned`    | boolean  | Flag to allow/disallow self signed SSL certificates, default's to `TRUE` to support appserver.io's self signed SSL certificate. |
| `disableCompression` | boolean  | The flag to disable TLS compression, default's to `TRUE`. This can help mitigate the CRIME attack vector. |
| `honorCipherOrder`   | boolean  | The flag to control cipher ordering preferences during negotiation has to be allowed, default's to `TRUE`. |
| `ecdhCurve`          | string   | The curve to use with ECDH ciphers, if not specified prime256v1 will be used. |
| `singleEcdhUse`      | boolean  | The flag to enable/disable generation of a new key pair in scenarios where ECDH cipher suites are negotiated (instead of the preferred ECDHE ciphers), default's to `TRUE`. |
| `singleDhUse`        | boolean  | The flag to enable/disable generation of a new key pair when using DH parameters (improves forward secrecy), default's to `TRUE`. |
| `ciphers`            | string   | Sets the list of available ciphers. The format of the string is described in [ciphers](https://www.openssl.org/docs/manmaster/apps/ciphers.html#CIPHER_LIST_FORMAT) and defaults to `DEFAULT` |

Since version 1.1 there is the possibility to define multiple SSL certificates. Multiple certificates can be enabled on server level by adding a `<certificates/>` node containing a `<certificate/>` node for each certificate you want to add. For example, if you want to add a wildcard certificate for `appserver.local` and `appserver.dev`, the following configuration will be appropriate

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

Do not forget to restart the server after adding the certificates.

> appserver.io comes with a default HTTPS server enabled. Beside the self signed SSL certificate, that will be generated during the first startup, we've tried to configure the HTTPS server for a maximum of security. But there are additional configuration options that allow to improve security, like using a Diffie Hellman parameter and a trusted certificate. To get a free trusted certificate, have a look at [Let's Encrypt](https://letsencrypt.org/), a free, automated and open Certificate Authority.

## Connection Handler

As we want to handle requests based on a specific protocol, the server needs a mechanism to understand and manage
those requests properly.

For our Webserver, we use `\AppserverIo\WebServer\ConnectionHandlers\HttpConnectionHandler`,
which implements the `\AppserverIo\Server\Interfaces\ConnectionHandlerInterface` and follows the [HTTP/1.1 specification](<http://tools.ietf.org/html/rfc7230>), using our [HTTP library](<https://github.com/appserver-io/http>).

```xml
<connectionHandlers>
    <connectionHandler type="\AppserverIo\WebServer\ConnectionHandlers\HttpConnectionHandler" />
</connectionHandlers>
```

> There is the possibility to provide more than one connection handler. In most cases, it wouldn't make sense, because
> you will have to handle another protocol, which might not be compatible with the modules you provided in the same server
> configuration.

## Server Modules

As mentioned in the beginning, we use our [multithreaded server framework](<https://github.com/appserver-io/server>). It allows you to provide modules for request and response processing triggered by several hooks.

The following table gives an overview of the hooks, which are also available in the corresponding dictionary class `\AppserverIo\Server\Dictionaries\ModuleHooks`.

| Hook             | Description |
| ---------------- | ----------- |
| `REQUEST_PRE`    | The request pre hook is used to do something before the request has been parsed. So if there is a keep-alive loop it will be triggered with every request loop. |
| `REQUEST_POST`   | The request post hook is used to do something after the request has been parsed. Most modules such as CoreModule use this hook. |
| `RESPONSE_PRE`   | The response pre hook is triggered before the response is prepared for sending it to the connection endpoint. |
| `RESPONSE_POST`  | The response post hook is the last hook triggered within a keep-alive loop. It executes the module's logic, when the response is well prepared and ready to be dispatched. |
| `SHUTDOWN`       | The shutdown hook is called, whenever a PHP fatal error shuts down the current worker process. In this case, the current filehandler module is called to process the shutdown hook. This enables the module to react on fatal errors. If it does not react to this shutdown hook, the default error handling response dispatcher logic is used. If the module reacts on the shutdown hook and sets the response state to be dispatched, no other error handling shutdown logic will be called to fill up the response. |

The next section elaborates on the list of modules provided for the Webserver by default.

```xml
<modules>
    <!-- REQUEST_POST hook -->
    <module type="\AppserverIo\WebServer\Modules\AuthenticationModule" />
    <module type="\AppserverIo\WebServer\Modules\VirtualHostModule" />
    <module type="\AppserverIo\WebServer\Modules\EnvironmentVariableModule" />
    <module type="\AppserverIo\WebServer\Modules\RewriteMapModule" />
    <module type="\AppserverIo\WebServer\Modules\RewriteModule" />
    <module type="\AppserverIo\WebServer\Modules\DirectoryModule" />
    <module type="\AppserverIo\WebServer\Modules\AccessModule" />
    <module type="\AppserverIo\WebServer\Modules\LocationModule" />
    <module type="\AppserverIo\WebServer\Modules\AutoIndexModule" />
    <module type="\AppserverIo\WebServer\Modules\CoreModule" />
    <module type="\AppserverIo\WebServer\Modules\PhpModule" />
    <module type="\AppserverIo\WebServer\Modules\FastCgiModule" />
    <module type="\AppserverIo\WebServer\Modules\ProxyModule" />
    <!-- RESPONSE_PRE hook -->
    <module type="\AppserverIo\WebServer\Modules\HeaderModule" />
    <module type="\AppserverIo\WebServer\Modules\DeflateModule" />
    <!-- RESPONSE_POST hook -->
    <module type="\AppserverIo\Appserver\Core\Modules\ProfileModule"/>
</modules>
```

For every hook, all modules are processed in the same order as they are listed in the xml configuration.
> The order of the modules provided by the default configuration is planned and should not be changed for security reasons. For example, if you position the AccessModule before the RewriteModule, it is possible to cancel out an access rule with a rewrite rule.

Our Webserver provides an interface called `\AppserverIo\WebServer\Interfaces\HttpModuleInterface`, which has to be implemented by every module.

All modules are described in the overview below.

| Module                      | Description |
| --------------------------- | ----------- |
| `VirtualHostModule`         | Provides virtual host functionality, which allows you to run more than one hostname (such as yourname.example.com and othername.example.com) on the same server, while having different params and configurations. |
| `AuthenticationModule`      | Offers the possibility to secure resources using basic or digest authentication based on request URI with regular expression support. |
| `EnvironmentVariableModule` | This module enables you to manipulate server environment variables. They can be set conditionally, unset and copied in form of an OS context. |
| `RewriteModule`             | A simple rewrite module for PHP based web servers, which use a self-made structure for usable rules. It can be used similar to Apaches mod_rewrite and provides rewriting and redirecting capabilities. |
| `DirectoryModule`           | Provides for "trailing slash" redirects and serving directory index files. |
| `AccessModule`              | Allows an HTTP header based access management with regular expression support. |
| `CoreModule`                | HTTP server features, which are always available, such as serving static resources and finding defined file handlers. |
| `PhpModule`                 | Acts like a classic PHP Webserver module (such as `mod_php` for apache) which calls and runs your requested PHP scripts in an isolated context with all globals (such as `$_SERVER`, `$_GET`, `$_POST` etc.) prepared in the common way. |
| `FastCgiModule`             | The Module allows you to connect several FastCGI backends (such as `php-fpm` or `hhvm`) based on configured file-handlers. |
| `ProxyModule`               | A classic proxy module that allows to make indirect network connections to other network services using userdefined upstream logic. |
| `ServletEngine`             | The ServletEngine introduces a super fast and simple way to implement an entry point to handle HTTP requests, which allows you to execute all performance critical tasks. Please see [Servlet Engine](<{{ "/get-started/documentation/servlet-engine.html" | prepend: site.baseurl }}>) for full documentation. |
| `HeaderModule`              | Overrides response headers via configuration |
| `DeflateModule`             | It provides the `deflate` output filter that enables output from your server to be compressed before being sent to the client via the network. |
| `ProfileModule`             | Allows request based realtime profiling using external tools like logstash and kibana. |

## Virtual Hosts

When using virtual hosts, the default server configuration can be extended and a host specific
environment to run your hostname or app can be produced.

This is done by adding a virtual host configuration to your global server configuration file. See
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

The configuration above is positioned within the server element and opens up the virtual host `example.local`,
which has a different document root than the global configuration. The virtual host is born.

> Most of the `params` that are available in the `server` node can be overwritten. Also, you can define all the following
> configurations like [Environment Variables](<#configure-your-environment-variables>), [Authentications](<#configure-authentications>), [Accesses](<#configure-accesses>) and of course [Locations](<#configure-locations>) for every virtual host.

The `virtualHost` element can hold params, rewrite rules or environment variables which are only
available for the specific host.

The following examples should help you to configure your legacy application with default settings usually
provided with the applications .htaccess files.

### Magento

```xml
<virtualHost name="magento.dev magento.local">
    <params>
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
```

### Neos

```xml
<virtualHost name="neos.dev neos.local">
    <params>
        <param name="documentRoot" type="string">webapps/neos-1.0.2/Web</param>
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

### ORO CRM

```xml
<virtualHost name="oro-crm.dev oro-crm.local">
    <params>
        <param name="documentRoot" type="string">webapps/crm-application/web
        </param>
    </params>
    <rewrites>
        <rewrite condition="-f" target="" flag="L" />
        <rewrite condition="^/(.*)$" target="app.php" flag="L" />
    </rewrites>
</virtualHost>
```

### Wordpress

```xml
<virtualHost name="wordpress.local">
    <params>
        <param name="documentRoot" type="string">webapps/wordpress</param>
    </params>
</virtualHost>
```

## Environment Variables

You can set environment variables using either the global or the virtual host based configuration.
The example below shows the basic usage of environment variables in XML format.

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

There are several ways of using this feature. You can get a rough idea by having a
look at Apache modules [mod_env](<http://httpd.apache.org/docs/2.2/mod/mod_env.html>) and
[mod_setenvif](<http://httpd.apache.org/docs/2.2/mod/mod_setenvif.html>) which we adopted.

You can make definitions of environment variables dependent on REGEX based conditions, which are performed on so called backreferences. These backreferences are request related server variables
like `HTTP_USER_AGENT`.

A condition has the format `<REGEX_CONDITION>@$<BACKREFERENCE>`. If the condition is empty the
environment variable will be set every time.

The definition is `<NAME_OF_VAR>=<THE_VALUE_TO_SET>`. It has
the following conditions:

- Setting a var to `null` will unset the variable if it existed before
- You can use backreferences for the value you want to set as well. They are limited to
  environment variables of the PHP process
- Values will be treated as strings

## Headers

It's possible to override response headers by using the headers module configuration which can be used server-wide,
in virtual-hosts or location based.

```xml
<headers>
    <header type="response" name="Server" value="My Own Server" override="true"/>
    <header type="response" name="X-Powered-By" value="appserver" append="true"/>
</headers>
```

| Param      | Description |
| ---------- | ----------- |
| `type`     | The type represents either the request or the response header. It's only possible to modify the response headers at the moment.  |
| `name`     | The name of the header to modify. |
| `value`    | The value to set for the header given in param name. |
| `override` | If the header should be set even if it exists before modifying it. |
| `append`   | If the header should be appended if the same header exists before modifying it. |

## Authentications

You can setup request URI based basic or digest authentication with regular expression support using authentications.
Here is an example of how to configure basic or digest auth.

```xml
<authentications>
    <authentication uri="^\/auth\/basic\/.*">
        <params>
            <param name="type" type="string">
                \AppserverIo\Http\Authentication\BasicAuthentication
            </param>
            <param name="realm" type="string">
                PhpWebServer Basic Authentication System
            </param>
            <param name="file" type="string">
                var/www/auth/basic/.htpasswd
            </param>
        </params>
    </authentication>
    <authentication uri="^\/auth\/digest\/.*">
        <params>
            <param name="type" type="string">
                \AppserverIo\Http\Authentication\DigestAuthentication
            </param>
            <param name="realm" type="string">
                appserver.io Digest Authentication System
            </param>
            <param name="file" type="string">
                var/www/auth/digest/.htpasswd
            </param>
        </params>
    </authentication>
</authentications>
```

As you can see, every `authentication` node has its `URI` attribute. You can use a regular expressions in the attribute to match the request URI. The `URI` attribute also has some params, which are described below.

| Param   | Description |
| ------- | ----------- |
| `type`  | The type represents an implementation of the `\AppserverIo\WebServer\Interfaces\AuthenticationInterface`, which provides specific auth mechanism. You can use the predelivered classes `\AppserverIo\WebServer\Authentication\BasicAuthentication` and `\AppserverIo\WebServer\Authentication\BasicAuthentication` for basic and digest authentication. |
| `realm` | The string assigned by the server to identify the protection space of the request URI. |
| `file`  | The path to your .htpasswd credential file. The path is relative to the server's root directory, if there is no beginning slash "/". |

User credentials can be created with the tool [htpasswd](http://httpd.apache.org/docs/2.2/programs/htpasswd.html), which will work on all supported OSes, except Windows. On Windows there are optional tools available. For example, you can use [.Htaccesstools](http://www.htaccesstools.com/htpasswd-generator-windows/) online to create a ile.

To create a file for HTTP digest authentication, you can use the tool [htdigest](http://httpd.apache.org/docs/2.2/programs/htdigest.html). Again, there is an online [website](http://jesin.tk/tools/htdigest-generator-tool/), which allows you to generate a file that will work on Windows also.

## Accesses

You can easily allow or deny access to resources based on client's HTTP request headers, by setting up accesses within
your server or virtual-host configuration.

```xml
    <accesses>
        <!-- per default allow everything -->
        <access type="allow">
            <params>
                <param name="X_REQUEST_URI" type="string">.*</param>
            </params>
        </access>
    </accesses>
```

All `access` nodes need to have a type, which can be either `allow` or `deny`. To react on specific request headers and
their values, you have to add one `param` node per header. The `name` attribute has to be the request header name and
the value is a regular expression you want to match.

Everything is denied if there are no accesses configured. That's the reason why you'll find an allow all access rule
in the appserver.xml by default. That means you can either allow everything and deny specific things or just allow
specific things. Deny rules will override access rules!

> All request header value checks (means all `param` nodes), given by an `access` node are AND combined.
> The default behavior is to deny everything if there are no accesses configured.

## File Handlers

File handlers are used to define a mapping between specific [Server Modules](<#server-modules>) and requested
resources by their file extensions.

```xml
<fileHandlers>
    <fileHandler name="fastcgi" extension=".php">
        <params>
            <param name="host" type="string">127.0.0.1</param>
            <param name="port" type="integer">9010</param>
        </params>
    </fileHandler>
<fileHandlers>
```

If you use this configuration, a client requesting a resource with the extension `.php` will be processed by the
FastCGI server module. That means, instead of serving the `.php` file as a static resource delivered by the core module, the FastCGI module will process the request by connecting to a FastCGI backend provided in the corresponding `params` node.

| Param  | Description |
| ------ | ----------- |
| `host` | The ip address to the FastCGI backend. |
| `port` | The port to the FastCGI backend. |

> The file handler's name has to be equal to the module's name you want to trigger. So, every module has to implement
> a `getModuleName()` method as defined in `\AppserverIo\Server\Interfaces\ModuleInterface`.

## Locations

Locations are useful, if you want to have other file handlers or if the file handler's configuration was changed on a
certain request URI pattern.

```xml
<locations>
    <location condition="\/test\.php">
        <handlers>
            <handler name="fastcgi" extension=".php">
                <!--
                <params>
                    <param name="host" type="string">127.0.0.1</param>
                    <param name="port" type="integer">9555</param>
                </params>
                 -->
            </handler>
        </handlers>
    </location>
</locations>
```

In this example the `/test.php` script is processed by another FastCGI backend listening on `127.0.0.1:9555`

## Rewrites

Of course rewriting is possible as well. To do so, have a look at this simple but well known rewrite example, where all
requests are moved to an `index.php` script.

```xml
<rewrites>
    <rewrite condition="-f" target="" flag="L" />
    <rewrite condition="^/(.*)$" target="index.php" flag="L" />
</rewrites>
```

All rewrites are based on rewrite rules, which consist of three important parts:

- *condition string* : Conditions, which have to be met to take effect for the rule. See more [here](<#condition-syntax>)

- *target string* : The target to rewrite the requested URI to. Within this string you can use backreferences similar
    to the Apache mod_rewrite module with the difference that you have to use the `$ syntax`
    (instead of the `$/%/%{} syntax` of Apache).
    Backreferences are parts of the matching rule conditions which you specifically pick out via regex.

    *Simple example* : A condition like `(.+)@$X_REQUEST_URI` produces a back reference `$1` with the value `/index`
        for a requested URI `/index`. The target string `$1/welcome.html` results in a rewrite to `/index/welcome.html`

- *flag string* : You can use flags similar to mod_rewrite, which are used to make rules react in a certain way or influence further processing. See more [here](<#flags>)

### Condition Syntax

The Syntax of possible conditions is roughly based on the possibilities of Apache's RewriteCondition and RewriteRule.
To make use of this combination, you can chain conditions using the `{OR}` symbol for OR-combined and the `{AND}`
symbol for AND-combined conditions.
Be aware of the fact that AND takes precedence over OR.
Conditions can either be PCRE regex or certain fixed expressions.
So, a condition string of `([A-Z]+\.txt){OR}^/([0-9]+){AND}-f` matches real files (through `-f`) only if it either begins with numbers or ends with capital letters and the extension .txt.
As you might have noticed: Backslashes do **not have to be escaped**.

You might also be curious about the `-f` condition.
This is a direct copy of Apaches -f RewriteCondition.
We also support several other expressions of regex based conditions, which are:

 - *<<COMPARE_STRING>* : Is the operand lexically preceding `<COMPARE_STRING>`?
 - *><COMPARE_STRING>* : Is the operand lexically following `<COMPARE_STRING>`?
 - *=<COMPARE_STRING>* : Is the operand lexically equal to `<COMPARE_STRING>`?
 - *-d* : Is the operand a directory?
 - *-f* : Is the operand a real file?
 - *-s* : Is the operand a real file of a size greater than 0?
 - *-l* : Is the operand a symbolic link?
 - *-x* : Is the operand an executable file?

If you are wondering what the `operand` might be: it is **whatever you want it to be**.
You can specify any operand you like using the `@` symbol.
All conditions within a rule will use the next operand to their right and if none is given, it will use the requested URI.
For example:

- *`([A-Z]+\.txt){OR}^/([0-9]+)`* Will take the requested URI for both conditions (note the `{OR}` symbol)
- *`([A-Z]+\.txt){OR}^/([0-9]+)@$DOCUMENT_ROOT`* Will test both conditions against the document root
- *`([A-Z]+\.txt)@$DOCUMENT_ROOT{OR}^/([0-9]+)`* Will only test the first one against the document root and the second against the requested URI

You might have noted the `$` symbol before `DOCUMENT_ROOT` and remembered it from the backreference syntax.
That is because all Apache common server vars can be explicitly used as backreferences too.

If this does not work for you, we also have an opposite approach.

All conditions, weather regex or expression based can be negated using the `!` symbol in front of them.
So `!^([0-9]+)` matches all strings, which do NOT begin with a number and `!-d` matches all non-directories.

### Flags

Flags are used to further influence processing.
You can specify as many flags per rewrite as you like, but be aware of their impact.
Syntax for several flags is simple: just separate them with a `,` symbol.
Flags, which might accept a parameter, can be assigned one by using the `=` symbol.
Currently supported flags are:

- *L* : As rules are normally processed one after the other, the `L` flag will make the flagged rule the last one processed
   if matched.

- *NC* : Will force regex conditions to be tested case-insensitive.

- *R* : If this flag is set, we redirect the client to the URL specified in the `target string`. If this is just a URI, we redirect to the same host.
    You might also specify a custom status code between 300 and 399 to indicate the reason for/ the kind of the redirect. Default is `301` aka `permanent`

- *M* : Stands for map. Using this flag, you can specify an external source (have a look at the Injector classes of the Webserver project) of a target map.
    With `M=<MY_BACKREFERENCE>` you specify what the map's index has to match to. This matching is done **only** if the rewrite condition matches and will behave like another condition.

- *NC* : Stands for "no case". The given condition will be checked in a case insensitive manner

### Common examples

The following examples give a solution to some often asked for use cases:

To let every request for actual existing files through, place the following rewrite up front:

```xml
<rewrite condition="-d{OR}-f{OR}-l" target="" flag="L" />
```

Block access to files from other sites aka. hotlinking:

```xml
<rewrite condition="!mydomain@$HTTP_REFERER${AND}\.(gif|jpg|png)" target="/no_hotlinking.html" flag="L" />
```

Simple renaming of directories or URI segments:

```xml
<rewrite condition="^/old-dir/(.*)$" target="/new-dir/$1" flag="L" />
```

Integrating e.g. an `index.php` into your URIs:

```xml
<rewrite condition="(.*){AND}!index\.php" target="/index.php/$1" flag="L" />
```

Redirect from a host starting with `www` to the non-`www` version of the same host.
Note: If not used within a virtual host the conditions should check for the domain name as well.

```xml
<rewrite condition="^www\.@$HTTP_HOST" target="http://mydomain.com$X_REQUEST_URI" flag="R,L" />
```

To redirect all HTTP requests to HTTPS add the following redirect to your HTTP server's configuration:

```xml
<rewrite condition="" target="https://$SERVER_NAME$X_REQUEST_URI" flag="L,R" />
```

## Auto Index

In some cases, you might need a autogenerated directory index on specific folders, like Apache's mod_autoindex provides. Since version 1.1, we've a similar functionality, that allows to enable that on specific directories. The enable the auto index functionality, the following params has to be added to the server configuration

| Param                     | Type     | Description                                                    |
| ------------------------- | ---------| ---------------------------------------------------------------|
| `autoIndex`               | boolean  | Enable auto index module by setting the value to `TRUE`. |
| `autoIndexTemplatePath`   | string   | Defines the root directory for the server to append the URI to and search for the requested file or directory. The document root path is relative to the servers root directory, if there is no beginning slash "/" |

Additionally it is neccessary to specify the location the auto index functionality has to be activated. This can be done by adding a location directive to a virtual host configuration. For example, to activate the auto index functionality on a folder named `static` in the root directory of an application, the following `location` directive has to be added to the virtual host configuration

```xml
<locations>
    <location condition="^\/static\/.*">
        <headers>
            <header type="response" name="X-Powered-By" value="autoIndex" append="true"/>
        </headers>
        <params>
            <param name="autoIndex" type="boolean">true</param>
        </params>
    </location>
</locations>
```

See the [Locations](#locations) directive for more details. Additionally you've the possiblity to specify one or more headers that will be sent with the response to the client when the directory index will be requested.

We've two options, the index of a directory can come from:

* A file located in that directory, typically called index.html. The `directoryIndex` param in the server configuration sets the name of the file or files to be used. This is controlled by the directory module.
* Otherwise, a listing generated by the server, whereas the file extension decides the icon that will be rendered. This is controlled by the auto index module.

The two functions are separated so that you can completely remove (or replace) automatic index generation, if you want to.

## Proxy

The proxy module provides full proxy functionality by supporting userdefined logic in upstream types which can be
used for implementing custom behaviour (e.g. load-balancing, round-roubin etc.).

Any usage of proxy mechanism needs an upstream configuration at servers-level first. This is an example of how to configure an
upstream backend for proxy usage with default upstream implementation ```\AppserverIo\WebServer\Upstreams\DefaultUpstream```
which means simple randomized behaviour between all upstream backend nodes.

```xml
<servers>
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
    <server ...
</servers>
```

There are several parameters available for each upstream backend server as you can see in the example configuration.

| Param         | Description |
| ------------- | ----------- |
| `address`     | The ip address to the backend service. |
| `port`        | The port to the the backend service. |
| `weight`      | The weight of an upstream server as value for sorting etc. |
| `maxFails`    | The max number of fails to accept from upstream server backend. |
| `failTimeout` | The max timeout duration to accept when trying to connect to upstream server backend. |
| `maxConns`    | The max connections to an upstream server backend. |
| `backup`      | Defines the upstream server as backend which will be used when all other backends are down. |
| `down`        | Defines the upstream server to be down per default. |
| `resolve`     | If the upstream logic should resolve dns entries every time a request is processed. |

As the proxy module acts like another file-handler within the server framework we have to use ```locations``` to
make use of those configured upstreams. This examples shows how to activate the proxy module to use the upstream
backend named ```exampleBackend``` given as upstream configuration example above. It'll be trigged by requests to urls
like ```http://localhost/test/test.html```.

```xml
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
```
