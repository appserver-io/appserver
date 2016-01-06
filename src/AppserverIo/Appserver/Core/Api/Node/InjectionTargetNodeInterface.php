<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\InjectionTargetNodeInterface
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
 * Interface for a enterprise bean reference DTO implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface InjectionTargetNodeInterface extends NodeInterface
{

    /**
     * Return's the enterprise bean injection target class information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\InjectionTargetClassNode The enterprise bean injection target class information
     */
    public function getInjectionTargetClass();

    /**
     * Return's the enterprise bean injection target method information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\InjectionTargetMethodNode The enterprise bean injection target method information
     */
    public function getInjectionTargetMethod();

    /**
     * Return's the enterprise bean injection target property information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\InjectionTargetPropertyNode The enterprise bean injection target property information
     */
    public function getInjectionTargetProperty();
}
