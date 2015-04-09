---
layout: docs
title: Architecture
meta_title: appserver.io architcture
meta_description: 
position: 60
group: Docs
permalink: /get-started/documentation/architecture.html
---

## Start-Up

The following sections describes the Start-Up process. The Start-Up process is complicated, because it is composed of several tasks that depends on other ones. For example, it is necessary to create the servers log directory before start logging.

The process is separated into two steps. The first step initializes the necessary instances in the required order.

* Umask
* InitialContext
* Filesystem
* Logger
* SSL Certificate

The second step the applications are extracted and the configured containers, servers and applications boots. 

* Extractors
* Container
* Server
* Applications
* Switch User
* Provision Applications 

After the server sockets has been opened, because of security reasons, the ownership of the process is switched from root to the configured user.

