# TechDivision_ApplicationServer

Main repository for the [appserver.io](http://www.appserver.io/) project.

[![Latest Stable Version](https://poser.pugx.org/techdivision/appserver/v/stable.png)](https://packagist.org/packages/techdivision/appserver) [![Total Downloads](https://poser.pugx.org/techdivision/appserver/downloads.png)](https://packagist.org/packages/techdivision/appserver) [![Latest Unstable Version](https://poser.pugx.org/techdivision/appserver/v/unstable.png)](https://packagist.org/packages/techdivision/appserver) [![License](https://poser.pugx.org/techdivision/appserver/license.png)](https://packagist.org/packages/techdivision/appserver) [![Build Status](https://travis-ci.org/techdivision/TechDivision_ApplicationServer.png)](https://travis-ci.org/techdivision/TechDivision_ApplicationServer) [![Code Coverage](https://scrutinizer-ci.com/g/techdivision/TechDivision_ApplicationServer/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/techdivision/TechDivision_ApplicationServer/?branch=master)

# Introduction

The objective of the project is to develop a multi-threaded application server for PHP, written in PHP. Yes, pure PHP! You think we 
aren't serious? Maybe! But we think, in order to enable as many developers in our great community, this will be the one and only way. 
So with your help we hopefully establish a solution as the standard for enterprise applications in PHP environments.

# External Links

* Documentation at [appserver.io](http://docs.appserver.io)
* Documentation on [GitHub](https://github.com/techdivision/TechDivision_AppserverDocumentation)
* [Getting started](https://github.com/techdivision/TechDivision_AppserverDocumentation/tree/master/docs/getting-started)
* [Appserver basics](https://github.com/techdivision/TechDivision_AppserverDocumentation/tree/master/docs/basics/appserver-basics)

# Roadmap

As we're in heavy development it may be, that we've to move some tasks from the following roadmap to a earlier/later version, please 
be aware of that. If you've got ideas or features that definitely have to be in one of the next releases, please contact us. We're 
always open for new ideas or feedback.

And yes, we have plans for a Community and an Enterprise edition. The Community Edition will provide all functionality needed to develop, 
run and maintain all kind of web applications. The Enterprise Edition will focus on large software solutions that run on many servers and 
need advanced features like cluster functionality.

## Community Edition

### Version 0.5.8 - Application Server + [WebSocketContainer](https://github.com/techdivision/TechDivision_WebSocketContainer)
- [x] Logging with [monolog](https://github.com/Seldaek/monolog>)
- [x] Generic management API
- [x] HTTP basic + digest authentication for Servlet Container
- [x] Integrate annotations for session beans
- [x] Administration interface with drag-and-drop PHAR installer
- [x] Automated Build- and Deployment using Travis-CI
- [x] Set environment variables in XML configuration files
- [x] Merging XML configuration files
- [x] WebSocket integration
- [x] Running Magento CE 1.7.x + 1.8.x demo applications

### Version 0.5.9 - [Servlet-Eninge](https://github.com/techdivision/TechDivision_ServletEngine)
- [x] Windows installer
- [x] PHAR based deployment
- [x] SSL Encryption for TechDivision_ServletContainer project
- [x] RPM packages

### Version 0.6.0 - [WebServer](https://github.com/techdivision/TechDivision_WebServer)
- [x] Webserver functionality to handle static content
- [x] Rewrite functionality for Webserver project
- [x] Authentication functionality for Webserver project
- [x] PHP Module to handle PHP scripts like Apache mod_php
- [x] FastCGI functionality with support for PHP-FPM and HHVM for Webserver
- [x] Easy configuration for Webserver Environment Variables
- [x] gzip/deflate compression handling for Webserver
- [x] Servlet Engine now runs as Webserver module
- [x] Refactored Servlet Engine routing, now using fnmatch instead of Symfony Routing
- [x] Running TYPO3 Flow 2.0.x demo application with PHP Module
- [x] Running TYPO3 Neos 1.x demo application with PHP Module
- [x] Running TYPO3 6.x demo application over FastCGI
- [x] Running all type of PHP applications over FastCGI

### Version 0.7 -[Application Server](https://github.com/techdivision/TechDivision_ApplicationServer)
- [x] Integration of [TechDivision_Server](https://github.com/techdivision/TechDivision_Server) as single point of entry

### Version 0.8 - [Persistence-Container](https://github.com/techdivision/TechDivision_PersistenceContainer)
- [x] Application based context [Issue #178](https://github.com/techdivision/TechDivision_ApplicationServer/issues/178)
- [x] [Design by Contract](https://github.com/wick-ed/php-by-contract) default integration
- [x] Stateful, Stateless + Singleton session bean functionality
- [x] Message bean functionality

### Version 0.9 - [Timer Service](https://github.com/techdivision/TechDivision_PersistenceContainer)
- [x] Timer Service [Issue #185](https://github.com/techdivision/TechDivision_ApplicationServer/issues/185)

### Version 1.0 - [Persistence-Container](https://github.com/techdivision/TechDivision_PersistenceContainer) + [Servlet-Engine(https://github.com/techdivision/TechDivision_Engine)
- [ ] AOP on all methods in Stateful, Stateless + Singleton Session Beans, Message Beans and Servlets [Issue #3](https://github.com/techdivision/TechDivision_ApplicationServer/issues/3)
- [ ] DI for Stateful, Stateless + Singleton Session Beans, Message Beans and Servlets [Issue #4](https://github.com/techdivision/TechDivision_ApplicationServer/issues/4)
- [ ] Separate configuration files for server, container and application [Issue #192](https://github.com/techdivision/TechDivision_ApplicationServer/issues/192)
- [ ] Documentation + Tutorials
- [ ] 100 % Coverage for PHPUnit test suite for TechDivision_MessageQueue project
- [ ] 100 % Coverage for PHPUnit test suite for TechDivision_PersistenceContainer project
- [ ] 100 % Coverage for PHPUnit test suite for TechDivision_ApplicationServer project

## Other Stuff
- [ ] RPM repository, http://rpm.appserver.io

## Enterprise Edition

### Version 1.1 - Cluster Functionality for all Services
- [ ] Appserver nodes get known each other in same network automatically
- [ ] Webapps running on nodes in same network can be executed via all appserver nodes
- [ ] Webapps can be synchronized between appserver nodes to be executed locally
- [ ] Snapshot functionality for webapps
- [ ] HA Loadbalancing Container
- [ ] Container based transactions
- [ ] Hot-Deployment
- [ ] Farming deployment
- [ ] Web Application Firewall (WAF)
