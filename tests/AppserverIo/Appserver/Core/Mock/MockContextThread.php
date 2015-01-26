<?php

/**
 * AppserverIo\Appserver\Core\Mock\MockContextThread
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

use AppserverIo\Appserver\Core\AbstractContextThread;

/**
 * A mock context thread.
 *
 * @author    Thomas Kreidenhuber <t.kreidenhuber@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class MockContextThread extends AbstractContextThread
{

    /**
     * (non-PHPdoc)
     *
     * @see \AppserverIo\Appserver\Core\AbstractThread::main()
     */
    public function main()
    {}

    /**
     * Method to initialze the thread with the constructor
     * params without the initial context.
     *
     * @return void
     */
    public function init()
    {}
}