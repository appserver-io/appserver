---
layout: docs_1_1
title: Architecture
meta_title: appserver.io architcture
meta_description: Describes appserver.io architecture
position: 170
group: Docs
subNav:
  - title: What is a Context
    href: what-is-a-context
  - title: How to handle Errors and Exceptions
    href: how-to-handle-errors-and-exceptions
  - title: The Context Hierarchy
    href: the-context-hierarchy
  - title: Start-Up
    href: start-up
permalink: /get-started/documentation/1.1/architecture.html
---

appserver.io makes heavy use of threads and their context to inherit instances, configuration values, constants, functions, class definitions and comments in a selective way. In addition to making good use of inheritance, appserver also takes advantage of threads and their context to also allow a separation of concerns where necessary. As opposed to processes, threads allow separation, yet gives developers the possibility to share data whenever needed.

## What is a Context

The context can be defined as the runtime environment of a thread. This includes that **EACH** thread has its own context. When a thread is created, depending on the options passed to the `start()` method, the complete context including configuration values, as well as declared constants, functions, classes and comments of the actual environment, are copied into the new threads context.

For example, if you declare a constant like

```php
<?php
define('SERVER_AUTOLOADER', 'vendor/autoloader.php');
```

it is possible to use this constant in a threads `run()` method

```php
<?php
/**
 * A very simple thread that loads and registers an autoloader in the threads main method.
 */
class MyThread
{

    /**
     * The threads main method.
     */
    public function run()
    {

        require SERVER_AUTOLOADER;

        /*
         * you now can create instances of classes the autoloader is aware of
         */
    }
}

$myThread = new MyThread()
$myThread->start(PTHREADS_INHERIT_NONE|PTHREADS_INHERIT_CONSTANTS);
```

as it will be copied into the new thread's context, when you start the thread by invoking the `start()` method. This is possible, through the use of the `PTHREADS_INHERIT_CONSTANTS` option.

Passing of constants would also be possible, if the thread's `start()` method would be invoked without any options or with the `PTHREADS_INHERIT_ALL`, which is the default value. In that case, all class definitions that have been declared before the thread has been started, will also be copied into the thread's context.

In some cases, this will be desired, but it is necessary to keep in mind, that copying everything into each threads context will also require, and, in most cases waste, a whole lot of memory. So the recommended way will be, to start each thread with `PTHREADS_INHERIT_NONE` and exactly define what has to be copied by passing additional options.

> Please be aware that the context we're talking about here **MUST** not be mixed up with a [function, method or class scope](http://php.net/manual/en/language.variables.scope.php), where you are also able to define variables or constants that can be either accessed in function, method or class scope.

## How to handle Errors and Exceptions

As there are possibilities for fatal errors to occur while processing a request, it is necessary to shutdown the thread in a controlled manner. Therefore, using PHP's `register_shutdown_function` allows a developer to catch fatal errors inside the thread's/context's `run()` method to implement a controlled shutdown.

```php
<?php
/**
 * A very simple thread that registers a shutdown function and an
 * exception handler to catch uncaught exceptions.
 */
class MyThread
{

    /**
     * The threads main method.
     */
    public function run()
    {

        try {

            // register the shutdown function
            register_shutdown_function(array(&$this, "shutdown"));

            // do some serious stuff here

        } catch (\Exception $e) {

            // handle exception here

        }
    }

    /**
     * Will be invoked when the threads run() method returns
     * or a fatal error occurs in the run() method.
     */
    public function shutdown()
    {
        // process a controlled shutdown here
    }
}

$myThread = new MyThread()
$myThread->start(PTHREADS_INHERIT_NONE|PTHREADS_INHERIT_CONSTANTS);
```

## The Context Hierarchy

As each thread has it's own context, the context hierarchy describes when and how threads and their child threads are created during the application servers start-up process. Finally, when the application server has been started, the result is a context or thread tree.

### Root Context

The root context is, as the name already implies, the context created, when the application server is started. To start the application server, it is generally necessary to invoke the `server.php` script. This script defines some mandatory constants, includes the `var/scripts/bootstrap.php`, parses the main configuration file `etc/appserver/appserver.xml`, creates a new instance of the main `Server` class and finally invokes the `start()` method. The process that'll be executed by the `start()` method is defined in section [Start-Up](#start-up).

### Container Context

The first context level is the container context, which is a child context of the root context. A container context will be created for each container declared in the `etc/appserver/appserver.xml` file. Each container context can again have a random number of child [server contexts](#server-context).

> Application Deployment will be processed on container context level. This means, that all applications will inherit the container environment they are deployed in.

### Server Context

Usually the main server functionality will be implemented on this context level. In most cases, a server context opens a server socket, possibly supporting SSL connections, and creates the configured numbers of [worker contexts](#worker-context) which will then listen for client connections.

> The server context usually will be a container context's child and therefore it'll inherit the root and the container context's environment it has been created in.

### Worker Context

The last context level, when using the webserver only, is the worker context. As described before, the workers accept the client connections and handles the request by looking up the requested file and sending the content back to the client. In most cases, a worker context uses a connection handler and handles the request by implementing a request protocol, which can be HTTP 1.1, for example.

> The worker context will be a server context's child and therefore it will inherit the root, the container and the server context's environment, which it had been created in.

### Application Context

In addition to the contexts, which results out of the webserver functionality, the application server also requires contexts to handle requests. Complementary to a webserver, an application server must also be aware of the deployed applications and their state. This makes the system a bit more complicated, since different applications may want to load a class with the same fully qualified class name. In a standard PHP environment, this would result in a `Can not redeclare class ...` fatal error. To avoid this, an application server provides a separate context for each application to protect classes from namespace conflicts.

> The application context will be a container's child and therefore it will inherit the root and the container context's environment, in which it was created.

### Request Context

The application context is not enough to handle concurrent requests. Because of race conditions, it would be necessary to execute them serialized. To allow parallel execution, each request will be processed in a separate context called request context. This is the only context that will be created just-in-time for each request the application has to handle.

> The request context will be a worker's child and therefore it will inherit the root, the container, the server and the worker context's environment, in which it was created.

## Start-Up

The following sections describes the application servers start-up process. The start-up process is complicated, because it is composed of several tasks that depends each other. For example, it is necessary to create the servers log directory, before logging can start.

The process is separated into two steps. The first step initializes the necessary instances in the required order.

* Normalize the System Configuration
* Set the Umask
* Initialize the InitialContext
* Initialize the Filesystem
* Initialize the Loggers
* Create an SSL Certificate (if necessary)
* Initialize the Extractors
* Initialize the Containers
* Initialize the Provisioners

In the second step, the applications are extracted from their PHAR archives. Then the configured containers, servers and applications boot. After the server sockets have been opened, in order to uphold utmost security, the ownership of the process is switched from root to the user configured in `etc/appserver.xm`.

* Process the Extractors
* Start the Containers
* Start the Applications
* Start the Servers
* Switch the User
* Provision the Applications

### Sequence Diagram

The following sequence diagram roughly describes the start-up workflow for version 1.0.x.

![Start-Up Sequence Diagram]({{ "/assets/img/server_start-up_sequence_diagram.jpg" | prepend: site.baseurl }} "Start-Up Sequence Diagram")

## Detailed Workflow and Dependencies

### Step 1 - Initialization

Following sections describe the workflow that will be executed during the application server's start-up. It is important to execute the steps in the order described above, because each step is a precondition for the following one.

#### 1. Normalize the System Configuration

As the system configuration is passed to the server's constructor, the first step is to convert it into a normalized and source independent object representation. After the nomalized system configuration has been set as a server's member variable, the main initialization process is started.

#### 2. Set the Umask

The next step is to set the umask for files and directories that will be created during the start-up process and later, when handling requests. The umask will be inherited from all child contexts and doesn't need to be set again.

> The umask will be loaded from the system configuration initailized in the prior step Normalize the System Configuration.

#### 3. Initialize the InitialContext

After setting the umask, the next step is the initialization of the InitialContext instance. The InitialContext instance is the primary context that is passed through to the created threads containing the necessary data like system configuration and loggers.

> As the the InitialContext is necessary for the use of any service instances, it is also a precondition for the next step, the file system initialization.

#### 4. Initialize the File System

After creating the InitialContext instance, the system is ready to prepare the file system. When the application server will be installed the first time, folders like `var/log` are **NOT** created. Instead, they will be created during the application server's first start-up. Additionally, on each start-up, the application server verifies that all necessary folders are available, or if one of the folders have to be cleaned-up. This is the case for the applications temporary directory, usually located at `var/tmp/<application-name>/tmp`, for example.

> The next step, initialize the loggers requires that the umask has been set, the InitialContext is available and the folder structure has been prepared.

#### 5. Initialize the Loggers

The system logger initialization is necessary to log the start-up process giving the administrator or developer valuable information about the deployed applications or listening server sockets.

> The loggers can be initialized as soon as the umask has been set (which influences the permissions of the log files that will be created), the system configuration is available and the filesystem has been prepared (because the folders, where the logfiles are located, have to be created during the start-up procedure).

#### 6. Create an SSL Certificate (if necessary)

As the application server also provides an HTTPS server, it is necessary that at least a self-signed SSL certificate is available, to which the default HTTPS server socket can be bound. On every start-up, the application server queries whether an SSL certificate `etc/appserver/server.pem` is available, if not, a new self-signed certificate will be created.

> The SSL certificate creation is necessary before the server context starts, because it is necessary that at least one certificate is available, in order to bind it to the HTTPS server socket.

#### 7. Initialize the Extractors

The extractors provide functionality to extract the application archives, like PHAR archives, which will be extracted to the container's default document root.

> As the extractors needs access to the application server's configuration and services, it is necessary to make sure these instances are available before the extractors are initialized.

#### 8. Initialize the Containers

A container is the root context for a random number of servers for the applications. In addition, each container has a separate naming directory to store environment variables and references to the application itself, as well as the application specific class loaders and managers.

> As the containers need access to the application server's configuration and services, it is necessary to make sure these instances are available before the containers are initialized.

#### 9. Initialize the Provisioners

The provisioners allow an application developer to execute custom steps after the application has been deployed, the server sockets are listening and the user has been switched. Provisioning steps are configured by an XML file, which has to be located either in the applications `META-INF` or `WEB-INF` directory.

> As the containers need access to the application server's configuration and services, it is necessary to make sure these instances are available before the containers are initialized.

### Step 2 - Start Server

After the configuration has been successful, the application server is ready to be started. Like the configuration process, the order of the steps to start the application server is also very important.

#### 1. Process the Extractors

Before the containers and the servers can be started, it is necessary, that the application archives are extracted and their content is moved to the containers default `webapps` directory.

> This is necesary, because the application deployment **MUST** be finished, before the container's servers starts, as modules like the `Servlet Engine` needs access to the deployed applications.

#### 2. Start the Containers

When all applications have been extracted, the containers are prepared. The next step is the initialization of the container's naming directory, followed by the datasource and application deployment and finishes with starting the servers.

> This process has to be executed step-by-step, to make sure that data sources are deployed **BEFORE** the applications, and the applications **BEFORE** the servers and their modules.

#### 3. Switch the User

After the server sockets have been started, the user can be switched from `root` to the user configured in the system configuration. This step is necessary to make sure that application provisioning will **NOT** be executed as `root`.

#### 4. Provision the Applications

The last step in the application server's start-up process is the application provisioning. Application provisioning allows an application vendor to create an SQLite database or execute a command line script for example.
