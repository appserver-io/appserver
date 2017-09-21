<?php

/**
 * \AppserverIo\Appserver\ServletEngine\ServletManager
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\ServletEngine;

use AppserverIo\Storage\GenericStackable;
use AppserverIo\Storage\StorageInterface;
use AppserverIo\Lang\Reflection\ReflectionClass;
use AppserverIo\Appserver\Core\AbstractEpbManager;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\Servlet\ServletInterface;
use AppserverIo\Psr\Servlet\ServletContextInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Description\ServletDescriptorInterface;
use AppserverIo\Appserver\ServletEngine\DependencyInjection\DirectoryParser;
use AppserverIo\Appserver\ServletEngine\DependencyInjection\DeploymentDescriptorParser;
use AppserverIo\Appserver\Application\Interfaces\ManagerSettingsInterface;
use AppserverIo\Appserver\Application\Interfaces\ManagerSettingsAwareInterface;

/**
 * The servlet manager handles the servlets registered for the application.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property array                                                                  $directories       The additional directories to be parsed
 * @property \AppserverIo\Storage\StorageInterface                                  $initParameters    The container for the init parameters
 * @property \AppserverIo\Appserver\ServletEngine\ResourceLocatorInterface          $resourceLocator   The resource locator for requested servlets
 * @property \AppserverIo\Appserver\ServletEngine\ResourceLocatorInterface          $servletLocator    The resource locator for the servlets
 * @property \AppserverIo\Storage\GenericStackable                                  $servletMappings   The container for the servlet mappings
 * @property \AppserverIo\Storage\StorageInterface                                  $servlets          The container for the servlets
 * @property \AppserverIo\Storage\StorageInterface                                  $sessionParameters The container for the session parameters
 * @property \AppserverIo\Appserver\Application\Interfaces\ManagerSettingsInterface $managerSettings   Settings for the servlet manager
 */
class ServletManager extends AbstractEpbManager implements ServletContextInterface, ManagerSettingsAwareInterface
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
     * @param \AppserverIo\Appserver\ServletEngine\ResourceLocatorInterface $resourceLocator The resource locator
     *
     * @return void
     */
    public function injectResourceLocator(ResourceLocatorInterface $resourceLocator)
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
     * Injects the container for the error page configuration.
     *
     * @param \AppserverIo\Storage\StorageInterface $errorPages The container for the error page configuration
     *
     * @return void
     */
    public function injectErrorPages(StorageInterface $errorPages)
    {
        $this->errorPages = $errorPages;
    }

    /**
     * Injects the servlet manager settings.
     *
     * @param \AppserverIo\Appserver\Application\Interfaces\ManagerSettingsInterface $managerSettings The servlet manager settings
     *
     * @return void
     */
    public function injectManagerSettings(ManagerSettingsInterface $managerSettings)
    {
        $this->managerSettings = $managerSettings;
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
    public function registerServlets(ApplicationInterface $application)
    {

        // query whether or not the web application folder exists
        if (is_dir($this->getWebappPath()) === false) {
            return;
        }

        // initialize the directory parser and parse the web application's base directory for annotated servlets
        $directoryParser = new DirectoryParser();
        $directoryParser->injectServletContext($this);
        $directoryParser->parse();

        // initialize the deployment descriptor parser and parse the web application's deployment descriptor for servlets
        $deploymentDescriptorParser = new DeploymentDescriptorParser();
        $deploymentDescriptorParser->injectServletContext($this);
        $deploymentDescriptorParser->parse();

        // load the object manager instance
        /** @var \AppserverIo\Psr\Di\ObjectManagerInterface $objectManager */
        $objectManager = $this->getApplication()->search('ObjectManagerInterface');

        // register the beans located by annotations and the XML configuration
        /** \AppserverIo\Psr\Deployment\DescriptorInterface $objectDescriptor */
        foreach ($objectManager->getObjectDescriptors() as $descriptor) {
            // check if we've found a servlet descriptor and register the servlet
            if ($descriptor instanceof ServletDescriptorInterface) {
                $this->registerServlet($descriptor);
            }
        }
    }

    /**
     * Register the servlet described by the passed descriptor.
     *
     * @param \AppserverIo\Psr\Servlet\Description\ServletDescriptorInterface $descriptor The servlet descriptor
     *
     * @return void
     */
    public function registerServlet(ServletDescriptorInterface $descriptor)
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

            // register the persistence unit references
            foreach ($descriptor->getPersistenceUnitReferences() as $persistenceUnitReference) {
                $this->registerPersistenceUnitReference($persistenceUnitReference);
            }

        } catch (\Exception $e) {
            // log the exception
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
     * @return \AppserverIo\Appserver\ServletEngine\ResourceLocatorInterface The resource locator for the servlets
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
     * @return \AppserverIo\Psr\Servlet\ServletInterface The servlet instance
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
     * @return \AppserverIo\Psr\Servlet\ServletInterface The servlet instance
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
     * @param string                                    $key     The servlet to key to register with
     * @param \AppserverIo\Psr\Servlet\ServletInterface $servlet The servlet to be registered
     *
     * @return void
     */
    public function addServlet($key, ServletInterface $servlet)
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
     * @return \AppserverIo\Appserver\ServletEngine\ResourceLocatorInterface The resource locator instance
     */
    public function getResourceLocator()
    {
        return $this->resourceLocator;
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
     * Registers the error page under the passed error code.
     *
     * @param string $errorCodePattern The error code for the page
     * @param string $errorLocation    The error page location
     *
     * @return void
     */
    public function addErrorPage($errorCodePattern, $errorLocation)
    {
        $this->errorPages->set($errorCodePattern, $errorLocation);
    }

    /**
     * Returns the container with the error page configuration.
     *
     * @return \AppserverIo\Storage\StorageInterface The container with the error page configuration
     */
    public function getErrorPages()
    {
        return $this->errorPages;
    }

    /**
     * Returns the webapps security context configurations.
     *
     * @return array The security context configurations
     * @throws \AppserverIo\Appserver\ServletEngine\OperationNotSupportedException Is thrown if this method has been invoked
     */
    public function getSecuredUrlConfigs()
    {
        throw new OperationNotSupportedException(sprintf('%s not yet implemented', __METHOD__));
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
     * @return boolean TRUE if we've at least one session parameter configured, else FALSE
     */
    public function hasSessionParameters()
    {
        return sizeof($this->sessionParameters) > 0;
    }

    /**
     * Return's the servlet manager settings.
     *
     * @return \AppserverIo\Appserver\Application\Interfaces\ManagerSettingsInterface The servlet manager settings
     */
    public function getManagerSettings()
    {
        return $this->managerSettings;
    }

    /**
     * Tries to locate the resource related with the request.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface $servletRequest The request instance to return the servlet for
     * @param array                                                     $args           The arguments passed to the servlet constructor
     *
     * @return \AppserverIo\Psr\Servlet\ServletInterface The requested servlet
     * @see \AppserverIo\Appserver\ServletEngine\ResourceLocator::locate()
     */
    public function locate(HttpServletRequestInterface $servletRequest, array $args = array())
    {

        // load the servlet path => to locate the servlet
        $servletPath = $servletRequest->getServletPath();

        // check if we've a HTTP session-ID
        $sessionId = null;

        // if no session has already been load, initialize the session manager
        if ($manager = $this->getApplication()->search('SessionManagerInterface')) {
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
        return ServletContextInterface::IDENTIFIER;
    }
}
