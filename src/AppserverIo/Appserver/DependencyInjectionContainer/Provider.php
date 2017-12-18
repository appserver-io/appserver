<?php

/**
 * \AppserverIo\Appserver\DependencyInjectionContainer\Provider
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

namespace AppserverIo\Appserver\DependencyInjectionContainer;

use AppserverIo\Storage\GenericStackable;
use AppserverIo\Lang\Reflection\ClassInterface;
use AppserverIo\Lang\Reflection\ReflectionClass;
use AppserverIo\Lang\Reflection\ReflectionMethod;
use AppserverIo\Lang\Reflection\AnnotationInterface;
use AppserverIo\Description\NameAwareDescriptorInterface;
use AppserverIo\Description\ReferenceDescriptorInterface;
use AppserverIo\Description\FactoryAwareDescriptorInterface;
use AppserverIo\Appserver\Core\Environment;
use AppserverIo\Appserver\Core\Utilities\EnvironmentKeys;
use AppserverIo\Psr\Servlet\Annotations\Route;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\Deployment\DescriptorInterface;
use AppserverIo\Psr\Di\ProviderInterface;
use AppserverIo\Psr\Di\ObjectManagerInterface;
use AppserverIo\Psr\Di\DependencyInjectionException;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Inject;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Factory;
use AppserverIo\Psr\EnterpriseBeans\Annotations\MessageDriven;
use AppserverIo\Psr\EnterpriseBeans\Annotations\PreDestroy;
use AppserverIo\Psr\EnterpriseBeans\Annotations\PostConstruct;
use AppserverIo\Psr\EnterpriseBeans\Annotations\PreAttach;
use AppserverIo\Psr\EnterpriseBeans\Annotations\PostDetach;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Singleton;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Startup;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Stateful;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Stateless;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Schedule;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Timeout;
use AppserverIo\Psr\EnterpriseBeans\Annotations\EnterpriseBean;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Resource;
use AppserverIo\Psr\EnterpriseBeans\Annotations\PersistenceUnit;
use AppserverIo\Psr\EnterpriseBeans\Description\BeanReferenceDescriptorInterface;

/**
 * A basic dependency injection provider implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property \AppserverIo\Psr\Naming\NamingDirectoryInterface $namingDirectory The applications naming directory interface
 * @property \AppserverIo\Psr\Application\ApplicationInterfac $application     The application instance
 */
class Provider extends GenericStackable implements ProviderInterface
{

    /**
     * Dependencies for the actual injection target.
     *
     * @var array
     */
    protected $dependencies = array();

    /**
     * The managers unique identifier.
     *
     * @return string The unique identifier
     * @see \AppserverIo\Psr\Application\ManagerInterface::getIdentifier()
     */
    public function getIdentifier()
    {
        return ProviderInterface::IDENTIFIER;
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

        // initialize the deployment descriptor parser and parse the web application's deployment descriptor for beans
        $deploymentDescriptorParser = new DeploymentDescriptorParser();
        $deploymentDescriptorParser->injectProviderContext($this);
        $deploymentDescriptorParser->parse();
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
        // not yet implemented
    }

    /**
     * Injects the storage for the loaded reflection classes.
     *
     * @param \AppserverIo\Storage\GenericStackable $reflectionClasses The storage for the reflection classes
     *
     * @return void
     */
    public function injectReflectionClasses($reflectionClasses)
    {
        $this->reflectionClasses = $reflectionClasses;
    }

    /**
     * Injects the naming directory aliases.
     *
     * @param \AppserverIo\Storage\GenericStackable $namingDirectoryAliases The naming directory aliases
     *
     * @return void
     */
    public function injectNamingDirectoryAliases($namingDirectoryAliases)
    {
        $this->namingDirectoryAliases = $namingDirectoryAliases;
    }

    /**
     * The application instance.
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
     * Returns the naming context instance.
     *
     * @return \AppserverIo\Psr\Naming\InitialContext The naming context instance
     */
    public function getInitialContext()
    {
        throw new \Exception(sprintf('%s not implemented yet', __METHOD__));
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
     * Returns the applications naming directory.
     *
     * @return \AppserverIo\Psr\Naming\NamingDirectoryInterface The applications naming directory interface
     */
    public function getNamingDirectory()
    {
        return $this->getApplication()->getNamingDirectory();
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
        return $annotation->newInstance($annotation->getAnnotationName(), $annotation->getValues());
    }

    /**
     * Returns a reflection class instance for the passed class name.
     *
     * @param string $className The class name to return the reflection instance for
     *
     * @return \AppserverIo\Lang\Reflection\ReflectionClass The reflection instance
     */
    public function newReflectionClass($className)
    {

        // initialize the array with the annotations we want to ignore
        $annotationsToIgnore = array(
            'author',
            'package',
            'license',
            'copyright',
            'param',
            'return',
            'throws',
            'see',
            'link'
        );

        // initialize the array with the aliases for the enterprise bean annotations
        $annotationAliases = array(
            Route::ANNOTATION           => Route::__getClass(),
            Inject::ANNOTATION          => Inject::__getClass(),
            Factory::ANNOTATION         => Factory::__getClass(),
            Resource::ANNOTATION        => Resource::__getClass(),
            Timeout::ANNOTATION         => Timeout::__getClass(),
            Stateless::ANNOTATION       => Stateless::__getClass(),
            Stateful::ANNOTATION        => Stateful::__getClass(),
            Startup::ANNOTATION         => Startup::__getClass(),
            Singleton::ANNOTATION       => Singleton::__getClass(),
            Schedule::ANNOTATION        => Schedule::__getClass(),
            PreAttach::ANNOTATION       => PreAttach::__getClass(),
            PostDetach::ANNOTATION      => PostDetach::__getClass(),
            PreDestroy::ANNOTATION      => PreDestroy::__getClass(),
            PostConstruct::ANNOTATION   => PostConstruct::__getClass(),
            MessageDriven::ANNOTATION   => MessageDriven::__getClass(),
            EnterpriseBean::ANNOTATION  => EnterpriseBean::__getClass(),
            PersistenceUnit::ANNOTATION => PersistenceUnit::__getClass()
        );

        // return the reflection class instance
        return new ReflectionClass($className, $annotationsToIgnore, $annotationAliases);
    }

    /**
     * Returns a new reflection class intance for the passed class name.
     *
     * @param string $className The class name to return the reflection class instance for
     *
     * @return \AppserverIo\Lang\Reflection\ReflectionClass The reflection instance
     */
    public function getReflectionClass($className)
    {

        // check if we've already initialized the reflection class
        if (isset($this->reflectionClasses[$className]) === false) {
            $this->reflectionClasses[$className] = $this->newReflectionClass($className);
        }

        // return the reflection class instance
        return $this->reflectionClasses[$className];
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
        return $this->getReflectionClass(get_class($instance));
    }

    /**
     * Adds the passe reflection class instance to the DI provider.
     *
     * @param \AppserverIo\Lang\Reflection\ClassInterface $reflectionClass The reflection class instance to add
     *
     * @return void
     */
    public function setReflectionClass(ClassInterface $reflectionClass)
    {
        $this->reflectionClasses[$reflectionClass->getName()] = $reflectionClass;
    }

    /**
     * Loads the dependencies for the passed object descriptor.
     *
     * @param \AppserverIo\Description\NameAwareDescriptorInterface $objectDescriptor The object descriptor to load the dependencies for
     *
     * @throws \AppserverIo\Psr\Di\DependencyInjectionException Is thrown, if the dependencies can not be loaded
     * @return array The array with the initialized dependencies
     */
    protected function loadDependencies(NameAwareDescriptorInterface $objectDescriptor)
    {

        // query whether or not the dependencies have been loaded
        if (isset($this->dependencies[$name = $objectDescriptor->getName()])) {
            return $this->dependencies[$name];
        }

        // initialize the array with the dependencies
        $dependencies = array('method' =>  array(), 'property' => array(), 'constructor' => array());

        // check if a reflection class instance has been passed or is already available
        $reflectionClass = $this->getReflectionClass($objectDescriptor->getClassName());

        // check for declared EPB and resource references
        /** @var \AppserverIo\Description\ReferenceDescriptorInterface $reference */
        foreach ($objectDescriptor->getReferences() as $reference) {
            // check if we've a reflection target defined
            if ($injectionTarget = $reference->getInjectionTarget()) {
                // add the dependency to the array
                if ($targetName = $injectionTarget->getTargetProperty()) {
                    // append the property dependency
                    $dependencies['property'][$targetName] = $this->loadDependency($reference);
                } elseif ($targetName = $injectionTarget->getTargetMethod()) {
                    // load the reflection method instance
                    $reflectionMethod = $reflectionClass->getMethod($targetName);

                    // prepare the array with the method dependencies
                    if (!isset($dependencies['method'][$targetName])) {
                        $dependencies['method'][$targetName] = array();
                    }

                    // iterate over the method's parameters and try to find the one that matches the reference
                    foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
                        // query whether the reflection parameter name equals the inject target method parameter name
                        if ($reference->equals($reflectionParameter)) {
                            // append the method/constructor dependency
                            $dependencies['method'][$targetName][$reflectionParameter->getPosition()] = $this->loadDependency($reference);
                        }
                    }

                    // finally sort them them by their position
                    ksort($dependencies['method'][$targetName]);

                } else {
                    // throw an exception
                    throw new DependencyInjectionException(
                        sprintf('Can\'t find property or method %s in class "%s"', $targetName, $name)
                    );
                }
            }
        }

        // return the array with the loaded dependencies
        return $this->dependencies[$name] = $dependencies;
    }

    /**
     * Load's and return's the dependency instance for the passed reference.
     *
     * @param \AppserverIo\Description\ReferenceDescriptorInterface $referenceDescriptor The reference descriptor
     *
     * @return object The reference instance
     * @throws \Exception Is thrown, if no DI type definition for the passed reference is available
     */
    protected function loadDependency(ReferenceDescriptorInterface $referenceDescriptor)
    {

        // load the session ID from the execution environment
        $sessionId = Environment::singleton()->getAttribute(EnvironmentKeys::SESSION_ID);

        // prepare the lookup name
        $lookupName = sprintf('php:global/%s/%s', $this->getApplication()->getUniqueName(), $referenceDescriptor->getRefName());

        // at least we need a type for instanciation, if we've a bean reference
        if ($referenceDescriptor instanceof BeanReferenceDescriptorInterface && $type = $referenceDescriptor->getType()) {
            // load the object manager instance
            /** @var \AppserverIo\Psr\Di\ObjectManagerInterface $objectManager */
            $objectManager = $this->getNamingDirectory()->search(
                sprintf('php:global/%s/%s', $this->getApplication()->getUniqueName(), ObjectManagerInterface::IDENTIFIER)
            );

            // try to directly instanciate the class by its defined type
            return $this->get($objectManager->getPreference($type), $sessionId);
        }

        // query if the instance is available, if yes load the instance by lookup the initial context
        if ($this->getNamingDirectory()->isBound($lookupName)) {
            return $this->getNamingDirectory()->search($lookupName, array($sessionId));
        }

        // throw an exception if the dependency can't be instanciated
        throw new \Exception(sprintf('Can\'t lookup bean "%s" nor find a DI type definition', $lookupName));
    }

    /**
     * Loads the dependencies for the passed reflection method.
     *
     * @param \AppserverIo\Lang\Reflection\ReflectionMethod $reflectionMethod The reflection method to load the dependencies for
     *
     * @return array The array with the initialized dependencies
     */
    protected function loadDependenciesByReflectionMethod(ReflectionMethod $reflectionMethod)
    {

        // initialize the array for the dependencies
        $dependencies = array();

        // load the object manager instance
        /** @var \AppserverIo\Psr\Di\ObjectManagerInterface $objectManager */
        $objectManager = $this->getNamingDirectory()->search(
            sprintf('php:global/%s/%s', $this->getApplication()->getUniqueName(), ObjectManagerInterface::IDENTIFIER)
        );

        // iterate over the constructor parameters
        /** @var \AppserverIo\Lang\Reflection\ParameterInterface $reflectionParameter */
        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            $dependencies[] = $this->get($objectManager->getPreference($reflectionParameter->getType()));
        }

        // return the initialized dependencies
        return $dependencies;
    }

    /**
     * Creates a new instance with the dependencies defined by the passed descriptor.
     *
     * @param \AppserverIo\Description\NameAwareDescriptorInterface $objectDescriptor The object descriptor with the dependencies
     *
     * @return object The instance
     */
    protected function createInstance(NameAwareDescriptorInterface $objectDescriptor)
    {

        // try to load the reflection class
        $reflectionClass = $this->getReflectionClass($objectDescriptor->getClassName());

        // load the dependencies for the passed descriptor
        $dependencies = $this->loadDependencies($objectDescriptor);

        // check if we've a constructor
        if ($reflectionClass->hasMethod($methodName = '__construct') &&
            $reflectionClass->getMethod($methodName)->getParameters()
        ) {
            // query whether or not constructor parameters are available
            if (isset($dependencies['method'][$methodName])) {
                // create a new instance and pass the constructor args
                return $reflectionClass->newInstanceArgs($dependencies['method'][$methodName]);
            }
        }

        // create a new instance
        $instance = $reflectionClass->newInstance();

        // inject the dependencies using properties/methods
        $this->injectDependencies($objectDescriptor, $instance);

        // return the initialized instance
        return $instance;
    }

    /**
     * Injects the dependencies of the passed instance defined in the object descriptor.
     *
     *
     * @param \AppserverIo\Psr\Deployment\DescriptorInterface $objectDescriptor The object descriptor with the dependencies
     * @param object                                          $instance         The instance to inject the dependencies for
     *
     * @return void
     */
    public function injectDependencies(DescriptorInterface $objectDescriptor, $instance)
    {

        // try to load the reflection class
        $reflectionClass = $this->getReflectionClass($objectDescriptor->getClassName());

        // load the dependencies for the passed descriptor
        $dependencies = $this->loadDependencies($objectDescriptor);

        // inject dependencies by method
        foreach ($dependencies['method'] as $methodName => $toInject) {
            // query whether or not the method exists
            if ($reflectionClass->hasMethod($methodName)) {
                // inject the target by invoking the method
                $instance->$methodName($toInject);
            }
        }

        // inject dependencies by property
        foreach ($dependencies['property'] as $propertyName => $toInject) {
            // query whether or not the property exists
            if ($reflectionClass->hasProperty($propertyName)) {
                // load the reflection property
                $reflectionProperty = $reflectionClass->getProperty($propertyName);

                // load the PHP ReflectionProperty instance to inject the bean instance
                $phpReflectionProperty = $reflectionProperty->toPhpReflectionProperty();
                $phpReflectionProperty->setAccessible(true);
                $phpReflectionProperty->setValue($instance, $toInject);
            }
        }
    }

    /**
     * Returns a new instance of the passed class name.
     *
     * @param string $className The fully qualified class name to return the instance for
     * @param array  $args      Arguments to pass to the constructor of the instance
     *
     * @return object The instance itself
     */
    public function newInstance($className, array $args = array())
    {

        // load the reflection data for the passed class name
        $reflectionClass = $this->getReflectionClass($className);

        // query whether or not the class has a constructor
        if ($reflectionClass->hasMethod($methodName = '__construct') &&
            $reflectionClass->getMethod($methodName)->getParameters() &&
            sizeof($args) > 0
        ) {
            return $reflectionClass->newInstanceArgs($args);
        }

        // return the instance without calling the constructor
        return $reflectionClass->newInstance();
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for
     *
     * @throws \Psr\Container\NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws \Psr\Container\ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {

        // query whether or not the instance can be created
        if ($this->has($id)) {
            // query the request context whether or not an instance has already been loaded
            if (Environment::singleton()->hasAttribute($id)) {
                return Environment::singleton()->getAttribute($id);
            }

            try {
                // load the object manager instance
                /** @var \AppserverIo\Psr\Di\ObjectManagerInterface $objectManager */
                $objectManager = $this->getNamingDirectory()->search(
                    sprintf('php:global/%s/%s', $this->getApplication()->getUniqueName(), ObjectManagerInterface::IDENTIFIER)
                );

                // query whether or not the passed ID has a descriptor
                if ($objectManager->hasObjectDescriptor($id)) {
                    // load the object descriptor instance
                    $objectDescriptor = $objectManager->getObjectDescriptor($id);

                    // query if the simple bean has to be initialized by a factory
                    if ($objectDescriptor instanceof FactoryAwareDescriptorInterface && $factory = $objectDescriptor->getFactory()) {
                        // query whether or not the factory is a simple class or a bean
                        if ($className = $factory->getClassName()) {
                            $factoryInstance = $this->get($className);
                        } else {
                            $factoryInstance = $this->get($factory->getName());
                        }

                        // create the instance by invoking the factory method
                        $instance = call_user_func(array($factoryInstance, $factory->getMethod()));

                    } else {
                        // create the instance and inject the dependencies
                        $instance = $this->createInstance($objectDescriptor);
                    }

                    // add the initialized instance to the request context if has to be shared
                    if ($objectDescriptor->isShared()) {
                        $this->set($id, $instance);
                    }

                    // immediately return the instance
                    return $instance;
                }

                // initialize the array for the dependencies
                $dependencies = array();

                // assume, that the passed ID is a FQCN
                $reflectionClass = $this->getReflectionClass($id);

                // query whether or not the class has a constructor that expects parameters
                if ($reflectionClass->hasMethod($methodName = '__construct') &&
                    $reflectionClass->getMethod($methodName)->getParameters()
                ) {
                    // if yes, load them by the the reflection method
                    $dependencies = $this->loadDependenciesByReflectionMethod($reflectionClass->getMethod($methodName));
                }

                // create and ddd the initialized instance to the request context
                $this->set($id, $instance = $this->newInstance($id, $dependencies));

                // finally return the instance
                return $instance;

            } catch (\Exception $e) {
                throw new NotFoundException(sprintf('DI error when try to inject dependencies for identifier "%s"', $id), null, $e);
            }
        }

        // throw an exception if no entry was found for **this** identifier
        throw new NotFoundException(sprintf('DI definition for identifier "%s" is not available', $id));
    }

    /**
     * Returns TRUE if the container can return an entry for the given identifier.
     * Returns FALSE otherwise.
     *
     * `has($id)` returning TRUE does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return boolean TRUE if an entry for the given identifier exists, else FALSE
     */
    public function has($id)
    {

        // load the object manager instance
        /** @var \AppserverIo\Psr\Di\ObjectManagerInterface $objectManager */
        $objectManager = $this->getNamingDirectory()->search(
            sprintf('php:global/%s/%s', $this->getApplication()->getUniqueName(), ObjectManagerInterface::IDENTIFIER)
        );

        // query whether or not a object descriptor or the class definition exists
        return class_exists($id) || $objectManager->hasObjectDescriptor($id);
    }

    /**
     * Register's the passed value with the passed ID.
     *
     * @param string $id    The ID of the value to add
     * @param string $value The value to add
     *
     * @return void
     * @throws \AppserverIo\Appserver\DependencyInjectionContainer\ContainerException Is thrown, if a value with the passed key has already been added
     */
    public function set($id, $value)
    {

        // query whether or not a value with the passed ID already exists
        if (Environment::singleton()->hasAttribute($id)) {
            throw new ContainerException(sprintf('A value for identifier "%s" has already been added to the DI container', $id));
        }

        // add the value to the environment
        Environment::singleton()->setAttribute($id, $value);
    }

    /**
     * Query's whether or not an instance of the passed already exists.
     *
     * @param string $id Identifier of the entry to look for
     *
     * @return boolean TRUE if an instance exists, else FALSE
     */
    public function exists($id)
    {
        return Environment::singleton()->hasAttribute($id);
    }

    /**
     * Stops the manager instance.
     *
     * @return void
     * @see \AppserverIo\Psr\Application\ManagerInterface::stop()
     */
    public function stop()
    {
        // Still not implemented yet
    }
}
