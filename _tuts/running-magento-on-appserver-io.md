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

First of all you have to download the latest appserver package which is available on the appserver.io webpage under downloads. We have installers for all major operating systems, but for that example we just download the `.pkg` for the Mac OS X. Once you have downloaded the package you just have to follow the steps in the installer. After the setup has been finished, try to open the welcome page [http://127.0.0.1:9080](<http://127.0.0.1:9080>) with your favorite browser.

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

This will create a folder `magento` and extracts the Magento source files to it. Before you're able to step over to installation you **MUST** correct the rights of the `magento` folder to ensure Magento is able to write the configuration files.

```bash
sudo chown -R _www:staff magento
sudo chmod -R 775 magento
```

Additional Magento requires an existing MySQL database and an user that has access to the database. To create the database and the user, we use the MySQL commandline utilities. To log in to the MySQL commandline utilities, type

```bash
$ mysql -uroot -p
```

on your system commandline. After successful login, we can create the database, the user and the password with

```bash
mysql> create database magento;
mysql> grant all on magento.* to "magento"@"localhost" identified by "magento";
mysql> flush privileges;
```

Optional you can use another database administration tool like `phpMyAdmin` to create the database. Of course you can also install [phpMyAdmin](<{{"/get-started/documentation/tutorials/running-phpmyadmin-on-appserver-io.html" | prepend: site.baseurl }}">) on appserver.io.

Now, as you're prepared to step through the Magento installer, start your favourite browser and open 
`http://127.0.0.1:9080/magento`.

![Magento Installation Wizard - Step 1]({{ "/assets/img/magento_installation_step_01.png" | prepend: site.baseurl }} "Magento License, agree to Terms and Condition")

The first step of the Magento installation wizard contains the OSL lisence and a checkbox. By activating the checkbox, you agreement to the Magento terms and conditions and are able to proceed to step 2 by clicking on the button `Continue`.

![Magento Installation Wizard - Step 2]({{ "/assets/img/magento_installation_step_02.png" | prepend: site.baseurl }} "Database and Webserver Configuration")

## Virtual Host Configuration

If you want to use a virtual host to run Magento simply follow the steps below. As with any other webserver using a virtual host, you first have to add the domain you like to use in your hosts file.

```bash
$ sudo vi /etc/hosts
```

Add the following lines there:

```bash
127.0.0.1 magento.dev
::1 magento.dev
fe80::1%lo0 magento.dev
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
