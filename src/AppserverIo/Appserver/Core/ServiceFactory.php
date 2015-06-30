<?php

/**
 * AppserverIo\Appserver\Core\ServiceFactory
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

namespace AppserverIo\Appserver\Core;

/**
 * Simple service factory storage implementation for execution service usage
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ServiceFactory
{

    /**
     * Defines storage for services
     *
     * @var array
     */
    public $services = array();

    /**
     * Returns or creates the given service type instance with optional
     * refInstance injection on ctor
     *
     * @param string $serviceType The service classname to instantiate
     * @param string $refInstance The optional refInstance class to inject on ctor
     *
     * @return \AppserverIo\Lab\Bootstrap\array
     * @Synchronized
     */
    public function get($serviceType, $refInstance = null)
    {
        if (!isset($this->services[$serviceType::getName()])) {
            if (is_null($refInstance)) {
                $this->services[$serviceType::getName()] = new $serviceType();
            } else {
                $this->services[$serviceType::getName()] = new $serviceType($refInstance);
            }
        }
        return $this->services[$serviceType::getName()];
    }
}
