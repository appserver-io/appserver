<?php

/**
 * AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\EpbReferenceDescriptorInterface
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

namespace AppserverIo\Appserver\DependencyInjectionContainer\Interfaces;

/**
 * Inferface for utility classes that stores a beans reference configuration.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
interface EpbReferenceDescriptorInterface extends DescriptorInterface
{

    /**
     * Returns the reference name.
     *
     * @return string The reference name
     */
    public function getRefName();

    /**
     * Returns the reference type.
     *
     * @return string The reference type
     */
    public function getRefType();

    /**
     * Returns the reference link.
     *
     * @return string The reference link
     */
    public function getLink();

    /**
     * Returns the injection target specification.
     *
     * @return \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\InjectionTargetDescriptorInterface The injection target specification
     */
    public function getInjectionTarget();

    /**
     * Merges the passed configuration into this one. Configuration values
     * of the passed configuration will overwrite the this one.
     *
     * @param \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\EpbReferenceDescriptorInterface $epbReferenceDescriptor The configuration to merge
     *
     * @return void
     */
    public function merge(EpbReferenceDescriptorInterface $epbReferenceDescriptor);
}
