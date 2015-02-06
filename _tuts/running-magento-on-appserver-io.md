---
layout: tuts
title: Running Magento on appserver.io
description: It shows you how easy it is to install appserver.io on a Mac and run Magento on it.
position: 20
group: Tutorials
permalink: /get-started/tutorials/running-magento-on-appserver-io.html
---

**Prerequirements**: *Up and running installation of MySQL*

appserver.io is a pretty cool and sophiscated infrastructure fully built upon the PHP stack. This makes it truely easy to develop and extend the platform. appserver.io comes with a built in webserver module that provides Fast-CGI support. Therefore it is possible to run and install any PHP application. The following tutorial guides you through the Magento installation process necessary to run on appserver.io.

## Installation

First of all you have to download the latest appserver package which is available on the appserver.io webpage under downloads. We have installers for all major operating systems, but for that example we just download the `.pkg` for the Mac OS X. Once you have downloaded the package you just have to follow the steps in the installer. After the setup has been finished, try to open the welcome page [http://127.0.0.1:9080/](<http://127.0.0.1:9080/>) with your favorite browser.

By default appserver.io is configured to run on port `9080` in order not to affect any existing webserver installations. You can easily change that in the `/opt/appserver/etc/appserver.xml` just by going to section

```xml
<server name="http"
	...
```

and change the port within that section to for example `80`. After that you have to restart with

```bash
$ sudo /opt/appserver/sbin/appserverctl restart
```

Of course there is no need to change the port if you only want ot check out the capabilities of appserver.io.

You are now set to install and run your application on appserver.io. To start, you've to [download]((http://www.magentocommerce.com/download)) the latest Magento CE version from the Magento website.

To go ahead and install Magento, we have now two options. The easiest way is to install Magento without creating a vhost. Therefore you just extract the Magento source into the document root under `/opt/appserver/webapps` by opening a commandline and type

```bash
$ cd /opt/appserver/webapps
$ tar xvfz magento-community-1.9.1.0.tar.gz
```

This will create a folder `magento` and extracts the Magento source files to it.

After successfully unpacking the Magento sources you are able to use the Magento intaller by just opening `http://127.0.0.1:9080/magento` with your favourite browser. Before you step over to the installation you **MUST** correct the rights of the `magento` folder to ensure Magento is able to write the configuration.

```bash
sudo chown -R _www:staff 
sudo chmod -R 775 magento
```

Now you are free to step over the installation wizard and for that it is necessary to create a database on your local running mysql. To create a database you can use the mysql command line or just use another database administration tool like phpMyAdmin. Of course you can also install phpMyAdmin on appserver.io. <a href="{{ "/get-started/documentation/tutorials/running-phpmyadmin-on-appserver-io.html" | prepend: site.baseurl }}"> Just read the appropriate tutorial.</a>

To create the database by the command line just use the following line

```bash
mysql -uroot -p
```

Now you are on the mysql command line and it is pretty easy to create an empty database. Just use the following command.

```sql
CREATE DATABASE wordpress;
```

Now you are ready to install wordpress. Just follow to steps on the install wizard.

If you want to use a virtual host to run wordpress simply follow the steps below. As with any other Webserver using a vhost you first have to add the domain you'd like to use in your hosts file.

```bash
sudo vi /etc/hosts
```

Add the following lines there:

```bash
127.0.0.1 wordpress.local
::1 wordpress.local
fe80::1%lo0 wordpress.local
```

Afterwards you have to add the vhost to the webserver config of the appserver which you also find in
`/opt/appserver/etc/appserver/conf.d/virtual-hosts.xml`. There is already an example virtual host configuration
available there. Put the following configuration within the <virtualHosts> tag.

```xml
<virtualHost name="wordpress.local">
    <params>
        <param name="admin" type="string">info@appserver.io</param>
        <param name="documentRoot" type="string">webapps/wordpress</param>
    </params>
</virtualHost>
```

After adding the Vhost you have to restart the appserver and you should start with the installation like described at the beginning of this tutorial

```bash
sudo /opt/appserver/sbin/appserverctl restart
```

If you alread installed wordpress and now you want to use the configured filename you just have to change the siteurl in the settings menu of wordpress.
