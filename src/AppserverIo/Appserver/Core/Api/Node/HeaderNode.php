<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\HeaderNode
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

use AppserverIo\Description\Api\Node\AbstractNode;

/**
 * DTO to transfer header information.
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class HeaderNode extends AbstractNode
{
    /**
     * The header type.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * The header name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The header value.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $value;

    /**
     * The header override flag.
     *
     * @var string
     * @AS\Mapping(nodeType="boolean")
     */
    protected $override;

    /**
     * The header append flag.
     *
     * @var string
     * @AS\Mapping(nodeType="boolean")
     */
    protected $append;

    /**
     * The header uri
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $uri;

    /**
     * Returns header type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns header name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns header value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns header override
     *
     * @return boolean
     */
    public function getOverride()
    {
        return $this->override;
    }

    /**
     * Returns header append
     *
     * @return boolean
     */
    public function getAppend()
    {
        return $this->append;
    }

    /**
     * Returns header uri
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }
}
