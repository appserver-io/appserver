<?php

/**
 * AppserverIo\Appserver\Naming\InitialContextAwareInterface
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Naming;

use AppserverIo\Psr\Naming\InitialContext;

/**
 * Interface for initial context aware manager implementations.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface InitialContextAwareInterface
{

    /**
     * Returns the global naming directory.
     *
     * @return \AppserverIo\Psr\Naming\InitialContext The initial context
     */
    public function getInitialContext();

    /**
     * The global naming directory.
     *
     * @param \AppserverIo\Psr\Naming\InitialContext $initialContext The initial context
     *
     * @return void
     */
    public function injectInitialContext(InitialContext $initialContext);
}
