---
layout: tutorial
title: Webserver - making the switch to appserver.io
description: A guide on how to making the switch to the appserver.io webserver
date: 2015-05-04 14:45:00
author: wick-ed
position: 2
group: Tutorials
subNav:
  - title: Prerequisite
    href: prerequisite
  - title: Installation
    href: installation
  - title: Basic configuration
    href: basic-configuration
  - title: App placement
    href: app-placement
  - title: Virtual hosts
    href: virtual-hosts
  - title: Rewrites
    href: rewrites
  - title: App security
    href: app-security
  - title: Development tools
    href: development-tools
  - title: PHP configuration
    href: php-configuration
  - title: Done!
    href: done!
permalink: /get-started/tutorials/webserver-making-the-switch.html
---

This tutorial shows how to successfully making the switch from a more commonly used webserver such as [apache httpd](http://httpd.apache.org/) or [nginx](http://nginx.org/) to the [webserver integrated with appserver.io](https://github.com/appserver-io/webserver).
This includes setting up your development environment as well as porting your applications.

## Prerequisite

You will need to have access to a plattform the appserver.io runtime is able to run on or have a thread-safe compiled PHP runtime (version >= 5.4) including the [pthreads extension](https://github.com/krakjoe/pthreads) at your hands.

This tutorial does assume basic knowledge about the functionality of a webserver and often makes references to componentes of other webservers.

## Installation

To make the switch to the appserver.io webserver it has to be installed first.
To do so there are two possibilities:

* Obtain the [webserver source](https://github.com/appserver-io/webserver) and run it with any compatible PHP runtime
* Install appserver.io as a whole and use the webserver as an integrated server component

We would recommend the later and style this tutorial in this way.
Installing the complete appserver.io product has the advantage of already including [a proper PHP runtime]({{ "/get-started/documentation/runtime-environment.html" | prepend: site.baseurl }}) and also offers [init scripts]({{ "/get-started/documentation/basic-usage.html#start-and-stop-scripts" | prepend: site.baseurl }})
to controll the services with.

Installation of appserver.io is explained [here]({{ "/get-started/documentation/installation.html" | prepend: site.baseurl }}).
After installation you should be able to access the webserver under http://127.0.0.1:9080/ and receive a welcome page.

And once done we can already start with the switching. 

## Basic configuration

### Accessibility

Most clients do assume port `80` as default for the `HTTP` protocol and port `443` for HTTPS.
To avoid collions with any other webservers you might have installed appserver.io does not use these default ports on initial start-up.

This behaviour can be changed within the appserver.io configuration file `appserver.xml`.
> To avoid collisions when changing the ports stop (and preferably disable) any service or application blocking port 80 and 443.

Second thing that is hindering accessibility of the webserver is the default reachability for localhost only.
As you might want to use tools like [vagrant](https://www.vagrantup.com/) or want to deploy apps on a remote server you have to make the webserver accessibly from a remote location.
To change this we also have to make a small adjustment to the `appserver.xml` file.

Below is the header of the HTTP server (determined by the `connectionHandler` node and the `transport` param node) where this two adjustments have to be made.

```xml
<server
        name="http"
        type="\AppserverIo\Server\Servers\MultiThreadedServer"
        worker="\AppserverIo\Server\Workers\ThreadWorker"
        socket="\AppserverIo\Server\Sockets\StreamSocket"
        requestContext="\AppserverIo\Server\Contexts\RequestContext"
        serverContext="\AppserverIo\Server\Contexts\ServerContext"
        streamContext="\AppserverIo\Server\Contexts\StreamContext"
        loggerName="System">
    <params>
        <param name="admin" type="string">info@appserver.io</param>
        <param name="transport" type="string">tcp</param>
        <param name="address" type="string">127.0.0.1</param>
        <param name="port" type="integer">9080</param>
        <param name="workerNumber" type="integer">16</param>
        <param name="workerAcceptMin" type="integer">3</param>

        <!-- ... -->

    <connectionHandlers>
        <connectionHandler type="\AppserverIo\WebServer\ConnectionHandlers\HttpConnectionHandler" />
    </connectionHandlers>

    <!-- ... -->
```

So to make the server accessible change the port to 80 and the address to your externally reachable IP or `0.0.0.0` to listen to any address.

```xml
<param name="address" type="string">0.0.0.0</param>
<param name="port" type="integer">80</param>
```

> Repeat for every server you want to make more generally accessible 

This is also documented in the [server accessibility docs]({{ "/get-started/documentation/basic-usage.html#service-availability" | prepend: site.baseurl }}).

### Daemon availability

Per default none of [the appserver daemons]({{ "/get-started/documentation/basic-usage.html#start-and-stop-scripts" | prepend: site.baseurl }}) will come back up if your host environment will restart.
To change this we have to enable their automatic restart behaviour.
This has to be done depending on the operating system you are using.

For most `Unix` like OSs we offer [init scripts]({{ "/get-started/documentation/basic-usage.html#start-and-stop-scripts" | prepend: site.baseurl }}) to do so.
Under `Windows` you have to use the service management tool provided by the OS.

## App placement

Most webservers do have a so called default `DocumentRoot` which is the directory the server will look for requested resources. This directory does vary depending on the operating system and webserver you use.
Common examples are:

- `/var/www`
- `C:\wamp\www\`
- `/usr/local/apache/share/htdocs`

With the appserver this will always be something below the `webapps` directory which is located in the root of your appserver installation.
So default would be:

- `/opt/appserver/webapps` (Unix like systems)
- `C:\Program Files\appserver\webapps` (Windows)

Besides the different directory paths there are not much differences.
The appserver.io webserver will serve every app or resource present under its document root.

> To make your app show in the browser simply symlink or copy it into the `webapps` directory

We assume you symlinked the sources of your app `myapp` from your development directory to `webapps/myapp` within your appserver root.

Have a look! 
127.0.0.1/myapp should show your app now.

## Virtual hosts

[Virtual hosts](http://en.wikipedia.org/wiki/Virtual_hosting) enable you to easily access different applications under their respective (domain) name, make app specific configurations and structure your work environment.
Today several frameworks and applications do require, or at least encourage the usage of virtual hosts.

Below are two examples of virtual hosts as configured for the most common webservers in the field: apache and nginx

First up is apache:
```bash
<VirtualHost *:80>
    DocumentRoot /www/myapp
    ServerName myapp.dev
    ServerAlias www.myapp.dev
</VirtualHost>
```

The basic virtual host config of apache above does state four things

* Address and port combination the host is listening on
* The document root for this host
* The name of the virtual host as well as possible aliases
* Any more host specific configurations 

A basic nginy configuration does contain similar elments.

```bash
 server {
    listen   80;
    root /var/www/myapp;
    index index.html index.htm;
    server_name myapp.dev www.myapp.dev;
}
```

If you want to configure a similar virtual host for our webserver you will find nearly the same elements within our [virtual host configuration]({{ "/get-started/documentation/webserver.html#virtual-hosts" | prepend: site.baseurl }})
within the `etc/appserver/conf.d/virtual-hosts.xml` file.
An example can be seen here:

```xml
<virtualHost name="myapp.dev www.myapp.dev">
  <params>
    <param name="admin" type="string">admin@myapp.dev</param>
    <param name="documentRoot" type="string">webapps/myapp</param>
  </params>
</virtualHost>
```

The most basic informations, virtual host name(s) and document root, are present as well as the possibility to override server configuration for one particular host.
But what about listening port and address?

appserver.io has [an explicit idea of components, contexts and their hierarchy]({{ "/get-started/documentation/architecture.html#the-context-hierarchy" | prepend: site.baseurl }}) and enforces the technical separation of concerns.
Therfore connection handling is solely done by [configured servers]({{ "/get-started/documentation/architecture.html#server-context" | prepend: site.baseurl }}) and virtual hosts do depend on the server context they live in.
This has the benefit of making virtual hosts reusable independently of server configuration.

This also explains why virtual hosts aren't configured within the `appserver.xml` main configuration file but rather their own, as it would lead to duplicate virtuals hosts if these have to be available for different servers.
So configure your virtual hosts within separate files and reference them within the server you want to use them using [XInclude](http://en.wikipedia.org/wiki/XInclude) as seen in the following example:

```xml
<server
        name="https"
        type="\AppserverIo\Server\Servers\MultiThreadedServer"
        ... >

    <!-- ... -->
    
    <!-- include of virtual host configurations for this server -->
    <xi:include href="conf.d/virtual-hosts.xml"/>
    
    <!-- ... -->
```

The concept of having dedicated folders `sites-available` and `sites-enabled` is not supported by the appserver.io webserver and has to be managed using the inclusion of different configuration snippets as shown above.

> To apply any virtual host configurations you have to restart the server

## Rewrites

[Rewrites](http://en.wikipedia.org/wiki/Rewrite_engine) are a commonly used technique for in-application routing based on client input and app layout.
A lot of applications make use of this technique and it is therefor [tightly integrated with the appserver.io webserver]({{ "/get-started/documentation/webserver.html#rewrites" | prepend: site.baseurl }}).

There are different takes on where to configure rewrites for a certain application.
The famous apache `.htaccess` mechanism enables rewrite configurations to be contained within the app as well as within the server configuration, whereas nginx only allows for central configuration.

The appserver.io webserver does not offer any functionality for in-app configuration as well. So all rewrites have to be configured within the [server or virtual host context]({{ "/get-started/documentation/architecture.html#the-context-hierarchy" | prepend: site.baseurl }}).

appserver.io rewrite engine does try to adopt certain functionalities of the [apache rewite engine](http://httpd.apache.org/docs/2.0/misc/rewriteguide.html) but with a simpler and shorter syntax.

A sample apache rewrite might look like the snippet below:

```bash
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_URI} !=/favicon.ico
RewriteRule ^ index.php [L]
```

It contains two directives (`RewriteCond` and `RewriteRule`) which do have a different syntax on their own and build up a stack of conditions ending with the actual condition of the `RewriteRule` directive at the end of the stack.
The additional `RewriteCond` directive allows for the usage of backreferences, making server variables accessibly for rewrite queries.

´´´bash
rewrite ^(/download/.*)/media/(.*)\..*$ $1/mp3/$2.mp3 last;
rewrite ^(/download/.*)/audio/(.*)\..*$ $1/mp3/$2.ra  last;
return  403;
´´´

The appserver.io rewrite engine does offer the same power and flexibility as apache rewites togehter with the simplicity of the nginx rewrites.
The same rewrite 

´´´xml
<rewrites>
    <rewrite condition="!-d{AND}!-f{AND}!^/favicon.ico" flag="L" target="/index.php"/>
</rewrites>
´´´

> Configuration changes do as always require a restart of the server

## App security

## Development tools

### Xdebug

If you are using the appserver.io runtime then you are already provided with a very common debugging tool: [`Xdebug`](http://xdebug.org/)
The Xdebug extension is already installed within the appserver.io runtime and can be enabled by simply editing the `etc/conf.d/xdebug.ini` file to make it look like the following:

```bash
zend_extension = /opt/appserver/lib/php/extensions/no-debug-zts-20131226/xdebug.so
```

## PHP configuration

Most webservers do use [FastCGI](http://en.wikipedia.org/wiki/FastCGI) to connect to a PHP backend which does the actual processing of PHP content.
Changing configuration values for this PHP backend is done using the known `php.ini` configuration file.

The location of the `php.ini` file does sometimes vary but can be simply queried e.g. with `php -i | grep php.ini`.

Keeping this in mind there is a speciality for appserver.io.
Whether you installed the complete appserver.io appserver or are using the webserver as a standalone solution the fact that your webserver is now written in PHP remains.

This means there are two different PHP processes running:

* The webserver within its runtime environment
* Your application within the PHP FastCGI backend (presumably `php-fpm`)

This duplication does result in the presence of two separate `php.ini` files:

* `etc/php.ini` for the appserver.io runtime environment
* `etc/php-fpm-fcgi.ini` (or your FastCGI backend of choice) for your application

> It is important to know where changes should take effect, to know where to change them

## Done!

Congratulations on successfully making the switch to appserver.io as your webserver of choice!
We know there is a lot more to it but there also is a lot [more to learn about our webserver]({{ "/get-started/documentation/webserver.html" | prepend: site.baseurl }}), so keep at it. :)
We hope you enjoyed this tutorial and keep on using appserver.io components.

Every feedback is appreciated so please do not hesitate to share experiences or any issue you may encounter with us.
