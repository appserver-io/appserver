<?php

/**
 * AppserverIo\Appserver\ServletEngine\ServletManager
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\ServletEngine;

use AppserverIo\Psr\Servlet\Servlet;
use AppserverIo\Psr\Servlet\ServletContext;
use AppserverIo\Storage\StorageInterface;
use AppserverIo\Storage\StackableStorage;
use AppserverIo\Psr\Servlet\Http\HttpServletRequest;
use AppserverIo\Appserver\ServletEngine\ServletConfiguration;
use AppserverIo\Appserver\ServletEngine\InvalidServletMappingException;
use AppserverIo\Psr\Application\ManagerInterface;
use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * The servlet manager handles the servlets registered for the application.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class ServletManager extends \Stackable implements ServletContext, ManagerInterface
{

    /**
     * Initializes the queue manager.
     *
     * @return void
     */
    public function __construct()
    {
        $this->webappPath = '';
    }

    /**
     * Inject the application instance.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     */
    public function injectApplication(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * Injects the absolute path to the web application.
     *
     * @param string $webappPath The path to this web application
     *
     * @return void
     */
    public function injectWebappPath($webappPath)
    {
        $this->webappPath = $webappPath;
    }

    /**
     * Injects the resource locator that locates the requested servlet.
     *
     * @param \AppserverIo\Appserver\ServletEngine\ResourceLocator $resourceLocator The resource locator
     *
     * @return void
     */
    public function injectResourceLocator(ResourceLocator $resourceLocator)
    {
        $this->resourceLocator = $resourceLocator;
    }

    /**
     * Injects the container for the servlets.
     *
     * @param \AppserverIo\Storage\StorageInterface $servlets The container for the servlets
     *
     * @return void
     */
    public function injectServlets(StorageInterface $servlets)
    {
        $this->servlets = $servlets;
    }

    /**
     * Injects the container for the servlet mappings.
     *
     * @param \AppserverIo\Storage\StorageInterface $servletMappings The container for the servlet mappings
     *
     * @return void
     */
    public function injectServletMappings(StorageInterface $servletMappings)
    {
        $this->servletMappings = $servletMappings;
    }

    /**
     * Injects the container for the init parameters.
     *
     * @param \AppserverIo\Storage\StorageInterface $initParameters The container for the init parameters
     *
     * @return void
     */
    public function injectInitParameters(StorageInterface $initParameters)
    {
        $this->initParameters = $initParameters;
    }

    /**
     * Injects the container for the secured URL configurations.
     *
     * @param \AppserverIo\Storage\StorageInterface $securedUrlConfigs The container for the secured URL configurations
     *
     * @return void
     */
    public function injectSecuredUrlConfigs(StorageInterface $securedUrlConfigs)
    {
        $this->securedUrlConfigs = $securedUrlConfigs;
    }

    /**
     * Injects the container for the session parameters.
     *
     * @param \AppserverIo\Storage\StorageInterface $sessionParameters The container for the session parameters
     *
     * @return void
     */
    public function injectSessionParameters(StorageInterface $sessionParameters)
    {
        $this->sessionParameters = $sessionParameters;
    }

    /**
     * Has been automatically invoked by the container after the application
     * instance has been created.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     * @see \AppserverIo\Psr\Application\ManagerInterface::initialize()
     */
    public function initialize(ApplicationInterface $application)
    {
        $this->registerServlets();
    }

    /**
     * Returns a new instance of the passed class name.
     *
     * @param string      $className The fully qualified class name to return the instance for
     * @param string|null $sessionId The session-ID, necessary to inject stateful session beans (SFBs)
     * @param array       $args      Arguments to pass to the constructor of the instance
     *
     * @return object The instance itself
     */
    public function newInstance($className, $sessionId = null, array $args = array())
    {
        return $this->getApplication()->search('ProviderInterface')->newInstance($className, $sessionId, $args);
    }

    /**
     * Finds all servlets which are provided by the webapps and initializes them.
     *
     * @return void
     */
    protected function registerServlets()
    {

        // the phar files have been deployed into folders
        if (is_dir($folder = $this->getWebappPath())) {

            // it's no valid application without at least the web.xml file
            if (!file_exists($web = $folder . DIRECTORY_SEPARATOR . 'WEB-INF' . DIRECTORY_SEPARATOR . 'web.xml')) {
                return;
            }

            // load the application config
            $config = new \SimpleXMLElement(file_get_contents($web));

            // intialize the security configuration by parseing the security nodes
            foreach ($config->xpath('/web-app/security') as $key => $securityParam) {
                // prepare the URL config in JSON format
                $securedUrlConfig = json_decode(json_encode($securityParam), 1);
                // add the web app path to the security config (to resolve relative filenames)
                $securedUrlConfig['webapp-path'] = $folder;
                // add the configuration to the array
                $this->securedUrlConfigs->set($key, $securedUrlConfig);
            }

            // initialize the context by parsing the context-param nodes
            foreach ($config->xpath('/web-app/context-param') as $contextParam) {
                $this->addInitParameter((string) $contextParam->{'param-name'}, (string) $contextParam->{'param-value'});
            }

            // initialize the session configuration by parsing the session-config childs
            foreach ($config->xpath('/web-app/session-config') as $sessionConfig) {
                foreach ($sessionConfig as $key => $value) {
                    $this->addSessionParameter(str_replace(' ', '', ucwords(str_replace('-', ' ', (string) $key))), (string) $value);
                }
            }

            // initialize the servlets by parsing the servlet-mapping nodes
            foreach ($config->xpath('/web-app/servlet') as $servlet) {

                // load the servlet name and check if it already has been initialized
                $servletName = (string) $servlet->{'servlet-name'};
                if (array_key_exists($servletName, $this->servlets)) {
                    continue;
                }

                // try to resolve the mapped servlet class
                $className = (string) $servlet->{'servlet-class'};
                if (!count($className)) {
                    throw new InvalidApplicationArchiveException(
                        sprintf('No servlet class defined for servlet %s', $servlet->{'servlet-class'})
                    );
                }

                // instantiate the servlet
                $instance = new $className();

                // initialize the servlet configuration
                $servletConfig = new ServletConfiguration();
                $servletConfig->injectServletContext($this);
                $servletConfig->injectServletName($servletName);

                // append the init params to the servlet configuration
                foreach ($servlet->{'init-param'} as $initParam) {
                    $servletConfig->addInitParameter((string) $initParam->{'param-name'}, (string) $initParam->{'param-value'});
                }

                // initialize the servlet
                $instance->init($servletConfig);

                // the servlet is added to the dictionary using the complete request path as the key
                $this->addServlet((string) $servlet->{'servlet-name'}, $instance);
            }

            // initialize the servlets by parsing the servlet-mapping nodes
            foreach ($config->xpath('/web-app/servlet-mapping') as $mapping) {

                // load the url pattern and the servlet name
                $urlPattern = (string) $mapping->{'url-pattern'};
                $servletName = (string) $mapping->{'servlet-name'};

                // the servlet is added to the dictionary using the complete request path as the key
                if (array_key_exists($servletName, $this->servlets) === false) {
                    throw new InvalidServletMappingException(
                        sprintf(
                            "Can't find servlet %s for url-pattern %s",
                            $servletName,
                            $urlPattern
                        )
                    );
                }

                // prepend the url-pattern - servlet mapping to the servlet mappings
                $this->servletMappings[$urlPattern] = $servletName;
            }
        }
    }

    /**
     * Returns the application instance.
     *
     * @return string The application instance
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Returns all servlets
     *
     * @return array The servlets collection
     */
    public function getServlets()
    {
        return $this->servlets;
    }

    /**
     * Returns the servlet mappings found in the
     * configuration file.
     *
     * @return array The servlet mappings
     */
    public function getServletMappings()
    {
        return $this->servletMappings;
    }

    /**
     * Returns the resource locator for the servlets.
     *
     * @return \AppserverIo\Appserver\ServletEngine\ResourceLocator The resource locator for the servlets
     */
    public function getServletLocator()
    {
        return $this->servletLocator;
    }

    /**
     * Returns the servlet with the passed name.
     *
     * @param string $key The name of the servlet to return
     *
     * @return \AppserverIo\Psr\Servlet\Servlet The servlet instance
     */
    public function getServlet($key)
    {
        if ($this->servlets->has($key)) {
            return $this->servlets->get($key);
        }
    }

    /**
     * Returns the servlet for the passed URL mapping.
     *
     * @param string $urlMapping The URL mapping to return the servlet for
     *
     * @return \AppserverIo\Psr\Servlet\Servlet The servlet instance
     */
    public function getServletByMapping($urlMapping)
    {
        if ($this->servletMappings->has($urlMapping)) {
            return $this->getServlet($this->servletMappings->get($urlMapping));
        }
    }

    /**
     * Registers a servlet under the passed key.
     *
     * @param string                           $key     The servlet to key to register with
     * @param \AppserverIo\Psr\Servlet\Servlet $servlet The servlet to be registered
     *
     * @return void
     */
    public function addServlet($key, Servlet $servlet)
    {
        $this->servlets->set($key, $servlet);
    }

    /**
     * Returns the path to the webapp.
     *
     * @return string The path to the webapp
     */
    public function getWebappPath()
    {
        return $this->webappPath;
    }

    /**
     * Return the resource locator instance.
     *
     * @return \AppserverIo\Appserver\ServletEngine\ResourceLocator The resource locator instance
     */
    public function getResourceLocator()
    {
        return $this->resourceLocator;
    }

    /**
     * Returns the host configuration.
     *
     * @return \AppserverIo\Appserver\Core\Configuration The host configuration
     */
    public function getConfiguration()
    {
        throw new \Exception(__METHOD__ . ' not implemented');
    }

    /**
     * Registers the init parameter under the passed name.
     *
     * @param string $name  Name to register the init parameter with
     * @param string $value The value of the init parameter
     *
     * @return void
     */
    public function addInitParameter($name, $value)
    {
        $this->initParameters->set($name, $value);
    }

    /**
     * Returns the init parameter with the passed name.
     *
     * @param string $name Name of the init parameter to return
     *
     * @return null|string
     */
    public function getInitParameter($name)
    {
        if ($this->initParameters->has($name)) {
            return $this->initParameters->get($name);
        }
    }

    /**
     * Returns the webapps security context configurations.
     *
     * @return array The security context configurations
     */
    public function getSecuredUrlConfigs()
    {
        return $this->securedUrlConfigs;
    }

    /**
     * Registers the session parameter under the passed name.
     *
     * @param string $name  Name to register the session parameter with
     * @param string $value The value of the session parameter
     *
     * @return void
     */
    public function addSessionParameter($name, $value)
    {
        $this->sessionParameters->set($name, $value);
    }

    /**
     * Returns the session parameter with the passed name.
     *
     * @param string $name Name of the session parameter to return
     *
     * @return null|string
     */
    public function getSessionParameter($name)
    {
        if ($this->sessionParameters->has($name)) {
            return $this->sessionParameters->get($name);
        }
    }

    /**
     * Returns TRUE if we've at least one session parameter configured, else FALSE.
     *
     * @return boolean TRUE if we've at least one session parametr configured, else FALSE
     */
    public function hasSessionParameters()
    {
        return sizeof($this->sessionParameters) > 0;
    }

    /**
     * Tries to locate the resource related with the request.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequest $servletRequest The request instance to return the servlet for
     * @param array                                            $args           The arguments passed to the servlet constructor
     *
     * @return \AppserverIo\Psr\Servlet\Servlet The requested servlet
     * @see \AppserverIo\Appserver\ServletEngine\ResourceLocator::locate()
     */
    public function locate(HttpServletRequest $servletRequest, array $args = array())
    {

        // load the servlet path => to locate the servlet
        $servletPath = $servletRequest->getServletPath();

        // check if we've a HTTP session-ID
        $sessionId = null;

        // if no session has already been load, initialize the session manager
        if ($manager = $this->getApplication()->search('SessionManager')) {
            $requestedSessionName = $manager->getSessionSettings()->getSessionName();
            if ($servletRequest->hasCookie($requestedSessionName)) {
                $sessionId = $servletRequest->getCookie($requestedSessionName)->getValue();
            }
        }

        // return the instance
        return $this->lookup($servletPath, $sessionId, $args);
    }

    /**
     * Runs a lookup for the servlet with the passed class name and
     * session ID.
     *
     * @param string $servletPath The servlet path
     * @param string $sessionId   The session ID
     * @param array  $args        The arguments passed to the servlet constructor
     *
     * @return \AppserverIo\Psr\Servlet\GenericServlet The requested servlet
     */
    public function lookup($servletPath, $sessionId = null, array $args = array())
    {

        // load the servlet instance
        $instance = $this->getResourceLocator()->locate($this, $servletPath, $sessionId, $args);

        // inject the dependencies
        $dependencyInjectionContainer = $this->getApplication()->search('ProviderInterface');
        $dependencyInjectionContainer->injectDependencies($instance, $sessionId);

        // return the instance
        return $instance;
    }

    /**
     * Initializes the manager instance.
     *
     * @return void
     * @see \AppserverIo\Psr\Application\ManagerInterface::initialize()
     */
    public function getIdentifier()
    {
        return ServletContext::IDENTIFIER;
    }

    /**
     * Returns the value with the passed name from the context.
     *
     * @param string $key The key of the value to return from the context.
     *
     * @return mixed The requested attribute
     */
    public function getAttribute($key)
    {
        throw new \Exception(sprintf('%s is not implemented yes', __METHOD__));
    }
}
