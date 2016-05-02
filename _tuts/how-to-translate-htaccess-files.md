---
layout: tutorial
title: Translate .htaccess files
meta_title: How to translate .htaccess files for appserver.io
meta_description: A basic tutorial on how to translate real life .htaccess files based on a given example
description: A basic tutorial on how to translate real life `.htaccess` files based on a given example.
position: 15
group: Tutorials
subNav:
  - title: A general note on configuration
    href: a-general-note-on-configuration
  - title: Rewrites and redirects
    href: rewrites-and-redirects
permalink: /get-started/tutorials/how-to-translate-htaccess-files.html
---

A basic tutorial on how to translate real life `.htaccess` files based on a given example.
At the end of this tutorial one should be able to understand the differences in between `.htaccess` files and the appserver configuration and be able to translate one into another.

## A general note on configuration

### Configuration distribution

There is a general difference of how `.htaccess` based configuration and the appserver configuration is distributed.
`.htaccess` files come with the application they are configuring the web infrastructure for and can be hidden within the applications source tree.
This is a very powerful approach, as it allows applications to manage their own configuration and also handle location or path based configuration in a graceful way.

The appserver configuration on the other hand is in its essence centralized within the configuration files located in the `etc` directory.
Most notebly the `appserver.xml` file and any files included using [XInclude](https://en.wikipedia.org/wiki/XInclude).
Though there is the possibility to include virtual host (and therefor most likely application based) configurations into your webapp, the configuration will always be centralized in a certain file at a certain place.
This makes configuration easy to look at but creates the need for refering locations within the application right there within the configuration itself.

### Modules and their activation

No matter what module the configuartion addresses, the module has to be in use for the config to make any sense.
Therefor `.htaccess` files include conditional brackets used to only make use of certain parts of their content if a needed module is loaded within the webserver.
An example for the default rewrite engine would look like this:

```bash
<IfModule mod_rewrite.c>
	# the rewrite configuration 
</IfModule>
```

In appserver.io this is not necessary as configurations of disabled modules are simply ignored.
If a module is disable though it can be enabled with a simple line of `XML` as such:

```xml
<module type="\AppserverIo\WebServer\Modules\RewriteModule"/>
```

Just add a module element within the `modules` element of the webserver your app will get served by and the module will pick up the configuration after a restart of the appserver.


## Rewrites and redirects

As first and foremost we will cover how to translate everything `mod_rewrite` would handle in an `Apache` based setup.

### Enable the rewrite engine

`.htaccess` files contain the following line as a global switch to rewrite runtime processing:

```bash
RewriteEngine on
```

This is something one can completely ignore as appserver.io does not have such a switch. Either the [rewrite module](https://github.com/appserver-io/webserver/blob/master/src/AppserverIo/WebServer/Modules/RewriteModule.php) is loaded, or it is not. If it is, all rewrite rules will be processed.

### The rewrite base

The [`RewriteBase directive`](http://httpd.apache.org/docs/current/mod/mod_rewrite.html#rewritebase) allows to specify a prefix which will be at the beginning of each rewrite target: 

```bash
RewriteBase /myApp/
```

In appserver.io there is no such thing. If a certain prefix is required it has to be explicitly named at the beginning of each rewrite target:

```xml
<rewrite condition="document/(.+)" target="/myApp/archive/docs/$1" flag="L" />
<rewrite condition="^/(.*)" target="/myApp/index.php?target=$1" flag="L" />
```

### Conditions and rules

In `.htaccess` files the main workhorse of rewrites are the [`RewriteCond`](http://httpd.apache.org/docs/current/mod/mod_rewrite.html#rewritecond) and [`RewriteRule directive`](http://httpd.apache.org/docs/current/mod/mod_rewrite.html#rewriterule).
Both have their specialities but roughly said the `RewriteCond` has a more elaborate set of conditions to check for when determining if a rewrite rule should be applied.
A `RewriteCond` can use different backreferences as strings to test certain patterns against. This as well as special pattern flags allow for very specific conditions.
The syntax of a `RewriteCond` is as follows:

```bash
RewriteCond <TEST_STRING> <PATTERN>
```

For an overview of the special pattern flags and test string backreferences please have a look at [the Apache manual](http://httpd.apache.org/docs/current/mod/mod_rewrite.html#rewritecond).

The `RewriteRule` itself is somewhat more limited in its use as [the syntax](http://httpd.apache.org/docs/current/mod/mod_rewrite.html#rewriterule) allows only for a regex condition, a substitute to rewrite to if the rule will be applied and a manyfold of flags on how exactly to apply the rule.

```bash
RewriteRule <PATTERN> <SUBSTITUTE> [<FLAGS>]
```

Together with the `RewriteCond` directive the rewrite rule builds a stack of conditions and actions to apply if all or any of these conditions is matched (they can be `AND` and `OR` combined).
So both directives work together to allow for very diverse and powerful conditions:

```bash
RewriteCond "%{REMOTE_HOST}"  "^host1"  [OR]
RewriteCond "%{REMOTE_HOST}"  "^host2"  [OR]
RewriteCond "%{REMOTE_HOST}"  "^host3"
RewriteRule .* /special_page.html
```

The above example stack would return special landing page for any of the remote hosts (server variable backreference) `host1`, `host2` or `host3` no matter what resource they request (the `.*` regex).

So pretty cool stuff. :)


The [appserver.io rewrite module](https://github.com/appserver-io/webserver/blob/master/src/AppserverIo/WebServer/Modules/RewriteModule.php) is in principle based on the techiques and elements described above, but tries to simplify and shorten things significantly.

Instead of two different directives with different possibilities in condition creation and chaining, we have one central directive which includes both, a single condition and the rule to apply together with optional flags.

A syntax example would look like this:

```xml
<rewrite condition="<PATTERN>" target="<SUBSTITUTE>" flag="<FLAGS>" />
```

So how should this be able to substitute for the might that is Apaches rewrite engine?
By offering powerful backreferencing, a combination of special pattern flags (in fact the same as Apache has), regex and the possibility to chain everything logically.

To see an example we translate the above condition/rule stack into an appserver.io rewrite rule: 

```xml
<rewrite condition="^host1{OR}^host2{OR}^host3@$REMOTE_HOST" target="/special_page.html" flag="" />
```

So what do we have here? 
Basically the three `RewriteCond` directive's patterns, `OR` combined targeting the remote host server variable backreference and a rewrite to a special landing page.
This is nothing out of the ordinary but written in a more compact way.

As the condition is tightly packed so is the actual test string.
Apache rewrite conditions need to explicitly mention against what the condition pattern is matched, and the `RewriteRule` directive implies a check against the requested path.
For appserver.io rewrites the same is true, only that test strings and their backrefernces don't have to be mentioned for all the involved patterns.
They are rather read from left to right as "Pattern 1 to x match against backreference y." depending where the backreference is placed within the string.

The below example has the same result as the one above, but all backreferences are explicitly mentioned:

```xml
<rewrite condition="^host1@$REMOTE_HOST{OR}^host2@$REMOTE_HOST{OR}^host3@$REMOTE_HOST" target="/special_page.html" flag="" />
```

This also works for different backreferences:

```bash
RewriteCond "%{REMOTE_HOST}"  "^host1"  [OR]
RewriteCond "%{REMOTE_HOST}"  "^host2"
RewriteCond "%{HTTP_REFERER}"  "^host3"
RewriteRule .* /special_page.html
```

Here either `host1` or `host2` coming from `host3` will match. In appserver.io syntax this would look like the following:

```xml
<rewrite condition="^host1{OR}^host2@$REMOTE_HOST{AND}^host3@$HTTP_REFERER" target="/special_page.html" flag="" />
```

The same thing just more compact and easily integrated into the appserver.io `XML` configuration structure.


### Full example

See the below example for the `.htaccess` files within the [Magento 2 eCommerce software](https://github.com/magento/magento2).

The Magento 2 `.htaccess` file looks like this:

```bash
############################################
## enable rewrites

    Options +FollowSymLinks
    RewriteEngine on

############################################
## you can put here your magento root folder
## path relative to web root

    #RewriteBase /magento/

############################################
## workaround for HTTP authorization
## in CGI environment

    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

############################################
## TRACE and TRACK HTTP methods disabled to prevent XSS attacks

    RewriteCond %{REQUEST_METHOD} ^TRAC[EK]
    RewriteRule .* - [L,R=405]

############################################
## redirect for mobile user agents

    #RewriteCond %{REQUEST_URI} !^/mobiledirectoryhere/.*$
    #RewriteCond %{HTTP_USER_AGENT} "android|blackberry|ipad|iphone|ipod|iemobile|opera mobile|palmos|webos|googlebot-mobile" [NC]
    #RewriteRule ^(.*)$ /mobiledirectoryhere/ [L,R=302]

############################################
## never rewrite for existing files, directories and links

    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-l

############################################
## rewrite everything else to index.php

    RewriteRule .* index.php [L]
```

The appserver.io equivalent would look the following:

```xml
<rewrites>
	<rewrite condition="!^/mobiledirectoryhere/.*${AND}android|blackberry|ipad|iphone|ipod|iemobile|opera mobile|palmos|webos|googlebot-mobile@$HTTP_USER_AGENT" target="/mobiledirectoryhere/" flags="L,NC,R=302" />
	<rewrite condition="!-f{AND}!-d{AND}!-l" target="/index.php" flags="L" />
</rewrites>

<environmentVariables>
    <environmentVariable condition="" definition="HTTP_AUTHORIZATION=$HTTP_AUTHORIZATION" />
</environmentVariables>
```

There are several things to note when looking at this translation.
Apache's `mod_rewrite` has rather wide spread responsibilities and possibilities the appserver.io rewrite module does not have.
At appserver.io we follow the principle of separation of concerns and the usage of small and specialized libraries and components.
Therefor some things might look different for this configuration, but lets get started:

> As mentioned above, there is no `RewriteBase` within appserver.io, so if one would want one it had to be added to all rewrite target strings.

An example would be:

```xml
<rewrite condition=".*" target="/magento/index.php" flags="L" />
```

> We have a separate module for environment variable control

In Apache, `mod_rewrite` can also set environment variables. Within appserver.io there is a separate module to do so: [the environment variable module](https://github.com/appserver-io/webserver/blob/master/src/AppserverIo/WebServer/Modules/EnvironmentVariableModule.php);
Hence the usage of the `<environmentVariable />` element.

> No non-rewrite status codes

The `.htaccess` file shows a "405 - Method not allowed" reaction on `TRACE` OR `TRACK` request methods. As the appserver.io rewrite module will only handle status codes which actually are relevant for rewriting and redirecting (300 to 308, potentially 399) we are not able to use it for such access control. We trust application code to filter such requests and to react accordingly by itself.
This is pretty simple, as e.g. the [routlt framework](https://github.com/appserver-io/routlt) will automatically react with a 405 status code on requests using a method an URI endpoint is not configured for.


