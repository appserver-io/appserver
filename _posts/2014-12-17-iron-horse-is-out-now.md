---
layout: post
title:  1.0.0-beta2 aka "Iron Horse" is out now
date:   2014-12-17 12:00:00
author: wagnert
version: 1.0.0beta2
categories: [release]
---

You may ask: Why *Iron Horse*? The answer is quite simple!

We chose that name, because it implicates a very important meaning. *Iron* on the one hand, what stands for the
rock solid infrastructure solution we want to provide and *Horse* on the other. *Horse* stands for the hard 
working animal mankind can always rely on since hundreds of years.

Both are a synonym for an infrastructure solution you can build your business critical application on!

Many guys out there, we talked to during the last years, told us: It's not possible to build an infrastructure
solution completely based on PHP: Too slow, too many segfaults and too risky are only some of things we've 
heard.

As developers who love using PHP and who saw the strong need for an application server in many of our projects,
we decided to give it a try.

### Download and install it
***

You can download the last 1.0.0-beta2 release directly from our [releases](https://github.com/appserver-io/appserver/releases) page. For detailed installation instructions, have a look at our [installation](https://github.com/appserver-io/appserver/wiki/01.-Installation) WIKI. After installation open `http://127.0.0.1:9080` with your favorite browser, you should see something like this

![You successfully installed Iron Horse]({{ "/assets/img/ironhorse-installed.png" | prepend: site.baseurl }} "You successfully installed Iron Horse")

Congratulations, you've installed the PHP application server!

### What is an Application Server
*** 

The common definition of what an application server is and what it has to provide is very loose. Therefore it
is worthwile to specify the term closer. For us it will be a server that delivers web pages in most cases.
To do this, we need some kind of web server functionality like Apache or nginx has. But this is by far not
the main purpose an application server should have. Beside the functionality of acting as a web server, it
comes with the following services, we'll focus on in our next blog posts

* [Servlet-Engine](https://github.com/appserver-io/appserver/wiki/05.-Servlet-Engine)
* [Persistence-Container](https://github.com/appserver-io/appserver/wiki/08.-Persistence-Container)
* [Message-Queue](https://github.com/appserver-io/appserver/wiki/09.-Message-Queue)
* [Timer-Service](https://github.com/appserver-io/appserver/wiki/10.-Timer-Service)

On top of these services, it provides client libraries that enables you to use those and extends them with 
functionality, that you know from some of the well known frameworks. To make it short, the application server
comes with

* [Annotations](https://github.com/appserver-io/appserver/wiki/06.-Annotations)
* [Dependendcy Injection (DI)](https://github.com/appserver-io/appserver/wiki/07.-Dependency-Injection)
* [Aspect Oriented Programming (AOP)](https://github.com/appserver-io/appserver/wiki/11.-AOP)
* [Design-by-Contract (DbC)](https://github.com/appserver-io/appserver/wiki/12.-Design-by-Contract)

The services, the client libraries plus these functionality enables you to write fast, scalable and secure
applications. If you want a more detailed description of a service or a functionality click on the item and
have a look at our project [WIKI](https://github.com/appserver-io/appserver/wiki).

### Why should i use an application server?
***

This is a good question and i'll try to explain the most important reasons from our point of view. Actually
there are many good PHP applications out there on their way to conquer the world, that's good and we like
that. Many of those applications are using the LAMP stack and they run well on it.

So if that is true, where is the need for an application server? Let's have a look at the market PHP 
applications are very popular in. Actually we see a lot of small and midrange content management and shop 
systems, bug trackers and other useful tools. But when we look at the financial market, hightech industries
enterprise content management and shop systems, we can't find any noteable PHP applications. One reason is,
that the LAMP stack all the applications are running on, is simply not ready to run enterprise applications.

> Against technologies like Java, where standardized, fast, secure, scalable and certified infrastructure 
> solutions are available, these are completely missing in the PHP ecosystem. It's not the language the 
> frameworks or the tools that lacks for enterprise, it's more the stack the language is running on. That is,
> where an application server like appserver.io will came in. appserver.io will provide exactly this and
> therefor it'll close the gap between language and stack.

We know, that you'll may think why even these guys should worked that out! That's what we're also wondering
about. But looking at PHP history we think one of the main reasons for this is the missing support for
multithreading and in consequence of that, data can not be shared between processes. Since Joe Watkins 
started pthreads this is not longer a restriction and PHP gives you the power to do that. Meanwhile you can
see a growing number of PHP developers that give multithreading a try and help to bring PHP into the next level.

### What is comming next?
***

As we've reached beta state now, we're looking forward to find and fix bugs, implement the last enhancements 
refactor code and write tests. To see what's going on, have a look at our [issues](https://github.com/appserver-io/appserver/milestones/Release%201.0.0.0%20%22Iron%20Horse%22). It'll
help a lot, if you'll give us feedback about your first tries and what you think about appserver.io. If you
have questions, feel free to contact us in our public [HipChat](http://appserver.io/community/contributing.html) channel.

We'll try to finished the first stable 1.0.0 aka "Iron Horse" in early january!
