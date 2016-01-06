<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\ResRefNodeInterface
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
 * Interface for a resource reference DTO implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface ResRefNodeInterface extends NodeInterface
{

    /**
     * Return's the resource reference name information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\EpbRefNameNode The resource reference name information
     */
    public function getResRefName();

    /**
     * Return's the resource reference type information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\EpbRefNameNode The resource reference type information
     */
    public function getResRefType();

    /**
     * Return's the resource description information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DescriptionNode The resource description information
     */
    public function getDescription();

    /**
     * Return's the resource lookup name information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\LookupNameNode The resource lookup name information
     */
    public function getLookupName();

    /**
     * Return's the resource injection target information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\InjectionTargetNode The resource injection target information
     */
    public function getInjectionTarget();
}
