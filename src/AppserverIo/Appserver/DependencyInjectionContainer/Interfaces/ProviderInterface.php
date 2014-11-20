<?php

/**
 * AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\ProviderInterface
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

namespace AppserverIo\Appserver\DependencyInjectionContainer\Interfaces;

use AppserverIo\Lang\Reflection\ClassInterface;
use AppserverIo\Lang\Reflection\AnnotationInterface;
use AppserverIo\Psr\Application\ManagerInterface;

/**
 * Interface for all dependency injection provider implementations.
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
interface ProviderInterface extends ManagerInterface
{

    /**
     * The unique identifier to be registered in the application context.
     *
     * @var string
     */
    const IDENTIFIER = 'ProviderInterface';

    /**
     * Injects the dependencies of the passed instance.
     *
     * @param object                                            $instance        The instance to inject the dependencies for
     * @param \AppserverIo\Lang\Reflection\ClassInterface|null $reflectionClass The reflection class for the passed instance
     * @param string|null                                       $sessionId       The session-ID, necessary to inject stateful session beans (SFBs)
     *
     * @return void
     */
    public function injectDependencies($instance, ClassInterface $reflectionClass = null, $sessionId = null);

    /**
     * Returns a new instance of the passed class name.
     *
     * @param string      $className The fully qualified class name to return the instance for
     * @param string|null $sessionId The session-ID, necessary to inject stateful session beans (SFBs)
     * @param array       $args      Arguments to pass to the constructor of the instance
     *
     * @return object The instance itself
     */
    public function newInstance($className, $sessionId = null, array $args = array());

    /**
     * Returns the naming context instance.
     *
     * @return \AppserverIo\Appserver\Naming\InitialContext The naming context instance
     */
    public function getInitialContext();

    /**
     * Returns the applications naming directory.
     *
     * @return \AppserverIo\Psr\Naming\NamingDirectoryInterface The applications naming directory interface
     */
    public function getNamingDirectory();

    /**
     * Creates a new new instance of the annotation type, defined in the passed reflection annotation.
     *
     * @param \AppserverIo\Lang\Reflection\AnnotationInterface $annotation The reflection annotation we want to create the instance for
     *
     * @return \AppserverIo\Lang\Reflection\AnnotationInterface The real annotation instance
     */
    public function newAnnotationInstance(AnnotationInterface $annotation);

    /**
     * Returns the lookup name for a @Enterprise or @Resource annotation.
     *
     * @param \AppserverIo\Lang\Reflection\AnnotationInterface $annotation The annotation to return the lookup name
     *
     * @return string The lookup name
     */
    public function getLookupName(AnnotationInterface $annotation);

    /**
     * Tries to resolve an alias, if given, for the passed lookup name.
     *
     * @param string $lookupName The lookup name we try to resolve the alias for
     *
     * @return string The lookup name itself, or the alias if found
     */
    public function resolveAlias($lookupName);

    /**
     * Returns a reflection class intance for the passed class name.
     *
     * @param string $className The class name to return the reflection instance for
     *
     * @return \AppserverIo\Lang\Reflection\ReflectionClass The reflection instance
     */
    public function newReflectionClass($className);
}
