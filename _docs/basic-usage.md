---
layout: docs
title: Basic Usage
meta_title: appserver.io basic usage
meta_description: The appserver will automatically start after your installation wizard finishes the setup. You can use it without limitations from now on.
position: 20
group: Docs
subNav:
  - title: Start and Stop Scripts
    href: start-and-stop-scripts
  - title: Setup script
    href: setup-script
permalink: /get-started/documentation/basic-usage.html
---

After the installation wizard (or package manager) is done, appserver starts automatically. You can now use it without limitations.

Below you find basic instructions on how to make use of the appserver. After the installation, you might want to look at some apps. A showcase example is bundled with the installation at `http://127.0.0.1:9080/example`

Start your preferred browser and check out what we can do. :) To pass the password barriers use
the default login `appserver/appserver.i0`.

## Start and Stop Scripts

Together with the appserver we deliver several standalone processes which we need for proper 
functioning of different features.

For these processes, we provide start and stop scripts for all *nix like operating systems.
They work the way they normally do base on the regarding system. They are:

* `appserver`: The main process which will start the appserver itself

* `appserver-php5-fpm`: PHP-fpm + appserver configuration. Our default FastCGI backend. Others might
  be added the same way

* `appserver-watcher`: A watchdog which monitors filesystem changes and manages appserver restarts

Using a normal setup; all three of these processes should run to enable the full feature set. To 
ultimately run the appserver only the appserver process is needed but you will miss simple on-the-fly 
deployment (`appserver-watcher`) and might have problems with legacy applications.

Depending on the FastCGI Backend you want to use you might ditch `appserver-php5-fpm` for other 
processes e.g. supplying you with a [hhvm](http://hhvm.com/) backend.

Currently, we support three different types of init scripts that support the commands `start`, `stop`,
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
On Windows, we sadly do not offer any of these scripts. After the installation, you can start the 
Application Server with the ``server.bat`` file located in the root directory of your installation.
Start a command prompt as an administrator and run the following commands
(assuming default installation path):

```
C:\Windows\system32>cd "C:\Program Files\appserver"
C:\Program Files\appserver>server.bat
```

## Setup Script

appserver comes with a simple setup mechanism, that will mainly set the correct filesystem permissions for your environment needs.

```bash
sudo /opt/appserver/server.php -s <MODE>
```

Actually there are 3 modes you can use to setup the appserver to your environment needs.

| Mode      | Description |
| ----------| ----------- |
| `install` | The *install* setup mode will be triggered when installing the appserver on your system. It sets a flag `/opt/appserver/etc/appserver/.is_installed` to indicate if the appserver has been already installed. If the flag file exists the *install* setup mode will not execute. |
| `prod`    | Use this mode to use the appserver in production mode. The *install* mode which is executed on first time installation represents the *prod* mode. |
| `dev`     | Sets the correct filesystem permissions for your user account and also let the appserver process itself run as current user which makes it a lot easier for local development. |

This is how it should be executed to be ready for local development.

```bash
sudo /opt/appserver/server.php -s dev
# Should return: Setup for mode 'dev' done successfully!
```
