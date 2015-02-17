---
layout: tutorial
title: Running Joomla
meta_title: Running Joomla on appserver.io
meta_description: This guide shows you how easy it is to install appserver.io on a Mac and run Joomla on the most powerful PHP infrastructure on the planet.
description: It shows you how easy it is to install Joomla on appserver.io.
position: 80
group: Tutorials
permalink: /get-started/tutorials/running-joomla-on-appserver-io.html
---


Appserver.io is a cool and sophiscated infrastructure fully built upon the PHP stack. This makes it truely easy
to develop and extend the platform. Appserver.io comes with an built in webserver module with PHP-FPM therefore it is
possible to install any PHP-App and run it on that platform. The following guide shows how easy it is to
install appserver.io on a Mac and run Wordpress.


**Prerequisite**: *Up and running installation of MySQL*

You will need a running installation of appserver.io *(>= Version 1.0.0)*. If you are new to this
project you can easily [download](<{{ "/get-started/downloads.html" | prepend: site.baseurl }}>) and follow the
[installation guide](<{{ "/get-started/documentation/installation.html" | prepend: site.baseurl }}>) for your specific OS.

After the setup has finished the appserver.io is up and running and you can call the welcome page with

[http://localhost:9080/](<http://localhost:9080/>)

By default, appserver.io is configured to run on port `9080` in order to not to affect any existing webserver installations.
You can easily change that in the /opt/appserver/etc/appserver.xml just by going to section

```xml
<server name="http"
	...
```

and change the port within that section for example to 80. After that restart the appserver.io which can be
done with the following command.

```bash
sudo /opt/appserver/sbin/appserverctl restart
```

Of course there is no need to change the port if you only want to check out the capabilities of this amazing platform.



##Installation:

In order to run the application on appserver.io, download the latest joomla release from joomla.org.

To install joomla there are now two options. The easiest way is to install joomla without creating a
vhost. Therefore just unpack the joomla source into your Webrootfolder which in case of the appserver is always
the webapps folder underneath /opt/appserver/webapps/. In that folder you will still find the already installed example
app and of course the welcome page. Just create a folder named „joomla“ and unpack the source there.

After successfully unpacking the joomla sources you are able to use the joomla webinstaller just by open a
browser and calling the URL http://127.0.0.1:9080/joomla/. Before you you start the installation it is necessary
to correct the rights of the joomla folder to ensure joomla is able to write the configuration.

```bash
chmod -R 775 /opt/appserver/webapps/joomla/
```

Now you are free to step through the installation wizard and therefore it is necessary to create a MySQL database. To create a database you can use the MySQL command line or just use another database administration tool
like phpMyAdmin. Of course you can also install phpMyAdmin on appserver.io. 
<a href="{{ "/get-started/tutorials/running-phpmyadmin-on-appserver-io.html" | prepend: site.baseurl }}">
Just read the tutorial.</a>

To create the database by the command line just use the following line

```bash
mysql -uroot -p
```

On the MySQL command line it is easy to create an empty database. Just use the following command.

```sql
CREATE DATABASE joomla;
```

Now you are ready to install joomla. Just follow the install wizard.


### Installing with Virtual Host

To run a virtual host simply follow the steps below. As with any other Webserver using a
vhost you have to add the domain you like to use in your hosts file first.

```bash
sudo vi /etc/hosts
```

Add the following lines:

```bash
127.0.0.1 joomla.local
::1 joomla.local
fe80::1%lo0 jomla.local
```

Afterwards add the vhost to the webserver config of the appserver which you also find in
`/opt/appserver/etc/appserver/conf.d/virtual-hosts.xml`. There is already an example virtual host configuration
available. Add the following configuration within the <virtualHosts> tag.

```xml
<virtualHost name="joomla.local">
    <params>
        <param name="admin" type="string">info@appserver.io</param>
        <param name="documentRoot" type="string">webapps/joomla</param>
    </params>
</virtualHost>
```

After adding the Vhost, restart the appserver and start with the Installation as described at
the beginning of this tutorial

```bash
sudo /opt/appserver/sbin/appserverctl restart
```
