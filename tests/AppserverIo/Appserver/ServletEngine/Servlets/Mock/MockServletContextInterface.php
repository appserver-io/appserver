<?php

/**
 * \AppserverIo\Appserver\ServletEngine\Servlets\Mock\MockServletContextInterface
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
 * @link      https://github.com/appserver-io-psr/servlet
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\ServletEngine\Servlets\Mock;

use AppserverIo\Psr\Servlet\ServletContextInterface;

/**
 * A mock interface that provides additional helper methods for mocking a servlet context.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io-psr/servlet
 * @link      http://www.appserver.io
 */
interface MockServletContextInterface extends ServletContextInterface
{

    /**
     * Returns the absolute path to the application server's base directory.
     *
     * @param string $directoryToAppend A directory to append to the base directory
     *
     * @return string The absolute path the application server's base directory
     */
    public function getBaseDirectory($directoryToAppend = null);

    /**
     * Returns the absolute path to the application directory.
     *
     * @return string The absolute path to the application directory
     */
    public function getAppBase();
}
