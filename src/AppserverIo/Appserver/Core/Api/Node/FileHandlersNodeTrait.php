<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\FileHandlersNodeTrait
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
 * Trait that serves nodes having a fileHandlers/fileHandler child.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
trait FileHandlersNodeTrait
{

    /**
     * The file handlers.
     *
     * @var array
     * @AS\Mapping(nodeName="fileHandlers/fileHandler", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\FileHandlerNode")
     */
    protected $fileHandlers = array();

    /**
     * Returns the file handler nodes.
     *
     * @return array
     */
    public function getFileHandlers()
    {
        return $this->fileHandlers;
    }

    /**
     * Returns the file handlers as an associative array.
     *
     * @return array The array with the sorted file handlers
     */
    public function getFileHandlersAsArray()
    {

        // initialize the array for the file handlers
        $fileHandlers = array();

        // iterate over the file handlers nodes and sort them into an array
        /** @var \AppserverIo\Appserver\Core\Api\Node\FileHandlerNode $fileHandler */
        foreach ($this->getFileHandlers() as $fileHandler) {
            $fileHandlers[$fileHandler->getExtension()] = array(
                'name' => $fileHandler->getName(),
                'params' => $fileHandler->getParamsAsArray()
            );
        }

        // return what we got
        return $fileHandlers;
    }
}
