# PHP Application Server

The objective of the project is to develop a multi-threaded application server for PHP, written in PHP. Yes, pure PHP! You think we're serious? Maybe! But we think, in order to enable as many developers in our great community, this will be the one and only way to enable you helping us. Through the broadest possible support of the PHP community we hopefully establish this solution as the standard for enterprise applications in PHP environment.

# Highlights

* Servlet engine, with full HTTP 1.1 support
* Session beans (stateful, stateless + singleton)
* Message beans
* Doctrine as standard Persistence provider
* Timer service
* Integrate message queue
* Web services
* Cluster functionality

# Technical Features

* Joe Watkins (https://github.com/krakjoe/pthreads) phtreads Library is used 
* DI & AO  usage within the respective container
* Use of annotations to configure beans
* Configuration by Exception (optional Usage of Deployment Descriptor possible)

The implementation of a Web application and its operation in the PHP Application Server must be as simple as possible. For this purpose, whenever possible, the utilization of standard solution based on existing components as a, such as Doctrine, are used. On the other hand, with the paradigm Configuration by exception, the operation of an application with a minimum of configuration is needed. So a lot of the use cases is already covered by the default behavior of the respective integrated components so that the developer often does not need declarative configuration information.To appeal to the widest possible community the architecture of the Application Server must be constructed so that as large a number of existing applications can easily be migrated via adapter. Furthermore, the future development of Web applications based on all relevant PHP frameworks by providing libraries is supported.

# Requirements

* PHP 5.4+ on x64 or x86
* ZTS Enabled (Thread Safety)
* Posix Threads Implementation
* Memcached (2.1+)

The lastest version is only tested with Mac OS 10.8+ and Debian Wheezy. PHP Application Server should run on any PHP version from 5.3+. However segmentation faults occurred in various tests with PHP 5.3.x repeatedly. Meanwhile this can lead to the early development stage of the pthreads library. We actually use PHP 5.5.+ for development.

# Installation

Actually we support Mac OS X Mountain Lion and Debian Wheezy. We also plan to release a Windows installer and a RPM package as soon as possible but as we're only Mac users we'll be happy if someone is out there to support us with that stuff. Finally it's possible to build the runtime by yourself. This can be done by cloning our runtime environment project (https://github.com/techdivision/TechDivision_Runtime). We've added two ANT targets `create-pkg` and `create-deb` that should do the stuff for you.

## Installation on Mountain Lion

To install on your Mac OS X Mountain Lion please download the actual .pkg Package from http://www.appserver.io. 
After downlaod the .pkg you can start installation process with a double click on the package. To install the 
software you need to have administration privileges (sudo). After the installation process, that is really simple, 
you'll find the Application Server software in the folder /opt/appserver. 

When the installation has been finished the Application Server will be started automatically. If you need to restart 
the Application Server, after you've deployed a new app for example, you can use the init scripts `sbin/appserverctl` 
and `sbin/memcachectl` therefore. Both accept start | stop | restart as parameter.

Start your favorite browser and open the URL `http://127.0.0.1:8586/demo` to load the demo application.

## Installation on a Debian Wheezy

If you're on a Debian system you don't need to download the .deb package. Follow these instructions:

```
root@debian:~# echo “deb http://deb.appserver.io/ wheezy main” >> /etc/apt/sources.list
root@debian:~# wget http://deb.appserver.io/appserver.gpg
root@debian:~# cat appserver.gpg | apt-key add -
root@debian:~# aptitude update
root@debian:~# aptitude install appserver
```

This will install the Application Server in directory `/opt/appserver`. Also it'll be started automatically, but you 
can start, stop or restart it with the init-script `/etc/init.d/appserver` stop | start | restart. Additionally it is 
necessary that the memcached daemon has been started BEFORE the Application Server will be started itself.

After installation you can open a really simply example app with your favorite browser open the URL 
`http://127.0.0.1:8586/demo`.
