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

The next minor release 1.1.0 aka **Iron Knight** will probably be released on **08/2015**. As we actually have no verified timetable for our [EE](<{{ "/products/enterprise-edition.html" | prepend: site.baseurl }}>), this date can change, due to customer needs for EE features.

## Version 1.1.0 **Iron Knight**
***
* Update PHP to version 5.6
* Upgrade to latest pthreads version
* Integration STOMP protocol to send messages
* Enhanced support for different PDO compatible database systems in Datasource integration
* Webapp based virtual host configuration
* Seamless Doctrine integration through Persistence Units
* SSH and telnet management console
* Unix style runlevel system
* Asynchronous deployment of webapps
* Event based server state management
* Webserver AutoIndex module to expose folder contents
* Extended debugability of cached application code
* Webserver capability for multiple SSL/TLS certificates
* Webserver ProxyModule for reverse proxy and load balancing capabilities
* Webserver HeadersModule for configurable default response headers