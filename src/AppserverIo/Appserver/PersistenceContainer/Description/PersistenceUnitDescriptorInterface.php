<?php

/**
 * AppserverIo\Appserver\PersistenceContainer\DependencyInjection\PersistenceUnitDescriptorInterface
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

use AppserverIo\Description\NameAwareDescriptorInterface;
use AppserverIo\Description\FactoryAwareDescriptorInterface;

/**
 * Interface for a persistence unit descriptor.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/description
 * @link      http://www.appserver.io
 */
interface PersistenceUnitDescriptorInterface extends NameAwareDescriptorInterface, FactoryAwareDescriptorInterface
{

    /**
     * Returns the beans class name.
     *
     * @return string The beans class name
     */
    public function getClassName();

    /**
     * Returns the factory that creates the bean.
     *
     * @return \AppserverIo\Psr\EnterpriseBeans\Description\FactoryDescriptorInterface The bean's factory
     */
    public function getFactory();
}
