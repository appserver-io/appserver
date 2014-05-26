# TechDivision_ApplicationServer
Main repository for the [appserver.io](<http://www.appserver.io/>) project.
____________________________________________

[![Latest Stable Version](https://poser.pugx.org/techdivision/appserver/v/stable.png)](https://packagist.org/packages/techdivision/appserver) [![Total Downloads](https://poser.pugx.org/techdivision/appserver/downloads.png)](https://packagist.org/packages/techdivision/appserver) [![Latest Unstable Version](https://poser.pugx.org/techdivision/appserver/v/unstable.png)](https://packagist.org/packages/techdivision/appserver) [![License](https://poser.pugx.org/techdivision/appserver/license.png)](https://packagist.org/packages/techdivision/appserver) [![Build Status](https://travis-ci.org/techdivision/TechDivision_ApplicationServer.png)](https://travis-ci.org/techdivision/TechDivision_ApplicationServer)
____________________________________________
# Introduction
The objective of the project is to develop a multi-threaded application server for PHP, written in PHP. Yes, pure PHP! You think we aren't serious? Maybe! But we think, in order to enable as many developers in our great community, this will be the one and only way. So with your help we hopefully establish a solution as the standard for enterprise applications in PHP environments.

## Navigation
For faster navigation within the documentation (it grew quite long):

- [Getting started](<#getting-started>)
	* [Installation](<#installation>)
		- [Mac OS X](<#mac-os-x>)
		- [Windows](<#windows>)
		- [Debian](<#debian>)
		- [Fedora](<#fedora>)
		- [CentOS](<#centos>)
		- [Raspbian](<#raspbian>)
	* [Basic Usage](<#basic-usage>)
		- [Start and Stop Scripts](<#start-and-stop-scripts>)
	* [Uninstallation](<#uninstallation>)
- [Appserver Basics](<#appserver-basics>)
	* [App Deployment](<https://github.com/techdivision/TechDivision_Runtime/tree/master/src/deploy>)
	* [App Development](<#app-development>)
- [Webapp Basics](<#webapp-basics>)
	* [Technical Background and Architecture](<#technical-background-and-architecture>)
	* [Appserver.xml the Configuration](<#appserver.xml-the-configuration>)
	* [Creating your own server](<#creating-your-own-server>)
- [Roadmap](<#roadmap>)
____________________________________________
# Getting started
Below are some simple steps to get you started using the appserver.

## Installation 
Besides supporting several operating systems and their specific ways of installing software, we also support several ways of getting this software.
So to get your appserver.io package you might do any of the following:

* Download one of our [**releases**](<https://github.com/techdivision/TechDivision_ApplicationServer/releases>) right from this repository which provide tested install packages

* Grab any of our [**nightlies**](<http://builds.appserver.io/>) from our project page to get bleeding edge install packages which still might have some bugs

* Build your own package using [ant](<http://ant.apache.org/>)! To do so clone [TechDivision_Runtime](<https://github.com/techdivision/TechDivision_Runtime>) first. Then update at least the `os.family` and `os.distribution` build properties according to your environment and build the appserver with the ant target appropriate for your installer (e.g. `create-pkg` for Mac or `create-deb` for Debian based systems).

The package will install with these basic default characteristics:

* Install dir: `/opt/appserver`
* Autostart after installation, no autostart on reboot
* Reachable under pre-configured ports as described [here](<#basic-usage>) 

For OS specific steps and characteristics see below for tested environments.

### Mac OS X

* Tested versions: 10.8.x +
* Ant build: 
	- `os.family` = mac 
	- target `create-pkg`


### Windows
* Tested versions: 7 +
* Ant build: 
	- `os.family` = win
	- target `WIN-create-jar`


As we deliver the Windows appserver as a .jar file, a installed Java Runtime Environment (or JDK that is) is a vital requirement for using it.
If the JRE/JDK is not installed you have to do so first. You might get it from [Oracle's download page](<http://www.oracle.com/technetwork/java/javase/downloads/jre7-downloads-1880261.html>).
If this requirement is met you can start the installation by simply double-clicking the .jar archive.

### Debian

* Tested versions: Squeeze +
* Ant build: 
	- `os.family` = linux
	- `os.distribution` = debian 
	- target `create-deb`

If you're on a Debian system you might also try our .deb repository:

```
root@debian:~# echo "deb http://deb.appserver.io/ wheezy main" > /etc/apt/sources.list.d/appserver.list
root@debian:~# wget http://deb.appserver.io/appserver.gpg -O - | apt-key add -
root@debian:~# aptitude update
root@debian:~# aptitude install appserver
```

### Fedora
* Tested versions: 20
* Ant build: 
	- `os.family` = linux
	- `os.distribution` = fedora 
	- target `create-rpm`
	

### CentOS
* Tested versions: 6.5
* Ant build: 
	- `os.family` = linux
	- `os.distribution` = centos 
	- target `create-rpm`

Installation and basic usage is the same as on Fedora **but** CentOS requires additional repositories like [remi](<http://rpms.famillecollet.com/>) or
[EPEL](<http://fedoraproject.org/wiki/EPEL>) to satisfy additional dependencies.

### Raspbian
As an experiment we offer Raspbian and brought the appserver to an ARM environment. What should we say, it worked! :D
With `os.distribution` = raspbian you might give it a try to build it yourself (plan at least 5 hours) as we currently do not offer prepared install packages.  

## Basic Usage
The appserver will automatically start after your intstallation wizard (or package manager) finishes the setup. You can use it without limitations from now on.

Below you can find basic instructions on how to make use of the appserver.
After the installation you might want to have a look and some of the bundled apps.
Two of are interesting in particular:

* **Example** shows basic usage of services. You can reach it at `http://127.0.0.1:9080/example`

* **Admin** appserver and app management `http://127.0.0.1:9080/admin`

Start your favorite browser and have a look at what we can do. :)
You will see that we provide basic frontend implementations of services the appserver runtime provides.
If you want to use these services yourself you should have a look into the code of our apps and read about [app development](<#app-development>)

You might be curious about the different port we use.
Per default the appserver will open several ports at which it's services are available. As we do not want to block (or be blocked by) other services we use ports of a higher range.

As a default we use the following ports:

* WebContainer
	- Http-Server: `9080`
	- Https-Server: `9443`
	- WebSocketServer: `8589`  
* Persistence-MQ-Container
	- PersistenceServer: `8585`
	- PersistenceServer: `8587`
* LemCacheContainer
	- MemcacheServer: `11210`

You can change this default port mapping by using the [configuration file](<#appserver.xml-the-configuration>).
If you are interested in our naming, you can see our container->server pattern, you might want to have a deeper look into our [architecture](<#technical-background-&-architecture>)

### Start and Stop Scripts

Together with the appserver we deliver several standalone processes which we need for proper functioning of different features.

For these processes we provide start and stop scripts for all *nix like operating systems.
These work the way they normally would on the regarding system.
They are:

* `appserver`: The main process which will start the appserver itself

* `appserver-php5-fpm`: php-fpm + appserver configuration. Our default FastCGI backend. Others might be added the same way

* `appserver-watcher`: A watchdog which monitors filesystem changes and manages appserver restarts

On a normal system all three of these processes should run to enable the full feature set.
To ultimately run the appserver only the appserver process is needed but you will miss simple on-the-fly deployment (`appserver-watcher`) and might have problems with legacy applications.
Depending on the FastCGI Backend you want to use you might ditch `appserver-php5-fpm` for other processes e.g. supplying you with a hhvm backend.

Currently we support three different types of init scripts which support the commands `start`, `stop`, `status` and `restart` (additional commands migth be available on other systems).

**Mac OS X (LAUNCHD)**
The LAUNCHD launch daemons are located within the appserver installation at `/opt/appserver/sbin`.
They can be used with the schema `/opt/appserver/sbin/<DAEMON> <COMMAND>`

**Debian, Raspbian, CentOS, ...(SystemV)**
Commonly known and located in `/etc/init.d/` they too support the commands mentioned above  provided in the form `/etc/init.d/<DAEMON> <COMMAND>`.

**Fedora, ... (systemd)**
systemd init scripts can be used using the `systemctl` command with the syntax `systemctl <COMMAND> <DAEMON>`.

**Windows**

On Windows we sadly do not offer any of these scripts.
After the installation you can start the Application Server with the ``server.bat`` file located within the root directory of your installation.
Best thing to do would be starting a command prompt as an administrator and run the following commands (assuming default installation path):

```
C:\Windows\system32>cd "C:\Program Files\appserver"
C:\Program Files\appserver>server.bat
```

# Uninstall

Before uninstalling you might stop all services which are still running, otherwise there might be probpems with existing pid-files on Linux for the next time you install it.

To uninstall the appserver on Linux you might rely on your package management system. 
On Windows you can use the normal uninstall process provided by the operating system.

Under Mac OS X you can simply delete the `/opt/appserver` folder and delete the configuration
files for the launch deameons. These are files are located in folder `/Library/LaunchDaemons` and named `io.appserver.<DAEMON>.plist`.

____________________________________________
# Webapp Basics
Below you will find instructions and further information about our concept of webapps, php applications running within the appserver.
To understand why we coined the term "webapps" for them we need to have a look on the two concepts we offer as a base for such apps.
The first one is well known in the PHP community: **PHP scripts**. These cover basically every PHP application right now which get bootstrapped by a script and mostly consists of basic object oriented structures PHP offers.
The second is a concept derived from the Java world we would like to introduce to the PHP world: [**servlets**](<http://en.wikipedia.org/wiki/Servlet>).
Servlets, and therefor the apps using them, are not bootstrapped by scripts but rather by the appserver itself. That in combination with the multithreaded [architecture](<#technical-background-and-architecture>) allows for a very unique use of PHP classes which are implemented as servlets.
To further get to know the concept you might check for a more [practical example](<#app-development>).

## App Deployment
We implemented a deployment system which took notes at the [WildFly](<http://en.wikipedia.org/wiki/Wildfly>)(formerly JBoss) deployment workflow. You can either use it via file manipulations or our own management API.
A documentation of general deployment can be found [here](<https://github.com/techdivision/TechDivision_Runtime/tree/master/src/deploy>)


## App Development
This is a "getting started" tutorial for everyone who wants to learn about the appserver speciality: servlets.
It will guide you through setting up your first webapp, which serves HTTP requests. All necessary steps are explained in
detail. It is assumed that you already installed the appserver as described [here](<#installation>).

### Let's get started
Within the appserver runtime there is a folder ``webapps`` where all your web applications are deployed. So let's get you up running your own webapp. Type
the following into your terminal::

    cd /opt/appserver/webapps
    composer.phar create-project techdivision/techdivision_applicationserverproject myfirstapp dev-master

*myfirstapp* is the name of the webapp, it is necessary to call it by url. If you haven't already started the appserver
do it now by typing using the appropriate restart commands as described [here](<#start-and-stop-scripts>).

By default the appserver is running on port 9080. Therefore head over to ``127.0.0.1:9080/myfirstapp/demo.do``. Notice the webapp
name in the url, if you have chosen something else as the name, use it instead of *myfirstapp*.
The basic app stub should be visible and should look like this.

![myfirstapp landing page](doc/images/myfirstapp.png)

Let's look into some source code to get to know where the `.do` comes from. Open up your webapps folder ``webapps/myfirstapp``
in your favourite editor. You will see that the structure of the web application is similar to webapp structures of [Tomcat](<http://en.wikipedia.org/wiki/Apache_Tomcat>) or WildFly. 
Open ``WEB-INF/web.xml``. This is the configuration file for your webapp's routes which contains servlets and their mapping to URIs.
A servlet can be defined as follows:

```xml
<servlet>
	<description><![CDATA[A demo servlet]]></description>
    <display-name>DemoServlet</display-name>
    <servlet-name>DemoServlet</servlet-name>
    <servlet-class>\TechDivision\Example\Servlets\DemoServlet</servlet-class>
</servlet>
```

There you define the servlet name and map it to a servlet class defined by a namespace. If you open ``WEB-INF/classes``
you will find the defined servlet. This servlet can now be used in a route mapping like the following.


```xml
<servlet-mapping>
   	<servlet-name>DemoServlet</servlet-name>
    <url-pattern>/*</url-pattern>
</servlet-mapping>
```

This means that the servlet `DemoServlet` is mapped to every URI (or PATH_INFO if your app is not your document root) and will therefor handle every request to your app.
So why the `.do`? As you will learn [later](<#technical-background-and-architecture>) the appserver's work-horse is the *Server* component. And simply put: the server responsible for http requests needs to know when it has to handle servlets.
So consider the `.do` a, in this case imaginary, file extension like .php or .html.

So try some other URI patterns here e.g. `/index.do*`, restart the appserver and test them in the browser's url bar. It will always call the
same servlet which delivers the same content. Let's inspect the corresponding servlet class by opening
``WEB-INF/webapps/classes/TechDivisioon/Example/Servlets/DemoServlet.php``. The servlet inherits from ``HttpServlet`` as
it conforms to the HTTP 1.1 protocol. For every method of this protocol a method is provided by this class which can
be overridden. Most of the time you will use ``doGet()`` or ``doPost()`` for GET and POST methods. Let's inspect the
``doGet()`` in detail.

```php
public function doGet(HttpServletRequest $req, HttpServletResponse $res)
{
  	// build path to template
    $pathToTemplate = $this->getServletConfig()->getWebappPath() .
      	DIRECTORY_SEPARATOR . 'static' .
       	DIRECTORY_SEPARATOR . 'templates' .
       	DIRECTORY_SEPARATOR . 'layout.phtml';

    // init template
    $template = new DemoTemplate($pathToTemplate);

   	$baseUrl = '/';
    // if the application has NOT been called over a vhost
    // configuration append application folder naem
    if (!$this->getServletConfig()->getApplication()->isVhostOf($req->getServerName())) {
     	$baseUrl .= $this->getServletConfig()->getApplication()->getName() . '/';
    }

    // set vars in template
    $template->setBaseUrl($baseUrl);
    $template->setRequestUri($req->getUri());
    $template->setUserAgent($req->getHeader("User-Agent"));
    $template->setWebappName($this->getServletConfig()->getApplication()->getName());

    // set response content by render template
   	$res->appendBodyStream($template->render());
}
```

First the path to the template is built, afterwards the template is constructed. The template needs some data to display,
which is set by several methods. The last line of the method sets the response content, which is sent back to the
client.
You can of course use your own template functionalities, engines or build a webservice on this base if you want. This template class is just a simple approach for demonstration purposes.

Please note that you only have to handle requests against servlets this way! Every other file, like images or other static content, will be delivered by the server automatically.

### Let's build something CRUDish

You already learned how to configure and create a servlet, which is conform to the HTTP protocol and can deliver content
to the client. Now it is time to dive deep into the structure of the appserver. As a first webapp we will build something
CRUDish, which involves data handling. You would normally do this with a database. But why implement a database layer in your app when the server can do that for you? As we use the appserver we have much more advantages. 
The appserver comes along with a persistence container. With this architecture, your webapp is scalable, as containers, which contain servers, can scale. You will learn by this tutorial how this works in detail.

So let's build a little system which can save customers. After creating a new customer, we can view them again. The first
step is to build a little form which takes the users input data and send it to the server. Therefore, we head over to
``WEB-INF/web.xml`` and add our route for this form.

```xml
<servlet>
  	<description><![CDATA[A customer servlet]]></description>
   	<display-name>CustomerServlet</display-name>
   	<servlet-name>CustomerServlet</servlet-name>
   	<servlet-class>\TechDivision\Example\Servlets\CustomerServlet</servlet-class>
</servlet>

<servlet-mapping>
  	<servlet-name>CustomerServlet</servlet-name>
   	<url-pattern>/customer.do*</url-pattern>
</servlet-mapping>
```

The customer servlet is now callable via the route ``/customer.do``. But before we do so, let's create the servlet. It is a
class in the path ``WEB-INF/classes/TechDivision/Example/Servlets`` (if you did not change it within the ``web.xml``).

```php
namespace TechDivision\Example\Servlets;
   
use TechDivision\Servlet\Http\HttpServlet;
use TechDivision\Servlet\Http\HttpServletRequest;
use TechDivision\Servlet\Http\HttpServletResponse;

class CustomerServlet extends HttpServlet
{
    public function doGet(HttpServletRequest $req, HttpServletResponse $res)
    {
        $webappPath = $this->getServletConfig()->getWebappPath();

        // check if the template is available
        if (!file_exists(
                $pathToTemplate = $webappPath .
                DIRECTORY_SEPARATOR . 'static/templates/customer.phtml'
        )) {
            throw new \Exception(
                "Requested template '$pathToTemplate' is not available"
            );
        }

        // render template
        ob_start();
        require $pathToTemplate;
        $html = ob_get_clean();

        $res->appendBodyStream($html);
    }  
    // ... 
```

A template containing a form can now be build and delivered as already seen above.
The templates are in the directory ``static/templates`` of the webapp root directory. If it exists it gets rendered and
its output is set as the response's content. The only thing to do is to fill the template with life. Create the file
``static/templates/customer.phtml`` and insert the following.

```html
<!DOCTYPE html>
<html lang="en">
<head>
  	<meta charset="utf-8">
</head>
<body>
 	<form action="customer" method="post">
       	<input type="hidden" name="action" value="persist" />
      	<input type="hidden" name="customerId" value="<?php echo $customerId ?>" />
       	input name="name" type="text" placeholder="Enter customer name" />
      	<button type="submit" class="btn">Submit</button>
  	</form>
</body>
</html>
```

As you can see the form uses the POST method to post its data. As we only support GET in ``CustomerServlet`` we have to
implement a corresponding method which can handle POST.

```php
public function doPost(HttpServletRequest $req, HttpServletResponse $res)
{
  	// load the params with the entity data
   	$parameterMap = $req->getParameterMap();

   	// check if the necessary params has been specified and are valid
   	if (!array_key_exists('customerId', $parameterMap)) {
      	throw new \Exception();
   	} else {
       	$customerId = filter_var($parameterMap['customerId'], FILTER_VALIDATE_INT);
   	}
   	if (!array_key_exists('name', $parameterMap)) {
       	throw new \Exception();
   	} else {
       	$name = filter_var($parameterMap['name'], FILTER_SANITIZE_STRING);
   	}

  	$res->setContent('Hello ' . $name);
}
```

So far so good, but we want to persist the customer to the database. Therefore we have to take a look on the persistence
container. Open ``META-INF/appserver-ds.xml``. This is a dummy configuration file for the persistence container. Change
it to the following.

.. code-block:: xml
    :linenos:

    <datasources>
        <datasource name="TechDivision\Example"
            type="TechDivision\PersistenceContainer\Application">
            <database>
                <driver>pdo_sqlite</driver>
                <user>appserver</user>
                <password>appserver</password>
                <path>META-INF/data/customers.sqlite</path>
                <memory>false</memory>
            </database>
        </datasource>
    </datasources>


This configuration defines a data source connection to a database. In this case we use a sqlite database for demonstration
purposes. We defined the path to the database as well as the path to the entities which get persisted to it. It is now
time to create our customer entity. Create the following class
``META-INF/classes/TechDivision/Example/Entities/Customer.php``.

.. code-block:: php
    :linenos:

    namespace TechDivision\Example\Entities;

    /**
     * @Entity @Table(name="customer")
     */
    class Customer {
        /**
         * @Id
         * @Column(type="integer")
         * @GeneratedValue
         */
        public $customerId;

        /**
         * @Column(type="string", length=255)
         */
        public $name;

        public function setCustomerId($customerId) {
            $this->customerId = $customerId;
        }

        public function getCustomerId() {
            return $this->customerId;
        }

        public function setName($name) {
            $this->name = $name;
        }

        public function getName() {
            return $this->name;
        }
    }

Maybe you can guess from the annotations which persistence layer we use here. It is Doctrine which is already part of the
appserver. This is an entity which Doctrine can parse and persist. The entity gets persisted by an entity processor which
takes care of the entities' states. At this point there is the tricky but cool part of appserver. As we want to persist
the customer we have to talk to the persistence container which is all part of the ``META-INF`` folder. This is done by
sockets in order to deploy both containers (servlet and persistence) on different machines, if necessary. Therefore, you
can scale appserver as much as you want. Right now only the persistence container has the customer class and the servlet
container does not know anything about it. We have to copy the customer class into the servlet container right into
``WEB-INF/classes/TechDivision/Example/Entities/Customer.php``. As such we can now use it in our servlet. Insert the
following lines right before the method ``doPost()`` ends:

.. code-block:: php
    :linenos:

    use TechDivision\Example\Entities\Customer;

    ...

    // create a new entity and persist it
    $entity = new Customer();
    $entity->setCustomerId((integer) $customerId);
    $entity->setName($name);

    $initialContext = $this->session->createInitialContext();
    $proxy = $initialContext->lookup(
        'TechDivision\Example\Services\CustomerProcessor'
    );
    $proxy->persist($entity);

    $res->setContent('Hello ' . $name);

As you can already see we have a session attribute. This session is a context session which can handle our current
context. The initial context provides us with a proxy class for a class of the persistence container. In this example
we want to connect to the ``CustomerProcessor`` class as it handles our CRUD actions for our entity. We can communicate
to the processor via a socket which is represented by the proxy class whereas the proxy class is just a general proxy
implementation and not dependent on the ``CustomerProcessor`` class. The method call of ``persist()`` is actually done
by remote method invocation via sockets. In order to make the code lines work we have to add the following lines to
our customer servlet.

.. code-block:: php
    :linenos:

    use TechDivision\PersistenceContainerClient\Context\Connection\Factory;

    ...

    protected $connection;
    protected $session;

    public function __construct() {
        $this->connection = Factory::createContextConnection();
        $this->session = $this->connection->createContextSession();
    }

We are now ready to start the implementation of ``CustomerProcessor``. As we don't want to overwhelm the documentation
with lines of code we will copy some prepared one. We head over to the ``example`` webapp of the appserver. You can
find the example webapp in appserver's root directory in the folder ``webapps``. Copy the class
``META-INF/classes/TechDivision/Example/Services/AbstractProcessor.php`` to our project at the same path. In the same
folder we create the ``CustomerProcessor.php`` class as follows.

.. code-block:: php
    :linenos:

    namespace TechDivision\Example\Services;

    use TechDivision\Example\Entities\Customer;
    use TechDivision\Example\Services\AbstractProcessor;
    use Doctrine\ORM\Tools\SchemaTool;

    /**
     * @Singleton
     */
    class CustomerProcessor extends AbstractProcessor
    {
        public function createSchema()
        {
            // load the entity manager and the schema tool
            $entityManager = $this->getEntityManager();
            $tool = new SchemaTool($entityManager);

            // initialize the schema data from the entities
            $classes = array(
                $entityManager->getClassMetadata('TechDivision\Example\Entities\Customer')
            );

            // drop the schema if it already exists and create it new
            $tool->dropSchema($classes);
            $tool->createSchema($classes);
        }

        public function persist(Customer $entity)
        {
            // load the entity manager
            $entityManager = $this->getEntityManager();
            // check if a detached entity has been passed
            if ($entity->getCustomerId()) {
                $merged = $entityManager->merge($entity);
                $entityManager->persist($merged);
            } else {
                $entityManager->persist($entity);
            }
            // flush the entity manager
            $entityManager->flush();
            // and return the entity itself
            return $entity;
        }
    }

We overwrite the ``createSchema()`` method of the abstract processor as we have a different entity. But the rest of the
abstract class works for us as well. You may have noticed the ``@Singleton`` above the class name. This exhibits that
the customer processor is a singleton bean. It means that only one instance of it is created which is necessary as there
are no conflicts while persisting. There are also stateless and stateful beans which are for other purposes, as they
either know the state between two requests of the same user or not. The ``persist()`` method gets the doctrine entity
manager in order to persist entities. After successful persistence we want to display all entities in the frontend. We
therefore implement the following method into our customer processor.

.. code-block:: php
    :linenos:

    public function findAll()
    {
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository(
            'TechDivision\Example\Entities\Customer'
        );
        return $repository->findAll();
    }

This works again with the doctrine entity manager as it does all the work for us. In order to display all customers
in the frontend we add the following lines of code at the start of the ``doGet()`` method in our customer servlet.

.. code-block:: php
    :linenos:

    // member variable
    protected $customers;

    ...

    // doGet() method
    $initialContext = $this->session->createInitialContext();
    $proxy = $initialContext->lookup('TechDivision\Example\Services\CustomerProcessor');
    $this->customers = $proxy->findAll();

It is the same principle. We request again a proxy class which takes all the communication for us via the socket. We just
call the ``findAll()`` method we already implemented in our customer processor. The final step is now to customize the
template like this:

.. code-block:: php
    :linenos:

    <?php foreach ($this->customers as $customer): ?>
        <div><?php echo $customer->getName() ?></div>
    <?php endforeach; ?>

We iterate over all customers and echo their names. The final step is now to change the ``doPost()`` method as we still
return the Hello World example. Change the last line of the method to this line:

.. code-block:: php
    :linenos:

    $this->doGet($req, $res);

It is now time to restart the appserver again and go to ``localhost:8586/myfirstapp/customer`` to see what we have built.
You can now type in new customer names and view them in the frontend. Please note that this is just a very simple example
to demonstrate how appserver works. It is a beginner tutorial. The demonstrated code should clearly be refactored, but
for this tutorial it is good to go. Feel free to try out more functionality like updating and deleting entities.


# Component Documentation FAQ
Most components the Application Server composes of have their own documentation. If you miss a piece of information you might look there.
But to shorten your search have a look below:

- *Runtime Installation* : Can be found [here](<https://github.com/techdivision/TechDivision_Runtime>)

- *Creating a Webapp* : Can be done using [this](<https://github.com/techdivision/TechDivision_ApplicationServerProject>) template

- *Direct App deployment* : Similar to [Red Hat WildFly](http://en.wikipedia.org/wiki/WildFly). See more [here](https://github.com/techdivision/TechDivision_Runtime/tree/master/src/deploy)

- *WebServer Options and Structure* : Have a look [here](<https://github.com/techdivision/TechDivision_WebServer>)

- *WebServer Rewrite Rules* : Similar to [Apache's mod_rewrite]. See more [here](http://httpd.apache.org/docs/current/mod/mod_rewrite.html)

- *Design by Contract usage* : You can use design by contract like described [here](<https://github.com/wick-ed/php-by-contract>)
____________________________________________
# Appserver Basics

## Technical Background & Architecture

The technical foundation was given by the introduction of PHP userland threads in the form of Joe Watkins' [phtreads](https://github.com/krakjoe/pthreads) library.
Using this library we are able to utilize real [POSIX](<http://en.wikipedia.org/wiki/Posix>) compatible threads which allows us to build up complex structures and non-blocking connection handlers within only one PHP process.
It also allows for communication in between these threads.

With this technology we can build up a versatile, component based and scaleable environment.
To structure this environment we use certain terms which represent specialized classes for building up component blocks, handling external communication or do the actual work. ;)
These components are:

* *Container*: 

* *Container*: 

* *Container*: 

* *Container*: 


![myfirstapp landing page](doc/images/myfirstapp.png)

The implementation of a Web application and its operation in the PHP Application Server must be as simple as possible. For this purpose, whenever possible, the utilization of standard solution based on existing components as a, such as Doctrine, are used. On the other hand, with the paradigm Configuration by exception, the operation of an application with a minimum of configuration is needed. So a lot of the use cases is already covered by the default behavior of the respective integrated components so that the developer often does not need declarative configuration information.To appeal to the widest possible community the architecture of the Application Server must be constructed so that as large a number of existing applications can easily be migrated via adapter. Furthermore, the future development of Web applications based on all relevant PHP frameworks by providing libraries is supported.

## appserver.xml the Configuration

## Creating your own service

____________________________________________
# Roadmap
As we're in heavy development it may be, that we've to move some tasks from the following roadmap to a earlier/later version, please be aware of that. If you've ideas or features that definitely has to be in one of the next releases, please contact us. We're always open for new ideas or feedback.

And yes, we've plans for a Community and a Enterprise edition. The Community Edition will provide all functionality needed to develop, run maintain all kind of web applications. The Enterprise Edition will focus on large software solutions that run on many servers and needs real cluster functionality.

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

### Version 0.5.9 - Application Server + [ServletContainer](https://github.com/techdivision/TechDivision_ServletContainer)
- [x] Windows installer
- [x] PHAR based deployment
- [x] SSL Encryption for TechDivision_ServletContainer project
- [x] RPM packages

### Version 0.6.0 - Application Server + [WebServer](https://github.com/techdivision/TechDivision_WebServer)
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
- [x] Integration of Webserver as single point of entry request handler

### Version 0.7 - Application Server + [Runtime](https://github.com/techdivision/TechDivision_Runtime)
- [ ] AOP
- [ ] DI
- [ ] [Design by Contract](https://github.com/wick-ed/php-by-contract) default integration
- [ ] Separate configuration files for server, container and application
- [ ] Add dynamic load of application specific PECL extensions
- [ ] 100 % Coverage for PHPUnit test suite for TechDivision_ApplicationServer project
- [ ] RPM repository
- [ ] Mac OS X Universal installer

### Version 0.8 - [Persistence Container](https://github.com/techdivision/TechDivision_PersistenceContainer)
- [ ] Stateful + Singleton session bean functionality
- [ ] Container managed entity beans for Doctrine
- [ ] Webservice for session beans
- [ ] 100 % Coverage for PHPUnit test suite for TechDivision_PersistenceContainer project

### Version 0.9 - [Message Queue](https://github.com/techdivision/TechDivision_MessageQueue)
- [ ] Message bean functionality
- [ ] 100 % Coverage for PHPUnit test suite for TechDivision_MessageQueue project

### Version 1.0 - Timer Service
- [ ] Timer Service
- [ ] 100 % Coverage for PHPUnit test suite for TechDivision_TimerService project

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
