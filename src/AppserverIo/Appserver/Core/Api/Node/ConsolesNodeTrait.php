<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\ConsolesNodeTrait
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

use AppserverIo\Description\Annotations as DI;

/**
 *
 * Trait that provides functionality to handle console nodes.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
trait ConsolesNodeTrait
{

    /**
     * The application servers console configuration.
     *
     * @var array
     * @DI\Mapping(nodeName="consoles/console", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\ConsoleNode")
     */
    protected $consoles = array();

    /**
     * Sets the application servers console configuration.
     *
     * @param array $consoles The console configuration
     *
     * @return void
     */
    public function setConsoles($consoles)
    {
        $this->consoles = $consoles;
    }

    /**
     * Returns the application servers console configuration.
     *
     * @return array The application server console configuration
     */
    public function getConsoles()
    {
        return $this->consoles;
    }
}
