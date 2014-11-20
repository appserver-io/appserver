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
use TechDivision\Storage\GenericStackable;
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

// ATTENTION: this is necessary for Windows
use AppserverIo\Appserver\Naming\InitialContext as NamingContext;

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
     * @param \TechDivision\Storage\GenericStackable $namingDirectoryAliases The naming directory aliases
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
     * Returns a reflection class intance for the passed class name.
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
            Resource::ANNOTATION       => Resource::__getClass()
        );

        // return the reflection class instance
        return new ReflectionClass($className, $annotationsToIgnore, $annotationAliases);
    }

    /**
     * Injects the dependencies of the passed instance.
     *
     * @param object                                            $instance        The instance to inject the dependencies for
     * @param \AppserverIo\Lang\Reflection\ClassInterface|null $reflectionClass The reflection class for the passed instance
     * @param string|null                                       $sessionId       The session-ID, necessary to inject stateful session beans (SFBs)
     *
     * @return void
     */
    public function injectDependencies($instance, ClassInterface $reflectionClass = null, $sessionId = null)
    {

        // check if a reflection class instance has been passed
        if ($reflectionClass == null) {
            $reflectionClass = $this->newReflectionClass($instance);
        }

        // we've to check for DI property annotations
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {

            // if we found a @EnterpriseBean annotation, inject the instance by property injection
            if ($reflectionProperty->hasAnnotation(EnterpriseBean::ANNOTATION)) {

                // load the annotation instance and the bean type we want to inject
                $annotation = $reflectionProperty->getAnnotation(EnterpriseBean::ANNOTATION);
                $lookupName = $this->getLookupName($annotation);

                // load the PHP ReflectionProperty instance to inject the bean instance
                $phpReflectionProperty = $reflectionProperty->toPhpReflectionProperty();
                $phpReflectionProperty->setAccessible(true);
                $phpReflectionProperty->setValue($instance, $this->getInitialContext()->lookup($lookupName, $sessionId));
            }

            // if we found a @Resource annotation, inject the instance by invoke the setter/inject method
            if ($reflectionProperty->hasAnnotation(Resource::ANNOTATION)) {

                // load the annotation instance and the resource type we want to inject
                $annotation = $reflectionProperty->getAnnotation(Resource::ANNOTATION);
                $lookupName = $this->getLookupName($annotation);

                // load the PHP ReflectionProperty instance to inject the resource instance
                $phpReflectionProperty = $reflectionProperty->toPhpReflectionProperty();
                $phpReflectionProperty->setAccessible(true);
                $phpReflectionProperty->setValue($instance, $this->getNamingDirectory()->search($lookupName));
            }
        }

        // we've to check for DI method annotations
        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {

            // if we found a @EnterpriseBean annotation, inject the instance by invoke the setter/inject method
            if ($reflectionMethod->hasAnnotation(EnterpriseBean::ANNOTATION)) {

                // load the annotation instance and the bean type we want to inject
                $annotation = $reflectionMethod->getAnnotation(EnterpriseBean::ANNOTATION);
                $lookupName = $this->getLookupName($annotation);

                // inject the bean instance
                $reflectionMethod->invoke($instance, $this->getInitialContext()->lookup($lookupName, $sessionId));
            }

            // if we found a @Resource annotation, inject the instance by invoke the setter/inject method
            if ($reflectionMethod->hasAnnotation(Resource::ANNOTATION)) {

                // load the annotation instance and the resource type we want to inject
                $annotation = $reflectionMethod->getAnnotation(Resource::ANNOTATION);
                $lookupName = $this->getLookupName($annotation);

                // inject the resource instance
                $reflectionMethod->invoke($instance, $this->getNamingDirectory()->search($lookupName));
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

        // create and return a new instance
        $reflectionClass = $this->newReflectionClass($className);

        // check if we've a constructor
        if ($reflectionClass->hasMethod('__construct')) {
            $instance = $reflectionClass->newInstanceArgs($args);
        } else {
            $instance = $reflectionClass->newInstance();
        }

        // inject the dependencies
        $this->injectDependencies($instance, $reflectionClass, $sessionId);

        // return the instance here
        return $instance;
    }
}
