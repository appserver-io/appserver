<?php

/**
 * AppserverIo\Appserver\Ldap\LdapManagerFactory
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
 * @copyright 2019 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Ldap;

use AppserverIo\Storage\StackableStorage;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Core\Api\Node\ManagerNodeInterface;
use AppserverIo\Appserver\Core\Interfaces\ManagerFactoryInterface;

/**
 * The LDAP manager factory to create a new LDAP manager instance.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2019 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class LdapManagerFactory implements ManagerFactoryInterface
{

    /**
     * The main method that creates new instances in a separate context.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface         $application          The application instance to register the class loader with
     * @param \AppserverIo\Appserver\Core\Api\Node\ManagerNodeInterface $managerConfiguration The manager configuration
     *
     * @return void
     */
    public static function visit(ApplicationInterface $application, ManagerNodeInterface $managerConfiguration)
    {

        // initialize the stackable for the entities and the naming directory
        $data = new StackableStorage();

        // initialize the default settings for the stateful session beans
        $ldapManagerSettings = new LdapManagerSettings();
        $ldapManagerSettings->mergeWithParams($managerConfiguration->getParamsAsArray());

        // initialize the bean manager
        $ldapManager = new LdapManager();
        $ldapManager->injectData($data);
        $ldapManager->injectApplication($application);
        $ldapManager->injectManagerSettings($ldapManagerSettings);
        $ldapManager->injectManagerConfiguration($managerConfiguration);

        // create the naming context and add it the manager
        $contextFactory = $managerConfiguration->getContextFactory();
        $contextFactory::visit($ldapManager);

        // attach the instance
        $application->addManager($ldapManager, $managerConfiguration);
    }
}
