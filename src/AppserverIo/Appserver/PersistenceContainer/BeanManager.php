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
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\PersistenceContainer;

use AppserverIo\Appserver\Naming\InitialContext;
use AppserverIo\Collections\ArrayList;
use AppserverIo\Collections\HashMap;
use AppserverIo\Storage\StorageInterface;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Storage\StackableStorage;
use AppserverIo\Psr\PersistenceContainerProtocol\BeanContext;
use AppserverIo\Psr\PersistenceContainerProtocol\RemoteMethod;
use AppserverIo\Psr\Application\ManagerInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\EnterpriseBeans\Annotations\MessageDriven;
use AppserverIo\Psr\EnterpriseBeans\Annotations\PreDestroy;
use AppserverIo\Psr\EnterpriseBeans\Annotations\PostConstruct;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Singleton;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Startup;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Stateful;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Stateless;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Schedule;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Timeout;
use AppserverIo\Psr\EnterpriseBeans\Annotations\EnterpriseBean;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Resource;
use AppserverIo\Lang\Reflection\ClassInterface;
use AppserverIo\Lang\Reflection\ReflectionClass;
use AppserverIo\Lang\Reflection\ReflectionObject;
use AppserverIo\Lang\Reflection\AnnotationInterface;
use AppserverIo\Appserver\PersistenceContainer\Utils\BeanConfiguration;
use AppserverIo\Appserver\PersistenceContainer\Utils\BeanConfigurationInterface;

/**
 * The bean manager handles the message and session beans registered for the application.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */
class BeanManager extends GenericStackable implements BeanContext, ManagerInterface
{

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
     * Injects the absolute path to the web application.
     *
     * @param string $webappPath The absolute path to this web application
     *
     * @return void
     */
    public function injectWebappPath($webappPath)
    {
        $this->webappPath = $webappPath;
    }

    /**
     * Injects the resource locator that locates the requested queue.
     *
     * @param \AppserverIo\Appserver\MessageQueue\ResourceLocator $resourceLocator The resource locator
     *
     * @return void
     */
    public function injectResourceLocator(ResourceLocator $resourceLocator)
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
     * @param \AppserverIo\Appserver\PersistenceContainer\StatefulSessionBeanSettings $statefulSessionBeanSettings Settings for the stateful session beans
     *
     * @return void
     */
    public function injectStatefulSessionBeanSettings(StatefulSessionBeanSettings $statefulSessionBeanSettings)
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
     * Injects the storage for the bean configurations.
     *
     * @param \AppserverIo\Storage\StorageInterface $beanConfigurations The storage for the bean configurations
     *
     * @return void
     */
    public function injectBeanConfigurations(StorageInterface $beanConfigurations)
    {
        $this->beanConfigurations = $beanConfigurations;
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

        // build up META-INF directory var
        $metaInfDir = $this->getWebappPath() . DIRECTORY_SEPARATOR .'META-INF';

        // check if we've found a valid directory
        if (is_dir($metaInfDir) === false) {
            return;
        }

        // check META-INF + subdirectories for classes with beans to be pre-initialized
        $service = $application->newService('AppserverIo\Appserver\Core\Api\DeploymentService');
        $phpFiles = $service->globDir($metaInfDir . DIRECTORY_SEPARATOR . '*.php');

        // iterate all php files
        foreach ($phpFiles as $phpFile) {

            try {

                // cut off the META-INF directory and replace OS specific directory separators
                $relativePathToPhpFile = str_replace(DIRECTORY_SEPARATOR, '\\', str_replace($metaInfDir, '', $phpFile));

                // now cut off the first directory, that'll be '/classes' by default
                $pregResult = preg_replace('%^(\\\\*)[^\\\\]+%', '', $relativePathToPhpFile);
                $className = substr($pregResult, 0, -4);

                // we need a reflection class to read the annotations
                $reflectionClass = $this->getReflectionClass($className);

                // load the bean configuration
                $configuration = BeanConfiguration::fromReflectionClass($reflectionClass);

                if ($configuration->getName()) { // if we've a name
                    $this->getBeanConfigurations()->set($configuration->getClassName(), $configuration);
                }

            } catch (\Exception $e) { // if class can not be reflected continue with next class

                // log an error message
                $application->getInitialContext()->getSystemLogger()->error($e->__toString());

                // proceed with the nexet bean
                continue;
            }
        }

        // query whether we found epb.xml deployment descriptor file
        if (file_exists($deploymentDescriptor = $metaInfDir . DIRECTORY_SEPARATOR . 'epb.xml') === true) {

            // load the application config
            $config = new \SimpleXMLElement(file_get_contents($deploymentDescriptor));

            // intialize the session beans by parsing the nodes
            foreach ($config->xpath('/epb/enterprise-beans/session') as $key => $session) {

                // load the configuration
                $configuration = BeanConfiguration::fromDeploymentDescriptor($session);

                // query whether we've to merge the configuration found in annotations
                if ($this->getBeanConfigurations()->has($configuration->getClassName())) { // merge the configuration

                    // load the existing configuration
                    $existingConfiguration = $this->getBeanConfigurations()->get($configuration->getClassName());

                    // merge the configurations => XML configuration overrides values from annotation
                    $existingConfiguration->merge($configuration);

                    // save the merge configuration
                    $this->getBeanConfigurations()->set($existingConfiguration->getClassName(), $existingConfiguration);

                } else {

                    // save the XML configuration
                    $this->getBeanConfigurations()->set($configuration->getClassName(), $configuration);
                }
            }

            // intialize the message beans by parsing the nodes
            foreach ($config->xpath('/epb/enterprise-beans/message-driven') as $key => $messageDriven) {

                // load the configuration
                $configuration = BeanConfiguration::fromDeploymentDescriptor($messageDriven);

                // query whether we've to merge the configuration found in annotations
                if ($this->getBeanConfigurations()->has($configuration->getClassName())) { // merge the configuration

                    // load the existing configuration
                    $existingConfiguration = $this->getBeanConfigurations()->get($configuration->getClassName());

                    // merge the configurations => XML configuration overrides values from annotation
                    $existingConfiguration->merge($configuration);

                    // save the merge configuration
                    $this->getBeanConfigurations()->set($existingConfiguration->getClassName(), $existingConfiguration);

                } else {

                    // save the XML configuration
                    $this->getBeanConfigurations()->set($configuration->getClassName(), $configuration);
                }
            }
        }

        // register the beans located by annotations and the XML configuration
        foreach ($this->getBeanConfigurations() as $className => $configuration) {

            // check if we've found a bean configuration
            if ($configuration instanceof BeanConfigurationInterface) {

                // register the bean
                $this->registerBean($configuration);

                // if we found a bean with @Singleton + @Startup annotation
                if ($configuration->isInitOnStartup()) {
                    $this->getApplication()->search($configuration->getName(), array(null, array($application)));
                }
            }
        }
    }

    /**
     * Register the bean with the passed configuration.
     *
     * @param \AppserverIo\Appserver\PersistenceContainer\Utils\BeanConfiguration $configuration The beans configuration
     *
     * @return void
     */
    protected function registerBean(BeanConfiguration $configuration)
    {

        // load the beans class name
        $className = $configuration->getClassName();

        // register the bean with the default name/short class name
        $this->getApplication()->bind($configuration->getName(), array(&$this, 'lookup'), array($className));

        // register the bean with the interface name
        if ($beanInterface = $configuration->getBeanInterface()) {
            $this->getApplication()->bind($beanInterface, array(&$this, 'lookup'), array($className));
        }
        // register the bean with the bean name
        if ($beanName = $configuration->getBeanName()) {
            $this->getNamingDirectory()->bind($beanName, array(&$this, 'lookup'), array($className));
        }

        // register the bean with the mapped name
        if ($mappedName = $configuration->getMappedName()) {
            $this->getNamingDirectory()->bind($mappedName, array(&$this, 'lookup'), array($className));
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
     * Returns the application instance.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application instance
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Returns the absolute path to the web application.
     *
     * @return string The absolute path
     */
    public function getWebappPath()
    {
        return $this->webappPath;
    }

    /**
     * Return the resource locator instance.
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\ResourceLocator The resource locator instance
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
     * @return \AppserverIo\Appserver\PersistenceContainer\BeanSettings The stateful session bean settings
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
     * Return the storage with the bean configurations.
     *
     * @return \AppserverIo\Storage\StorageInterface The storage with the bean configurations
     */
    public function getBeanConfigurations()
    {
        return $this->beanConfigurations;
    }

    /**
     * Tries to locate the queue that handles the request and returns the instance
     * if one can be found.
     *
     * @param \AppserverIo\Psr\PersistenceContainerProtocol\RemoteMethod $remoteMethod The remote method call
     * @param array                                                      $args         The arguments passed to the session beans constructor
     *
     * @return object The requested bean instance
     */
    public function locate(RemoteMethod $remoteMethod, array $args = array())
    {

        // load the information to locate the requested bean
        $className = $remoteMethod->getClassName();
        $sessionId = $remoteMethod->getSessionId();

        // lookup the requested bean
        return $this->lookup($className, $sessionId, $args);
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
     * @throws \AppserverIo\Appserver\PersistenceContainer\InvalidBeanTypeException Is thrown if passed class name is no session bean or is a entity bean (not implmented yet)
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
     * Invokes the bean method with the @PreDestroy annotation.
     *
     * @param object $instance The instance to invoke the method
     *
     * @return void
     */
    public function destroyBeanInstance($instance)
    {

        // load the bean configuration
        $beanConfiguration = $this->getBeanConfigurations()->get(get_class($instance));

        // invoke the pre-destory callbacks
        foreach ($beanConfiguration->getPreDestroyCallbacks() as $preDestroyCallback) {
            $instance->$preDestroyCallback();
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

        // load the bean configuration
        $configuration = $this->getBeanConfigurations()->get(get_class($instance));

        // query the bean type, one of
        switch ($configuration->getSessionType()) {

            case Stateful::ANNOTATION:

                // check if we've a session-ID available
                if ($sessionId == null) {
                    throw new \Exception('Can\'t find a session-ID to attach stateful session bean');
                }

                // load the lifetime from the session bean settings
                $lifetime = $this->getStatefulSessionBeanSettings()->getLifetime();

                // initialize the map for the stateful session beans
                if ($this->getStatefulSessionBeans()->has($sessionId) === false) { // create a new session bean map instance
                    $this->getStatefulSessionBeanMapFactory()->newInstance($sessionId);

                }

                // load the session bean map instance
                $sessions = $this->getStatefulSessionBeans()->get($sessionId);

                // add the stateful session bean to the map
                $sessions->add($configuration->getClassName(), $instance, $lifetime);

                break;

            case Stateless::ANNOTATION:
            case MessageDriven::ANNOTATION:

                // simply destroy the instance
                $this->destroyBeanInstance($instance);

                break;

            case Singleton::ANNOTATION:

                // do nothing here
                break;

            default:

                // we've an unknown bean type => throw an exception
                throw new InvalidBeanTypeException('Try to attach invalid bean type');
        }
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
     * Returns a reflection class intance for the passed class name.
     *
     * @param string $className The class name to return the reflection class instance for
     *
     * @return \AppserverIo\Lang\Reflection\ReflectionClass The reflection instance
     * @see \DependencyInjectionContainer\Interfaces\ProviderInterface::getReflectionClass()
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
     * @see \DependencyInjectionContainer\Interfaces\ProviderInterface::newReflectionClass()
     * @see \DependencyInjectionContainer\Interfaces\ProviderInterface::getReflectionClass()
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
     * Initializes the manager instance.
     *
     * @return void
     * @see \AppserverIo\Psr\Application\ManagerInterface::initialize()
     */
    public function getIdentifier()
    {
        return BeanContext::IDENTIFIER;
    }
}
