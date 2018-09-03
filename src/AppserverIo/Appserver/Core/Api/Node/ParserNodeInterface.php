<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\ParserNodeInterface
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

use AppserverIo\Description\Configuration\DirectoriesAwareConfigurationInterface;

/**
 * The interface for a parse configuration implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface ParserNodeInterface extends DirectoriesAwareConfigurationInterface
{

    /**
     * Returns the application name.
     *
     * @return string The unique application name
     */
    public function getName();

    /**
     * Returns the class name.
     *
     * @return string The class name
     */
    public function getType();

    /**
     * Returns the factory class name.
     *
     * @return string The factory class name
     */
    public function getFactory();

    /**
     * Returns the descriptor name.
     *
     * @return string The descriptor name
     */
    public function getDescriptorName();
}
