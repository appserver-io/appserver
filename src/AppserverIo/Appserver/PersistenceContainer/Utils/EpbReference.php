<?php

/**
 * AppserverIo\Appserver\PersistenceContainer\Utils\EpbReference
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

namespace AppserverIo\Appserver\PersistenceContainer\Utils;

/**
 * Utility class that stores a beans reference configuration.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class EpbReference implements EpbReferenceInterface
{

    /**
     * The reference name.
     *
     * @var string
     */
    protected $refName;

    /**
     * The reference type.
     *
     * @var string
     */
    protected $refType;

    /**
     * The configurable bean name.
     *
     * @var string
     */
    protected $link;

    /**
     * The injection target specification.
     *
     * @var \AppserverIo\Appserver\PersistenceContainer\Utils\InjectionTargetInterface
     */
    protected $injectionTarget;

    /**
     * Sets the reference name.
     *
     * @param string $refName The reference name
     *
     * @return void
     */
    public function setRefName($refName)
    {
        $this->refName = $refName;
    }

    /**
     * Returns the reference name.
     *
     * @return string The reference name
     */
    public function getRefName()
    {
        return $this->name;
    }

    /**
     * Sets the reference type.
     *
     * @param string $refType The reference type
     *
     * @return void
     */
    public function setRefType($refType)
    {
        $this->refType = $refType;
    }

    /**
     * Returns the reference type.
     *
     * @return string The reference type
     */
    public function getRefType()
    {
        return $this->refType;
    }

    /**
     * Sets the reference link.
     *
     * @param string $link The reference link
     *
     * @return void
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * Returns the reference link.
     *
     * @return string The reference link
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Sets the injection target specification.
     *
     * @param \AppserverIo\Appserver\PersistenceContainer\Utils\InjectionTargetInterface $injectionTarget The injection target specification
     *
     * @return void
     */
    public function setInjectionTarget(InjectionTargetInterface $injectionTarget)
    {
        $this->injectionTarget = $injectionTarget;
    }

    /**
     * Returns the injection target specification.
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\Utils\InjectionTargetInterface The injection target specification
     */
    public function getInjectionTarget()
    {
        return $this->injectionTarget;
    }

    /**
     * Creates and initializes a beans reference configuration instance from the passed
     * deployment node.
     *
     * @param \AppserverIo\Lang\Reflection\ClassInterface $reflectionClass The reflection class with the beans reference configuration
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\Utils\EpbReference The initialized beans reference configuration
     */
    public static function fromReflectionClass(ClassInterface $reflectionClass)
    {
        // still to implement
    }

    /**
     * Creates and initializes a beans reference configuration instance from the passed
     * deployment node.
     *
     * @param \SimpleXmlElement $node The deployment node with the beans reference configuration
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\Utils\BeanConfiguration The initialized beans reference configuration
     */
    public static function fromDeploymentDescriptor(\SimpleXmlElement $node)
    {

        // create a new configuration instance
        $epbReference = new EpbReference();

        // query for the reference name
        if ($refName = (string) $node->{'epb-ref-name'}) {
            $epbReference->setRefName($refName);
        }

        // query for the reference type
        if ($refType = (string) $node->{'epb-ref-type'}) {
            $epbReference->setRefType($refType);
        }

        // query for reference link
        if ($link = (string) $node->{'link'}) {
            $epbReference->setLink($link);
        }

        // query for the injection target
        if ($injectionTarget = (string) $node->{'injection-target'}) {
            $epbReference->setInjectionTarget(InjectionTarget::fromDeploymentDescriptor($injectionTarget));
        }

        // return the initialized configuration
        return $epbReference;
    }

    /**
     * Merges the passed configuration into this one. Configuration values
     * of the passed configuration will overwrite the this one.
     *
     * @param \AppserverIo\Appserver\PersistenceContainer\EbpReferenceInterface $epbReference The configuration to merge
     *
     * @return void
     */
    public function merge(EpbReferenceInterface $epbReference)
    {

        // merge the reference name
        if ($refName = $epbReference->getRefName()) {
            $this->setRefName($refName);
        }

        // merge the reference type
        if ($refType = $epbReference->getRefType()) {
            $this->setRefType($refType);
        }

        // merge the reference link
        if ($link = $epbReference->getLink()) {
            $this->setLink($link);
        }

        // merge the injection target
        if ($injectionTarget = $epbReference->getInjectionTarget()) {
            $this->setInjectionTarget($injectionTarget);
        }
    }
}
