---
layout: docs
title: Basic Usage
position: 20
group: Docs
subNav:
  - title: Start and Stop Scripts
    href: start-and-stop-scripts
  - title: Setup script
    href: setup-script
permalink: /get-started/documentation/basic-usage.html
---

The appserver will automatically start after your installation wizard (or package manager) finishes
the setup. You can use it without limitations from now on.

Below you can find basic instructions on how to make use of the appserver. After the installation
you might want to have a look and some apps. We got a showcase example bundled with the installation
which you can reach at `http://127.0.0.1:9080/example`

Start your favorite browser and have a look at what we can do. :) To pass the password barriers use
the default login `appserver/appserver.i0`.

## Start and Stop Scripts

Together with the appserver we deliver several standalone processes which we need for proper 
functioning of different features.

For these processes we provide start and stop scripts for all *nix like operating systems.
These work the way they normally would on the regarding system. They are:

* `appserver`: The main process which will start the appserver itself

* `appserver-php5-fpm`: php-fpm + appserver configuration. Our default FastCGI backend. Others might
  be added the same way

* `appserver-watcher`: A watchdog which monitors filesystem changes and manages appserver restarts

On a normal system all three of these processes should run to enable the full feature set. To 
ultimately run the appserver only the appserver process is needed but you will miss simple on-the-fly 
deployment (`appserver-watcher`) and might have problems with legacy applications.

Depending on the FastCGI Backend you want to use you might ditch `appserver-php5-fpm` for other 
processes e.g. supplying you with a [hhvm](http://hhvm.com/) backend.

Currently we support three different types of init scripts which support the commands `start`, `stop`,
`status` and `restart` (additional commands might be available on other systems).

**Mac OS X (LAUNCHD)**
The LAUNCHD launch daemons are located within the appserver installation at `/opt/appserver/sbin`.
They can be used with the schema `/opt/appserver/sbin/<DAEMON> <COMMAND>`

**Debian, Raspbian, CentOS, ...(SystemV)**
Commonly known and located in `/etc/init.d/` they too support the commands mentioned above provided 
in the form `/etc/init.d/<DAEMON> <COMMAND>`.

**Fedora, ... (systemd)**
systemd init scripts can be used using the `systemctl` command with the syntax `systemctl <COMMAND> <DAEMON>`.

**Windows**

On Windows we sadly do not offer any of these scripts. After the installation you can start the 
Application Server with the ``server.bat`` file located within the root directory of your installation.
Best thing to do would be starting a command prompt as an administrator and run the following commands
(assuming default installation path):

```
C:\Windows\system32>cd "C:\Program Files\appserver"
C:\Program Files\appserver>server.bat
```

## Setup Script

The appserver comes with a simple setup mechanism, that will mainly set the correct filesystem
permissions for your environmental needs.

```bash
sudo /opt/appserver/server.php -s <MODE>
```

Actually there are 3 modes you can use to setup the appserver to your environmental needs.

| Mode      | Description |
| ----------| ----------- |
| `install` | The *install* setup mode will be triggered when installing the appserver on your system. It'll put a flag file `/opt/appserver/etc/appserver/.is_installed` to indicate if the appserver has been installed already. If the flag file exists the *install* setup mode will not executed. |
| `prod`    | Use this mode if you want to use the appserver in production mode. The *install* mode which is executed on first time installation represents the *prod* mode. |
| `dev`     | It'll set the correct filesystem permissions for your user account and also let the appserver process itself run as current user which makes it a lot easier for local development. |

This is how it should be executed if you want to be everything ready for local development.

```bash
sudo /opt/appserver/server.php -s dev
# Should return: Setup for mode 'dev' done successfully!
```
