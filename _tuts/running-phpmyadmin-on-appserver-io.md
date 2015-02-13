---
layout: tutorial
title: Running phpMyAdmin
meta_title: Running phpMyAdmin on appserver.io
meta_description: appserver.io comes with an built in webserver module with PHP-FPM therefore it is possible to install any PHP-App you like and run it on that platform.
description: It shows you how easy it is installing phpMyAdmin on appserver.io
position: 40
group: Tutorials
permalink: /get-started/tutorials/running-phpmyadmin-on-appserver-io.html
---


Appserver.io is a pretty cool and sophiscated infrastructure fully built upon the PHP stack. This makes it truely easy
to develop and extend the platform. Appserver.io comes with an built in webserver module with PHP-FPM therefore it is
possible to install any PHP-App you like and run it on that platform. The following guide shows you how easy it is to
install appserver.io on a Mac and run Wordpress on it.


**Prerequirements**: *Up and running installation of MySQL*

You will need a running installation of appserver.io *(>= Version 1.0.0-rc3)*. If you are new to this
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

Of course there is no need to change the port if you only want ot check out the capabilities of this incredible platform.



##Installation:

Download the latest phpMyAdmin release from [http://phpmyadmin.net](<http://phpmyadmin.net>).

After successfully unpacking the phpmyadmin sources into the webapps folder within the your appserver installation you 
should correct the rights of the phpmyadmin folder to ensure phpmyadmin is able to write the configuration. 

```bash
chmod -R 775 /opt/appserver/webapps/phpmyadmin/
```

> Please note that we renamed the folder to lower case just for a more convenient handling.

Now you are able to login into your phpMyAdmin installation just by opening the following URL in your favourite browser.

[http://localhost:9080/phpmyadmin](<http://localhost:9080/phpmyadmin>)

Just log in with your mysql credentials and you are able administer your database.
