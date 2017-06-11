---
layout: tutorial
title: Running Magento 2 in an apperver.io Docker container
meta_title: Learn how to run Magento 2 in an appserve.io Docker container
meta_description: How-to-guide that shows how a Magento 2 instance can be setup in an appserver.io Docker container using the PHAR deployment
date: 2017-06-10 14:45:00
author: wagnert
position: 1
group: Tutorials
subNav:
  - title: Step 1 - Docker Installation
    href: step-1---docker-installation
  - title: Step 2 - Starting a MySQL Docker container
    href: step-2---starting-a-mysql-docker-container
  - title: Step 3 - Starting a appserver.io Docker container
    href: step-3---starting-a-appserver-io-docker-container
  - title: Step 4 - Download and deploy Magento 2 as PHAR archive
    href: step-4---download-and-deploy-magento-2-as-phar-rchive
  - title: Step 5 - Magento 2 Setup
    href: step-5---magento-2-setup
permalink: /get-started/tutorials/running-magento2-in-an-appserver-io-docker-container.html
---

Running Magento 2 on our appserver.io Docker container provides some advantages because some 
of the requirements that usually needs manual tweaks will be already set-up for you, like

* URL rewriting
* SSL support
* CRON configuration as part of the application sources

So installing Magento 2 in an appserver.io Docker container needs basically five steps.

### Step 1 - Docker Installation

Download and install [Docker](https://www.docker.com/community-edition) for your system.

If you already have a running Docker installation, you can skip this step and proceed with 
[Step 2](#step-2-starting-a-mysql-docker-container).

### Step 2 - Starting a MySQL Docker container

> If you already have MySQL running on your system, you have to stop this temporarily,
> because port `3306` will already be in use and the Docker container can not be started.

Starting a MySQL container with Docker is pretty simple. Open your commandline and
execute the following command

```sh
MacBook-Pro:~ docker run -d \
  --name mysql-5.6 -p127.0.0.1:3306:3306 \
  -e MYSQL_ROOT_HOST=% \
  -e MYSQL_ROOT_PASSWORD=appserver.i0 \
  mysql/mysql-server:5.6
```

This downloads a MySQL 5.6 Docker image and starts a container with MySQL listen on port
`127.0.0.1:3306` of your local machine.

### Step 3 - Starting a appserver.io Docker container

> If you already have a webserver running that listens either to one of the ports `80` and `443` 
> you'll also have to stop it temporarly.

Beside the MySQL container, we need a appserver.io container that runs the Magento 2 instance. 
Again, open your commandline, if you've already closed it, and execute the following command

```sh
MacBook-Pro:~ docker run -d \
  -p127.0.0.1:80:80 \
  -p127.0.0.1:443:443 \
  -v /Users/wagnert/Workspace:/root/Workspace \
  --name appserver-1.1.4-magento \
  --link mysql-5.6:mysql \
  appserver/dist:1.1.4
```

This downlaods the latest stable appserver.io image and starts a container with the webserver and
PHP-FPM listening to the ports `127.0.0.1:80` and `127.0.0.1:443`. Additionally it links the MySQL 
container, that we've created in [Step 2](#step-2-starting-a-mysql-docker-container) and makes it 
available as service with the name `mysql`.

### Step 4 - Download and deploy Magento 2 as PHAR archive

Beside the possiblity to copy the files to the webserver root directory, appserver.io also supports
a PHAR based [deployment](http://appserver.io/get-started/documentation/1.1/deployment.html). This
allows us to deploy Magento 2 by downloading the sources - including all composer dependencies - as 
a PHAR archive and copy it to the deployment directory inside the Docker container. To do this,
execute the following lines on the commandline

```sh
MacBook-Pro:~ docker exec appserver-1.1.4-magento bash -c \
  "wget http://apps.appserver.io/magento2/magento2-ce-2.1.7.phar \
   -O /opt/appserver/deploy/magento2-ce-2.1.7.phar"
MacBook-Pro:~ docker exec appserver-1.1.4-magento bash -c \
  "touch /opt/appserver/deploy/magento2-ce-2.1.7.phar.dodeploy"
```

Now wait until appserver.io has deployed the Magento 2 sources. To check if everything is ready, 
execute the following command several times and check the output

```sh
MacBook-Pro:~ wagnert$ docker exec appserver-1.1.4-magento bash -c "ls -l /opt/appserver/deploy"
total 195588
-rw-r--r-- 1 root root      4384 Jun  9 07:21 README.md
-rw-r--r-- 1 root root 200269499 Jun  9  0017 magento2-ce-2.1.7.phar
-rw-rw-r-- 1 root root        44 Jun 10 15:23 magento2-ce-2.1.7.phar.deployed
```
When the file `magento2-ce-2.1.7.phar.deploying` changed to `magento2-ce-2.1.7.phar.deployed` Magento 2 
has successfully been deployed. 

> The Magento 2 deployment process could take up to 1 minute, depending on your hardware!

#### Step 5 - Magento 2 Setup

The final step is the Magento 2 installation and restart the CRON. This can either be done by running 
the setup in your browser or on the commandline by executing the following lines

```sh
MacBook-Pro:~ docker exec mysql-5.6 bash -c "mysql -uroot -pappserver.i0 --execute='CREATE DATABASE magento2'"
MacBook-Pro:~ docker exec appserver-1.1.4-magento bash -c \
  "bin/php webapps/magento2-ce-2.1.7/bin/magento setup:install \
   --backend-frontname=admin \
   --db-host=mysql \
   --db-password=appserver.i0 \
   --admin-user=appserver \
   --admin-firstname=Foo \
   --admin-lastname=Bar \
   --admin-password=appserver.i0 \
   --admin-email=info@appserver.io"
MacBook-Pro:~ docker exec mysql-5.6 bash -c "supervisorctl restart appserver-watcher'"
```

Finally, open the installed [Magento 2](http://127.0.0.1/magento2-ce-2.1.7/) instance with your browser.

That's it - have fun!