<?php

/**
 * AppserverIo\Appserver\DependencyInjectionContainer\Description\InjectionTarget
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

namespace AppserverIo\Appserver\DependencyInjectionContainer\Description;

use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\InjectionTargetDescriptorInterface;

/**
 * Utility classe that stores a beans injection target configuration.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class InjectionTargetParser implements InjectionTargetParserInterface
{

    /**
     * The target class we want to inject to.
     *
     * @var string
     */
    protected $targetClass;

    /**
     * The target member name we want to inject to.
     *
     * @var string
     */
    protected $targetName;

    /**
     * Sets the target class we want to inject to.
     *
     * @param string $targetClass The target class we want to inject to
     *
     * @return void
     */
    public function setTargetClass($targetClass)
    {
        $this->targetClass = $targetClass;
    }

    /**
     * Returns the target class we want to inject to.
     *
     * @return string The target class we want to inject to
     */
    public function getTargetClass()
    {
        return $this->targetClass;
    }

    /**
     * Sets the target member name we want to inject to.
     *
     * @param string $targetName The target member name we want to inject to
     *
     * @return void
     */
    public function setTargetName($targetName)
    {
        $this->targetName = $targetName;
    }

    /**
     * Returns the target member name we want to inject to.
     *
     * @return string The target member name we want to inject to
     */
    public function getTargetName()
    {
        return $this->targetName;
    }

    /**
     * Creates and initializes a beans injection target configuration instance from the passed
     * deployment node.
     *
     * @param \AppserverIo\Lang\Reflection\ClassInterface $reflectionClass The reflection class with the beans injection target configuration
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\Utils\BeanConfiguration The initialized beans injection target configuration
     */
    public static function fromReflectionClass(ClassInterface $reflectionClass)
    {
        // still to implement
    }

    /**
     * Creates and initializes a beans injection target configuration instance from the passed
     * deployment node.
     *
     * @param \SimpleXmlElement $node The deployment node with the beans injection target configuration
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\Utils\BeanConfiguration The initialized beans injection target configuration
     */
    public static function fromDeploymentDescriptor(\SimpleXmlElement $node)
    {

        // create a new configuration instance
        $injectionTarget = new InjectionTarget();

        // query for the target class name we want to inject to
        if ($targetClass = (string) $node->{'injection-target-class'}) {
            $injectionTarget->setClassName($targetClass);
        }

        // query for the target member name we want to inject to
        if ($targetName = (string) $node->{'injection-target-name'}) {
            $injectionTarget->setName($targetName);
        }

        // return the initialized configuration
        return $injectionTarget;
    }

    /**
     * Merges the passed injection target configuration into this one. Configuration
     * values of the passed configuration will overwrite the this one.
     *
     * @param \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\InjectionTargetDescriptorInterface $injectionTargetDescriptor The injection target to merge
     *
     * @return void
     */
    public function merge(InjectionTargetDescriptorInterface $injectionTargetDescriptor)
    {

        // merge the injection target name
        if ($targetName = $injectionTargetDescriptor->getTargetName()) {
            $this->setTargetName($targetName);
        }

        // merge the injection target class
        if ($targetClass = $injectionTargetDescriptor->getTargetClass()) {
            $this->setTargetClass($targetClass);
        }
    }
}
