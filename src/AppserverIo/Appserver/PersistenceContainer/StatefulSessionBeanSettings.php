<?php

/**
 * AppserverIo\Appserver\PersistenceContainer\StatefulSessionBeanSettings
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

/**
 * Interface for the stateful session bean settings.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface StatefulSessionBeanSettings
{

    /**
     * Returns the number of seconds for a stateful session bean lifetime.
     *
     * @return integer The stateful session bean lifetime
     */
    public function getLifetime();

    /**
     * Returns the probability the garbage collector will be invoked on the session.
     *
     * @return float The garbage collector probability
     */
    public function getGarbageCollectionProbability();
}
