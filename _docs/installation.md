---
layout: docs
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
permalink: /get-started/documentation/installation.html
---

Besides supporting several operating systems and specific ways of installing the respective software, we
also demonstrate how to get an appserver.io package. You might do any of the following:

* Download one of our [releases](http://appserver.io/get-started/downloads.html) right from our download page
  that provide tested install packages
* Get any of our [developer builds](<http://builds.appserver.io/>) from our project page to get innovative install
  packages (including minor issues)
* Build your own packages using [ANT](<http://ant.apache.org/>). How to do this is described [below](#building-it-yourself)

The package is installed with the following basic default characteristics:

* Install directory: `/opt/appserver` (`C:\Program Files\appserver` on Windows)
* Autostart after installation, no autostart on reboot
* Reachable under pre-configured ports as described [here]({{"/get-started/documentation/configuration.html#configuration-defaults" | prepend: site.baseurl }})

For OS specific steps and characteristics review the tested environments in the section below.

> We **STRONGLY** recommend to have a look at our [upgrade guides](https://github.com/appserver-io/appserver/search?utf8=%E2%9C%93&q=UPGRADE+in%3Apath&type=Code), before upgrading any previous installation.

## Mac OS X

> Runs and tested on Mac OS X 10.8.x and higher.

For Mac OS X > 10.8.x we provide a `.pkg` file for [download]({{"/get-started/downloads.html#osx" | prepend: site.baseurl }}) that contains the `runtime` and the `distribution`. A double-click on the `.pkg` triggers the installation process.

## Windows

> Runs and tested on Windows 7 (32-bit) and higher.

As we deliver the Windows appserver as a .jar file for [download]({{"/get-started/downloads.html#win" | prepend: site.baseurl }}), an installed `Java Runtime Environment (or JDK)` is a requirement for using it. In case the JRE/JDK is not installed, you have to do so
first. You might get it from [Oracle's download page](<http://www.oracle.com/technetwork/java/javase/downloads/jre7-downloads-1880261.html>).
If this requirement is met, the installation is triggered by double-clicking the `.jar` archive.

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

> Please install the runtime first, as it is a precondition for the distribution.

## Fedora

> Runs and tested on version Fedora 20 (64-bit).

We  also provide `.rpm` [files for Fedora]({{"/get-started/downloads.html#fedora" | prepend: site.baseurl }}), one for `runtime` and `distribution` for download. A double-click triggers the installation process. Doing this will invoke the system default package manager and guides you through the installation process. 

> Please install the runtime first, as it is a precondition for the distribution.

## CentOS

> Runs and tested on CentOS 6 (64-bit).

Installation and basic usage are the same as on Fedora but we [provide different packages]({{"/get-started/downloads.html#centos" | prepend: site.baseurl }}) for `runtime` and `distribution`. CentOS requires additional repositories like [remi](<http://rpms.famillecollet.com/>) or [EPEL](<http://fedoraproject.org/wiki/EPEL>) to satisfy additional dependencies.

> Please install the runtime first, as it is a precondition for the distribution.

## Building it yourself

The following steps describe how to build the appserver for other environments using the provided [ANT](<http://ant.apache.org/>) targets, which is the recommended build tool.
Please download and install ANT to proceed.

As an experiment, we tried [Raspbian](http://www.raspbian.org/) and brought the appserver to an ARM environment. This is why Rasbian is used as an example in the following.

### The runtime

> The common base for all appserver installations is the [runtime repository](https://github.com/appserver-io-php/runtime). Please clone or download it to your preferred workspace.

All our builds are orchestrated using [ANT property files](http://www.tutorialspoint.com/ant/ant_property_files.htm) which are included in the runtime package. They contain meta-information needed for building the appserver.io sources.
Most important is the `build.default.properties` file within the package root.
It contains information about the environment, about the dependencies appserver.io has and the versions which can be used.

> Build properties can be overwritten locally within a `build.properties` file in the package root.

For our Raspbian example, we provide additional meta-information within the `buildfiles` directory. 
To use it, the `os.distribution` property of our default build properties need to be overwritten which is shown below:

```
# ---- Default Operating System -------------------------------------------------
os.family = linux
os.distribution = debian
```

So, creating a file `build.properties` containing the line `os.distribution = raspbian` is sufficient for setting up the Raspbian build environment.

> The actual build process can be started issuing `sudo ant build` on the command line.

But what about environments without prepared properties?

Use `os.family` and `os.distribution` properties to load the build files best fitting to your environment and overwrite different properties locally.
The same can be done for our ANT targets within the `build.xml`.
The doctrine *First come first serve* applies, so targets within the initial build file can take the place of more specific targets in included files.

### The distribution source

The built runtime is still missing the distribution sources to create a running instance of the appserver.
To get them download or clone from [our github repository](https://github.com/appserver-io/appserver) and use ANT (`sudo ant deploy`) or copy the sources into the freshly built runtime.

Finally, we need to get all dependencies. To do so invoke `sudo composer install` within your built runtime.

### Services and scripts

Any self-built environment will lack proper services and init-scripts as we do offer those for supported operating systems only.
Any script we provide can be found in [our distribution repositories](https://github.com/appserver-io-dist).

Otherwise start the appserver using the built `PHP` binaries and the `server.php` script like `/opt/appserver/bin/php /opt/appserver/server.php`.
