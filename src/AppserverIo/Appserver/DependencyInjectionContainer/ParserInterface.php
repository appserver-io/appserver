<?php

/**
 * \AppserverIo\Appserver\DependencyInjectionContainer\ParserInterface
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

namespace AppserverIo\Appserver\DependencyInjectionContainer;

/**
 * Interface for all object descriptor parser implementations.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface ParserInterface
{

    /**
     * Returns the manager instance.
     *
     * @return \AppserverIo\Psr\Application\ManagerInterface The manager instance
     */
    public function getManager();

    /**
     * Returns the application context instance the bean context is bound to.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application context instance
     */
    public function getApplication();

    /**
     * The parser configuration.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\ParserNodeInterface The parser configuration
     */
    public function getConfiguration();

    /**
     * Returns the configured directories.
     *
     * @return array The directories
     */
    public function getDirectories();

    /**
     * Returns the configured descriptor classes.
     *
     * @return array The descriptors
     */
    public function getDescriptors();

    /**
     * Tries to parse a deployment descriptor or directory for beans
     * that has to be registered in the object manager.
     *
     * @return void
     */
    public function parse();
}
