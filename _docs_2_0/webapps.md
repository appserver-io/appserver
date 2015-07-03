---
layout: docs_2_0
title: Webapps
meta_title: appserver.io webapps
meta_description: appserver.io specific applications aka webapps follow certain app principles, find out which
position: 130
group: Docs
subNav:
  - title: Structure
    href: structure
  - title: Responsibility separation
    href: responsibility-separation
permalink: /get-started/documentation/2.0/webapps.html
---

appserver.io introduces the concept of so-called `webapps` as a new naming for PHP applications.
This new naming is used to differentiate between what we call `legacy applications`, PHP applications as of the current generation, and applications specifically developed for the appserver.io ecosystem.

> A webapp is a PHP application that can only run within the appserver.io infrastructure as it heavily relies on services provided by it.

The definition above means that the application uses [`servlets`]({{ "/get-started/documentation/servlet-engine.html" | prepend: site.baseurl }}) and must therefore be executed within a [`Servlet Engine`]({{ "/get-started/documentation/servlet-engine.html" | prepend: site.baseurl }}).

## Structure

A webapp needs a certain folder structure. We will describe this structure and its background.

Any webapp may contain three directories that are expected to have a certain functionality. These three are the following.

| Name          |  Description                                                                                         |
| --------------| -----------------------------------------------------------------------------------------------------|
| META-INF      | Files with relevance to background services such as the [`Persistence Container`]({{ "/get-started/documentation/persistence-container.html" | prepend: site.baseurl }}).   |
| WEB-INF       | Files handling incoming requests and a relevance to services responsible for user interaction such as the [`Servlet Engine`]({{ "/get-started/documentation/servlet-engine.html" | prepend: site.baseurl }}).  |
| common        | Files which might be needed by both. |

The reason for this separation will be given [further below](#responsibility-separation).

All three directories expect a certain structure for their contained files:

* Classes and other PHP structures MUST be located within a `classes` folder and follow a `PSR-0` compatible folder structure
* All appserver.io specific configuration files MUST be placed as direct children of the structure folder
* All other files can be structured as needed

This results in a folder structure such as the following.

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

The structure above holds true when using the default configuration of the appserver.
If this structure has to be changed for some reason, it can be done by using the [`application configuration`]({{ "/get-started/documentation/configuration.html#application-configuration" | prepend: site.baseurl }})

## Responsibility separation

As mentioned [above](#structure), the webapp is separated into different directories. The directory separation into `META-INF` and `WEB-INF` (and `common` as common ground) serves the purpose of responsibility separation.

> To put it simply, we can state that files from `META-INF` get processed by backend services, and `WEB-INF` by services responsible for client interaction.

The purpose of this separation is hinted at by the use of the term "service" and prepares for a situation in which services are spread over a network.

When having a closer look at [app construction](https://github.com/appserver-io-apps/example) one can see that servlets (`WEB-INF`) and processors (`META-INF`) communicate over a proxy object.
This proxy allows to communicate using [remote method invocations](http://en.wikipedia.org/wiki/Java_remote_method_invocation) and supports scalability by allowing for asymmetrical scaling of application services.

Thus, a central [Persistence Container]({{ "/get-started/documentation/persistence-container.html" | prepend: site.baseurl }}) could handle several server instances serving servlets and static files to connected clients.
