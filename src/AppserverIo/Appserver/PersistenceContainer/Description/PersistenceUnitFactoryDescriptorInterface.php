<?php

/**
 * AppserverIo\Appserver\PersistenceContainer\Description\PersistenceUnitFactoryDescriptorInterface
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
 * @link      https://github.com/appserver-io/description
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\PersistenceContainer\Description;

use AppserverIo\Psr\EnterpriseBeans\Description\BeanDescriptorInterface;

/**
 * Interface for a persistence unit factory descriptor.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/description
 * @link      http://www.appserver.io
 */
interface PersistenceUnitFactoryDescriptorInterface extends BeanDescriptorInterface
{

    /**
     * Returns a new descriptor instance.
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\Description\PersistenceUnitFactoryDescriptorInterface The descriptor instance
     */
    public static function newDescriptorInstance();
}
