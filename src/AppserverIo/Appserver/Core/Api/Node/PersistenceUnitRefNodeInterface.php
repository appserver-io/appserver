<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\PersistenceUnitRefNodeInterface
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
 * Interface for a persistence unit reference DTO implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface PersistenceUnitRefNodeInterface extends NodeInterface
{

    /**
     * Return's the persistence unit reference name information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\EpbRefNameNode The persitsence unit reference name information
     */
    public function getPersitenceUnitRefName();

    /**
     * Return's the persistence unit name information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\EpbRefNameNode The persistence unit name information
     */
    public function getPersitenceUnitName();

    /**
     * Return's the persistence unit description information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DescriptionNode The persistence unit description information
     */
    public function getDescription();

    /**
     * Return's the persistence unit injection target information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\InjectionTargetNode The persistence unit injection target information
     */
    public function getInjectionTarget();
}
