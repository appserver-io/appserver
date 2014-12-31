<?php

/**
 * AppserverIo\Appserver\DependencyInjectionContainer\Provider
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
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\DependencyInjectionContainer;

use AppserverIo\Psr\Naming\NamingDirectoryInterface;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Lang\Reflection\ClassInterface;
use AppserverIo\Lang\Reflection\ReflectionClass;
use AppserverIo\Lang\Reflection\ReflectionMethod;
use AppserverIo\Lang\Reflection\ReflectionAnnotation;
use AppserverIo\Lang\Reflection\ReflectionProperty;
use AppserverIo\Lang\Reflection\AnnotationInterface;
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
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\ProviderInterface;
use AppserverIo\Psr\Servlet\Annotations\Route;

// ATTENTION: this is necessary for Windows
use AppserverIo\Appserver\Naming\InitialContext as NamingContext;
use AppserverIo\Psr\EnterpriseBeans\EnterpriseBeansException;
use AppserverIo\Appserver\DependencyInjectionContainer\Description\EpbReferenceDescriptor;

/**
 * A basic dependency injection provider implementation.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */
class Provider extends GenericStackable implements ProviderInterface
{

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
        // set application alias => resolve application with @Resource(name="ApplicationInterface") anntotation
        $this->namingDirectoryAliases['ApplicationInterface'] = sprintf('php:global/%s', $application->getName());
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
     * Injects the naming context.
     *
     * @param \AppserverIo\Appserver\Naming\InitialContext $initialContext The naming context
     *
     * @return void
     */
    public function injectInitialContext(NamingContext $initialContext)
    {
        $this->initialContext = $initialContext;
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
     * Injects the naming directory.
     *
     * @param \AppserverIo\Psr\Naming\NamingDirectoryInterface $namingDirectory The naming directory instance
     *
     * @return void
     */
    public function injectNamingDirectory(NamingDirectoryInterface $namingDirectory)
    {
        $this->namingDirectory = $namingDirectory;
    }

    /**
     * Returns the naming context instance.
     *
     * @return \AppserverIo\Appserver\Naming\InitialContext The naming context instance
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * Returns the applications naming directory.
     *
     * @return \AppserverIo\Psr\Naming\NamingDirectoryInterface The applications naming directory interface
     */
    public function getNamingDirectory()
    {
        return $this->namingDirectory;
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
     * Returns the lookup name for a @Enterprise or @Resource annotation.
     *
     * @param \AppserverIo\Lang\Reflection\AnnotationInterface $annotation The annotation to return the lookup name
     *
     * @return string The lookup name
     */
    public function getLookupName(AnnotationInterface $annotation)
    {
        return $this->resolveAlias($this->newAnnotationInstance($annotation)->getLookupName());
    }

    /**
     * Tries to resolve an alias, if given, for the passed lookup name.
     *
     * @param string $lookupName The lookup name we try to resolve the alias for
     *
     * @return string The lookup name itself, or the alias if found
     */
    public function resolveAlias($lookupName)
    {

        // check if a naming directory alias for the lookup name is available
        if (isset($this->namingDirectoryAliases[$lookupName])) {
            return $this->namingDirectoryAliases[$lookupName];
        }

        // if not, return the original lookup name
        return $lookupName;
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
            EnterpriseBean::ANNOTATION => EnterpriseBean::__getClass(),
            MessageDriven::ANNOTATION  => MessageDriven::__getClass(),
            PostConstruct::ANNOTATION  => PostConstruct::__getClass(),
            PreDestroy::ANNOTATION     => PreDestroy::__getClass(),
            Schedule::ANNOTATION       => Schedule::__getClass(),
            Singleton::ANNOTATION      => Singleton::__getClass(),
            Startup::ANNOTATION        => Startup::__getClass(),
            Stateful::ANNOTATION       => Stateful::__getClass(),
            Stateless::ANNOTATION      => Stateless::__getClass(),
            Timeout::ANNOTATION        => Timeout::__getClass(),
            Resource::ANNOTATION       => Resource::__getClass(),
            Route::ANNOTATION          => Route::__getClass()
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
     * @see \DependencyInjectionContainer\Interfaces\ProviderInterface::newReflectionClass()
     * @see \DependencyInjectionContainer\Interfaces\ProviderInterface::getReflectionClass()
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
     * Injects the dependencies of the passed instance.
     *
     * @param object      $instance  The instance to inject the dependencies for
     * @param string|null $sessionId The session-ID, necessary to inject stateful session beans (SFBs)
     *
     * @return void
     */
    public function injectDependencies($instance, $sessionId = null)
    {

        // load the object manager instance
        $objectManager = $this->getNamingDirectory()->search('ObjectManagerInterface');

        // load the object descriptor for the instance from the the object manager
        if ($objectManager->hasObjectDescriptor($className = get_class($instance))) {

            // load the object descriptor
            $objectDescriptor = $objectManager->getObjectDescriptor($className);

            // check if a reflection class instance has been passed or is already available
            $reflectionClass = $this->getReflectionClassForObject($instance);

            // check for declared EPB references
            foreach ($objectDescriptor->getEpbReferences() as $epbReference) {

                // check if we've a reflection target defined
                if ($injectionTarget = $epbReference->getInjectionTarget()) {

                    // resolve the lookup name
                    $lookupName = $this->resolveAlias($epbReference->getRefName());

                    // check the reference type
                    switch ($refType = $epbReference->getRefType()) {

                        case EpbReferenceDescriptor::REF_TYPE_SESSION: // a stateful, stateless or singleton session bean

                            // load the instance to inject by lookup the initial context
                            $toInject = $this->getInitialContext()->lookup($lookupName, $sessionId);
                            break;

                        case EpbReferenceDescriptor::REF_TYPE_RESOURCE: // a resource

                            // load the instance to inject be searching the naming directory
                            $toInject = $this->getNamingDirectory()->search($lookupName, array($sessionId));
                            break;

                        default:

                            // throw an execption, because we found an unknown reference type
                            throw new DependencyInjectionException(
                                sprintf('Value %s for reference type of class %s not supported', $refType, $className)
                            );
                    }

                    // query for method injection
                    if (method_exists($instance, $targetMethod = $injectionTarget->getTargetMethod())) {

                        // inject the target by invoking the method
                        $instance->$targetMethod($toInject);

                    // query if we've a reflection property with the target name - this is the faster method!
                    } elseif (property_exists($instance, $targetProperty = $injectionTarget->getTargetProperty())) {

                        // load the reflection property
                        $reflectionProperty = $reflectionClass->getProperty($targetProperty);

                        // load the PHP ReflectionProperty instance to inject the bean instance
                        $phpReflectionProperty = $reflectionProperty->toPhpReflectionProperty();
                        $phpReflectionProperty->setAccessible(true);
                        $phpReflectionProperty->setValue($instance, $toInject);

                    } else { // throw an exception
                        throw new DependencyInjectionException(
                            sprintf('Can\'t find property or method %s in class %s', $targetName, $className)
                        );
                    }
                }
            }
        }
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

        // load/create and return a new instance
        $reflectionClass = $this->getReflectionClass($className);

        // check if we've a constructor
        if ($reflectionClass->hasMethod('__construct')) {
            $instance = $reflectionClass->newInstanceArgs($args);
        } else {
            $instance = $reflectionClass->newInstance();
        }

        // inject the dependencies
        $this->injectDependencies($instance, $sessionId);

        // return the instance here
        return $instance;
    }
}
