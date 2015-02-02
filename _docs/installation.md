---
layout: docs
title: Installation
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
  - title: Raspbian
    href: raspbian
permalink: /documentation/installation.html
---

Besides supporting several operating systems and their specific ways of installing software, we
also support several ways of getting this software. So to get your appserver.io package you might
do any of the following:

* Download one of our [**releases**](<https://github.com/appserver-io/appserver/releases>) right from this repository
  which provide tested install packages
* Grab any of our [**nightlies**](<http://builds.appserver.io/>) from our project page to get bleeding edge install
  packages which still might have some bugs
* Build your own package using [ANT](<http://ant.apache.org/>)! To do so clone the
  [runtime](<https://github.com/appserver-io-php/runtime>) first. Then update at least the `os.family` and
  `os.distribution` build properties according to your environment and build the appserver with the ANT `build`
  and `create-package` target

The package will install with these basic default characteristics:

* Install dir: `/opt/appserver`
* Autostart after installation, no autostart on reboot
* Reachable under pre-configured ports as described [here](#basic-usage)

For OS specific steps and characteristics see below for tested environments.

## Mac OS X

> Runs and tested on Mac OS X 10.8.x and higher!

For Mac OS X > 10.8.x we provide a `.pkg` file for [download](https://github.com/appserver-io/appserver/releases/download/1.0.0-rc1/appserver-dist-1.0.0-rc1.30.mac.x86_64.pkg) that contains the runtime and the distribution. Double-clicking on the `.pkg` starts and guides you through the installation process.

## Windows

> Runs and tested on Windows 7 (32-bit) and higher!

As we deliver the Windows appserver as a .jar file you can [download](https://github.com/appserver-io/appserver/releases/download/1.0.0-rc1/appserver-dist-1.0.0-rc1.43.win.x86.jar), a installed Java Runtime Environment (or JDK
that is) is a vital requirement for using it. If the JRE/JDK is not installed you have to do so
first. You might get it from [Oracle's download page](<http://www.oracle.com/technetwork/java/javase/downloads/jre7-downloads-1880261.html>).
If this requirement is met you can start the installation by simply double-clicking the .jar archive.

## Debian

> Runs and tested on Debian Squeeze (64-bit) and higher!

If you're on a Debian system you might also try our `.deb` repository:

```
root@debian:~# echo "deb http://deb.appserver.io/ wheezy main" > /etc/apt/sources.list.d/appserver.list
root@debian:~# wget http://deb.appserver.io/appserver.gpg -O - | apt-key add -
root@debian:~# aptitude update
root@debian:~# aptitude install appserver-dist
```

Optionally you can download the `.deb` files for the [runtime](https://github.com/appserver-io/appserver/releases/download/1.0.0-rc1/appserver-runtime-1.0.0-beta3.16.linux.debian.x86_64.deb) and the [distribution](https://github.com/appserver-io/appserver/releases/download/1.0.0-rc1/appserver-dist-1.0.0-rc1.25.linux.debian.x86_64.deb) and install them by double-clicking on them. This will invoke the system default package manager and guides you through the installation process. Please install the runtime first, as this is a dependency for the distribution.

## Fedora

> Runs and tested on versions Fedora 20 (64-bit) and higher!

We  also provide `.rpm` files for Fedora, one for [runtime](https://github.com/appserver-io/appserver/releases/download/1.0.0-rc1/appserver-runtime-1.0.0-beta3.24.linux.fedora.x86_64.rpm) and [distribution](https://github.com/appserver-io/appserver/releases/download/1.0.0-rc1/appserver-dist-1.0.0-rc1.47.linux.fedora.x86_64.rpm), that you can download and start the installation process by double-clicking on it. This will start the systems default package manager and guides you through the installation process.

## CentOS

> Runs and tested on CentOS 6.5 (64-bit) and higher!

Installation and basic usage is the same as on Fedora but we provide different packages for [runtime](https://github.com/appserver-io/appserver/releases/download/1.0.0-rc1/appserver-runtime-1.0.0-beta3.26.linux.centos.x86_64.rpm) and [distribution](https://github.com/appserver-io/appserver/releases/download/1.0.0-rc1/appserver-dist-1.0.0-rc1.32.linux.centos.x86_64.rpm). CentOS requires additional repositories
like [remi](<http://rpms.famillecollet.com/>) or [EPEL](<http://fedoraproject.org/wiki/EPEL>) to
satisfy additional dependencies.

## Raspbian

As an experiment we offer Raspbian and brought the appserver to an ARM environment. What should
we say, it worked! :D With `os.distribution` = raspbian you might give it a try to build it
yourself (plan at least 5 hours) as we currently do not offer prepared install packages.
