<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\ScannerNode
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

use AppserverIo\Configuration\Interfaces\NodeInterface;

/**
 * Interface for the scanner node information.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface ScannerNodeInterface extends NodeInterface
{

    /**
     * Returns the provisioner type.
     *
     * @return string The provisioner type
     */
    public function getType();

    /**
     * Returns the scanner factory class.
     *
     * @return string The scanner factory class
     */
    public function getFactory();

    /**
     * Returns the provisioner name.
     *
     * @return string The provisioner name
     */
    public function getName();

    /**
     * Array with the handler params to use.
     *
     * @return array
     */
    public function getParams();

    /**
     * Returns the param with the passed name casted to
     * the specified type.
     *
     * @param string $name The name of the param to be returned
     *
     * @return mixed The requested param casted to the specified type
     */
    public function getParam($name);

    /**
     * Returns the params casted to the defined type
     * as associative array.
     *
     * @return array The array with the casted params
     */
    public function getParamsAsArray();

    /**
     * Array with the directories.
     *
     * @return array
     */
    public function getDirectories();
}
