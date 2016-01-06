<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\PersistenceUnitRefNode
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
 * DTO to transfer persistence unit reference information.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class PersistenceUnitRefNode extends AbstractNode implements PersistenceUnitRefNodeInterface
{

    /**
     * The persistence unit reference name information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\PersistenceUnitRefNameNode
     * @AS\Mapping(nodeName="epb-ref-name", nodeType="AppserverIo\Appserver\Core\Api\Node\PersistenceUnitRefNameNode")
     */
    protected $persistenceUnitRefName;

    /**
     * The persistence unit name information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\PersistenceUnitNameNode
     * @AS\Mapping(nodeName="epb-ref-name", nodeType="AppserverIo\Appserver\Core\Api\Node\PersistenceUnitNameNode")
     */
    protected $persistenceUnitName;

    /**
     * The persistence unit description information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\DescriptionNode
     * @AS\Mapping(nodeName="description", nodeType="AppserverIo\Appserver\Core\Api\Node\DescriptionNode")
     */
    protected $description;

    /**
     * The persistence unit injection target information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\InjectionTargetNode
     * @AS\Mapping(nodeName="injection-target", nodeType="AppserverIo\Appserver\Core\Api\Node\InjectionTargetNode")
     */
    protected $injectionTarget;

    /**
     * Return's the persistence unit reference name information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\PersistenceUnitRefNameNode The persitsence unit reference name information
     */
    public function getPersitenceUnitRefName()
    {
        return $this->persistenceUnitRefName;
    }

    /**
     * Return's the persistence unit name information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\PersistenceUnitNameNode The persistence unit name information
     */
    public function getPersitenceUnitName()
    {
        return $this->persistenceUnitName;
    }

    /**
     * Return's the persistence unit description information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DescriptionNode The persistence unit description information
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Return's the persistence unit injection target information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\InjectionTargetNode The persistence unit injection target information
     */
    public function getInjectionTarget()
    {
        return $this->injectionTarget;
    }
}
