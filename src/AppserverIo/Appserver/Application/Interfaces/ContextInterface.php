<?php

/**
 * AppserverIo\Appserver\Application\Interfaces\ContextInterface
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

namespace AppserverIo\Appserver\Application\Interfaces;

use AppserverIo\Psr\Context\Context;

/**
 * Interface for a context.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface ContextInterface extends Context
{

    /**
     * Returns a new instance of the passed class name.
     *
     * @param string $className The fully qualified class name to return the instance for
     * @param array  $args      Arguments to pass to the constructor of the instance
     *
     * @return object The instance itself
     */
    public function newInstance($className, array $args = array());

    /**
     * Creates a new service instance.
     *
     * @param string $className The API service class name to return the instance for
     *
     * @return object The service instance
     */
    public function newService($className);

    /**
     * Returns the default class loader.
     *
     * @return object The class loader used
     */
    public function getClassLoader();

    /**
     * Returns the system logger instance.
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function getSystemLogger();

    /**
     * Returns the system configuration.
     *
     * @return \AppserverIo\Configuration\Interfaces\ConfigurationInterface The system configuration
     */
    public function getSystemConfiguration();
}
