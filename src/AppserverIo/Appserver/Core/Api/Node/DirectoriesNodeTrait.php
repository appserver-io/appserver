<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\DirectoriesNodeTrait
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
 * @copyright 2014 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * Abstract node that serves nodes having a directories/directory child.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2014 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
trait DirectoriesNodeTrait
{

    /**
     * The directories.
     *
     * @var array
     * @AS\Mapping(nodeName="directories/directory", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\DirectoryNode")
     */
    protected $directories = array();

    /**
     * Array with the directories.
     *
     * @param array $directories The directories
     *
     * @return void
     */
    public function setDirectories(array $directories)
    {
        $this->directories = $directories;
    }

    /**
     * Array with the directories.
     *
     * @return array
     */
    public function getDirectories()
    {
        return $this->directories;
    }
}
