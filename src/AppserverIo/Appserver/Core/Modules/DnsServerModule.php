<?php

/**
 * \AppserverIo\Appserver\Core\Modules\DnsServerModule
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

namespace AppserverIo\Appserver\Core\Modules;

use AppserverIo\DnsServer\Modules\CoreModule;
use AppserverIo\Server\Interfaces\ServerContextInterface;
use AppserverIo\Server\Interfaces\ModuleConfigurationInterface;
use AppserverIo\Server\Interfaces\ModuleConfigurationAwareInterface;
use AppserverIo\DnsServer\StorageProvider\StackableResolver;
use AppserverIo\DnsServer\StorageProvider\RecursiveProvider;
use AppserverIo\DnsServer\StorageProvider\JsonStorageProvider;
use AppserverIo\Appserver\Core\Modules\StorageProvider\SystemConfigStorageProvider;
use AppserverIo\DnsServer\Interfaces\DnsModuleInterface;
use AppserverIo\Lang\Reflection\ReflectionClass;

/**
 * Core module that provides basic DNS name resolution.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class DnsServerModule extends CoreModule implements ModuleConfigurationAwareInterface
{

    /**
     * The module's configuration.
     *
     * @var \AppserverIo\Server\Interfaces\ModuleConfigurationInterface
     */
    protected $moduleConfiguration;

    /**
     * Inject's the passed module configuration into the module instance.
     *
     * @param \AppserverIo\Server\Interfaces\ModuleConfigurationInterface $moduleConfiguration The module configuration to inject
     *
     * @return void
     */
    public function injectModuleConfiguration(ModuleConfigurationInterface $moduleConfiguration)
    {
        $this->moduleConfiguration = $moduleConfiguration;
    }

    /**
     * Return's the module configuration.
     *
     * @return \AppserverIo\Server\Interfaces\ModuleConfigurationInterface The module configuration
     */
    public function getModuleConfiguration()
    {
        return $this->moduleConfiguration;
    }
}
