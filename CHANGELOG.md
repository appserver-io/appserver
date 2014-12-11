# Version 1.0.0-beta2

# Bugfixes

* Bugfixing for invalid folder check when try to parse folders defined in context.xml for servlets

# Features

* None

# Version 1.0.0-beta1

## Bugfixes

* Set correct class name for Core\Api\Node\StorageServerNode to avoid warning if use Composer --optimizer-autoloader
* Changed order of provisioning and process user switch to avoid problems of file system related provisioning steps

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