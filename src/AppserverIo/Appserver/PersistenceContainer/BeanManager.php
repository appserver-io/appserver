<?php

/**
 * \AppserverIo\Appserver\PersistenceContainer\BeanManager
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

use AppserverIo\Storage\GenericStackable;
use AppserverIo\Storage\StorageInterface;
use AppserverIo\Collections\CollectionInterface;
use AppserverIo\Lang\Reflection\AnnotationInterface;
use AppserverIo\RemoteMethodInvocation\RemoteMethodInterface;
use AppserverIo\Appserver\Core\Environment;
use AppserverIo\Appserver\Core\AbstractEpbManager;
use AppserverIo\Appserver\Core\Utilities\EnvironmentKeys;
use AppserverIo\Psr\Servlet\SessionUtils;
use AppserverIo\Psr\Di\ObjectManagerInterface;
use AppserverIo\Psr\Deployment\DescriptorInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\EnterpriseBeans\BeanContextInterface;
use AppserverIo\Psr\EnterpriseBeans\ResourceLocatorInterface;
use AppserverIo\Psr\EnterpriseBeans\InvalidBeanTypeException;
use AppserverIo\Psr\EnterpriseBeans\Description\BeanDescriptorInterface;
use AppserverIo\Psr\EnterpriseBeans\Description\SessionBeanDescriptorInterface;
use AppserverIo\Psr\EnterpriseBeans\Description\SingletonSessionBeanDescriptorInterface;
use AppserverIo\Psr\EnterpriseBeans\Description\StatefulSessionBeanDescriptorInterface;
use AppserverIo\Psr\EnterpriseBeans\Description\StatelessSessionBeanDescriptorInterface;
use AppserverIo\Psr\EnterpriseBeans\Description\MessageDrivenBeanDescriptorInterface;
use AppserverIo\Appserver\Application\Interfaces\ManagerSettingsInterface;
use AppserverIo\Appserver\Application\Interfaces\ManagerSettingsAwareInterface;
use AppserverIo\Appserver\PersistenceContainer\Utils\SessionBeanUtil;
use AppserverIo\Appserver\PersistenceContainer\Tasks\StartupBeanTask;
use AppserverIo\Appserver\PersistenceContainer\GarbageCollectors\StandardGarbageCollector;
use AppserverIo\Appserver\PersistenceContainer\RemoteMethodInvocation\ProxyGeneratorInterface;
use AppserverIo\Appserver\PersistenceContainer\GarbageCollectors\StartupBeanTaskGarbageCollector;

/**
 * The bean manager handles the message and session beans registered for the application.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property array                                                                                         $requestContext                  The request context to cache the beans
 * @property \AppserverIo\Psr\EnterpriseBeans\ResourceLocatorInterface                                     $resourceLocator                 The resource locator
 * @property \AppserverIo\Storage\StorageInterface                                                         $statefulSessionBeans            The storage for the stateful session beans
 * @property \AppserverIo\Storage\StorageInterface                                                         $singletonSessionBeans           The storage for the singleton session beans
 * @property \AppserverIo\Appserver\PersistenceContainer\BeanManagerSettingsInterface                      $managerSettings                 Settings for the bean manager
 * @property \AppserverIo\Appserver\PersistenceContainer\StatefulSessionBeanMapFactory                     $statefulSessionBeanMapFactory   The factory instance
 * @property \AppserverIo\Appserver\PersistenceContainer\ObjectFactoryInterface                            $objectFactory                   The object factory instance
 * @property \AppserverIo\Appserver\PersistenceContainer\RemoteMethodInvocation\ProxyGeneratorInterface    $remoteProxyGenerator            The remote proxy generator
 * @property \AppserverIo\Storage\GenericStackable                                                         $startupBeanTasks                The storage with manager's startup bean tasks
 * @property \AppserverIo\Appserver\PersistenceContainer\GarbageCollectors\StandardGarbageCollector        $garbageCollector                The standard garbage collection for the SFSBs
 * @property \AppserverIo\Appserver\PersistenceContainer\GarbageCollectors\StartupBeanTaskGarbageCollector $startupBeanTaskGarbageCollector The garbage collector for the startup bean tasks
 */
class BeanManager extends AbstractEpbManager implements BeanContextInterface, ManagerSettingsAwareInterface
{

    /**
     * Injects the request context to cache the beans.
     *
     * @param array $requestContext The request context
     *
     * @return void
     */
    public function injectRequestContext(array $requestContext)
    {
        $this->requestContext = $requestContext;
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
    public function injectStatefulSessionBeans($statefulSessionBeans)
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
     * Injects the bean manager settings.
     *
     * @param \AppserverIo\Appserver\PersistenceContainer\BeanManagerSettingsInterface $managerSettings The bean manager settings
     *
     * @return void
     */
    public function injectManagerSettings(ManagerSettingsInterface $managerSettings)
    {
        $this->managerSettings = $managerSettings;
    }

    /**
     * Injects the object factory instance.
     *
     * @param \AppserverIo\Appserver\PersistenceContainer\ObjectFactoryInterface $objectFactory The object factory instance
     *
     * @return void
     */
    public function injectObjectFactory(ObjectFactoryInterface $objectFactory)
    {
        $this->objectFactory = $objectFactory;
    }

    /**
     * Injects the garbage collector.
     *
     * @param \AppserverIo\Appserver\PersistenceContainer\GarbageCollectors\StandardGarbageCollector $garbageCollector The garbage collector
     *
     * @return void
     */
    public function injectGarbageCollector(StandardGarbageCollector $garbageCollector)
    {
        $this->garbageCollector = $garbageCollector;
    }

    /**
     * Injects the startup bean task garbage collector.
     *
     * @param \AppserverIo\Appserver\PersistenceContainer\GarbageCollectors\StartupBeanTaskGarbageCollector $startupBeanTaskGarbageCollector The garbage collector
     *
     * @return void
     */
    public function injectStartupBeanTaskGarbageCollector(StartupBeanTaskGarbageCollector $startupBeanTaskGarbageCollector)
    {
        $this->startupBeanTaskGarbageCollector = $startupBeanTaskGarbageCollector;
    }

    /**
     * Injects the remote proxy generator.
     *
     * @param \AppserverIo\Appserver\PersistenceContainer\RemoteMethodInvocation\ProxyGeneratorInterface $remoteProxyGenerator The remote proxy generator
     *
     * @return void
     */
    public function injectRemoteProxyGenerator(ProxyGeneratorInterface $remoteProxyGenerator)
    {
        $this->remoteProxyGenerator = $remoteProxyGenerator;
    }

    /**
     * Injects the storage for the startup bean tasks.
     *
     * @param \AppserverIo\Storage\GenericStackable $startupBeanTasks The storage for the startup bean tasks
     *
     * @return void
     */
    public function injectStartupBeanTasks(GenericStackable $startupBeanTasks)
    {
        $this->startupBeanTasks = $startupBeanTasks;
    }

    /**
     * Lifecycle callback that'll be invoked after the application has been started.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     * @see \AppserverIo\Psr\Application\ManagerInterface::postStartup()
     */
    public function postStartup(ApplicationInterface $application)
    {

        // load the object manager
        /** @var \AppserverIo\Psr\Di\ObjectManagerInterface $objectManager */
        $objectManager = $application->search(ObjectManagerInterface::IDENTIFIER);

        // register the beans found by annotations and the XML configuration
        /** \AppserverIo\Psr\Deployment\DescriptorInterface $objectDescriptor */
        foreach ($objectManager->getObjectDescriptors() as $descriptor) {
            // if we found a singleton session bean with a startup callback instanciate it
            if ($descriptor instanceof SingletonSessionBeanDescriptorInterface && $descriptor->isInitOnStartup()) {
                $this->startupBeanTasks[] = new StartupBeanTask($application, $descriptor);
            }
        }
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

        // add the application instance to the environment
        Environment::singleton()->setAttribute(EnvironmentKeys::APPLICATION, $application);

        // create s simulated request/session ID whereas session equals request ID
        Environment::singleton()->setAttribute(EnvironmentKeys::SESSION_ID, $sessionId = SessionUtils::generateRandomString());
        Environment::singleton()->setAttribute(EnvironmentKeys::REQUEST_ID, $sessionId);

        // finally register the beans
        $this->registerBeans($application);
    }

    /**
     * Registers the message beans at startup.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     */
    public function registerBeans(ApplicationInterface $application)
    {

        // parse the object descriptors
        $this->parseObjectDescriptors();

        // load the object manager
        /** @var \AppserverIo\Psr\Di\ObjectManagerInterface $objectManager */
        $objectManager = $this->getApplication()->search(ObjectManagerInterface::IDENTIFIER);

        // register the beans found by annotations and the XML configuration
        /** \AppserverIo\Psr\Deployment\DescriptorInterface $objectDescriptor */
        foreach ($objectManager->getObjectDescriptors() as $descriptor) {
            // check if we've found a bean descriptor and register the bean
            if ($descriptor instanceof BeanDescriptorInterface) {
                $this->registerBean($descriptor);
            }
        }
    }

    /**
     * Register the bean described by the passed descriptor.
     *
     * @param \AppserverIo\Psr\EnterpriseBeans\Description\BeanDescriptorInterface $descriptor The bean descriptor
     *
     * @return void
     */
    public function registerBean(BeanDescriptorInterface $descriptor)
    {

        try {
            // load the application instance
            $application = $this->getApplication();

            // register the bean with the default name/short class name
            $application->getNamingDirectory()
                        ->bind(
                            sprintf('php:global/%s/%s', $application->getUniqueName(), $descriptor->getName()),
                            array(&$this, 'lookup'),
                            array($descriptor->getName())
                        );

            // register's the bean's references
            $this->registerReferences($descriptor);

            // generate the remote proxy and register it in the naming directory
            $this->getRemoteProxyGenerator()->generate($descriptor);

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
    public function newAnnotationInstance(AnnotationInterface $annotation)
    {
        return $this->getApplication()->search('ProviderInterface')->newAnnotationInstance($annotation);
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
     * Return's the bean manager settings.
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\BeanManagerSettingsInterface The bean manager settings
     */
    public function getManagerSettings()
    {
        return $this->managerSettings;
    }

    /**
     * Returns the object factory instance.
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\ObjectFactoryInterface The object factory instance
     */
    public function getObjectFactory()
    {
        return $this->objectFactory;
    }

    /**
     * Returns the garbage collector instance.
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\GarbageCollectors\StandardGarbageCollector The garbage collector instance
     */
    public function getGarbageCollector()
    {
        return $this->garbageCollector;
    }

    /**
     * Returns the startup bean task garbage collector instance.
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\GarbageCollectors\StartupBeanTaskGarbageCollector The garbage collector instance
     */
    public function getStartupBeanTaskGarbageCollector()
    {
        return $this->startupBeanTaskGarbageCollector;
    }

    /**
     * Return's the remote proxy generator instance.
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\RemoteMethodInvocation\ProxyGeneratorInterface The remote proxy generator instance
     */
    public function getRemoteProxyGenerator()
    {
        return $this->remoteProxyGenerator;
    }

    /**
     * Return's the storage of the manager's post startup threads.
     *
     * @return \AppserverIo\Storage\GenericStackable The storage for the post startup threads
     */
    public function getStartupBeanTasks()
    {
        return $this->startupBeanTasks;
    }

    /**
     * Runs a lookup for the session bean with the passed class name and
     * session ID.
     *
     * If the passed class name is a session bean an instance
     * will be returned.
     *
     * @param string $className The name of the session bean's class
     * @param array  $args      The arguments passed to the session beans constructor
     *
     * @return object The requested bean instance
     */
    public function lookup($className, array $args = array())
    {
        return $this->getResourceLocator()->lookup($this, $className, $args);
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

        // create a unique SFSB identifier
        $identifier = SessionBeanUtil::createIdentifier($sessionId, $className);

        // load the map with the SFSBs
        $sessionBeans = $this->getStatefulSessionBeans();

        // if the SFSB exists, return it
        if ($sessionBeans->exists($identifier)) {
            return $sessionBeans->get($identifier);
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

        // create a unique SFSB identifier
        $identifier = SessionBeanUtil::createIdentifier($sessionId, $className);

        // load the map with the SFSBs
        $sessionBeans = $this->getStatefulSessionBeans();

        // query whether the SFSB with the passed identifier exists
        if ($sessionBeans->exists($identifier)) {
            $sessionBeans->remove($identifier, array($this, 'destroyBeanInstance'));
        }
    }

    /**
     * Returns a new instance of the SSB with the passed class name.
     *
     * @param string $className The fully qualified class name to return the instance for
     * @param array  $args      Arguments to pass to the constructor of the instance
     *
     * @return object The instance itself
     */
    public function newSingletonSessionBeanInstance($className, array $args = array())
    {
        return $this->getObjectFactory()->newInstance($className, $args);
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
        $objectManager = $this->getApplication()->search(ObjectManagerInterface::IDENTIFIER);

        // load the bean descriptor
        $descriptor = $objectManager->getObjectDescriptors()->get(get_class($instance));

        // invoke the pre-destroy callbacks if we've a session bean
        if ($descriptor instanceof SessionBeanDescriptorInterface) {
            foreach ($descriptor->getPreDestroyCallbacks() as $preDestroyCallback) {
                $instance->$preDestroyCallback();
            }
        }
    }

    /**
     * Invoke the passed remote method on the described session bean and return the result.
     *
     * @param \AppserverIo\RemoteMethodInvocation\RemoteMethodInterface $remoteMethod The remote method description
     * @param \AppserverIo\Collections\CollectionInterface              $sessions     The collection with the sessions
     *
     * @return mixed The result of the remote method invocation
     */
    public function invoke(RemoteMethodInterface $remoteMethod, CollectionInterface $sessions)
    {

        // prepare method name and parameters and invoke method
        $className  = $remoteMethod->getClassName();
        $methodName = $remoteMethod->getMethodName();
        $parameters = $remoteMethod->getParameters();

        // load the session ID from the environment
        $sessionId = Environment::singleton()->getAttribute(EnvironmentKeys::SESSION_ID);

        // load the application instance
        $application = $this->getApplication();

        // load a fresh bean instance and add it to the session container
        $instance = $this->lookup($className);

        // invoke the remote method call on the local instance
        $response = call_user_func_array(array($instance, $methodName), $parameters);

        // load the object manager
        $objectManager = $application->search(ObjectManagerInterface::IDENTIFIER);

        // load the bean descriptor
        $objectDescriptor = $objectManager->getObjectDescriptor($className);

        // initialize the flag to mark the instance to be re-attached
        $attach = true;

        // query if we've SFSB
        if ($objectDescriptor instanceof StatefulSessionBeanDescriptorInterface) {
            // remove the SFSB instance if a remove method has been called
            if ($objectDescriptor->isRemoveMethod($methodName)) {
                $this->removeStatefulSessionBean($sessionId, $objectDescriptor->getClassName());
                $attach = false;
            }
        }

        // re-attach the bean instance if necessary
        if ($attach === true) {
            $this->attach($objectDescriptor, $instance);
        }

        // return the remote method call result
        return $response;
    }

    /**
     * Attaches the passed bean, depending on it's type to the container.
     *
     * @param \AppserverIo\Psr\Deployment\DescriptorInterface $objectDescriptor The object descriptor for the passed instance
     * @param object                                          $instance         The bean instance to attach
     *
     * @return void
     * @throws \AppserverIo\Psr\EnterpriseBeans\InvalidBeanTypeException Is thrown if a invalid bean type has been detected
     */
    public function attach(DescriptorInterface $objectDescriptor, $instance)
    {

        // load the session ID from the environment
        $sessionId = Environment::singleton()->getAttribute(EnvironmentKeys::SESSION_ID);

        // query if we've stateful session bean
        if ($objectDescriptor instanceof StatefulSessionBeanDescriptorInterface) {
            // check if we've a session-ID available
            if ($sessionId == null) {
                throw new \Exception('Can\'t find a session-ID to attach stateful session bean');
            }

            // load the lifetime from the session bean settings
            $lifetime = $this->getManagerSettings()->getLifetime();

            // we've to check for pre-attach callbacks
            foreach ($objectDescriptor->getPreAttachCallbacks() as $preAttachCallback) {
                $instance->$preAttachCallback();
            }

            // create a unique SFSB identifier
            $identifier = SessionBeanUtil::createIdentifier($sessionId, $objectDescriptor->getName());

            // load the map with the SFSBs
            $sessionBeans = $this->getStatefulSessionBeans();

            // add the stateful session bean to the map
            $sessionBeans->add($identifier, $instance, $lifetime);

            // stop processing here
            return;
        }

        // query if we've stateless session or message bean
        if ($objectDescriptor instanceof StatelessSessionBeanDescriptorInterface ||
            $objectDescriptor instanceof MessageDrivenBeanDescriptorInterface) {
            // simply destroy the instance
            $this->destroyBeanInstance($instance);

            // stop processing here
            return;
        }

        // query if we've singleton session bean
        if ($objectDescriptor instanceof SingletonSessionBeanDescriptorInterface) {
            // we've to check for pre-attach callbacks
            foreach ($objectDescriptor->getPreAttachCallbacks() as $preAttachCallback) {
                $instance->$preAttachCallback();
            }

            // stop processing here
            return;
        }

        // we've an unknown bean type => throw an exception
        throw new InvalidBeanTypeException('Tried to attach invalid bean type');
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

    /**
     * Shutdown the session manager instance.
     *
     * @return void
     * \AppserverIo\Psr\Application\ManagerInterface::stop()
     */
    public function stop()
    {
        $this->getGarbageCollector()->stop();
        $this->getStartupBeanTaskGarbageCollector()->stop();
        $this->getObjectFactory()->stop();
    }
}
