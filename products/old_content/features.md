---
layout: default
title: Features
position: 60
group: What is appserver.io
permalink: /what-is-appserver-io/features.html
author: all
---

## Features
***

appserver.io comes with its own runtime environment, containing PHP in version 5.5 extended with several third party
libraries, PHP extensions and service daemons. This idea of a self-containing environment makes appserver.io an
out-of-the-box runtime environment for PHP development but has a downside with the specific setup. Mainly the usage
of a multithreading environment is seen critically, as it is considered to break the so called
[shared nothing concept](https://en.wikipedia.org/wiki/Shared_nothing_architecture)
of PHP as it allows for inter-process communication on object level.

Together with the bundled runtime, appserver.io tries to bundle administration by offering a central admin backend
which is capable of managing features provided by the middleware as well as managing application deployment and status.
As of version 0.6.0 application management is implemented to the full extent and allows for easy drag and drop
deployment of applications. Additional features such as virtual host management, logging, dashboards and extended
clustering and deployment options are planned for the near future.

As another concept besides known sandboxed PHP applications appserver.io offers the usage of
[servlets](https://en.wikipedia.org/wiki/Servlet), objects which are persistent in between client requests.
These are able to, in theory, yield big performance gains as repeated bootstrapping of applications is avoided,
but they need the wrapping of these bootstrap parts to make use of the servlet concept.

As the project's Java role model, appserver.io offers several services which can be used individually by internal
and external applications. As a core concept of application servers, these services are organized in a modular way.
<p><br/></p>

Below is a list of features an appserver.io installation provides (some of them usable as standalone products):

 * Webserver
 * Persistence Container
 * Message Queue
 * Servlet Engine
 * Data Grid
 * Design by Contract support
 * Deployment API
 * Integrated FastCGI client
<p><br/></p>

As described above, appserver.io offers enterprise features which have to be payed for.
These include features not present in the community edition like:

 * Clustering functionality
 * An Application firewall
 * Application snapshotting
 * Load balancing
 * Hot backup
<p><br/></p>

Check out our roadmap for further informations on planned features and the next big thing.