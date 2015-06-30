<?php

/**
 * AppserverIo\Appserver\Core\Commands\CommandFactoryInterface
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

namespace AppserverIo\Appserver\Core\Commands;

/**
 * Factory to create new command instances.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface CommandFactoryInterface
{

    /**
     * Creates and returns a new command instance of the command with the passed name.
     *
     * @param string $name            The name of the command to create the instance for
     * @param array  $constructorArgs The arguments passed to the constructor
     *
     * @return \AppserverIo\Appserver\Core\Commands\CommandInterface The command instance
     */
    public static function factory($name, array $constructorArgs = array());
}
