<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\PersistenceUnitNode
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
 * DTO to transfer a applications persistence unit configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class PersistenceUnitNode extends AbstractNode implements PersistenceUnitNodeInterface
{

    /**
     * A directories node trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\DirectoriesNodeTrait
     */
    use DirectoriesNodeTrait;

    /**
     * A params node trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ParamsNodeTrait
     */
    use ParamsNodeTrait;

    /**
     * A annotation registries node trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\AnnotationRegistriesNodeTrait
     */
    use AnnotationRegistriesNodeTrait;

    /**
     * The interface name the class loader has.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $interface;

    /**
     * The unique class loader name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The class loaders class name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * The class loaders factory class name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $factory;

    /**
     * The node containing datasource information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\DatasourceNode
     * @AS\Mapping(nodeName="datasource", nodeType="AppserverIo\Appserver\Core\Api\Node\DatasourceNode")
     */
    protected $datasource;

    /**
     * The node containing the metadata configuration information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\DatasourceNode
     * @AS\Mapping(nodeName="metadataConfiguration", nodeType="AppserverIo\Appserver\Core\Api\Node\MetadataConfigurationNode")
     */
    protected $metadataConfiguration;

    /**
     * Returns the entity manager's interface.
     *
     * @return string The entity manager's interface
     */
    public function getInterface()
    {
        return $this->name;
    }

    /**
     * Returns the entity manager's name.
     *
     * @return string The entity manager's name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the entity manager's class name.
     *
     * @return string The entity manager's class name
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the entity manager's factory class name.
     *
     * @return string The entity manager's factory class name
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Returns the entity manager's datasource configuration.
     *
     * @return AppserverIo\Appserver\Core\Api\Node\DatasourceNode The entity manager's datasource configuration
     */
    public function getDatasource()
    {
        return $this->datasource;
    }

    /**
     * Returns the entity manager's metadata configuration.
     *
     * @return AppserverIo\Appserver\Core\Api\Node\MetadataConfigurationNode The entity manager's metadata configuration
     */
    public function getMetadataConfiguration()
    {
        return $this->metadataConfiguration;
    }
}
