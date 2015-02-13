---
layout: post
title:  Webserver Basics
meta_title: appserver.io webserver basics
meta_description: As you may already know, the appserver has a fully HTTP/1.1 compliant Webserver included which is build upon our multithreaded server framework
date:   2015-01-28 19:24:00
author: zelgerj
categories: [webserver]
---

As you may already know, the appserver has a fully HTTP/1.1 compliant [Webserver](<https://github.com/appserver-io/webserver>)
included which is build upon our [multithreaded server framework](<https://github.com/appserver-io/server>) which
is also available open-source on github as well.

Your vision is to detach Webservers as apache, nginx or lighttpd in long terms by providing the same feature-set and give you the opportunity to enhance our Webserver by just writing PHP code because its all written in PHP.

### How to configure the Webserver?

At the beginning you have to set some main parameters for the server itself as shown in the example taken from
our appserver main https-server configuration.

```xml
<server name="https" ...>
    <params>
        <param name="admin" type="string">info@appserver.io</param>
        <param name="software" type="string">appserver/1.0.0-beta4.19 (linux) PHP/5.5.19</param>
        <param name="workerNumber" type="integer">64</param>
        <param name="workerAcceptMin" type="integer">3</param>
        <param name="workerAcceptMax" type="integer">8</param>
        <param name="transport" type="string">ssl</param>
        <param name="address" type="string">127.0.0.1</param>
        <param name="port" type="integer">9443</param>
        <param name="certPath" type="string">etc/appserver/server.pem</param>
        <param name="passphrase" type="string"></param>
        <param name="documentRoot" type="string">webapps</param>
        <param name="directoryIndex" type="string">index.php index.html index.htm</param>
        <param name="keepAliveMax" type="integer">64</param>
        <param name="keepAliveTimeout" type="integer">5</param>
        <param name="errorsPageTemplatePath" type="string">var/www/errors/error.phtml</param>
    </params>
```

An detailed description of every parameter can be found under [docs](<{{ "/get-started/documentation/webserver.html" | prepend: site.baseurl }}>).

### Virtual Hosts

What would live without having the possibility setting up virtual hosts within a HTTP(S) Server and of course our
Webserver is offering you the ability in a simple but flexible way.

See the simplest virtual host configuration:

```xml
<server name="https" ...>
    ...
    <virtualHosts>
      <virtualHost name="myapp.local">
        <params>
          <param name="documentRoot" type="string">webapps/myapp</param>
        </params>
      </virtualHost>
    </virtualHosts>
```

It will introduce a new virtual host for `myapp.local` with the document root pointing to `webapps/myapp`. You can
imagine that just reseting the document root is not the only thing you can do with the virtual host feature. More
informations on that can be found under [docs](<{{ "/get-started/documentation/webserver.html#virtual-hosts" | prepend: site.baseurl }}>).

### Rewrites

So what are rewrites for? Simply, rewrites are used to rewrite a requested url at server level to give the user the
output for that final page he should see. So, for example, a user may go for `http://mysite.io/home`, but will
really be given `http://mysite.io/index.php/home` by the server. Of course, the user will be none the wiser to this
little bit of chicanery.

A simple rewrite to do this could look like this:

```xml
<server name="https" ...>
    ...
    <rewrites>
        <rewrite condition="-f" target="" flag="L" />
        <rewrite condition="^/(.*)$" target="index.php/$1" flag="L" />
    </rewrites>
```

If you want to know how it will work in detail and whats exactly beyond those attributes `condition`, `target` and
`flag` just have a look to our [docs](<{{ "/get-started/documentation/webserver.html#rewrites" | prepend: site.baseurl }}>).

### Authentications

You want to protect a folder or directory very easy using username and password credentials as you know it from apache
or other Webservers? Just do it using the same `.htpasswd` files and a simple configuration like this:

```xml
<server name="https" ...>
    ...
    <authentications>
        <authentication uri="^\/my\/protected\/folder\/.*">
            <params>
                <param name="type" type="string">
                    \AppserverIo\WebServer\Authentication\BasicAuthentication
                </param>
                <param name="realm" type="string">
                    Basic Authentication System
                </param>
                <param name="file" type="string">
                    var/credentials/.htpasswd
                </param>
        </params>
    </authentication>
```

Checkout our [docs](<{{ "/get-started/documentation/webserver.html#authentications" | prepend: site.baseurl }}>) to get more into
authentications and all the other cool features the Webserver provides you.

We appreciate any feedback So, do not hesitate to share your experiences or any problems you encountered while using it
with us here via comments or better on github via the issue tracker. Cheers :-)
