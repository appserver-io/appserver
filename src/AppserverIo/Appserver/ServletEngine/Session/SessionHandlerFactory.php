<?php

/**
 * AppserverIo\Appserver\ServletEngine\Session\SessionHandlerFactory
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

namespace AppserverIo\Appserver\ServletEngine\Session;

use AppserverIo\Lang\Reflection\ReflectionClass;
use AppserverIo\Appserver\ServletEngine\SessionSettingsInterface;
use AppserverIo\Appserver\ServletEngine\SessionMarshallerInterface;
use AppserverIo\Appserver\Core\Api\Node\SessionHandlerNodeInterface;

/**
 * Factory implementation to create new session handler instances.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class SessionHandlerFactory
{

    /**
     * Create and return a new instance of the session handler with
     * the passed configuration.
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\SessionHandlerNodeInterface $sessionHandlerNode The session handler configuration
     * @param \AppserverIo\Appserver\ServletEngine\SessionSettingsInterface    $sessionSettings    The session settings
     *
     * @return \AppserverIo\Appserver\ServletEngine\Session\SessionHandlerInterface The session handler instance
     */
    public static function create(
        SessionHandlerNodeInterface $sessionHandlerNode,
        SessionSettingsInterface $sessionSettings
    ) {

        // reflect the class
        $reflectionClass = new ReflectionClass($sessionHandlerNode->getType());

        // create and initialize the session handler instance
        $sessionHandler = $reflectionClass->newInstanceArgs($sessionHandlerNode->getParamsAsArray());
        $sessionHandler->injectSessionSettings($sessionSettings);

        // return the initialzed instance
        return $sessionHandler;
    }
}
