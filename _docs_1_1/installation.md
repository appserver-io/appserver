---
layout: docs_1_1
title: Installation
meta_title: appserver.io installation
meta_description: Besides supporting several operating systems and their specific ways of installing software, we also support several ways of getting this software.
position: 10
group: Docs
subNav:
  - title: Mac OS X
    href: mac-os-x
  - title: Windows
    href: windows
  - title: Debian
    href: debian
  - title: Fedora
    href: fedora
  - title: CentOS
    href: centos
  - title: Building it yourself
    href: building-it-yourself
  - title: Using Docker
    href: using-docker
permalink: /get-started/documentation/1.1/installation.html
---

In addition to supporting several operating systems and specific ways of installing the respective software, we'll
also demonstrate how to get an appserver.io package. You can do any of the following:

* Download one of our [releases](http://appserver.io/get-started/downloads.html) right from our download page, which provide tested install packages.
* Get any of our [developer builds](http://builds.appserver.io/) from our project page to get the most bleeding edge install packages (possibly including minor issues)
* Build your own packages using [ANT](http://ant.apache.org/) as described [below](#building-it-yourself)

The package is installed with the following basic default characteristics:

* Install directory: `/opt/appserver` (`C:\Program Files\appserver` on Windows)
* Autostart after installation, no autostart on reboot
* Reachable under pre-configured ports as described [here]({{"/get-started/documentation/configuration.html#configuration-defaults" | prepend: site.baseurl }})

For OS specific steps and characteristics review the tested environments in the section below.

> We **STRONGLY** recommend to have a look at our [upgrade guides](https://github.com/appserver-io/appserver/search?utf8=%E2%9C%93&q=UPGRADE+in%3Apath&type=Code), before upgrading any previous installation.

## Mac OS X

> Runs and tested on Mac OS X 10.8.x and higher.

For Mac OS X > 10.8.x we provide a `.pkg` file for [download]({{"/get-started/downloads.html#osx" | prepend: site.baseurl }}), which contains the `runtime` and the `distribution`. A double-click on the `.pkg` triggers the installation process.

Optionally you can install the appserver using [Homebrew Cask](http://caskroom.io/) as we do offer an appserver cask now.
To do so use the following command:

```bash
brew cask install appserver
```

## Windows

> Runs and tested on Windows 7 (32-bit) and higher.

As we deliver the Windows application server as a .exe file, which packages everything, there are no further dependencies.
You can [download]({{"/get-started/downloads.html#win" | prepend: site.baseurl }}) it and install it on your machine, as you would with other software.

appserver.io will be added as a service daemon bundle to your Windows service management tool.

Alternatively, we provide a JAR file which you can use if you have an installed Java Runtime Environment (or JDK
that is). It offers the same functionality as the EXE file, but might be preferred by some. If the Java requirement is met, you can start the installation by simply double-clicking the .jar archive.

## Debian

> Runs and tested on Debian Squeeze (64-bit) and higher.

If you are on a Debian system you might also try our `.deb` repository:

```
root@debian:~# echo "deb http://deb.appserver.io/ wheezy main" > /etc/apt/sources.list.d/appserver.list
root@debian:~# wget http://deb.appserver.io/appserver.gpg -O - | apt-key add -
root@debian:~# aptitude update
root@debian:~# aptitude install appserver-dist
```

Optionally you can [download]({{"/get-started/downloads.html#debian" | prepend: site.baseurl }}) the `.deb` files for `runtime` and `distribution`. A double-click triggers the installation process. Doing this will invoke the system default package manager and guides you through the installation process.

> Please install the runtime first, as it is a dependency of the distribution.

## Fedora

> Runs and tested on version Fedora 20 (64-bit).

We  also provide `.rpm` [files for Fedora]({{"/get-started/downloads.html#fedora" | prepend: site.baseurl }}), one for `runtime` and `distribution` for download. A double-click triggers the installation process. Doing this will invoke the system default package manager and will guide you through the installation process.

> Please install the runtime first, as it is a dependency of the distribution.

## CentOS

> Runs and tested on CentOS 6 (64-bit).

Installation and basic usage are the same as on Fedora, but we [provide different packages]({{"/get-started/downloads.html#centos" | prepend: site.baseurl }}) for `runtime` and `distribution`. CentOS requires additional repositories like [remi](http://rpms.famillecollet.com/) or [EPEL](http://fedoraproject.org/wiki/EPEL) to satisfy additional dependencies.

> Please install the runtime first, as it is a dependency of the distribution.

## Building it yourself

The following steps describe how to build appserver.io for other environments using the provided [ANT](http://ant.apache.org/) targets, which is the recommended build tool.
Please download and install ANT to proceed.

As an experiment, we tried [Raspbian](http://www.raspbian.org/) and brought the application server to an ARM environment. This is why Rasbian is used as an example below.

### The runtime

> The common base for all appserver installations is the [runtime repository](https://github.com/appserver-io-php/runtime). Please clone or download it to your preferred workspace.

All our builds are orchestrated using [ANT property files](http://www.tutorialspoint.com/ant/ant_property_files.htm), which are included in the runtime package. They contain meta-information needed for building the appserver.io sources.
Most important is the `build.default.properties` file within the package root.
It contains information about the environment, about the dependencies appserver.io has and the versions that can be used.

> Build properties can be overwritten locally within a `build.properties` file in the package root.

For our Raspbian example, we provide additional meta-information within the `buildfiles` directory.
To use it, the `os.distribution` property of our default build properties needs to be overwritten, as it is shown below:

```
# ---- Default Operating System -------------------------------------------------
os.family = linux
os.distribution = debian
```

So, creating a file `build.properties` containing the line `os.distribution = raspbian` is sufficient for setting up the Raspbian build environment.

> The actual build process can be started by issuing `sudo ant local-build` on the command line.

But, what about environments without prepared properties?

Use `os.family` and `os.distribution` properties to load the build files best fitting to your environment and overwrite different properties locally.
The same can be done for our ANT targets within the `build.xml`.
The doctrine *First come first serve* applies, so targets within the initial build file can take the place of more specific targets in included files.

### The distribution source

The built runtime is still missing the distribution sources to create a running instance of the appserver.
To get them download or clone from [our github repository](https://github.com/appserver-io/appserver) and use ANT (`sudo ant deploy`) or copy the sources into the freshly built runtime.

Finally, we need to get all dependencies. To do so invoke `sudo composer install` within your built runtime.

### Services and scripts

Any self-built environment will lack proper services and init-scripts, as we do offer those for supported operating systems only.
Any script we provide can be found in [our distribution repositories](https://github.com/appserver-io-dist).

Otherwise, start the appserver using the installed `PHP` binaries and the `server.php` script, for example with `/opt/appserver/bin/php /opt/appserver/server.php`.


## Using Docker

Docker is probably the fastest growing technology currently in terms of software packaging and deployment standardization. Docker allows software to be utilized in practically the same way, anywhere docker containers are supported. It is also a step above normal virtualization systems, because Docker saves on a layer of OS duplication, which means containers are more portable and efficient. For more on Docker visit the [Docker website](https://www.docker.com).   

We have two possibilities to get going with appserver on Docker. The "All-in-one" container, which holds everything needed to get running with appserver in one container and the more pragmatic and Docker-like multi-container approach, where each of the important services are split up into separate containers. This version also includes a MySQL container for the example application.

The information below will assume you have experience with Docker and have Docker installed properly on your host computer. 

The information below is also in reference to the [appserver-io-apps/example](https://github.com/appserver-io-apps/example) repository on Github. For some of the installation possibilities, you'll need to clone the repository onto the machine where you'd like to have the containers installed.  

### The All-in-One Container Container

There are two ways to get going with this container. 

The first and easiest way is to simply run the container from the appserver repository on Docker Hub.

```
$ docker run -p 9080:80 appserver/example
```

This will pull the `appserver/example` container and run it, routing port 9080 to port 80 on your host machine. If you are running on your local machine, you should be able to access the appserver's index page at

`http://docker.local:9080` or `http://127.0.0.1:9080`, depending on how Docker is installed.

The example app can be found under the resource locator `/example`

The second method of getting appserver running under docker is to build your own docker container image and run it. The repository mentioned above has a `dockerfile`, which you can use to build the container image. `cd` into the `/example` directory and run the following command

`docker build -t your-org-name/container-name .`

Once the image is built, you can use the same `docker run` command used above, but with the name you gave the image above, to run the image as a container. 

### The Containerized Version

To run the properly containerized version of appserver, there is a `docker-compose.yml` file in the repository mentioned above. `cd` into the `/example` directory and then run 

`docker-compose up`    

Once all the containers are built and running, you will find appserver running under the URLs mentioned above.

If you'd like to learn more about developing applications for appserver in a Docker environment, please visit the Development with Docker[http://appserver.io/get-started/tutorials/development-with-docker](http://appserver.io/get-started/tutorials/development-with-docker) tutorial.  





