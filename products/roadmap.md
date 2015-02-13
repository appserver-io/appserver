---
layout: default
title: Roadmap
position: 80
group: Products
permalink: /products/roadmap.html
author: all
---

#<i class="fa fa-road"></i> Roadmap
***

## Version 1.1.0 "Iron Knight"
***
* Update PHP to version 5.6
* Upgrade to latest pthreads version
* Optional Integration of Dotdeb-Pakete
* Integration of servlet filters
* Use ACLs to define groups, roles and and users and bind them to the internal naming directory
* Enable authentication agains internal naming directory
* Enable HTTP basic + digest authentication against ACLs
* Use ACLs to authorize users + groups for method/resource usage by annotations or XML configuration
* Container Managed Transactions
* Expose SLSB as SOAP + RESTful Webservice  endpoint
* Integration Speedy/HTTP 2.0
* Integration of generated proxy classes instead of creating them on-the-fly
* Integration of Message-Queue topics
* Integration of Environment variables for Session/Message Driven Beans
* Integration STOMP protocol to send messages
* Implement and integrate HTTP 2.0 protocol
* Replace PHP standard sockets with rockets socket library
* Integration thephpleague/flysystem für VFS und bessere PHPUnit Testbarkeit
* [#363](<{{ "363" | prepend: site.github_issue }}>) - Provide alternative class loader
* [#360](<{{ "360" | prepend: site.github_issue }}>) - Webapp appserver.minimal-version property not used yet
* [#356](<{{ "356" | prepend: site.github_issue }}>) - Webserver has problems with multiple SSL/TLS certificates per server
* [#188](<{{ "188" | prepend: site.github_issue }}>) - Provide an rpm repository
* [#187](<{{ "187" | prepend: site.github_issue }}>) - Functionality to use .htaccess files
* [#186](<{{ "186" | prepend: site.github_issue }}>) - Provide appserver Docker container
* [#184](<{{ "184" | prepend: site.github_issue }}>) - Homebrew installation file
* [#179](<{{ "179" | prepend: site.github_issue }}>) - Standardize Windows builds