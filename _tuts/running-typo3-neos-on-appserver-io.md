---
layout: tutorial
title: Running TYPO3 Neos
meta_title: Running TYPO3 Neos on appserver.io
meta_description: This guide shows you how easy it is to install appserver.io on a Mac and run TYPO3 Neos on the most powerful PHP infrastructure on the planet.
description: It shows you how easy it is to install appserver.io on a Mac and run TYPO3 Neos on it.
position: 20
group: Tutorials
permalink: /get-started/tutorials/running-typo3-neos-on-appserver-io.html
---


Appserver.io is a pretty cool and sophiscated infrastructure fully built upon the PHP stack. This makes it truely easy
to develop and extend the platform. Appserver.io comes with a built in webserver module with PHP-FPM therefore it is
possible to install any PHP-App you like and run it on that platform. The following guide shows you how easy it is to
install appserver.io on a Mac and run TYPO3 Neos on it.


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

Of course there is no need to change the port if you only want to check out the capabilities of this amazingly platform.




## Installation:

Download the latest TYPO3 Neos Release from neos.typo3.org. To go ahead and install TYPO3 Neos first you have to create a virtual host.

As with any other Webserver using a vhost you first have to add the domain you like to use in your hosts file.

```bash
sudo vi /etc/hosts
```

Add the following lines there:

```bash
127.0.0.1 neos.local
::1 neos.local
fe80::1%lo0 neos.local
```

Afterwards you had to add the vhost to the webserver config of the appserver which you also find in
`/opt/appserver/etc/appserver/conf.d/virtual-hosts.xml`. There is already an example virtual host configuration
available there. Put the following configuration within the <virtualHosts> tag.

```xml
<virtualHost name="neos.local">
    <params>
        <param name="admin" type="string">info@appserver.io</param>
        <param name="documentRoot" type="string">webapps/neos/Web
        </param>
    </params>
    <rewrites>
        <rewrite
            condition="^/(_Resources/Packages/|robots\.txt|favicon\.ico){OR}-d{OR}-f{OR}-l"
            target="" flag="L" />
        <rewrite
            condition="^/(_Resources/Persistent/[a-z0-9]+/(.+/)?[a-f0-9]{40})/.+(\..+)"
            target="$1$3" flag="L" />
        <rewrite condition="^/(_Resources/Persistent/.{40})/.+(\..+)"
            target="$1$2" flag="L" />
        <rewrite condition="^/_Resources/.*" target="" flag="L" />
        <rewrite condition="(.*)" target="index.php" flag="L" />
    </rewrites>
    <environmentVariables>
        <environmentVariable condition=""
            definition="FLOW_REWRITEURLS=1" />
        <environmentVariable condition=""
            definition="FLOW_CONTEXT=Production" />
        <environmentVariable condition="Basic ([a-zA-Z0-9\+/=]+)@$Authorization"
            definition="REMOTE_AUTHORIZATION=$1" />
    </environmentVariables>
</virtualHost>
```

After adding the Vhost you have to restart the appserver and if everything was correct you you can go ahead directly
with the Neos installation itself.

`sudo /opt/appserver/sbin/appserverctl restart`

Therefore you just unpack the TYPO3 Neos source into your Webrootfolder which in case of the appserver is always the
webapps folder underneath `/opt/appserver/webapps/`. In that folder you will still find the already installed example app
and of course the welcome page. We are just creating a folder with name „neos“ and unpacking the source there.
Now you have to change the rights so Neos is able to create folders and files below.

`chmod -R 775 /opt/appserver/webapps/neos/`

After that you are able to use the TYPO3 Neos installer just by opening a browser and calling the URL
http://neos.local:9080/setup. If you do so TYPO3 Neos let you go thorugh the installation easily. At the first page
Neos is asking for the initial installation password which you can find in

`/opt/appserver/webapps/neos/Data/SetupPassword.txt`

Now you just have to type in your database credentials and select if you want to use an existing database or want to
create a new one. The second last step is creating an user account. Finally you are able to import the Demo site
package or if you already have developed your own site packege you can import that as well.

Now you are all set and can enjoy the power TYPO3 Neos.
