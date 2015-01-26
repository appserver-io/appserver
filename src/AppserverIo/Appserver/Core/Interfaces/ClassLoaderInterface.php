<?php

/**
 * AppserverIo\Appserver\Core\Interfaces\ClassLoaderInterface
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

namespace AppserverIo\Appserver\Core\Interfaces;

use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Core\Api\Node\ClassLoaderNodeInterface;

/**
 * Interface for all class loaders that can be registered in the application server.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface ClassLoaderInterface
{

    /**
     * Installs this class loader on the SPL autoload stack.
     *
     * @param bool $throw   If register should throw an exception or not
     * @param bool $prepend If register should prepend
     *
     * @return void
     */
    public function register($throw = true, $prepend = false);
}
