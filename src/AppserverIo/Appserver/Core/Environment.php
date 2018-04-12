<?php

/**
 * AppserverIo\Appserver\Core\Environment
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
 * @copyright 2017 TechDivision GmbH - <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core;

use AppserverIo\Psr\Context\ArrayContext;

/**
 * Environment context implementation
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2017 TechDivision GmbH - <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class Environment extends ArrayContext
{

    /**
     * The singleton instance.
     *
     * @var \AppserverIo\Appserver\Core\Environment
     */
    protected static $instance;

    /**
     * Singleton implementation.
     *
     * @return \AppserverIo\Appserver\Core\Environment The singleton instance
     */
    public static function singleton()
    {

        // query whether or not an instance has been created
        if (Environment::$instance === null) {
            Environment::$instance = new Environment();
        }

        // return the singleton instance
        return Environment::$instance;
    }

    /**
     * Query whether or not the attribute with the passed key is available.
     *
     * @param mixed $key The key to query for
     *
     * @return boolean Return's TRUE if the attribute is available, else FALSE
     */
    public function hasAttribute($key)
    {
        return isset($this->attributes[$key]);
    }
}
