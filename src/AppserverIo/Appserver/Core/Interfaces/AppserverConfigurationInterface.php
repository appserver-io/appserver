<?php

/**
 * \AppserverIo\Appserver\Core\Interfaces\AppserverConfigurationInterface
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH - <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io/
 */

namespace AppserverIo\Appserver\Core\Interfaces;

use AppserverIo\Appserver\Core\Api\Node\AppNode;
use AppserverIo\Appserver\Core\Api\Node\DatasourceNode;
use AppserverIo\Configuration\Interfaces\ConfigurationInterface;

/**
 * Interface common to all classes which represent an OO configuration for the complete appserver
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH - <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io/
 */
interface AppserverConfigurationInterface extends ConfigurationInterface
{
    /*
    \AppserverIo\Configuration\Interfaces\ConfigurationInterface::getParam
    \AppserverIo\Configuration\Interfaces\ConfigurationInterface::getUuid
    */

    /**
     * Attaches the passed app node.
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\AppNode $app The app node to attach
     *
     * @return void
     */
    public function attachApp(AppNode $app);

    /**
     * Attaches the passed datasource node.
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\DatasourceNode $datasource The datasource node to attach
     *
     * @return void
     */
    public function attachDatasource(DatasourceNode $datasource);

    /**
     * Returns an array with the information about the deployed applications.
     *
     * @return array The array with the information about the deployed applications
     */
    public function getApps();

    /**
     * Returns the node with the base directory information.
     *
     * @return string The base directory information
     */
    public function getBaseDirectory();

    /**
     * Returns the array with all available containers.
     *
     * @return array The available containers
     */
    public function getContainers();

    /**
     * Returns an array with the information about the deployed datasources.
     *
     * @return array The array with the information about the deployed datasources
     */
    public function getDatasources();

    /**
     * Returns the array with registered extractors.
     *
     * @return array The registered extractors
     */
    public function getExtractors();

    /**
     * Returns the groupname configured in the system configuration.
     *
     * @return string The groupname
     */
    public function getGroup();

    /**
     * Returns the node containing information about the initial context.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\InitialContextNode The initial context information
     */
    public function getInitialContext();

    /**
     * Returns the array with all available loggers.
     *
     * @return array The available loggers
     */
    public function getLoggers();

    /**
     * Returns the array with registered provisioners.
     *
     * @return array The registered provisioners
     */
    public function getProvisioners();

    /**
     * Returns the array with all available scanners.
     *
     * @return array The available scanners
     */
    public function getScanners();

    /**
     * Returns the umask configured in the system configuration.
     *
     * @return string The umask
     */
    public function getUmask();

    /**
     * Returns the username configured in the system configuration.
     *
     * @return string The username
     */
    public function getUser();
}
