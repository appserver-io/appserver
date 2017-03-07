# Version 1.1.4-beta7

## Bugfixes

* Explictly define react/dns version 0.4.4 to avoid Cannot redeclare React\Promise\Timer\timeout()
* Explictly define react/socket version 0.4.*

## Features

* None

# Version 1.1.4-beta6

## Bugfixes

* Fixed issue in CronScanner that prevents running multiple jobs within the same schedule

## Features

* Switched to new security/authentication libraries

# Version 1.1.4-beta5

## Bugfixes

* Fixed invalid references to old DI interfaces/exceptions

## Features

* None

# Version 1.1.4-beta4

## Bugfixes

* None

## Features

* Switch to latest appserver-io-psr/servlet version 3.0.0
* Switch to new appserver-io-psr/di version 2.0.0

# Version 1.1.4-beta3

## Bugfixes

* None

## Features

* Switch to latest appserver-io-psr/servlet version 2.0.0

# Version 1.1.4-beta2

## Bugfixes

* Remove invalid class loader re-registration from TimerServiceRegistry::initialize() method

## Features

* Invoke @PreAttach + @PostDetach lifecycle callbacks on SSB
* Add Doctrine Psr4AnnotationRegistry implementation with support for PSR-4 class loading

# Version 1.1.4-beta1

## Bugfixes

* None

## Features

* Append system properties webapp and webapp.name when parsing datasource XML files

# Version 1.1.3

## Bugfixes

* None

## Features

* Add serverVersion and platform parameters to Doctrine configuration
* Do override loggers defined in etc/appserver/conf.d/context.xml with loggers defined in META-INF/context.xml

# Version 1.1.3-beta3

## Bugfixes

* None

## Features

* Add crunch/fastcgi 2.x branch as Composer dependency
* Replace error_log with exceptions in FileSystem::chown() method
* Refactor FileSystem utility to use internal FileSystem::chown() method instead of \chown()

# Version 1.1.3-beta2

## Bugfixes

* Fixed invalid class name in RedisCacheFactory

## Features

* Adding error_log if chmod/chown/chgrp fails

# Version 1.1.3-beta1

## Bugfixes

* Closed [#881](https://github.com/appserver-io/appserver/issues/881) - EntityManager instance gets nulled
* Closed [#1029](https://github.com/appserver-io/appserver/issues/1029) - Servlet Engine not starting, when an application registers the same datasource twice

## Features

* Closed [#1018](https://github.com/appserver-io/appserver/issues/1018) - Pass environment variable to virtual-hosts.xml
* Closed [#840](https://github.com/appserver-io/appserver/issues/840) - URL Routes according to REST defacto standard
* Closed [#771](https://github.com/appserver-io/appserver/issues/771) - Include a .editorconfing file to help enforce coding standards
* Adding functionality to activate Doctrine production mode and allow cache configuration

# Version 1.1.2

## Bugfixes

* None

## Features

* None

# Version 1.1.2-beta2

## Bugfixes

* Removed PhpModule from appserver configuration files

## Features

* None

# Version 1.1.2-beta1

## Bugfixes

* Fixed random startup problems, occuring when container start's faster than configured servers

## Features

* Add Rout.Lt package to appserver.io distribution
* Refactoring Runlevel integration to work with PHP 7 and pthreads 3.x
* Switched to runtime 1.1.5 with PHP 5.6.23 and always_populate_raw_post_data to -1 in all PHP ini files

# Version 1.1.1

## Bugfixes

* None

## Features

* None

# Version 1.1.1-beta16

## Bugfixes

* Fixed servlet path issue when using virtual host or appserver-runner on Windows OS

## Features

* None

# Version 1.1.1-beta15

## Bugfixes

* None

## Features

* Switch to latest v3.0.0 of appserver-io/single-app

# Version 1.1.1-beta14

## Bugfixes

* None

## Features

* Optimizing Doctrine Entity Manager integration to optimize performance

# Version 1.1.1-beta13

## Bugfixes

* None

## Features

* Refactoring NamingDirectory implementation to improve performance

# Version 1.1.1-beta12

## Bugfixes

* Fixed issue that prevents using Doctrine Entity Manager in Singleton Session Beans

## Features

* None

# Version 1.1.1-beta11

## Bugfixes

* Fixed invalid access on ContextSession instance on remote call on persistence container

## Features

* None

# Version 1.1.1-beta10

## Bugfixes

* Closed [#859](https://github.com/appserver-io/appserver/issues/859) - Memory Leaks in Session Beans

## Features

* Add @Remove annotation to allow explicit desctruction of SFSBs
* Closed [#940](https://github.com/appserver-io/appserver/issues/940) - Allow different environments aka stages with corresponding configuration

# Version 1.1.1-beta9

## Bugfixes

* None

## Features

* Switch to latest DNS server/Webserver version to improve SSL security
* Closed [#893](https://github.com/appserver-io/appserver/issues/893) - Implement mechanics for Forward Secrecy

# Version 1.1.1-beta8

## Bugfixes

* Fixed fatal error when try to access application in ErrorUtil on fatal error handling

## Features

* None

# Version 1.1.1-beta7

## Bugfixes

* Fixed invalid class loader initialization in AbstractContainerThread
* Coding Guide Lines (CGL) corrections (Hans Höchtl)
* Removing unnecessary NamingDirectoryImpl class

## Features

* None

# Version 1.1.1-beta6

## Bugfixes

* Update Dockerfile for using Supervisor restart command instead of systemd
* Closed [#960](https://github.com/appserver-io/appserver/issues/960) - Wrong restart commands for newer Linux distributions

## Features

* None

# Version 1.1.1-beta5

## Bugfixes

* Fixed rename() issue while deployin in a Docker environment using a mounted webapps directory

## Features

* Update Dockerfile to latest Debian runtime

# Version 1.1.1-beta4

## Bugfixes

* None

## Features

* Switch to latest appserver-io/properties version

# Version 1.1.1-beta3

## Bugfixes

* Fixed invalid FormAuthenticator forward on successfull login

## Features

* Add possibility to define container class loaders in appserver.xml and application META-INF/containers.xml

# Version 1.1.1-beta2

## Bugfixes

* Fixed endless loop in \AppserverIo\Appserver\ServletEngine\Http\Request when no filehandler has been registered to handle DHTML template

## Features

* Add functionality to use virtual host configuration in runner mode also
* Enable DHTML filehandler for API container
* Enable DNS server when using runner mode

# Version 1.1.1-beta1

## Bugfixes

* None

## Features

* Switch to default port 9053 for DNS server
* Add Strict-Transport-Security header to enable HTTPS Strict Transport Security (HSTS)

# Version 1.1.1-alpha13

## Bugfixes

* None

## Features

* Switch to latest appserver-io/webserver and appserver-io/dnsserver version with extended SSL configuration options

# Version 1.1.1-alpha12

## Bugfixes

* None

## Features

* Remove blackfire.io support because of segfault caused by pthreads

# Version 1.1.1-alpha11

## Bugfixes

* None

## Features

* Add runner mode enabling a node.JS style usability
* Add log configuration to appserver.xml to allow modfiying it in Dockerfile
* [Docker] supervisord log output will now be redirected to Docker stdout/stderr

# Version 1.1.1-alpha10

## Bugfixes

* Fix for change in Composer workflow, referenced by issue [#5070](composer/composer#5070)

## Features

* None

# Version 1.1.1-alpha9

## Bugfixes

* None

## Features

* Extend container configuration to en-/disable application provisioning

# Version 1.1.1-alpha8

## Bugfixes

* None

## Features

* Add generic Dockerfile ready to build a docker image on every Github tag
* Add possibility to declare a factory to initialize a the naming context with a epb-client.properties file
* Add possibility to initialize MQ client with a pms-client.properties file within the META-INF directory

# Version 1.1.1-alpha7

## Bugfixes

* Fixed missing recursive owner/mode change on webapps directory when switching between setup modes

## Features

* Also switching user for PHP-FPM when switching between setup modes

# Version 1.1.1-alpha6

## Bugfixes

* Switch to appserver-io/dnsserver 2.0.1 to fix segfault because of closing server socket in worker

## Features

* Add IPv6 address to DNS server configuration

# Version 1.1.1-alpha5

## Bugfixes

* None

## Features

* Add functionality to use variables in configuration files
* Add internal DNS server that automatically resolves registered virtual hosts

# Version 1.1.1-alpha4

## Bugfixes

* None

## Features

* Refactor session handling to make it more generic and customizable

# Version 1.1.1-alpha3

## Bugfixes

* None

## Features

* Add blackfire.io profiling module 

# Version 1.1.1-alpha2

## Bugfixes

* None

## Features

* Enable application based logger configuration by META-INF/context.xml
* Add X-Powered-By Header with ServletEngine signature for debugging purposes
* Closing database connection after loading users and roles when using DatabasePDOLoginModule

# Version 1.1.1-alpha1

## Bugfixes

* Fixed invalid order for application exctraction => results in invalid application configuration
* Fixed [#938](https://github.com/appserver-io/appserver/issues/938) - Only one container can be configured

## Features

* Switch to PHP 5.6.17
* Register datasources in naming directory
* Add missing NodeInterface to some API nodes
* Allow step args without name in provision.xml
* Remove PHP 5.4 from Travis-CI configuration file
* Add command to execute Doctrine CLI commands via Telnet console
* Add VirtualHostNodeInterface and DatasourceNodeInterfaces classes
* Add internal container with HTTP server to allow access to appserver.io RESTFul API
* Move servlet/enterprise bean descriptor/configuration functionality to description library
* Log a critical error message, if container's application and temporary directory are not available
* Add error-page configuration in web.xml to allow application specific error pages bound to HTTP response code

# Version 1.1.0

## Bugfixes

* None

## Features

* None

# Version 1.1.0-rc1

## Bugfixes

* None

## Features

* Optimize MQ and enable configuration through context.xml file
* Make Persistence Unit database port configurable

# Version 1.1.0-beta7

## Bugfixes

* Extend exception message when catching fatal errors in RequestHandler

## Features

* Allow application configuration by default
* Default virtual host now points to application server's root directory
* Optimize MQ and enable configuration through context.xml file

# Version 1.1.0-beta6

## Bugfixes

* Refactoring SFSB management implementation to solve memory leak

## Features

* Closed [#901](https://github.com/appserver-io/appserver/issues/901) - Allow re-attaching of SFSBs to current user sessions
* Closed [#898](https://github.com/appserver-io/appserver/issues/898) - Ignore annotations within persistence unit config

# Version 1.1.0-beta5

## Bugfixes

* Fixed [#855](https://github.com/appserver-io/appserver/issues/855) - Call to a protected method errors due to context mismatch
* Fixed [#854](https://github.com/appserver-io/appserver/issues/854) - Problems with systemctl enable/disable
* Fixed [#853](https://github.com/appserver-io/appserver/issues/853) - /opt/appserver/tmp gets created as user root
* Fixed [#847](https://github.com/appserver-io/appserver/issues/847) - Webserver based authentication is missing "realm"
* Fixed [#829](https://github.com/appserver-io/appserver/issues/829) - Setup with parameter -s=dev set's invalid user on Mac OS X
* Fixed [#828](https://github.com/appserver-io/appserver/issues/828) - Unknown application causes 500 instead of 404
* Fixed [#824](https://github.com/appserver-io/appserver/issues/824) - Several comment blocks break docBlock assignment
* Fixed [#815](https://github.com/appserver-io/appserver/issues/815) - Local processing does not support "none" value

## Features

* Closed [#850](https://github.com/appserver-io/appserver/issues/850) - Datasource configuration lacks driver specific options
* Closed [#849](https://github.com/appserver-io/appserver/issues/849) - Webapp based virtual host configuration

# Version 1.1.0-beta4

## Bugfixes

* Fixed [#836](https://github.com/appserver-io/appserver/issues/842) - Cannot use Traits
* Fixed [#839](https://github.com/appserver-io/appserver/issues/839) - appserver and appserver-watcher Provides collision
* Fixed [#836](https://github.com/appserver-io/appserver/issues/836) - Appserver.xml does contain invalid host attributes

## Features

* Closed [#844](https://github.com/appserver-io/appserver/issues/844) - Default server reachability should be all IPs

# Version 1.1.0-beta3

## Bugfixes

* Fixed [#834](https://github.com/appserver-io/appserver/issues/834) - Check for existing datasource node in StandardProvisioner

## Features

* None

# Version 1.1.0-beta2

## Bugfixes

* Fixed [#805](https://github.com/appserver-io/appserver/issues/805) - Constructs like <CLASSNAME>::class break parsing 
* Fixed [#778](https://github.com/appserver-io/appserver/issues/778) - CreateDatabaseStep provisioning step does delete schema
* Fixed [#811](https://github.com/appserver-io/appserver/issues/811) - Endless recursion on parent::<METHOD> call

## Features

* Closed [#819](https://github.com/appserver-io/appserver/issues/819) - Seamless Doctrine integration

# Version 1.1.0-beta1

## Bugfixes

* None

## Features

* Closed [#809](https://github.com/appserver-io/appserver/issues/809) - Add lifecycle callbacks for pre-attach and post-detach
* Closed [#759](https://github.com/appserver-io/appserver/issues/759) - Update to latest PHP 5.6.8
* Closed [#760](https://github.com/appserver-io/appserver/issues/760) - Asynchronous Deployment of Applications
* Closed [#684](https://github.com/appserver-io/appserver/issues/684) - Update to latest pthreads version

# Version 1.1.0-alpha3

## Bugfixes

* Fixed [#735](https://github.com/appserver-io/appserver/issues/735) - Endless Loop for URLs without servlet name
* Fixed [#719](https://github.com/appserver-io/appserver/issues/719) - Around advice chain does break at certain size
* Fixed [#721](https://github.com/appserver-io/appserver/issues/721) - Different order of Advices in pointcut.xml depending on type

## Features

* Closed [#720](https://github.com/appserver-io/appserver/issues/720) - Add Request::getProposedSessionId()
* Closed [#715](https://github.com/appserver-io/appserver/issues/715) - Add setAttribute/getAttribute methods to Request
* Closed [#714](https://github.com/appserver-io/appserver/issues/714) - Moved and refactored error, welcome and auto-index pages
* Closed [#743](https://github.com/appserver-io/appserver/issues/743) - Move PhtmlServlet as DhtmlServlet from Routlt 2 (to avoid conflicts with simple PHTML templates)
* Closed [#744](https://github.com/appserver-io/appserver/issues/744) - Add .dhtml file handler for ServletEngine
* Closed [#200](https://github.com/appserver-io/appserver/issues/200) - Create a proxy WebServer module

# Version 1.1.0-alpha2

## Bugfixes

* None

## Features

* Closed [#700](https://github.com/appserver-io/appserver/issues/700) - Create an AutoIndex Module
* Closed [#356](https://github.com/appserver-io/appserver/issues/356) - webserver has problems with multiple SSL/TLS certificates per server

# Version 1.1.0-alpha1

## Bugfixes

* None

## Features

* Closed [#683](https://github.com/appserver-io/appserver/issues/683) - Update PHP to 5.6

# Version 1.0.6

## Bugfixes

* Fixed [#828](https://github.com/appserver-io/appserver/issues/828) - Unknown application causes 500 instead of 404
* Fixed [#829](https://github.com/appserver-io/appserver/issues/829) - Setup with parameter -s=dev set's invalid user on Mac OS X

## Features

* None

# Version 1.0.5

## Bugfixes

* Fixed [#784](https://github.com/appserver-io/appserver/issues/784) - Application Deployment after switching to safe user
* Fixed [#790](https://github.com/appserver-io/appserver/issues/790) - Long running messages in Message Queue blocks other messages

## Features

* Remove [#777](https://github.com/appserver-io/appserver/issues/777) - Remove remote http://www.w3.org/2001/03/xml.xsd from schemas and configurations
* Closed [#758](https://github.com/appserver-io/appserver/issues/758) - Update to latest PHP 5.5.24

# Version 1.0.4

## Bugfixes

* Fixed [#725](https://github.com/appserver-io/appserver/issues/725) - no Datasources in Singleton SessionBean
* Fixed [#731](https://github.com/appserver-io/appserver/issues/731) - Custom include paths in SplClassLoader not used
* Fixed [#719](https://github.com/appserver-io/appserver/issues/719) - Around advice chain does break at certain size
* Fixed [#721](https://github.com/appserver-io/appserver/issues/721) - Different order of Advices in pointcut.xml depending on type

## Features

* None

# Version 1.0.3

## Bugfixes 

* Fixed [#682](https://github.com/appserver-io/appserver/issues/682) - Invalid output handling for fatal errors in Servlet-Engine
* Fixed [#680](https://github.com/appserver-io/appserver/issues/680) - Multiple advices by different pointcuts are eliminating each other

## Features

* None

# Version 1.0.2

## Bugfixes 

* Fixed [#110](https://github.com/appserver-io/appserver/issues/110) - Digest auth does not work on windows build
* Fixed [#605](https://github.com/appserver-io/appserver/issues/605) - Problems saving structure map on Windows
* Fixed [#654](https://github.com/appserver-io/appserver/issues/654) - Existing files containing spaces are ignored 
* Fixed [#635](https://github.com/appserver-io/appserver/issues/635) - Extracting PHAR archives containing empty files results in an exception
* Fixed [#666](https://github.com/appserver-io/appserver/issues/666) - appserver-watcher daemon does not work on Windows
* Fixed [#673](https://github.com/appserver-io/appserver/issues/673) - "Cannot re-declare class ..." error on certain circumstances
* Fixes [#675](https://github.com/appserver-io/appserver/issues/675) - Canceled authentication does not default to 401 error page

## Features

* Closed [#179](https://github.com/appserver-io/appserver/issues/179) - Standardize Windows builds
* Closed [#283](https://github.com/appserver-io/appserver/issues/283) - MSI based Windows installer
* Closed [#620](https://github.com/appserver-io/appserver/issues/620) - Extend in-code comments in regards to missing properties
* Closed [#645](https://github.com/appserver-io/appserver/issues/645) - Remove obsolete authentication adapters from ServletEngine
* Closed [#657](https://github.com/appserver-io/appserver/issues/657) - Remove serverSoftware and serverAdmin attributes from host node
* Closed [#656](https://github.com/appserver-io/appserver/issues/656) - Refactoring ServletEngine + PersistenceContainerModule for less usage of \Stackables
* Closed [#655](https://github.com/appserver-io/appserver/issues/655) - Reduce memory consumption by decrease worker number

# Version 1.0.1

## Bugfixes

* Fixed [#618](https://github.com/appserver-io/appserver/issues/618) - Segfault on Mac OS X when restarting after a new installation
* Fixed [#599](https://github.com/appserver-io/appserver/issues/599) - After updating on Debian, the server signature will not been updated if appserver.xml has not been replaced
* Fixed [#598](https://github.com/appserver-io/appserver/issues/598) - Update changes fileowner to root instead of user configured in configuration
* Fixed [#597](https://github.com/appserver-io/appserver/issues/597) - Example app should not be reinstalled on upgrades
* Fixed [#551](https://github.com/appserver-io/appserver/issues/551) - Pre-uninstall can fail to stop php-fpm process
* Fixed [#550](https://github.com/appserver-io/appserver/issues/550) - Content of var/tmp does not get cleared correctly
* Fixed [#489](https://github.com/appserver-io/appserver/issues/489) - Problems setting base dir for wrong configuration sequence
* Fixed [#569](https://github.com/appserver-io/appserver/issues/569) - Comment-less structures are ignored by pointcuts
* Fixed [#580](https://github.com/appserver-io/appserver/issues/580) - Update on *nix OS does not restart daemons
* Fixed [#612](https://github.com/appserver-io/appserver/issues/612) - php_opcache.dll failed to load if installed in non default path (Windows)

## Features

* Closed [#607](https://github.com/appserver-io/appserver/issues/607) - Improve ServletEngine exception handling
* Closed [#593](https://github.com/appserver-io/appserver/issues/593) - Update PHP/PECL version + upload_tmp_dir configuration directive  enhancement
* Closed [#582](https://github.com/appserver-io/appserver/issues/582) - Move manager/class loader interfaces to application PSR
* Closed [#572](https://github.com/appserver-io/appserver/issues/572) - Refactor Descriptor integration, move interfaces to PSRs

# Version 1.0.0

## Bugfixes

* Fixed [#514](https://github.com/appserver-io/appserver/issues/514) - @Singleton session bean needs @Startup annotation
* Fixed [#513](https://github.com/appserver-io/appserver/issues/513) - Manually creating a timer results in a segfault
* Fixed bug for invalid call to format() method if calculateNextTimeout() returns NULL
* Fixed bug in SplClassLoaderFactory::visit() method by add missing $configuration parameter

## Features

* Removed old dependencies
* Updated to stable requirements

# Version 1.0.0-rc3

## Bugfixes

* Fixed error by adding clearstatcache() when adding additional files to logrotate configuration
* Fixed [#478](https://github.com/appserver-io/appserver/issues/478) - Optimize update process on all OS
* Fixed [#492](https://github.com/appserver-io/appserver/issues/492) - 500 Internal error page will be rendered on missing PHP file
* Fixed [#503](https://github.com/appserver-io/appserver/issues/486) - Wrong PHP version within welcome page
* Usage of wrong annotation classes within the AspectManager class + new dependencies

## Features

* Closed [#487](https://github.com/appserver-io/appserver/issues/487) - Register logger instances in Naming Directory 
* Closed [#508](https://github.com/appserver-io/appserver/issues/508) - Refactoring Naming to improve decoupling of Frameworks
* Closed [#457](https://github.com/appserver-io/appserver/issues/457) - Refactoring of annotation syntax
* Closed [#458](https://github.com/appserver-io/appserver/issues/458) - Create new PSR for pbc and aop usage
* Closed [#505](https://github.com/appserver-io/appserver/issues/505) - Refactoring Application initialisation to better support community Applications
* Added welcome-page support for servers to be configurable as well
* Refactored installation setup process to be called only once in dist post install scripts
* Introduced [#469](https://github.com/appserver-io/appserver/issues/469) Provide setup script for developer mode
* Updated dependencies of appserver-io/doppelgaenger and appserver-io/rmi

# Version 1.0.0-rc2

## Bugfixes

* Fixed missing documentRoot param to persistence-container configuration in appserver.xml
* Fixed invalid registration of local/remote business interfaces for session beans
* Fixed XSD validation problems for logger entries based on wrong handlers element namespace

## Features

* Closed #473 - Create deployment PSR
* Log exceptions thrown in ServletEngine::process method
* Move OS specific templates and resources to dist packages
* Allow argument --install-dir for composer post-install-cmd
* Remove var/www/core_functions.php script and include from server.php
* Add method logCriticalException() to AbstractServletEngine to simplify exception logging

# Version 1.0.0-rc1

## Bugfixes

* Added missing dependency to appserver-io/lang
* Fixed MQ memory leak because of missing job thread when handling messages
* Fixed invalid namespace in QueueManager::createSenderForQueue() method
* Remove unnecessary interfaces SenderInterface + ReceiverInterface
* Fixed error when prepared directories to be created on startup
* Bugfix within service tests
* Minor bugfixes

## Features

* Refactoring, move interfaces of Persistence-Container + Message-Queue to separate packages
* Removed risk factor of non-injected \Stackable within class loader
* Applied new file name and coding conventions
* Updated dependencies

# Version 1.0.0-beta4

## Bugfixes

* Fixed #290 - Segfault in Windows build
* Fixed #336 - Positioning of namespace definition next to php tag
* Fixed #348 - Changed determination of omitted namespaces

## Features

* Closed #282 - Implement logrotate functionality as Server
* Closed #192 - Refactor configuration
* Closed #350 - Creating EPB references by annotations + XML configuration
* Closed #284 - Refactor Application implementation/interface
* Closed #289 - Refactoring bean/servlet/manager registration in naming directory
* Closed #285 - Refactor servlet engine virtual host management
* Closed #291 - Configuration (XML configuration) based bean declaration
* Closed #300 - Timer Service doesn't support seconds as period
* Closed #281 - Refactoring InitialContext in NamingDirectory
* Closed #182 - HTTP digest authentication within webserver
* Closed #367 - Add XSD validation for additional XML configuration files
* Introduced XSD validation for app based configuration files
* Changed nikic/phlexy version from unstable `dev-master` to stable release `0.1`
* Extended configuration validation and provided the new `configtest` CLI command
* `appserver.xml` configuration can now be splitted into several files using the `xinclude` XML feature
* Add --c start argument to change default configuration file
* Add scanner for changed files in webapps directory
* Remove some SPL Iteratators
* Remove automatic directory parsing of appserver-io/routlt package from context.xml
* Add scanner to restart application server when a PHP file changes in webapps directory (deactivated by default)
* Integration of appserver-io/microcron to allow for second based timer task execution
* Refactored servlet engines to provide app path information without the use of virtual hosts + cleanup in applications
* Improved performance by dynamic switching to simplified class loading
* Refactored and extended the webserver's authentication capabilities

# Version 1.0.0-beta3

## Bugfixes

* Fixing problems with FastCGI connection to latest HHVM versions
* Analytics module configuration within virtual hosts was ignored

## Features

* None

# Version 1.0.0-beta2

## Bugfixes

* Query directories under webapps for WEB-INF or META-INF to make sure we have a valid application
* Bugfixing invalid servlet init parameter initialization when using @Route annotation on servlets
* Bugfixing for invalid folder check when try to parse folders defined in context.xml for servlets

## Features

* Closed #299 - Refactor Message-Queue Client
* Add welcome page functionality + Iron Horse logos in webapps/welcome-page directory
* Added support for the webserver's analytics module configuration

# Version 1.0.0-beta1

## Bugfixes

* Set correct class name for Core\Api\Node\StorageServerNode to avoid warning if use Composer --optimizer-autoloader
* Changed behaviour of DB creation provisioning step so it does not need root permissions

## Features

* Closed #286 - Version number in server software signature
* Closed #294 - Session-ID structure
* Closed #288 - Session-ID will be reused
* Closed #292 - Annotation based configuration for servlets
* Closed #298 - Invoke destroy() method on Servlets after handling a request
* Move var/tmp/opcache-blacklist.txt to runtime build
* Remove unnecessary handler manager because WebSocketServer is not activated by default any longer
* Optimize class loaders for performance
* App based AOP can now be configured using pure XML file META-INF/pointcuts.xml

# Version 1.0.0-beta

## Bugfixes

* Performance optimizations by refactoring DI provider
* Switch to new performance optimized appserver-io/lang package
* Use CustomFileHandler as default handler for access/error log
* Move TimerServiceExecutor initialization to TimerServiceRegistryFactory::visit() method
* Call composer post install script after invoking deploy target
* Bugfix for invalid directory structure in copy/deploy targets
* Switch to latest appserver-io/build package because of necessary appserver.bin.dir ANT variable

## Features

* Switch to 1.0.0-beta status

# Version 0.11.1

## Bugfixes

* Bugfix invalid interface reference in Part class

## Features

* Add servlet engine implementation that uses pre-initialized request handler threads to improve performance

# Version 0.11.0

## Bugfixes

* None

## Features

* Added support for AOP using appserver-io/doppelgaenger

# Version 0.10.7

## Bugfixes

* None

## Features

* Add missing composer dependency to DI container techdivision/dependencyinjectioncontainer

# Version 0.10.6

## Bugfixes

* None

## Features

* Remove DI container => now in techdivision/dependencyinjectioncontainer
* Switch to new techdivision/naming version that allows to register application instance as naming directory also
* Extend ManagerNode + ClassLoaderNode with additional properties from system configuration
* Remove AbstractApplication + AbstractApplicationTest

# Version 0.10.5

## Bugfixes

* Bugfix invalid check for registered profile logger in ProfileModule::init()

## Features

* Add DependencyInjectionContainer::injectDependencies() method to allow DI on existing instances

# Version 0.10.4

## Bugfixes

* Add namespace alias NamingContext for TechDivision\Naming\InitialContext to solve Windows bugs

## Features

* None

# Version 0.10.3

## Bugfixes

* Replace for invalid $serverContext->getLogger() invokation with $serverContext->hasLogger()

## Features

* None

# Version 0.10.2

## Bugfixes

* None

## Features

* Add DependencyInjectionContainer as manager implementation
* Refactoring application deployment

# Version 0.10.1

## Bugfixes

* None

## Features

* Add dependency to new appserver-io/logger library
* Integration of monitoring/profiling functionality
* Move RotatingMonologHandler implemenatation => use appserver-io/logger version instead
* Move back to POPO manager/class loader factory implementations
* Remove AbstractManagerFactory implementation

# Version 0.10.0

## Bugfixes

* None

## Features

* Integration to initialize manager instances with thread based factories

# Version 0.9.16

## Bugfixes

* Refactoring SplClassLoader include path handling
* Remove GenericStackable => use techdivision/storage version
* Inject all Stackable instances instead of initialize them in __construct => pthreads 2.x compatibility

## Features

* None

# Version 0.9.15

## Bugfixes

* Replace unnecessary GenericStackable => TechDivision\Storage\GenericStackable

## Features

* None

# Version 0.9.14

## Bugfixes

* Wrong order of log handler parameter used for default setup

## Features

* None

# Version 0.9.13

## Bugfixes

* None

## Features

* Changed log rotation behaviour to keep updating a file without date and file size suffix

# Version 0.9.12

## Bugfixes

* None

## Features

* Refactoring to work with new directory structure provided with appserver-io/meta package installation

# Version 0.9.11

## Bugfixes

* None

## Features

* Added the RotatingMonologHandler class, which allows for date and filesize based log rotation

# Version 0.9.10

## Bugfixes

* Bugfix in StandardProvisioner for regex to parse WEB-INF/META-INF directory for provision.xml files to make that work on Windows systems
* Bugfix in StandardProvisionerget::AbsolutPathToPhpExecutable() to also return correct absolute path to php.exe on Windows systems

## Features

* None

# Version 0.9.9

## Bugfixes

* None

## Features

* Switch to new ClassLoader + ManagerInterface
* Add configuration parameters to manager configuration

# Version 0.9.8

## Bugfixes

* Set encryption key length when generating a SSL certificate to 2048 on Unix based operating systems

## Features

* None

# Version 0.9.7

## Bugfixes

* Bugfix for missing parameters when generating server.pem on Windows in AbstractService::createSslCertificate on system startup

## Features

* None

# Version 0.9.6

## Bugfixes

* Refactor container startup process to make sure all server sockets has been established before init user permissions and proceed with provision

## Features

* None

# Version 0.9.5

## Bugfixes

* Bugfix invalid parameter dir when calling AbstractService::cleanUpDir() method from AbstractExtractor::removeDir() method

## Features

* None

# Version 0.9.4

## Bugfixes

* Bugfix invalid path concatenation in AbstractService::getBaseDirectory() when directory with OS specific directory separator has been passed
* Move copyDir() method from AbstractExctractor to AbstractService class
* Use AbstractService::cleanUpDir() method in AbstractExtractor when delete a directory with removeDir()

## Features

* None

# Version 0.9.3

## Bugfixes

* Add missing variable type cast when initializing API node types from configuration in AbstractNode::getValueForReflectionProperty() method
* Do not overwrite preinitialized API node configuration variables with empty values in AbstractNode::getValueForReflectionProperty()
* Bugfix invalid argument initialization in AbstractArgsNode:getArg() method

## Features

* Issue #191 - initially add functionality to create certificate on system startup
* Add a programmatical default configuration for initial context, loggers, extractors + provisioners (makes configuration in appserver.xml optionally)
* Make extractors + provisioners configurable in appserver.xml
* Add composer dependency to techdivision/lang package >= 0.1

# Version 0.9.2

## Bugfixes

* None

## Features

* Clean applications cache directory when application server restarts
* Add DeploymentService::cleanUpFolders() method to clean up directories

# Version 0.9.1

## Bugfixes

* None

## Features

* Refactoring ANT PHPUnit execution process
* Composer integration by optimizing folder structure (move bootstrap.php + phpunit.xml.dist => phpunit.xml)
* Switch to new appserver-io/build build- and deployment environment

# Version 0.9.0

## Bugfixes

* Add missing %s placeholder for successfully deployed application log message

## Features

* [Issue #178](https://github.com/appserver-io/appserver/issues/178) App-based context configuration
* Add directory keys for configuration folders etc/appserver + etc/appserver/conf.d to DirectoryKeys
* Add path to be appended as parameter for methods to return directories in AbstractService
* Move method to create temporary directories for applications from AbstractDeployment to DeploymentService

# Version 0.8.2

## Bugfixes

* Bugfix invalid manager + class loader initialization in ContextNode::merge() method

## Features

* Bugfix ComposerClassLoader to allow the usage of autoload_files.php also
* Replace type hint from InitialContext with ContextInterface in SplClassLoader::__construct()
* Add SplClassLoader::get() factory method to allow declarative initialization in application context
* Refactoring SplClassLoader::getIncludePath() to allow pass additional include paths to constructor
