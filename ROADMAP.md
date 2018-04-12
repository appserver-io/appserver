# Roadmap

As we're in heavy development it may be, that we've to move some tasks from the following roadmap to a earlier/later version, please 
be aware of that. If you've got ideas or features that definitely have to be in one of the next releases, please contact us. We're 
always open for new ideas or feedback.

This is a rough roadmap that gives a good overview what is still available and what will come. For more detailed information about
implemented features have a look in the CHANGELOG.md or visit our [github](https://github.com/appserver-io) repository.

## Version 0.5.8 - Application Server + WebSocketServer
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

## Version 0.5.9 - Servlet-Engine
- [x] Windows installer
- [x] PHAR based deployment
- [x] SSL Encryption for servlet engine project
- [x] RPM packages

## Version 0.6.0 - [WebServer](https://github.com/appserver.io/webserver)
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

## Version 0.7 - Application Server
- [x] Integration of [server](https://github.com/appserver-io/server) as single point of entry

## Version 0.8 - Persistence-Container
- [x] Application based context [Issue #178](https://github.com/appserver-io/appserver/issues/178)
- [x] [Design by Contract](https://github.com/wick-ed/php-by-contract) default integration
- [x] Stateful, Stateless + Singleton session bean functionality
- [x] Message bean functionality

## Version 0.9 - Persistence-Container
- [x] Timer Service [Issue #185](https://github.com/appserver-io/appserver/issues/185)

## Version 1.0.x - Persistence-Container + Servlet-Engine
- [x] AOP on all methods in Stateful, Stateless + Singleton Session Beans, Message Beans and Servlets [Issue #3](https://github.com/appserver-io/appserver/issues/3)
- [x] DI for Stateful, Stateless + Singleton Session Beans, Message Beans and Servlets [Issue #4](https://github.com/appserver-io/appserver/issues/4)
- [x] Separate configuration files for server, container and application [Issue #192](https://github.com/appserver-io/appserver/issues/192)
- [x] Documentation + Tutorials

## Version 1.1.x
- [x] PHP 5.6
- [x] DNS server
- [x] Webserver auto index module
- [x] Allow usage of variables in configuration files
- [x] Seamless doctrine integration 
- [x] Optimise naming directory for performance
- [x] Authentication- and Authorization services
- [ ] Extend Dependency Injection to support simple beans
- [ ] Clean-Up Manager Interfaces and Managers

## Version 1.2.x
- [ ] PHP 7
- [ ] HTTP 2
- [ ] Allow direct execution of web application as PHAR file
- [ ] Expose Stateless Session Beans as SOAP Web Service endpoint
- [ ] 100 % Coverage for PHPUnit test suite for appserver project
