---
layout: default
title: For Developers
position: 10
group: What is appserver.io
permalink: /what-is-appserver-io/for-developers.html
author: all
---

## appserver.io for developers
***

We want to provide you with the world´s leading PHP infrastructure - powerful, flexible, secure and easy to use!
And we want to build it completely in PHP and as an open source project! You think we´re crazy?
No, we´re not - we´ve only high targets and from a technical perspective there are no boundaries anymore....

Some of you might think: What the hell are they doing? Why building an application server like JBoss from Java
within PHP? Well, we believe that some aspects from Java are really smart and can also be adapted into the
PHP World giving us more possibilities, security and performance! And in our point of view PHP is in the meantime
far more than "just" a scripting language....

Download it - test it and tell us your thoughts, wishes, improvements etc. - or help us by contributing to our
project! We´re looking forward to any support from your side!

![appserver.io stack]({{ "/assets/img/appserver-stack.jpg" | prepend: site.baseurl }} "appserver.io stack")

### Technical aspects
***

 * Servlet engine, with full HTTP 1.1 support
 * Web Socket engine, based on Ratchet
 * Session beans ( stateful, stateless + singleton )
 * Message beans
 * Doctrine as standard Persistence provider
 * Timer service
 * Integrated message queue
 * Web services
 * Cluster functionality
 * Hot deployment of Web Apps (Mac OS X and Debian only)

### Technical Features
***

The implementation of a Web application and its operation in the PHP Application Server must be as simple as possible.
For this purpose, whenever possible, the utilization of standard solution based on existing components as a,
such as Doctrine, are used. On the other hand, with the paradigm Configuration by exception,
the operation of an application with a minimum of configuration is needed. So a lot of the use cases
is already covered by the default behavior of the respective integrated components so that the developer
often does not need declarative configuration information.To appeal to the widest possible community
the architecture of the Application Server must be constructed so that as large a number of existing
applications can easily be migrated via adapter. Furthermore, the future development of Web applications
based on all relevant PHP frameworks by providing libraries is supported.

 * Usage of Joe Watkins pthreads library
 * Usage of DI & AO within the respective container
 * Usage of annotations to configure beans
 * Configuration by exception (optional usage of deployment descriptor possible)
 * PHP 5.4+ on x64 or x86
 * ZTS Enabled (Thread Safety)
 * Posix Threads Implementation
 * Memcached (2.1+)

The lastest version is only tested with Mac OS 10.8+ and Debian Wheezy. PHP Application Server should run on any
PHP version from 5.3+. However segmentation faults occurred in various tests with PHP 5.3.x repeatedly.
Meanwhile this can lead to the early development stage of the pthreads library.
We actually use PHP 5.5.+ for development.

Download the [latest release of appserver.io]({{ "/downloads.html" | prepend: site.baseurl }})
for Linux, Mac or Windows and start experimenting with the most powerful PHP infrastructure in the world!