<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\EpbRefNodeInterface
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
interface EpbRefNodeInterface extends NodeInterface
{

    /**
     * Return's the enterprise bean reference information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\EpbRefNameNode The enterprise bean reference information
     */
    public function getEpbRefName();

    /**
     * Return's the enterprise bean description information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DescriptionNode The enterprise bean description information
     */
    public function getDescription();

    /**
     * Return's the enterprise bean link information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\EpbLinkNode The enterprise bean link information
     */
    public function getEpbLink();

    /**
     * Return's the enterprise bean lookup name information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\LookupNameNode The enterprise bean lookup name information
     */
    public function getLookupName();

    /**
     * Return's the enterprise bean remote interface information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\RemoteNode The enterprise bean remote interface information
     */
    public function getRemote();

    /**
     * Return's the enterprise bean local interface information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\LocalNode The enterprise bean local interface information
     */
    public function getLocal();

    /**
     * Return's the enterprise bean injection target information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\InjectionTargetNode The enterprise bean injection target information
     */
    public function getInjectionTarget();
}
