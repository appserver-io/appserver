<?php

/**
 * AppserverIo\Appserver\PersistenceContainer\GenericObjectFactory
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

namespace AppserverIo\Appserver\PersistenceContainer;

use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * A thread which creates instances in a protected context.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface ObjectFactoryInterface
{

    /**
     * Create a new instance with the passed data.
     *
     * @param string      $className The fully qualified class name to return the instance for
     * @param string|null $sessionId The session-ID, necessary to inject stateful session beans (SFBs)
     * @param array       $args      Arguments to pass to the constructor of the instance
     *
     * @return object The instance itself
     */
    public function newInstance($className, $sessionId = null, array $args = array());
}
