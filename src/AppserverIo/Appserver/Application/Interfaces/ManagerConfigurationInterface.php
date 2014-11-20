<?php

/**
 * AppserverIo\Appserver\Application\Interfaces\ManagerConfigurationInterface
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
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Application\Interfaces;

/**
 * Interface for the manager configuration node.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */
interface ManagerConfigurationInterface
{

    /**
     * Returns the application name.
     *
     * @return string The unique application name
     */
    public function getName();

    /**
     * Returns the class name.
     *
     * @return string The class name
     */
    public function getType();

    /**
     * Returns the factory class name.
     *
     * @return string The factory class name
     */
    public function getFactory();

    /**
     * Returns the bean class name.
     *
     * @return string The bean class name
     */
    public function getBeanName();

    /**
     * Returns the mapped class name.
     *
     * @return string The mapped class name
     */
    public function getMappedName();

    /**
     * Returns the bean interface name.
     *
     * @return string The bean interface name
     */
    public function getBeanInterface();

    /**
     * Returns the beans fully qualified PNDI lookup name.
     *
     * @return string The beans fully qualified lookup name
     */
    public function getLookup();

    /**
     * Returns the params casted to the defined type
     * as associative array.
     *
     * @return array The array with the casted params
     */
    public function getParamsAsArray();

    /**
     * Returns the managers ENC lookup names found in the configuration, merge with the annotation
     * values, whereas the configuration values will override the annotation values.
     *
     * @return array The array with the managers lookup names
     */
    public function toLookupNames();
}
