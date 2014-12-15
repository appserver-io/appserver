<?php

/**
 * AppserverIo\Appserver\ServletEngine\GarbageCollector
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
namespace AppserverIo\Appserver\ServletEngine;

/**
 * A thread thats preinitialized session instances and adds them to the
 * the session pool.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
interface GarbageCollector
{

    /**
     * Initializes the garbage collector.
     *
     * @return void
     */
    public function initialize();

    /**
     * Starts the garbage collector.
     *
     * @return void
     */
    public function run();

    /**
     * Stops the garbage collector.
     *
     * @return void
     */
    public function stop();
}
