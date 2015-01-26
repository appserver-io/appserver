<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\FileHandlerNode
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * DTO to transfer file handler information.
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class FileHandlerNode extends AbstractNode
{

    /**
     * A params node trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ParamsNodeTrait
     */
    use ParamsNodeTrait;

    /**
     * The file handler name
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The file extension
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $extension;

    /**
     * Returns the file handler's name
     *
     * @return string The file handler's name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return's the file extension
     *
     * @return string The file extension
     */
    public function getExtension()
    {
        return $this->extension;
    }
}
