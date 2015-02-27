---
layout: docs
title: Webapps
meta_title: appserver.io webapps
meta_description: appserver.io specific applications aka webapps follow certain app principles, find out which 
position: 110
group: Docs
subNav:
  - title: Structure
    href: structure
  - title: Responsibility separation
    href: responsibility-separation
permalink: /get-started/documentation/webapps.html
---

appserver.io introduces the concept of so called `webapps` as a new naming for PHP applications.
This new naming is used to differentiate between what we call `legacy applications`, PHP applications as of the current generation, and applications specifically developed for the appserver.io ecosystem.


> A webapp is a PHP application which can only run within the appserver.io infrastructure as it heavily relies on services provided by it 

The above definition does most likely mean that the application uses [`servlets`]({{ "/get-started/documentation/servlet-engine.html" | prepend: site.baseurl }}) and must therefor be executed within a [`Servlet Engine`]({{ "/get-started/documentation/servlet-engine.html" | prepend: site.baseurl }}).

## Structure

A webapp needs a certain folder structure we assume.
Here we will describe this structure and the background behind it.

Any webapp may contain three directories which are expected to have a certain functionality. 
These three are:

| Name          |  Description                                                                                         |
| --------------| -----------------------------------------------------------------------------------------------------|
| META-INF      | Files with relevance to background services such as the [`Persistence Container`]({{ "/get-started/documentation/persistence-container.html" | prepend: site.baseurl }})   |
| WEB-INF       | Files handling incoming requests and a relevance to services responsible for user interaction such as the [`Servlet Engine`]({{ "/get-started/documentation/servlet-engine.html" | prepend: site.baseurl }})  |
| common        | Files which might be needed by both |

The reason for this separation will be given [further below](#responsibility-separation).

All three directories expect a certain structure for their contained files:

* Classes and other PHP structures MUST be located within a `classes` folder and follow a `PSR-0` compatible folder structure
* All appserver.io specific configuration files MUST be placed as direct children of the structure folder
* All other files can be structured as need be

This results in a folder structure such as:

```
<WEBAPP NAME>
    |
    |- common 
            |- classes
                    |- <PSR-0 path to classes>
    |
    |- META-INF 
            |- classes
            |       |- <PSR-0 path to classes>
            |
            |- <Configuration files>
    |
    |- WEB-INF 
            |- classes
            |       |- <PSR-0 path to classes>
            |
            |- <Configuration files>
    |
    |- vendor
```

The above structure holds true when using the default configuration of the appserver.
If this structure has to be changed for any reason it can be done using the [`application configuration`]({{ "/get-started/documentation/configuration.html#application-configuration" | prepend: site.baseurl }})

## Responsibility separation

As already mentioned [above](#structure), the webapp is separated into different directories.

The directory separation into `META-INF` and `WEB-INF` (and `common` as common ground) serves the purpose of responsibility separation.


> To put it simply we can state that files from `META-INF` get processed by backend services, and `WEB-INF` by services responsible for client interaction.

The purpose of this separation is already hinted at by the use of the term "service" and prepares for a situation where services are spread over a network.

If paying a closer look at [app construction](https://github.com/appserver-io-apps/example) one can see that servlets (`WEB-INF`) and processors (`META-INF`) communicate
over a proxy object.
This proxy allows to communicate using [remote method invocations](http://en.wikipedia.org/wiki/Java_remote_method_invocation) and helps scalability by allowing for asymmetrical scaling of application services.

So a central [Persistence Container]({{ "/get-started/documentation/persistence-container.html" | prepend: site.baseurl }}) could handle several server instances serving servlets and static files to connected clients.
