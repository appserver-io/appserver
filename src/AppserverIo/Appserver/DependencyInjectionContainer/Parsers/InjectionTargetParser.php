<?php

/**
 * AppserverIo\Appserver\PersistenceContainer\Utils\InjectionTarget
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

namespace AppserverIo\Appserver\DependencyInjectionContainer\Parsers;

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
     * The configurable bean name.
     *
     * @var string
     */
    protected $targetClass;

    /**
     * The bean name.
     *
     * @var string
     */
    protected $targetName;

    /**
     * Sets the bean name.
     *
     * @param string $name The bean name
     *
     * @return void
     */
    public function setTargetClass($targetClass)
    {
        $this->targetClass = $targetClass;
    }

    /**
     * Returns the bean name.
     *
     * @return string The bean name
     */
    public function getTargetClass()
    {
        return $this->targetClass;
    }

    /**
     * Sets the bean name.
     *
     * @param string $name The bean name
     *
     * @return void
     */
    public function setTargetName($targetName)
    {
        $this->targetName = $targetName;
    }

    /**
     * Returns the bean name.
     *
     * @return string The bean name
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

        // query for the class name and set it
        if ($targetClass = (string) $node->{'injection-target-class'}) {
            $injectionTarget->setClassName($targetClass);
        }

        // query for the name and set it
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
     * @param \AppserverIo\Appserver\PersistenceContainer\Utils\InjectionTargetInterface $injectionTarget The injection target to merge
     *
     * @return void
     */
    public function merge(InjectionTarget $injectionTarget)
    {

        // merge the injection target name
        if ($targetName = $injectionTarget->getTargetName()) {
            $this->setTargetName($targetName);
        }

        // merge the injection target class
        if ($targetClass = $injectionTarget->getTargetClass()) {
            $this->setTargetClass($targetClass);
        }
    }
}
