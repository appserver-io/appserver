<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\RewriteNode
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
use AppserverIo\Appserver\Core\Api\ExtensionInjectorParameterTrait;

/**
 * DTO to transfer module information.
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class RewriteNode extends AbstractNode
{
    // We have to use this trait to allow for the injection of additional target strings
    use ExtensionInjectorParameterTrait;

    /**
     * The rewrite condition.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $condition;

    /**
     * The rule target.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $target;

    /**
     * The rewrite flat.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $flag;

    /**
     * Returns the rewrite condition.
     *
     * @return string The rewrite condition
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Returns the rule target.
     *
     * @return string The rule target
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Returns the rewrite flag.
     *
     * @return string The rewrite flag
     */
    public function getFlag()
    {
        return $this->flag;
    }
}
