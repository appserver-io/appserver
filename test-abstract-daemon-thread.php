#!/opt/appserver/bin/php
<?php

/**
 * server.php
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category  Server
 * @package   Appserver
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2014 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core;

use Psr\Log\LogLevel;

// define a all constants appserver base directory
define('APPSERVER_BP', __DIR__);

// bootstrap the application
require __DIR__ . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'bootstrap.php';

class TestDaemon extends AbstractDaemonThread
{

    protected function bootstrap()
    {
        require SERVER_AUTOLOADER;
    }

    protected function iterate($timeout)
    {

        parent::iterate($timeout);

        $this->log(LogLevel::INFO, "Now in daemon with a timeout of $timeout microseconds");
    }
}

$test = new TestDaemon();
$test->start();

for ($i = 0; $i < 5; $i++) {
    sleep(1);
}

$test->stop();

$test->join();