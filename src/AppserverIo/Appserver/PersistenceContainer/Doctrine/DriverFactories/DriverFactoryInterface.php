<?php

/**
 * AppserverIo\Appserver\PersistenceContainer\Doctrine\DriverFactories\DriverFactoryInterface
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
 * @link      https://github.com/appserver-io/rmi
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\PersistenceContainer\Doctrine\DriverFactories;

use Doctrine\ORM\Configuration;

/**
 * The interface for the factories of all Doctrine driver implementations.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/rmi
 * @link      http://www.appserver.io
 */
interface DriverFactoryInterface
{

    /**
     * Return's the new cache instance.
     *
     * @param Doctrine\ORM\Configuration $configuration The DBAL configuration to create the driver for
     * @param array                      $paths         The path to the driver configuration
     * @param array                      $params        The additional configuration params
     *
     * @return Doctrine\Common\Persistence\Mapping\Driver\MappingDriver The driver instance
     */
    public static function get(Configuration $configuration, array $paths = array(), array $params = array());
}