# Roadmap

As we're in heavy development it may be, that we've to move some tasks from the following roadmap to a earlier/later version, please 
be aware of that. If you've got ideas or features that definitely have to be in one of the next releases, please contact us. We're 
always open for new ideas or feedback.

And yes, we have plans for a Community and an Enterprise edition. The Community Edition will provide all functionality needed to develop, 
run and maintain all kind of web applications. The Enterprise Edition will focus on large software solutions that run on many servers and 
need advanced features like cluster functionality.

## Community Edition

### Version 0.5.8 - Application Server + WebSocketServer
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

### Version 0.5.9 - Servlet-Engine
- [x] Windows installer
- [x] PHAR based deployment
- [x] SSL Encryption for servlet engine project
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

### Version 0.7 - Application Server
- [x] Integration of [TechDivision_Server](https://github.com/techdivision/TechDivision_Server) as single point of entry

### Version 0.8 - Persistence-Container
- [x] Application based context [Issue #178](https://github.com/appserver-io/appserver/issues/178)
- [x] [Design by Contract](https://github.com/wick-ed/php-by-contract) default integration
- [x] Stateful, Stateless + Singleton session bean functionality
- [x] Message bean functionality

### Version 0.9 - Persistence-Container
- [x] Timer Service [Issue #185](https://github.com/appserver-io/appserver/issues/185)

### Version 1.0 - Persistence-Container + Servlet-Engine
- [x] AOP on all methods in Stateful, Stateless + Singleton Session Beans, Message Beans and Servlets [Issue #3](https://github.com/appserver-io/appserver/issues/3)
- [x] DI for Stateful, Stateless + Singleton Session Beans, Message Beans and Servlets [Issue #4](https://github.com/appserver-io/appserver/issues/4)
- [ ] Documentation + Tutorials
- [ ] 100 % Coverage for PHPUnit test suite for appserver project

### Version 1.1

- [ ] Separate configuration files for server, container and application [Issue #192](https://github.com/appserver-io/appserver/issues/192)
- [ ] Expose Stateless Session Beans as SOAP Web Service endpoint
- [ ] RPM repository, http://rpm.appserver.io

## Enterprise Edition

Yes, we're thinking about an Enterprise Edition of the PHP application server. Actually we only have identified some features we think
that they are part of this edition. We're working on a more specific definition of that features and try to priorize them based on the
communities needs. So, if you've an idea or there is something on your mind for a long time, you think it should be part of an PHP 
application server, we'll be happy to hear from you. If possible we'll add it on our list, and hopefully, someday your wish come true.

Beside this, we promise, that Community and Enterprise will be the same codebase and developement for the Community code will always be 
public and maintained with the same enthusiasm as the Enterprise will.

### Requested Features

Below we've defined a list with features we plan to integrate in the EE. Before development will start, we'll priorize this features by community, partners and customers business value.

#### Cluster

This category defines features that will be part of a cluster functionality. The cluster functionality targets systems with HA needs, e. g. ecommerce systems with really high number of transactions.

* HA Load-Balancing Container
* Nodes get known each other in same network automatically
* Webapps running on nodes in same network can be executed via all appserver nodes
* Webapps can be synchronized between appserver nodes to be executed locally
* Farming-Deployment, an application deployed on one node will deployed automatically on all nodes within the cluster
* Hot-Deployment, deploy an application among the cluster without restarting any of the cluster nodes

#### Functional

This category covers functional add-on's that enables developers to implement business logic in faster, more reusable und maintanable way. 

* Container-Managed Transactions

#### Snapshots/Backup

* Snapshot functionality for webapps

#### Servers

Category that provides additional services.

* SSH Server
* Search Engine
* Data-Grid, fast key-value store

#### Security

Security relevant tools and services.

* Web Application Firewall (WAF)
