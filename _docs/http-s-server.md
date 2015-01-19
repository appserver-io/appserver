---
layout: docs
title: HTTP(S) Server
position: 40
permalink: /docs/http-s-server.html
---

> [Configure a Virtual Host](#configure-a-virtual-host)
> [Configure your Environment Variables](#configure-your-environment-variables)  

The configuration itself is mostly self-explanatory so just have a look to the preferred config
file and try to change settings. 
Please make sure you restart the appserver after making changes. :)
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