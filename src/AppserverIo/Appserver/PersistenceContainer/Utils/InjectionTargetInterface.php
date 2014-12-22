<?php

/**
 * AppserverIo\Appserver\PersistenceContainer\Utils\InjectionTargetInterface
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
 * Inferface for utility classes that stores a beans injection target configuration.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
interface InjectionTargetInterface
{

    /**
     * Returns the injection target class name.
     *
     * @return string The bean name
     */
    public function getTargetClass();

    /**
     * Returns the injection target property name.
     *
     * @return string The bean name
     */
    public function getTargetName();

    /**
     * Merges the passed injection target configuration into this one. Configuration
     * values of the passed configuration will overwrite the this one.
     *
     * @param \AppserverIo\Appserver\PersistenceContainer\Utils\InjectionTargetInterface $injectionTarget The injection target to merge
     *
     * @return void
     */
    public function merge(InjectionTargetInterface $injectionTarget);
}
