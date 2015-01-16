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

use AppserverIo\Appserver\Core\Api\InvalidConfigurationException;
use AppserverIo\Psr\Servlet\Servlet;
use AppserverIo\Psr\Servlet\ServletContext;
use AppserverIo\Psr\Servlet\Annotations\Route;
use AppserverIo\Psr\Servlet\Http\HttpServletRequest;
use AppserverIo\Appserver\Core\AbstractManager;
use AppserverIo\Storage\StorageInterface;
use AppserverIo\Storage\StackableStorage;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Lang\Reflection\ReflectionClass;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\ServletEngine\ServletConfiguration;
use AppserverIo\Appserver\ServletEngine\InvalidServletMappingException;
use AppserverIo\Appserver\DependencyInjectionContainer\DirectoryParser;
use AppserverIo\Appserver\DependencyInjectionContainer\DeploymentDescriptorParser;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\ServletDescriptorInterface;

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
class ServletManager extends AbstractManager implements ServletContext
{

    /**
     * Injects the additional directories to be parsed when looking for servlets.
     *
     * @param array $directories The additional directories to be parsed
     *
     * @return void
     */
    public function injectDirectories(array $directories)
    {
        $this->directories = $directories;
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
     * @param \AppserverIo\Storage\GenericStackable $servletMappings The container for the servlet mappings
     *
     * @return void
     */
    public function injectServletMappings(GenericStackable $servletMappings)
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
        $this->registerServlets($application);
    }

    /**
     * Finds all servlets which are provided by the webapps and initializes them.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     *
     * @throws \AppserverIo\Appserver\ServletEngine\InvalidServletMappingException
     */
    protected function registerServlets(ApplicationInterface $application)
    {

        // query if the web application folder exists
        if (is_dir($folder = $this->getWebappPath()) === false) { // if not, do nothing
            return;
        }

        // load the object manager
        $objectManager = $this->getApplication()->search('ObjectManagerInterface');

        // load the directories to be parsed
        $directories = array();

        // append the directory found in the servlet managers configuration
        foreach ($this->getDirectories() as $directoryNode) {

            // prepare the custom directory defined in the servlet managers configuration
            $customDir = $folder . DIRECTORY_SEPARATOR . ltrim($directoryNode->getNodeValue()->getValue(), DIRECTORY_SEPARATOR);

            // check if the directory exists
            if (is_dir($customDir)) {
                $directories[] = $customDir;
            }
        }

        // initialize the directory parser
        $directoryParser = new DirectoryParser();
        $directoryParser->injectApplication($application);

        // parse the directories for annotated servlets
        foreach ($directories as $directory) {
            $directoryParser->parse($directory);
        }

        // it's no valid application without at least the web.xml file
        if (file_exists($deploymentDescriptor = $folder . DIRECTORY_SEPARATOR . 'WEB-INF' . DIRECTORY_SEPARATOR . 'web.xml')) {

            try {
                // parse the deployment descriptor for registered servlets
                $deploymentDescriptorParser = new DeploymentDescriptorParser();
                $deploymentDescriptorParser->injectApplication($application);
                $deploymentDescriptorParser->parse($deploymentDescriptor, '/a:web-app/a:servlet');

            } catch (InvalidConfigurationException $e) {

                $application->getInitialContext()->getSystemLogger()->critical($e->getMessage());
                return;
            }

            // load the application config
            $config = new \SimpleXMLElement(file_get_contents($deploymentDescriptor));
            $config->registerXPathNamespace('a', 'http://www.appserver.io/appserver');

            // initialize the servlets by parsing the servlet-mapping nodes
            foreach ($config->xpath('/a:web-app/a:servlet-mapping') as $mapping) {

                // load the url pattern and the servlet name
                $urlPattern = (string) $mapping->{'url-pattern'};
                $servletName = (string) $mapping->{'servlet-name'};

                // try to find the servlet with the configured name
                foreach ($objectManager->getObjectDescriptors() as $descriptor) {

                    // query if we've a servlet and the name matches the mapped servlet name
                    if ($descriptor instanceof ServletDescriptorInterface &&
                        $descriptor->getName() === $servletName) {

                        // add the URL pattern
                        $descriptor->addUrlPattern($urlPattern);

                        // override the descriptor with the URL pattern
                        $objectManager->setObjectDescriptor($descriptor);

                        // proceed the next mapping
                        continue 2;
                    }
                }

                // the servlet is added to the dictionary using the complete request path as the key
                throw new InvalidServletMappingException(
                    sprintf('Can\'t find servlet %s for url-pattern %s', $servletName, $urlPattern)
                );
            }

            // initialize the security configuration by parseing the security nodes
            foreach ($config->xpath('/a:web-app/a:security') as $key => $securityParam) {
                // prepare the URL config in JSON format
                $securedUrlConfig = json_decode(json_encode($securityParam), 1);
                // add the web app path to the security config (to resolve relative filenames)
                $securedUrlConfig['webapp-path'] = $folder;
                // add the configuration to the array
                $this->securedUrlConfigs->set($key, $securedUrlConfig);
            }

            // initialize the context by parsing the context-param nodes
            foreach ($config->xpath('/a:web-app/a:context-param') as $contextParam) {
                $this->addInitParameter((string) $contextParam->{'param-name'}, (string) $contextParam->{'param-value'});
            }

            // initialize the session configuration by parsing the session-config children
            foreach ($config->xpath('/a:web-app/a:session-config') as $sessionConfig) {
                foreach ($sessionConfig as $key => $value) {
                    $this->addSessionParameter(str_replace(' ', '', ucwords(str_replace('-', ' ', (string) $key))), (string) $value);
                }
            }
        }

        // register the beans located by annotations and the XML configuration
        foreach ($objectManager->getObjectDescriptors() as $descriptor) {

            // check if we've found a servlet descriptor
            if ($descriptor instanceof ServletDescriptorInterface) { // register the servlet
                $this->registerServlet($descriptor);
            }
        }
    }

    /**
     * Register the servlet described by the passed descriptor.
     *
     * @param \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\ServletDescriptorInterface $descriptor The servlet descriptor
     *
     * @return void
     */
    protected function registerServlet(ServletDescriptorInterface $descriptor)
    {

        try {

            // create a new reflection class instance
            $reflectionClass = new ReflectionClass($descriptor->getClassName());

            // instantiate the servlet
            $instance = $reflectionClass->newInstance();

            // load servlet name
            $servletName = $descriptor->getName();

            // initialize the servlet configuration
            $servletConfig = new ServletConfiguration();
            $servletConfig->injectServletContext($this);
            $servletConfig->injectServletName($servletName);

            // append the init params to the servlet configuration
            foreach ($descriptor->getInitParams() as $paramName => $paramValue) {
                $servletConfig->addInitParameter($paramName, $paramValue);
            }

            // initialize the servlet
            $instance->init($servletConfig);

            // the servlet is added to the dictionary using the complete request path as the key
            $this->addServlet($servletName, $instance);

            // prepend the url-pattern - servlet mapping to the servlet mappings
            foreach ($descriptor->getUrlPatterns() as $pattern) {
                $this->addServletMapping($pattern, $servletName);
            }

            // register the EPB references
            foreach ($descriptor->getEpbReferences() as $epbReference) {
                $this->registerEpbReference($epbReference);
            }

            // register the resource references
            foreach ($descriptor->getResReferences() as $resReference) {
                $this->registerResReference($resReference);
            }

        } catch (\Exception $e) { // log the exception
            $this->getApplication()->getInitialContext()->getSystemLogger()->critical($e->__toString());
        }
    }

    /**
     * Returns all the additional directories to be parsed for servlets.
     *
     * @return array The additional directories
     */
    public function getDirectories()
    {
        return $this->directories;
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
     * @return \AppserverIo\Storage\GenericStackable The servlet mappings
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
        if (isset($this->servletMappings[$urlMapping])) {
            return $this->getServlet($this->servletMappings[$urlMapping]);
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
     * Adds an URL mapping for a servlet.
     *
     * @param string $pattern     The URL pattern we want the servlet to map to
     * @param string $servletName The servlet name to map
     *
     * @return void
     */
    public function addServletMapping($pattern, $servletName)
    {
        $this->servletMappings[$pattern] = $servletName;
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
     * Returns the identifier for the servlet manager instance.
     *
     * @return string
     * @see \AppserverIo\Psr\Application\ManagerInterface::getIdentifier()
     */
    public function getIdentifier()
    {
        return ServletContext::IDENTIFIER;
    }
}
