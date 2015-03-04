---
layout: default
title: Roadmap
meta_title: appserver.io roadmap – Only the sky is the limit
meta_description: We´re continously working hard to improve appserver.io. Check out the appserver.io roadmap for detailed informations.
position: 80
group: Products
permalink: /products/roadmap.html
author: all
---

#<i class="fa fa-road"></i> Roadmap
***

The roadmap only covers functionality of upcomming [CE](<{{ "/products/community-edition.html" | prepend: site.baseurl }}>) minor releases. If you wanna have more detailed information (including bugfix releases), please check the issue list of our public <a href="{{ site.github_repository }}"><i class="fa fa-github"></i> GitHub</a> repository.

The next minor release 1.1.0 aka **Iron Knight** will probably released on **05/2015**. As we actually have no verified timetable for our [EE](<{{ "/products/enterprise-edition.html" | prepend: site.baseurl }}>), this date can change, due to customer needs for EE features.

## Version 1.1.0 **Iron Knight**
***
* Update PHP to version 5.6
* Upgrade to latest pthreads version
* Optional make use of [Dotdeb](https://www.dotdeb.org) packages on Debian based linux distributions
* Integration of servlet filter functionality
* Use ACLs to define groups, roles and and users and bind them to the internal naming directory
* Enable authentication against internal naming directory
* Enable HTTP basic + digest authentication against ACLs
* Use ACLs to authorize users + groups for method/resource usage by annotations or XML configuration
* Container Managed Transactions
* Expose SLSB as SOAP + RESTful webservice  endpoint
* Integration Speedy/HTTP 2.0
* Integration of generated proxy classes instead of creating them on-the-fly
* Integration of Message-Queue topics
* Integration of environment variables for Session/Message Driven Beans
* Integration STOMP protocol to send messages
* Implement and integrate HTTP 2.0 protocol
* Replace PHP standard sockets with [rockets](https://github.com/appserver-io-php/rockets) socket library for improved performance and higher flexibility in a multithreaded environment
* Integration [thephpleague/flysystem](https://github.com/thephpleague/flysystem) for VFS and better PHPUnit support
* [#363](<{{ "363" | prepend: site.github_issue }}>) - Provide alternative class loader
* [#360](<{{ "360" | prepend: site.github_issue }}>) - Webapp appserver.minimal-version property not used yet
* [#356](<{{ "356" | prepend: site.github_issue }}>) - Webserver has problems with multiple SSL/TLS certificates per server
* [#188](<{{ "188" | prepend: site.github_issue }}>) - Provide an rpm repository
* [#187](<{{ "187" | prepend: site.github_issue }}>) - Functionality to use .htaccess files
* [#186](<{{ "186" | prepend: site.github_issue }}>) - Provide appserver Docker container
* [#184](<{{ "184" | prepend: site.github_issue }}>) - Homebrew installation file
* [#179](<{{ "179" | prepend: site.github_issue }}>) - Standardize Windows builds