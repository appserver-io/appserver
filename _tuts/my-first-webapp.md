---
layout: tutorial
title: My first webapp
description: It gives you a guide how to implement your first webapp on appserver.io
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
permalink: /get-started/tutorials/my-first-webapp.html
---
[PSR-0]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md

![My first webapp]({{ "/assets/img/tutorials/my-first-webapp/my-first-webapp-image.jpg" | prepend: site.baseurl }})
***

This tutorial will show you how to implement your first webapp on **appserver.io** from the scratch using the
**Servlet-Engine** mixed with **Dependency Injection** and **Session-Bean** integration.

<br/>
## Prerequirements

Of course you'll need a running installation of appserver.io *(>= Version 1.0.0-rc3)*. If you are new to this
project you can easily [download](http://127.0.0.1:4000/get-started/downloads.html) it and follow the
[installation guide](http://127.0.0.1:4000/get-started/documentation/installation.html) for your specific OS.

<br/>
## Preparations

At first we have to switch to *dev mode* in our local appserver.io installation. This will set the correct filesystem
permissions for your user account and also let the appserver process itself run as your user which it a lot easier for
local development.

```bash
sudo /opt/appserver/server.php -s dev

# Should return: Setup for mode 'dev' done successfully!
```

Now we're ready to create our webapp called `myapp`

```bash
# Goto appserver.io webapps folder
cd /opt/appserver/webapps/

# Create myapp
mkdir myapp

# Go into myapp
cd myapp

# If you would like to use PHPStorm, you can easily open it right now with
pstorm .
```

Lets keep our webapp under version controler from the early beginning, so you always have the chance rollback things
and maybe push it finally to github with all the history in it.

```bash
git init

# Lets do some default git ignore stuff
echo ".idea\n/vendor\ncomposer.lock" > .gitignore

# Do initial commit
git add .
git commit -m "initial commit"
```

## *Hello-World* Script

The simplest way to echo things like *Hello-World* to the client is the way you already know. Using a PHP script.
So let's create one called `hello.php` directly in our webapps folder `/opt/appserver/webapps/myapp`.

```php
<?php echo "hello i'am a simple php script"; ?>
```

Open your browser at [http://127.0.0.1/myapp/hello.php] and you should get...

![Simple PHP script browser result]({{ "/assets/img/tutorials/my-first-webapp/simple-php-script-browser.png" | prepend: site.baseurl }})
<br/>

Commit the progress state via git.

```bash
git add .
git commit -m "added hello-world script"
```

<br/>
## *Hello-World* Servlet

Ok let's do the same thing using the Servlet-Engine by creating your first simple *Hello-World* Servlet. In the
beginning we need to create the `WEB-INF/classes` folder where all Servlets have to be. In this folder the namespaces
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

Due to the reason that servlets are pre-initialised by the servlet-engine at the appserver.io startup we have to
restart the appserver process every time we wanted to test our recent changes. Here's a short overview for all systems.

```bash
# OSX
sudo /opt/appserver/sbin/appserver restart

# Debian / Ubuntu / CentOS
sudo /etc/init.d/appserver restart

# Fedora
sudo systemctl restart appserver
```

After the appserver has restarted we can goto [http://127.0.0.1/myapp/hello.do] and should get...

![Simple servlet browser result]({{ "/assets/img/tutorials/my-first-webapp/simple-servlet-browser.png" | prepend: site.baseurl }})

Strike! :) Commit the progress state via git.

```bash
git add .
git commit -m "added hello-world servlet"
```

## Using Services

As the most business logic should be separated into services we'll implement a simple `HelloService` which we want to
inject into our `HelloServlet` via [Dependency-Injection](<{{ "/get-started/documentation/dependency-injection.html" | prepend: site.baseurl }}>).
To use Dependency-Injection we have to put our service classes at `META-INF/classes` where the namespaces
and classes MUST also follow an *autoloading* PSR: [PSR-0].

```bash
mkdir -p META-INF/classes/MyVendor/MyApp
```

Let's introduce our `HelloService` which provides the `getHelloMessage()` method which returns the hello-world message
the servlet should serve to the client. Create a PHP class file called `HelloService.php` in folder `META-INF/classes/MyVendor/MyApp`

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

To inject the `HelloService` into our `HelloServlet` we have to add a Setter-Injection method and modify the `doGet`
method to make usage of the injected service instance. The `HelloServlet` should now look like this...

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
    public function setFileService($helloService)
    {
        $this->helloService = $helloService;
    }

    public function doGet($servletRequest, $servletResponse)
    {
        $servletResponse->appendBodyStream(
            $this->helloService->getHelloMessage()
        );
    }
}
```

Restart the appserver again and refresh our browser at [http://127.0.0.1/myapp/hello.do]. Here we go...

![Simple servlet service browser result]({{ "/assets/img/tutorials/my-first-webapp/simple-servlet-service-browser.png" | prepend: site.baseurl }})

Hint about the annotations and link to docu...






















