<?php

/**
 * AppserverIo\Appserver\Core\Mock\MockAbstractContainerThread
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Thomas Kreidenhuber <t.kreidenhuber@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Mock;

use AppserverIo\Appserver\Core\AbstractContainerThread;

/**
 * An abstract mock container tread implementation.
 *
 * @author    Thomas Kreidenhuber <t.kreidenhuber@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class MockAbstractContainerThread extends AbstractContainerThread
{

    /**
     * The threads main function.
     *
     * @see \AppserverIo\Appserver\Core\AbstractContainerThread::main()
     */
    public function main()
    {
        // We have to notify the logical parent thread, the appserver, as it has to
        // know the port has been opened
        $this->synchronized(
            function () {
                $this->notify();
            }
        );
    }
}

