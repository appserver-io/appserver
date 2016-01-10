<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\OptionsNode
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2015 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 * @deprecated Since 1.2.0
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * DTO to transfer the authentication adapter options.
 *
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2015 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 * @deprecated Since 1.2.0
 */
class OptionsNode extends AbstractNode implements OptionsNodeInterface
{

    /**
     * The authentication adapter file option information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\FileNode
     * @AS\Mapping(nodeName="file", nodeType="AppserverIo\Appserver\Core\Api\Node\FileNode")
     */
    protected $file;

    /**
     * Return's the authentication adapter file option information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\FileNode The authentication adapter file option information
     */
    public function getFile()
    {
        return $this->file;
    }
}