<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\HostNodeInterface
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

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * The interface for all host node DTO implementations.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface HostNodeInterface
{

    /**
     * Returns the servers context configuration.
     *
     * @return array servers context configuration
     */
    public function getContexts();

    /**
     * Returns the context with the passed name.
     *
     * @param string $name The name of the requested context
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\ContextNode|null The requested context node
     */
    public function getContext($name);

    /**
     * Returns the host name.
     *
     * @return string The host name
     */
    public function getName();

    /**
     * Returns the application base directory.
     *
     * @return string The application base directory
     */
    public function getAppBase();

    /**
     * Returns the temporary base directory.
     *
     * @return string The temporary base directory
     */
    public function getTmpBase();

    /**
     * Returns the deployment base directory.
     *
     * @return string The deployment base directory
     */
    public function getDeployBase();

    /**
     * Return's the host's directories, e. g. to be created.
     *
     * @return array The array with the host's directories
     */
    public function getDirectories();
}
