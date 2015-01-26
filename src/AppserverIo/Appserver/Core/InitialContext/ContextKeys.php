<?php

/**
 * AppserverIo\Appserver\Core\ContextKeys
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
 * @copyright 2014 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\InitialContext;

/**
 * Utility providing some context keys.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2014 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ContextKeys
{

    /**
     * Key the system configuration is available in the initial context.
     *
     * @var string
     */
    const SYSTEM_CONFIGURATION = 'context_keys_system_configuration';

    /**
     * This is a utility, so don't allow direct instantiation
     */
    final private function __construct()
    {
    }

    /**
     * This is a utility, so don't allow direct instantiation
     *
     * @return void
     */
    final private function __clone()
    {
    }
}
