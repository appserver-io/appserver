<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\PersistenceUnitNodeInterface
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
use AppserverIo\Description\Configuration\ConfigurationInterface;

/**
 * Interface for the persistence unit node information.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface PersistenceUnitNodeInterface extends NodeInterface, ConfigurationInterface
{

    /**
     * Array with the directories.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DirectoryNode[]
     */
    public function getDirectories();

    /**
     * Returns an array with the directories as string value, each
     * prepended with the passed value.
     *
     * @param string $prepend Prepend to each directory
     *
     * @return array The array with the directories as string
     */
    public function getDirectoriesAsArray($prepend = null);

    /**
     * Array with the handler params to use.
     *
     * @return array
     */
    public function getParams();

    /**
     * Returns the param with the passed name casted to
     * the specified type.
     *
     * @param string $name The name of the param to be returned
     *
     * @return mixed The requested param casted to the specified type
     */
    public function getParam($name);

    /**
     * Returns the params casted to the defined type
     * as associative array.
     *
     * @return array The array with the casted params
     */
    public function getParamsAsArray();

    /**
     * Returns the entity managers annotation registries configuration.
     *
     * @return array The entity managers annotation registries configuration
     */
    public function getAnnotationRegistries();

    /**
     * Returns the entity manager's interface.
     *
     * @return string The entity manager's interface
     */
    public function getInterface();

    /**
     * Returns the entity manager's name.
     *
     * @return string The entity manager's name
     */
    public function getName();

    /**
     * Returns the entity manager's class name.
     *
     * @return string The entity manager's class name
     */
    public function getType();

    /**
     * Returns the entity manager's factory class name.
     *
     * @return string The entity manager's factory class name
     */
    public function getFactory();

    /**
     * Returns the entity manager's datasource configuration.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DatasourceNode The entity manager's datasource configuration
     */
    public function getDatasource();

    /**
     * Returns the entity manager's metadata configuration.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\MetadataConfigurationNode The entity manager's metadata configuration
     */
    public function getMetadataConfiguration();

    /**
     * Returns the entity manager's query cache configuration.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\MetadataConfigurationNode The entity manager's query cache configuration
     */
    public function getQueryCacheConfiguration();

    /**
     * Returns the entity manager's result cache configuration.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\MetadataConfigurationNode The entity manager's result cache configuration
     */
    public function getResultCacheConfiguration();

    /**
     * Returns the entity manager's metadata cache configuration.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\MetadataCacheConfigurationNode The entity manager's metadata cache configuration
     */
    public function getMetadataCacheConfiguration();
}
