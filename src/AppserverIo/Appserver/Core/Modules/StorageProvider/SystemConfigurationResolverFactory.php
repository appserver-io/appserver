<?php

/**
 * AppserverIo\Appserver\Core\Modules\StorageProvider\SystemConfigurationResolverFactory
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
 * @copyright 2016 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io/
 */

namespace AppserverIo\Appserver\Core\Modules\StorageProvider;

use AppserverIo\Server\Interfaces\ServerContextInterface;
use AppserverIo\Server\Interfaces\ModuleConfigurationInterface;
use AppserverIo\DnsServer\StorageProvider\RecursiveProvider;
use AppserverIo\DnsServer\StorageProvider\StackableResolver;

/**
 * A storage provider implementation using a JSON file to load the DNS records from.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2016 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io/
 */
class SystemConfigurationResolverFactory
{

    /**
     * Factory method to create a new DNS resolver instance.
     *
     * @param \AppserverIo\Server\Interfaces\ServerContextInterface       $serverContext       The server context for the resolver
     * @param \AppserverIo\Server\Interfaces\ModuleConfigurationInterface $moduleConfiguration The module configuration with the initialization parameters
     *
     * @return \AppserverIo\DnsServer\Interfaces\StorageProviderInterface The initialized DNS resolver
     */
    public static function factory(ServerContextInterface $serverContext, ModuleConfigurationInterface $moduleConfiguration)
    {

        // laod the system configuration from the server context
        $systemConfiguration = $serverContext->getContainer()->getInitialContext()->getSystemConfiguration();

        // initialize the storage provider
        $storageProvider = new SystemConfigurationStorageProvider($systemConfiguration, $moduleConfiguration);

        // initialize the DNS resolver to load the DNS entries from the storage
        return new StackableResolver(array($storageProvider, new RecursiveProvider()));
    }
}
