<?php

/**
 * \AppserverIo\Appserver\Core\Interfaces\ServerInterface
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

use AppserverIo\Appserver\Core\InitialContext;
use AppserverIo\Appserver\Core\Interfaces\ScannerInterface;
use AppserverIo\Appserver\Core\Interfaces\ExtractorInterface;
use AppserverIo\Appserver\Core\Interfaces\ContainerInterface;
use AppserverIo\Appserver\Core\Interfaces\SystemConfigurationInterface;

/**
 * Interface for the application server implementation

 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface ServerInterface
{

    /**
     * Adds the passed container to the server.
     *
     * @param \AppserverIo\Appserver\Core\Interfaces\ContainerInterface $container The container to add
     *
     * @return void
     */
    public function addContainer(ContainerInterface $container);

    /**
     * Returns the running container threads.
     *
     * @return array Array with the running container threads
     */
    public function getContainers();

    /**
     * Sets the system configuration.
     *
     * @param \AppserverIo\Appserver\Core\Interfaces\SystemConfigurationInterface $systemConfiguration The system configuration object
     *
     * @return null
     */
    public function setSystemConfiguration(SystemConfigurationInterface $systemConfiguration);

    /**
     * Returns the system configuration.
     *
     * @return \AppserverIo\Appserver\Core\Interfaces\SystemConfigurationInterface The system configuration
     */
    public function getSystemConfiguration();

    /**
     * Sets the initial context instance.
     *
     * @param \AppserverIo\Appserver\Core\InitialContext $initialContext The initial context instance
     *
     * @return void
     */
    public function setInitialContext(InitialContext $initialContext);

    /**
     * Returns the initial context instance.
     *
     * @return \AppserverIo\Appserver\Core\InitialContext The initial context instance
     */
    public function getInitialContext();

    /**
     * Returns the system logger instance.
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function getSystemLogger();

    /**
     * Adds the passed extractor to the server.
     *
     * @param \AppserverIo\Appserver\Core\Interfaces\ExtractorInterface $extractor The extractor instance to add
     *
     * @return void
     */
    public function addExtractor(ExtractorInterface $extractor);

    /**
     * Returns all registered extractors.
     *
     * @return array The array with the extractors
     */
    public function getExtractors();

    /**
     * Adds the passed scanner to the server.
     *
     * @param \AppserverIo\Appserver\Core\Interfaces\ScannerInterface $extractor The scanner instance to add
     *
     * @return void
     */
    public function addScanner(ScannerInterface $scanner);

    /**
     * Returns all registered scanners.
     *
     * @return array The array with the scanners
     */
    public function getScanners();

    /**
     * Start the container threads.
     *
     * @return void
     * @see \AppserverIo\Appserver\Core\ServerInterface::watch();
     */
    public function start();

    /**
     * Profiles the server instance for memory usage and system load
     *
     * @return void
     */
    public function profile();

    /**
     * Scan's the deployment directory for changes and restarts
     * the server instance if necessary.
     *
     * This is an alternative method to call start() because the
     * monitor is running exclusively like the start() method.
     *
     * @return void
     * @see \AppserverIo\Appserver\Core\ServerInterface::start();
     */
    public function watch();

    /**
     * Returns a new instance of the passed class name.
     *
     * @param string $className The fully qualified class name to return the instance for
     * @param array  $args      Arguments to pass to the constructor of the instance
     *
     * @return object The instance itself
     * @see \AppserverIo\Appserver\Core\InitialContext::newInstance()
     */
    public function newInstance($className, array $args = array());

    /**
     * Returns a new instance of the passed API service.
     *
     * @param string $className The API service class name to return the instance for
     *
     * @return \AppserverIo\Appserver\Core\Api\ServiceInterface The service instance
     * @see \AppserverIo\Appserver\Core\InitialContext::newService()
     */
    public function newService($className);

    /**
     * Will safely put the appserver to rest by cleaning up after the last run
     *
     * @return void
     */
    public function cleanup();
}
