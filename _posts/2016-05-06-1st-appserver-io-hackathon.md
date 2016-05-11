---
layout: post
title: 1st appserver.io hackathon
meta_title: Community - 1st appserver.io hackathon at april 23rd
meta_description: On april 23rd, the 1st appserver.io hackathon took place at the TechDivision GmbH office
date: 2016-05-06 10:00:00
author: wagnert
categories: [community]
---

The very first appserver.io Hackathon took place at the TechDivision GmbH Office at April 23rd, to April 24th, 2016.

## Participants

During this first Event of his kind at the **appserver.io** Ecosystem, the Appserver Team members Johann Zelger, Bernhard Wick and Tim Wagner, could gladly welcome nine more attendees of 3 external Companys.

* Michael Döhler (Intellishop AG)
* Martin Mohr (Intellishop AG)
* Hans Höchtl (Onedrop Solutions)
* Jens Scherbl (Freiberuflicher Softareentwickler)
* Peter Höcherl (TechDivision GmbH)
* Ilya Shmygol (TechDivision GmbH)
* Bernhard Wick (TechDivision GmbH)
* Lars Röttig (TechDivision GmbH)
* Johann Zelger (TechDivision GmbH)
* Tim Wagner (TechDivision GmbH)

![Team Brainstorming]({{ "/assets/img/posts//appserver_20160423_0022.jpg" | prepend: site.baseurl }} "Hans Höchtl + **appserver.io** Team Brainstorming")

## Process

After a 1 hour Introduction to the current State of [appserver.io](http://www.appserver.io) and some Project Historie, the Attendees worked out some interesting Topics to work on at the Hackathon.
Therefore, the Attendees split up to Teams to implement the Topics. 

### Application Environments

Hans Höchtl and  Bernhard Wick picked a Feature wish coming from the Team [8select](http://www.8select.de) with Andreas Klaiber regarding a current [Issue](https://github.com/appserver-io/appserver/issues/940) for realisation. 
On this occasion, it is primarily about the integration of different Application Environments for Development, Staging and Production Context of use. The developer, therefore, will have the possibility to create own specific configuration files for every context of use depending on a surroundings variable during a boot process. 

We hope to finish this on time so we can deliver the feature with the next version 1. 1. 1 which gets released in April. 

![Application Environments]({{ "/assets/img/posts//appserver_20160423_0027.jpg" | prepend: site.baseurl }} "Hans Höchtl + Bernhard Wick")

### Windows Azure Cluster Environment

A team all around Michael Döhler dealt with the construction of cluster surroundings in [Microsoft Azure](https://azure.microsoft.com/) based on the most reduced **appserver.io**-infrastructure and [Ansible](https://www.ansible.com/).
 
Another goal of the team consisted of replacing the Standard Servlet Engine with a web server module which permits the integration and use of any Frameworks based on the appserver.io [Webservers](https://github.com/appserver-io/webserver), a lightweight multithreaded web server, written in PHP for PHP. 

As a Proof-of-Concept a minimum, functioning Servlet engine which implements a rudimentary Session-Handling on the base of Redis was implemented. The result [Bitbucket Repository](https://bitbucket.org/michaeldoehler/appserverhackathon) can be used as a base for own projects.  

### Console Implementation für appserver.io Configuration

After installing the current Intellishop Version, Martin Mohr was concentrating on implementing a [Console Implementation](https://github.com/mohrwurm/appserver-io-cli/) wich allows managing the Settings on a locale instance of **appserver.io**. In line with the Hackathon, Martin accomplished to implement a restart command, a command to create or remove a server as well as a command for adjusting Parameters. First Approaches for the Scaffolding are already existing.

![Working on a console configuration tool]({{ "/assets/img/posts//appserver_20160423_0026.jpg" | prepend: site.baseurl }} "Martin Mohr, Tim Wagner + Ilya Shmygol")

### mod_include

After a short settling-in period Ilya Shmygol dealt with the Implementation of a [Server-Side-Include](https://en.wikipedia.org/wiki/Server_Side_Includes) module which orientates itself in his functionality strongly by Apache [mod_include](http://httpd.apache.org/docs/current/mod/mod_include.html).

After initial problems, the first barriers could be taken fast and the base functionality of a web server module has been implemented. Ilya will finish the minimum required functionality in the course of the next weeks. The module should be available approximately in one of the next versions of the Community web server and complements the web server around a new and exciting feature. 
 
### fhreads und rockets

Already at the end of the last year Johann Zelger began with the work on [fhreads](https://github.com/appserver-io-php/fhreads) and [rockets](https://github.com/appserver-io-php/rockets).
 
fhreads will define Multithreading in PHP entirely in a new way, contrary to Joe Watkins [phtreads](https://github.com/krakjoe/pthreads) elected approach, as far as possible to carry out the implementing to a user-defined PHP class.

The Library represents a wrapper for the POSIX Thread function implementation, classes like Threads, Mutex and Conditions, however, are implemented via PHP. PHP Developers therefore have substantially deeper ideas of the Internals and can implement required functionality if needed.  Additionally, the Library lifts, for developers PHP hardly understandable, restrictions on the use of objects to be synchronised.

With fhreads, therefore,  all objects are automatically synced, doesn't matter if it was coded with C or as a user-defined PHP class. The internal, performance-critical serialisierung of objects, the handing over of user-defined PHP class objects exclusively as a copy as well as the restriction that objects must not have Closures are omitted as well.

The same is valid for the Handling of resources, for example, Sockets, until now one of the biggest problems for implementing of server applications. rockets will become a lightweight and multithreading compatible *Next-Generation* Socket applying with the use of simple integer value references instead of heavyweight ignoring to Threads complicated manageable PHP Resources.

The current problems have been thereby solved to one by the use of Sockets in multithreaded surroundings, to the other complicated implementing like multithreaded Websocket server becoming much easier and compelling and necessary features like HTTP / 2  are even usable from now on.

Within the scope of the Hackathons, Johann Zelger could adapt fhreads and rocket to the most topical version PHP and publish the topical state on Github. Currently, both Libraries are still in the beta phase, can be downloaded for test purposes, however, anytime and be tried out. About feedback, we are always grateful of course. See [fhreads library](https://github.com/appserver-io/fhreads).

### Conclusion

Because of the knowledge state concerning appserver. io with the participants on a different level, it was tried to provide the necessary knowledge for adaptations, new development or advancement within the scope of the Hackathons in concentrated form. On account of the results and the feedback of the participants, one can assume from the fact that this has succeeded. 

About the whole Hackathon away has appeared that the web server, as a base from **appserver**. io will move in future more in the centre of the project. On one hand, because this simplifies the ideal entrance point for already existing projects no matter whether explains in the form of a server module or own server implementing, to the other because of the natural component oriented construction the entrance in **appserver.io** ecosystem is enormous simplyfied. Additionally, the topic Microservices presents **appserver.io** as a lightweight alternative to Apache and Nginx. So, if needed a [Microservice](https://en.wikipedia.org/wiki/Microservices) or the [SCS](https://en.wikipedia.org/wiki/Self-contained_Systems) **appserver.io** can be delivered bundled as well.
 
After Saturday afternoon and Sunday morning, it was strictly worked on the implementation of the discussed features; the participants could present on Sunday afternoon her results. Even if none of the features became completely finished, the Hackathon has shown that in relatively short time and partly without foreknowledge, in the shortest amount of time substantial features can be implemented which looks very promising to consistent advancement. 
 
Final agreed on all participants that the first Hackathon has given on the one hand a lot of pleasure. Otherwise, the project moved further step forwards. The **appserver.io** team will try to organise such events in future in the distance of six months, a step in the right direction, therefore, in particular with the Community. 