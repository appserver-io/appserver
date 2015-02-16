---
layout: tutorial
title: My First WebApp
meta_title: Building your first web app on appserver.io
meta_description: How-to-guide for your first web app on appserver.io from the scratch using the Servlet-Engine mixed with Dependency Injection and Session-Bean integration.
description: It gives you a guide how to implement your first webapp with appserver.io
date: 2015-02-11 14:45:00
author: zelgerj
position: 1
group: Tutorials
subNav:
  - title: Prerequirements
    href: prerequirements
  - title: Preparations
    href: preparations
  - title: Hello-World Script
    href: hello-world-script
  - title: Hello-World Servlet
    href: hello-world-servlet
  - title: Using Services
    href: using-services
  - title: Okay thats all folks!
    href: okay-thats-all-folks! 
permalink: /get-started/tutorials/my-first-webapp.html
---
[PSR-0]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md

![My first webapp]({{ "/assets/img/tutorials/my-first-webapp/my-first-webapp-image.jpg" | prepend: site.baseurl }})
***

This tutorial explains how to implement your first webapp on **appserver.io** from scratch using the
**Servlet-Engine** mixed with **Dependency Injection** and **Session-Bean** integration.

<br/>
## Prerequisite

First you will need a running installation of appserver.io *(>= Version 1.0.0-rc3)*. If you are new to this
project you can easily [download](<{{ "/get-started/downloads.html" | prepend: site.baseurl }}>) and follow the
[installation guide](<{{ "/get-started/documentation/installation.html" | prepend: site.baseurl }}>) for your specific OS.

<br/>
## Preparations

At first switch to *dev mode* in our local appserver.io installation. This will set the correct filesystem
permissions for your user account and also let the appserver process itself run as the current user. This will make it easy 
for local development.

```bash
sudo /opt/appserver/server.php -s dev
# Should return: Setup for mode 'dev' done successfully!
```

Now  you are ready to create the webapp called `myapp`

```bash
# Goto appserver.io webapps folder
cd /opt/appserver/webapps/

# Create myapp
mkdir myapp

# Go into myapp
cd myapp

# Open it with your favorite editor if you want to...
pstorm .
brackets .
atom .
```

Keep the webapp under version control from the early beginning, so that you always have a chance to rollback things
and maybe push it finally to GitHub with all the history in it if you want to.

```bash
git init

# Some default git ignore stuff
echo ".idea\n/vendor\ncomposer.lock" > .gitignore

# Do initial commit
git add .
git commit -m "initial commit"
```

## *Hello-World* Script

The simplest way to echo things like *Hello-World* to the client is the way you already know. Using a simple PHP script.
So check if that works in appserver and create a PHP script called `hello.php` directly in the webapps folder `/opt/appserver/webapps/myapp`.

```php
<?php echo "hello i'am a simple php script"; ?>
```

Open the browser at [http://127.0.0.1:9080/myapp/hello.php] and you should get...

![Simple PHP script browser result]({{ "/assets/img/tutorials/my-first-webapp/simple-php-script-browser.png" | prepend: site.baseurl }})
<br/>

Wooow it works... looks great :)

```bash
# Commit the current state via git.
git add .
git commit -m "added hello-world script"
```

<br/>
## *Hello-World* Servlet

Ok now do the same thing using the Servlet-Engine by creating your first simple *Hello-World* Servlet. In the
beginning create the `WEB-INF/classes` folder where all Servlets are. In this folder the namespaces
and classes MUST follow an *autoloading* PSR: [PSR-0].

> [PSR-4] support is comming in one of the next appserver.io releases.

If our vendor name is `MyVendor` the folder should look like `WEB-INF/classes/MyVendor/MyApp`

```bash
mkdir -p WEB-INF/classes/MyVendor/MyApp
```

Finally we introduce our servlet by creating a PHP class file called `HelloServlet.php` in that folder.

```php
<?php

namespace MyVendor\MyApp;

use AppserverIo\Psr\Servlet\Http\HttpServlet;

/**
 * @Route(name="helloWorld", urlPattern={"/hello.do", "/hello.do*"})
 */
class HelloServlet extends HttpServlet
{
    public function doGet($servletRequest, $servletResponse)
    {
        $servletResponse->appendBodyStream("hello i'am a simple servlet");
    }
}
```

Due to the reason that servlets are pre-initialised by the servlet-engine at the appserver.io startup it is necessary to
restart the appserver every time it is needed to test the recent changes. Here is a short overview how to restart.

```bash
# OSX
sudo /opt/appserver/sbin/appserverctl restart

# Debian / Ubuntu / CentOS
sudo /etc/init.d/appserver restart

# Fedora
sudo systemctl restart appserver
```

After the appserver has restarted goto [http://127.0.0.1:9080/myapp/hello.do] and you should get...

![Simple servlet browser result]({{ "/assets/img/tutorials/my-first-webapp/simple-servlet-browser.png" | prepend: site.baseurl }})

Strike! :)

```bash
# Commit the current state via git.
git add .
git commit -m "added hello-world servlet"
```

## Using Services

As the most business logic should be separated into services now *implement a simple `HelloService` which is getting
inject into the `HelloServlet` via [Dependency-Injection](<{{ "/get-started/documentation/dependency-injection.html" | prepend: site.baseurl }}>).
To use Dependency-Injection you have to put the service classes at `META-INF/classes` where the namespaces
and classes MUST also follow an *autoloading* PSR: [PSR-0].

```bash
mkdir -p META-INF/classes/MyVendor/MyApp
```

Let us introduce the `HelloService` which provides the `getHelloMessage()` method that returns the hello-world message
that the servlet should serve to the client. Create a PHP class file called `HelloService.php` in folder `META-INF/classes/MyVendor/MyApp`

```php
<?php

namespace MyVendor\MyApp;

/**
 * @Stateless
 */
class HelloService
{
    public function getHelloMessage()
    {
        return "hello i'am a simple servlet with service usage";
    }
}
```

To inject the `HelloService` into the `HelloServlet` add an annotated property `$helloService` and modify the `doGet`
method to make use of the injected service instance. The `HelloServlet` should now look like this...

```php
<?php

namespace MyVendor\MyApp;

use AppserverIo\Psr\Servlet\Http\HttpServlet;

/**
 * @Route(name="helloWorld", urlPattern={"/hello.do", "/hello.do*"})
 */
class HelloServlet extends HttpServlet
{
    /**
     * @EnterpriseBean(name="HelloService")
     */
    protected $helloService;

    public function doGet($servletRequest, $servletResponse)
    {
        $servletResponse->appendBodyStream(
            $this->helloService->getHelloMessage()
        );
    }
}
```

Restart the appserver again and refresh the browser at [http://127.0.0.1/myapp/hello.do]. Here you go...

![Simple servlet service browser result]({{ "/assets/img/tutorials/my-first-webapp/simple-servlet-service-browser.png" | prepend: site.baseurl }})

And here it is... Your First WebApp on appserver.io!

```bash
# Commit the current state via git.
git add .
git commit -m "added hello-world service and enhanced servlet"
```

> Feel free to enhance it and let us know what you have built upon the next PHP infrastructure!

<br/>
## Annotations! But why? 

To use servlets without configuration, it is necessary to add a `@Route` annotation so the servlet-engine is able to map a specific url to the servlet.

```php
<?php
/**
 * @Route(name="helloWorld", urlPattern={"/hello.do", "/hello.do*"})
 */
 class HelloServlet extends HttpServlet
 ```
 
 This annotation maps the URL `http://127.0.0.1/myapp/hello.do` and `http://127.0.0.1/myapp/hello.do/anything/you/want` to the servlet.
 For more servelt details checkout out [Servlet Engine](<{{ "/get-started/documentation/servlet-engine.html" | prepend: site.baseurl }}>)
 section in our [Documentation](<{{ "/get-started/documentation.html | prepend: site.baseurl }}>)

You also use annotations to use Dependency-Injection. To make our `HelloService` injectable add an annotation
above the class definition. In this case we want to have a stateless Session-Bean so put `@Stateless` to class doc block.

```php
<?php
/**
 * @Stateless
 */
class HelloService
```

To inject our `HelloService` to the `HelloServlet` via [Property-Injection](<{{ "/get-started/documentation/dependency-injection.html#how-to-inject-an-instance" | prepend: site.baseurl }}>) we just have to put the annotation above the member property like this...

```php
<?php
class HelloServlet extends HttpServlet
{
    /**
     * @EnterpriseBean(name="HelloService")
     */
    protected $helloService;
```

<br/>
## Okay, folks that is all!

We hope this tutorial helps you to have a smooth start into the world of **appserver.io webapps**!

Any feedback is appreciated so do not hesitate to share your experiences or any problems you encounter with us. Cheers! :)























