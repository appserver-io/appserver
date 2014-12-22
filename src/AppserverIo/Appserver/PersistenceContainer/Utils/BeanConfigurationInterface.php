<?php

/**
 * AppserverIo\Appserver\PersistenceContainer\Utils\BeanConfigurationInterface
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\PersistenceContainer\Utils;

/**
 * Interface for a bean configuration.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
interface BeanConfigurationInterface
{

    /**
     * Returns the bean name.
     *
     * @return string The bean name
     */
    public function getName();

    /**
     * Returns the beans class name.
     *
     * @return string The beans class name
     */
    public function getClassName();

    /**
     * Returns the beans description.
     *
     * @return string The beans description
     */
    public function getDescription();

    /**
     * Returns the configurable bean name.
     *
     * @return string The configurable bean name
     */
    public function getBeanName();

    /**
     * Returns the bean interface.
     *
     * @return string The bean interface
     */
    public function getBeanInterface();

    /**
     * Returns the mapped name.
     *
     * @return string The mapped name
     */
    public function getMappedName();

    /**
     * Returns the beans session type.
     *
     * @return string The beans session type
     */
    public function getSessionType();

    /**
     * Queries whether the bean should be initialized on startup or not.
     *
     * @return boolean TRUE if the bean should be initialized on startup, else FALSE
     */
    public function isInitOnStartup();

    /**
     * The array with the post construct callback method names.
     *
     * @return array The post construct callback method names
     */
    public function getPostConstructCallbacks();

    /**
     * The array with the pre destroy callback method names.
     *
     * @return array The pre destroy callback method names
     */
    public function getPreDestroyCallbacks();

    /**
     * The array with the EPB references.
     *
     * @return array The EPB references
     */
    public function getEpbReferences();

    /**
     * Merges the passed configuration into this one. Configuration values
     * of the passed configuration will overwrite the this one.
     *
     * @param \AppserverIo\Appserver\PersistenceContainer\UtilsBeanConfigurationInterface $configuration The configuration to merge
     *
     * @return void
     */
    public function merge(BeanConfigurationInterface $configuration);
}
