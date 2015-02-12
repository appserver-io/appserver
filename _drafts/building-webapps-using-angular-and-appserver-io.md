---
layout: tutorial
title: Building WebApps with AngularJS and appserver.io
description: A guide how to build a single page app with AngularJS and appserver.io
date: 2015-02-13 14:45:00
author: zelgerj
position: 5
group: Tutorials
subNav:
  - title: Prerequirements
    href: prerequirements
  - title: Preparations
    href: preparations
permalink: /get-started/tutorials/building-webapps-using-angular-and-appserver-io.html
---
![Building WebApps with AngularJS and appserver.io]({{ "/assets/img/tutorials/building-webapps-using-angular-and-appserver-io/angular_and_appserver.jpg" | prepend: site.baseurl }})
***

This tutorial shows how to build a webapp using AngularJS as single page app in the frontend and **appserver.io** as a restful service backend
using **Design by Contract** for automated input validation and **AOP** for json output formating and ACL integration.

<br/>
## Prerequirements

A running installation of appserver.io *(>= Version 1.0.0-rc3)*. If you are new to this
project you can easily [download](http://127.0.0.1:4000/get-started/downloads.html) it and follow the
[installation guide](http://127.0.0.1:4000/get-started/documentation/installation.html) for your specific OS.

You also need to have your system prepared for Javascript, HTML and CSS/SASS development. We will generate our AngularJS app using [Yeoman](http://yeoman.io) which allows us to kickstart an AngularJS app, prescribing best practices and tools to help you stay productive.

So please check out and follow the [Instructions](http://yeoman.io/codelab/setup.html) at Yeoman guide to setup your system correctly.

<br/>
## Preparations

At first we have to switch to *dev mode* in our local appserver.io installation. This will set the correct filesystem
permissions for your user account and also let the appserver process itself run as current user which makes it a lot easier
for local development.

```bash
sudo /opt/appserver/server.php -s dev
# Should return: Setup for mode 'dev' done successfully!
```

Now we're ready to create our webapp called `myapp`