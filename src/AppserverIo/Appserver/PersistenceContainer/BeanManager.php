<?php

/**
 * AppserverIo\Appserver\PersistenceContainer\BeanManager
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\PersistenceContainer;

use AppserverIo\Appserver\Core\AbstractEpbManager;
use AppserverIo\Storage\StorageInterface;
use AppserverIo\Appserver\Core\Api\InvalidConfigurationException;
use AppserverIo\Lang\Reflection\AnnotationInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\EnterpriseBeans\BeanContextInterface;
use AppserverIo\Psr\EnterpriseBeans\ResourceLocatorInterface;
use AppserverIo\Psr\EnterpriseBeans\InvalidBeanTypeException;
use AppserverIo\Appserver\DependencyInjectionContainer\DirectoryParser;
use AppserverIo\Appserver\DependencyInjectionContainer\DeploymentDescriptorParser;
use AppserverIo\Psr\EnterpriseBeans\Description\BeanDescriptorInterface;
use AppserverIo\Psr\EnterpriseBeans\Description\SessionBeanDescriptorInterface;
use AppserverIo\Psr\EnterpriseBeans\Description\SingletonSessionBeanDescriptorInterface;
use AppserverIo\Psr\EnterpriseBeans\Description\StatefulSessionBeanDescriptorInterface;
use AppserverIo\Psr\EnterpriseBeans\Description\StatelessSessionBeanDescriptorInterface;
use AppserverIo\Psr\EnterpriseBeans\Description\MessageDrivenBeanDescriptorInterface;

/**
 * The bean manager handles the message and session beans registered for the application.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class BeanManager extends AbstractEpbManager implements BeanContextInterface
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
     * Injects the resource locator to lookup the requested queue.
     *
     * @param \AppserverIo\Psr\EnterpriseBeans\ResourceLocatorInterface $resourceLocator The resource locator
     *
     * @return void
     */
    public function injectResourceLocator(ResourceLocatorInterface $resourceLocator)
    {
        $this->resourceLocator = $resourceLocator;
    }

    /**
     * Injects the storage for the stateful session beans.
     *
     * @param \AppserverIo\Storage\StorageInterface $statefulSessionBeans The storage for the stateful session beans
     *
     * @return void
     */
    public function injectStatefulSessionBeans(StorageInterface $statefulSessionBeans)
    {
        $this->statefulSessionBeans = $statefulSessionBeans;
    }

    /**
     * Injects the storage for the singleton session beans.
     *
     * @param \AppserverIo\Storage\StorageInterface $singletonSessionBeans The storage for the singleton session beans
     *
     * @return void
     */
    public function injectSingletonSessionBeans(StorageInterface $singletonSessionBeans)
    {
        $this->singletonSessionBeans = $singletonSessionBeans;
    }

    /**
     * Injects the stateful session bean settings.
     *
     * @param \AppserverIo\Appserver\PersistenceContainer\StatefulSessionBeanSettingsInterface $statefulSessionBeanSettings Settings for the stateful session beans
     *
     * @return void
     */
    public function injectStatefulSessionBeanSettings(StatefulSessionBeanSettingsInterface $statefulSessionBeanSettings)
    {
        $this->statefulSessionBeanSettings = $statefulSessionBeanSettings;
    }

    /**
     * Injects the stateful session bean map factory.
     *
     * @param \AppserverIo\Appserver\PersistenceContainer\StatefulSessionBeanMapFactory $statefulSessionBeanMapFactory The factory instance
     *
     * @return void
     */
    public function injectStatefulSessionBeanMapFactory(StatefulSessionBeanMapFactory $statefulSessionBeanMapFactory)
    {
        $this->statefulSessionBeanMapFactory = $statefulSessionBeanMapFactory;
    }

    /**
     * Injects the object factory instance.
     *
     * @param \AppserverIo\Appserver\PersistenceContainer\ObjectFactory $objectFactory The object factory instance
     *
     * @return void
     */
    public function injectObjectFactory(ObjectFactoryInterface $objectFactory)
    {
        $this->objectFactory = $objectFactory;
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
        $this->registerBeans($application);
    }

    /**
     * Registers the message beans at startup.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     */
    protected function registerBeans(ApplicationInterface $application)
    {

        // query if the web application folder exists
        if (is_dir($folder = $this->getWebappPath()) === false) {
            // if not, do nothing
            return;
        }

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

        // parse the directory for annotated beans
        $directoryParser = new DirectoryParser();
        $directoryParser->injectApplication($application);

        // parse the directories for annotated servlets
        foreach ($directories as $directory) {
            $directoryParser->parse($directory);
        }

        // it's no valid application without at least the epb.xml file
        if (file_exists($deploymentDescriptor = $folder . DIRECTORY_SEPARATOR . 'META-INF' . DIRECTORY_SEPARATOR . 'epb.xml')) {
            try {
                // parse the deployment descriptor for registered beans
                $deploymentDescriptorParser = new DeploymentDescriptorParser();
                $deploymentDescriptorParser->injectApplication($application);
                $deploymentDescriptorParser->parse($deploymentDescriptor, '/a:epb/a:enterprise-beans/a:session');
                $deploymentDescriptorParser->parse($deploymentDescriptor, '/a:epb/a:enterprise-beans/a:message-driven');

            } catch (InvalidConfigurationException $e) {
                $application->getInitialContext()->getSystemLogger()->critical($e->getMessage());
            }
        }

        // load the object manager
        $objectManager = $this->getApplication()->search('ObjectManagerInterface');

        // register the beans found by annotations and the XML configuration
        foreach ($objectManager->getObjectDescriptors() as $descriptor) {
            // check if we've found a bean descriptor
            if ($descriptor instanceof BeanDescriptorInterface) {
                // register the bean
                $this->registerBean($descriptor);
            }

            // if we found a singleton session bean with a startup callback
            if ($descriptor instanceof SingletonSessionBeanDescriptorInterface && $descriptor->isInitOnStartup()) {
                $this->getApplication()->search($descriptor->getName(), array($sessionId = null, array($application)));
            }
        }
    }

    /**
     * Register the bean described by the passed descriptor.
     *
     * @param \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\BeanDescriptorInterface $descriptor The bean descriptor
     *
     * @return void
     */
    protected function registerBean(BeanDescriptorInterface $descriptor)
    {

        try {
            // register the bean with the default name/short class name
            $this->getApplication()->bind($descriptor->getName(), array(&$this, 'lookup'), array($descriptor->getClassName()));

            //  register the EPB references
            foreach ($descriptor->getEpbReferences() as $epbReference) {
                $this->registerEpbReference($epbReference);
            }

            // register the resource references
            foreach ($descriptor->getResReferences() as $resReference) {
                $this->registerResReference($resReference);
            }

        } catch (\Exception $e) {
            // log the exception
            $this->getApplication()->getInitialContext()->getSystemLogger()->critical($e->__toString());
        }
    }

    /**
     * Creates a new new instance of the annotation type, defined in the passed reflection annotation.
     *
     * @param \AppserverIo\Lang\Reflection\AnnotationInterface $annotation The reflection annotation we want to create the instance for
     *
     * @return \AppserverIo\Lang\Reflection\AnnotationInterface The real annotation instance
     */
    protected function newAnnotationInstance(AnnotationInterface $annotation)
    {
        return $this->getApplication()->search('ProviderInterface')->newAnnotationInstance($annotation);
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
     * Return the resource locator instance.
     *
     * @return \AppserverIo\Psr\EnterpriseBeans\ResourceLocatorInterface The resource locator instance
     */
    public function getResourceLocator()
    {
        return $this->resourceLocator;
    }

    /**
     * Return the storage with the naming directory.
     *
     * @return \AppserverIo\Storage\StorageInterface The storage with the naming directory
     */
    public function getNamingDirectory()
    {
        return $this->getApplication()->getNamingDirectory();
    }

    /**
     * Return the storage with the singleton session beans.
     *
     * @return \AppserverIo\Storage\StorageInterface The storage with the singleton session beans
     */
    public function getSingletonSessionBeans()
    {
        return $this->singletonSessionBeans;
    }

    /**
     * Return the storage with the stateful session beans.
     *
     * @return \AppserverIo\Storage\StorageInterface The storage with the stateful session beans
     */
    public function getStatefulSessionBeans()
    {
        return $this->statefulSessionBeans;
    }

    /**
     * Returns the stateful session bean settings.
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\StatefulSessionBeanSettingsInterface The stateful session bean settings
     */
    public function getStatefulSessionBeanSettings()
    {
        return $this->statefulSessionBeanSettings;
    }

    /**
     * Returns the stateful session bean map factory.
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\StatefulSessionBeanMapFactory The factory instance
     */
    public function getStatefulSessionBeanMapFactory()
    {
        return $this->statefulSessionBeanMapFactory;
    }

    /**
     * Returns the object factory instance.
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\ObjectFactory The object factory instance
     */
    public function getObjectFactory()
    {
        return $this->objectFactory;
    }

    /**
     * Runs a lookup for the session bean with the passed class name and
     * session ID.
     *
     * If the passed class name is a session bean an instance
     * will be returned.
     *
     * @param string $className The name of the session bean's class
     * @param string $sessionId The session ID
     * @param array  $args      The arguments passed to the session beans constructor
     *
     * @return object The requested bean instance
     * @throws \AppserverIo\Psr\EnterpriseBeans\InvalidBeanTypeException Is thrown if passed class name is no session bean or is a entity bean (not implmented yet)
     */
    public function lookup($className, $sessionId = null, array $args = array())
    {
        return $this->getResourceLocator()->lookup($this, $className, $sessionId, $args);
    }

    /**
     * Retrieves the requested stateful session bean.
     *
     * @param string $sessionId The session-ID of the stateful session bean to retrieve
     * @param string $className The class name of the session bean to retrieve
     *
     * @return object|null The stateful session bean if available
     */
    public function lookupStatefulSessionBean($sessionId, $className)
    {

        // check if the session has already been initialized
        if ($this->getStatefulSessionBeans()->has($sessionId) === false) {
            return;
        }

        // check if the stateful session bean has already been initialized
        if ($this->getStatefulSessionBeans()->get($sessionId)->exists($className) === true) {
            return $this->getStatefulSessionBeans()->get($sessionId)->get($className);
        }
    }

    /**
     * Removes the stateful session bean with the passed session-ID and class name
     * from the bean manager.
     *
     * @param string $sessionId The session-ID of the stateful session bean to retrieve
     * @param string $className The class name of the session bean to retrieve
     *
     * @return void
     */
    public function removeStatefulSessionBean($sessionId, $className)
    {

        // check if the session has already been initialized
        if ($this->getStatefulSessionBeans()->has($sessionId) === false) {
            return;
        }

        // check if the stateful session bean has already been initialized
        if ($this->getStatefulSessionBeans()->get($sessionId)->exists($className) === true) {
            // remove the stateful session bean from the sessions
            $sessions = $this->getStatefulSessionBeans()->get($sessionId);

            // remove the instance from the sessions
            $sessions->remove($className, array($this, 'destroyBeanInstance'));

            // check if we've to remove the SFB map
            if ($sessions->size() === 0) {
                $this->getStatefulSessionBeans()->remove($sessionId);
            }
        }
    }

    /**
     * Returns a new instance of the SSB with the passed class name.
     *
     * @param string      $className The fully qualified class name to return the instance for
     * @param string|null $sessionId The session-ID, necessary to inject stateful session beans (SFBs)
     * @param array       $args      Arguments to pass to the constructor of the instance
     *
     * @return object The instance itself
     */
    public function newSingletonSessionBeanInstance($className, $sessionId = null, array $args = array())
    {
        return $this->getObjectFactory()->newInstance($className, $sessionId, $args);
    }

    /**
     * Retrieves the requested singleton session bean.
     *
     * @param string $className The class name of the session bean to retrieve
     *
     * @return object|null The singleton session bean if available
     */
    public function lookupSingletonSessionBean($className)
    {
        if ($this->getSingletonSessionBeans()->has($className) === true) {
            return $this->getSingletonSessionBeans()->get($className);
        }
    }

    /**
     * Invokes the bean method with a pre-destroy callback.
     *
     * @param object $instance The instance to invoke the method
     *
     * @return void
     */
    public function destroyBeanInstance($instance)
    {

        // load the object manager
        $objectManager = $this->getApplication()->search('ObjectManagerInterface');

        // load the bean descriptor
        $descriptor = $objectManager->getObjectDescriptors()->get(get_class($instance));

        // invoke the pre-destory callbacks if we've a session bean
        if ($descriptor instanceof SessionBeanDescriptorInterface) {
            foreach ($descriptor->getPreDestroyCallbacks() as $preDestroyCallback) {
                $instance->$preDestroyCallback();
            }
        }
    }

    /**
     * Attaches the passed bean, depending on it's type to the container.
     *
     * @param object $instance  The bean instance to attach
     * @param string $sessionId The session-ID when we have stateful session bean
     *
     * @return void
     * @throws \Exception Is thrown if we have a stateful session bean, but no session-ID passed
     */
    public function attach($instance, $sessionId = null)
    {

        // load the object manager
        $objectManager = $this->getApplication()->search('ObjectManagerInterface');

        // load the bean descriptor
        $descriptor = $objectManager->getObjectDescriptors()->get(get_class($instance));

        // query if we've stateful session bean
        if ($descriptor instanceof StatefulSessionBeanDescriptorInterface) {
            // check if we've a session-ID available
            if ($sessionId == null) {
                throw new \Exception('Can\'t find a session-ID to attach stateful session bean');
            }

            // load the lifetime from the session bean settings
            $lifetime = $this->getStatefulSessionBeanSettings()->getLifetime();

            // initialize the map for the stateful session beans
            if ($this->getStatefulSessionBeans()->has($sessionId) === false) {
                // create a new session bean map instance
                $this->getStatefulSessionBeanMapFactory()->newInstance($sessionId);
            }

            // load the session bean map instance
            $sessions = $this->getStatefulSessionBeans()->get($sessionId);

            // add the stateful session bean to the map
            $sessions->add($descriptor->getClassName(), $instance, $lifetime);

            // stop processing here
            return;
        }

        // query if we've stateless session or message bean
        if ($descriptor instanceof StatelessSessionBeanDescriptorInterface ||
            $descriptor instanceof MessageDrivenBeanDescriptorInterface) {
            // simply destroy the instance
            $this->destroyBeanInstance($instance);

            // stop processing here
            return;
        }

        // query if we've singleton session bean
        if ($descriptor instanceof SingletonSessionBeanDescriptorInterface) {
            // do nothing here
            return;
        }

        // we've an unknown bean type => throw an exception
        throw new InvalidBeanTypeException('Try to attach invalid bean type');
    }

    /**
     * Returns the identifier for the bean manager instance.
     *
     * @return string
     * @see \AppserverIo\Psr\Application\ManagerInterface::getIdentifier()
     */
    public function getIdentifier()
    {
        return BeanContextInterface::IDENTIFIER;
    }
}
