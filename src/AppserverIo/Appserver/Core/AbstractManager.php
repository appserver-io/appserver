<?php

/**
 * AppserverIo\Appserver\Core\AbstractManager
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
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core;

use AppserverIo\Storage\StorageInterface;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Psr\Application\ManagerInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\Application\ManagerConfigurationInterface;
use AppserverIo\Psr\Naming\InitialContext as NamingDirectory;
use AppserverIo\Appserver\ServletEngine\RequestHandler;

/**
 * Abstract manager implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property \AppserverIo\Storage\StorageInterface                      $data                 Storage container for arbitrary data
 * @property \AppserverIo\Psr\Naming\InitialContext                     $initialContext       The initial context of our naming directory
 * @property \AppserverIo\Psr\Application\ApplicationInterface          $application          The application to manage
 * @property \AppserverIo\Psr\Application\ManagerConfigurationInterface $managerConfiguration The application to manage
 */
abstract class AbstractManager extends GenericStackable implements ManagerInterface
{

    /**
     * Inject the configuration for this manager.
     *
     * @param \AppserverIo\Psr\Application\ManagerConfigurationInterface $managerConfiguration The managers configuration
     *
     * @return void
     */
    public function injectManagerConfiguration(ManagerConfigurationInterface $managerConfiguration)
    {
        $this->managerConfiguration = $managerConfiguration;
    }

    /**
     * Inject the data storage.
     *
     * @param \AppserverIo\Storage\StorageInterface $data The data storage to use
     *
     * @return void
     */
    public function injectData(StorageInterface $data)
    {
        $this->data = $data;
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
     * Returns the application instance.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface|\AppserverIo\Psr\Naming\NamingDirectoryInterface The application instance
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * The global naming directory.
     *
     * @param \AppserverIo\Psr\Naming\InitialContext $initialContext The global naming directory
     *
     * @return void
     */
    public function injectInitialContext(NamingDirectory $initialContext)
    {
        $this->initialContext = $initialContext;
    }

    /**
     * Returns the global naming directory.
     *
     * @return \AppserverIo\Psr\Naming\InitialContext The global naming directory
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * Returns the absolute path to the web application.
     *
     * @return string The absolute path
     */
    public function getWebappPath()
    {
        return $this->getApplication()->getWebappPath();
    }

    /**
     * Returns the absolute path to the application directory.
     *
     * @return string The absolute path to the application directory
     */
    public function getAppBase()
    {
        return $this->getApplication()->getAppBase();
    }

    /**
     * Returns the absolute path to the application server's base directory.
     *
     * @param string $directoryToAppend A directory to append to the base directory
     *
     * @return string The absolute path the application server's base directory
     */
    public function getBaseDirectory($directoryToAppend = null)
    {
        return $this->getApplication()->getBaseDirectory($directoryToAppend);
    }

    /**
     * Registers the value with the passed key in the container.
     *
     * @param string $key   The key to register the value with
     * @param object $value The value to register
     *
     * @return void
     */
    public function setAttribute($key, $value)
    {
        $this->data->set($key, $value);
    }

    /**
     * Returns the attribute with the passed key from the container.
     *
     * @param string $key The key the requested value is registered with
     *
     * @return mixed|null The requested value if available
     */
    public function getAttribute($key)
    {
        if ($this->data->has($key)) {
            return $this->data->get($key);
        }
    }

    /**
     * Returns a new reflection class intance for the passed class name.
     *
     * @param string $className The class name to return the reflection class instance for
     *
     * @return \AppserverIo\Lang\Reflection\ReflectionClass The reflection instance
     */
    public function newReflectionClass($className)
    {
        return $this->getApplication()->search('ProviderInterface')->newReflectionClass($className);
    }

    /**
     * This returns a proxy to the requested session bean.
     *
     * @param string $lookupName The lookup name for the requested session bean
     * @param string $sessionId  The session-ID if available
     *
     * @return \AppserverIo\RemoteMethodInvocation\RemoteObjectInterface The proxy instance
     */
    public function lookupProxy($lookupName, $sessionId = null)
    {

        // load the initial context instance
        $initialContext = $this->getInitialContext();

        // query whether a request context is available
        if ($servletRequest = RequestHandler::getRequestContext()) {
            // inject the servlet request to handle SFSBs correctly
            $initialContext->injectServletRequest($servletRequest);
        }

        // lookup the proxy by the name and session ID if available
        return $initialContext->lookup($lookupName, $sessionId);
    }

    /**
     * Return's the manager configuration.
     *
     * @return \AppserverIo\Psr\Application\ManagerConfigurationInterface The manager configuration
     */
    public function getManagerConfiguration()
    {
        return $this->managerConfiguration;
    }

    /**
     * Returns a reflection class intance for the passed class name.
     *
     * @param string $className The class name to return the reflection class instance for
     *
     * @return \AppserverIo\Lang\Reflection\ReflectionClass The reflection instance
     * @see \AppserverIo\Psr\Di\ProviderInterface::getReflectionClass()
     */
    public function getReflectionClass($className)
    {
        return $this->getApplication()->search('ProviderInterface')->getReflectionClass($className);
    }

    /**
     * Returns a reflection class intance for the passed class name.
     *
     * @param object $instance The instance to return the reflection class instance for
     *
     * @return \AppserverIo\Lang\Reflection\ReflectionClass The reflection instance
     * @see \AppserverIo\Psr\Di\ProviderInterface::newReflectionClass()
     * @see \AppserverIo\Psr\Di\ProviderInterface::getReflectionClass()
     */
    public function getReflectionClassForObject($instance)
    {
        return $this->getApplication()->search('ProviderInterface')->getReflectionClassForObject($instance);
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
     * A dummy functionality to implement the stop functionality.
     *
     * @return void
     * \AppserverIo\Psr\Application\ManagerInterface::stop()
     */
    public function stop()
    {
        error_log(sprintf('Now shutdown manager %s', $this->getIdentifier()));
    }
}
