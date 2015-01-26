<?php

/**
 * AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\InjectionTargetDescriptorInterface
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

namespace AppserverIo\Appserver\DependencyInjectionContainer\Interfaces;

/**
 * Inferface for utility classes that stores a beans injection target configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface InjectionTargetDescriptorInterface extends DescriptorInterface
{

    /**
     * Returns the injection target class name.
     *
     * @return string The bean name
     */
    public function getTargetClass();

    /**
     * Returns the target property name we want to inject to.
     *
     * @return string The target property name we want to inject to
     */
    public function getTargetProperty();

    /**
     * Returns the target method we want use for injection.
     *
     * @return string The target method used for injection
     */
    public function getTargetMethod();

    /**
     * Merges the passed injection target configuration into this one. Configuration
     * values of the passed configuration will overwrite the this one.
     *
     * @param \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\InjectionTargetDescriptorInterface $injectionTargetDescriptor The injection target to merge
     *
     * @return void
     */
    public function merge(InjectionTargetDescriptorInterface $injectionTargetDescriptor);
}
