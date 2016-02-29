<?php

/**
 * AppserverIo\Appserver\Core\Modules\StorageProvider\SystemConfigStorageProvider
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

use AppserverIo\Server\Interfaces\ModuleConfigurationInterface;
use AppserverIo\DnsServer\StorageProvider\JsonStorageProvider;
use AppserverIo\DnsServer\StorageProvider\AbstractStorageProvider;
use AppserverIo\Appserver\Core\Interfaces\SystemConfigurationInterface;

/**
 * A storage provider implementation that uses the system configuration
 * to create DNS records from.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2016 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io/
 */
class SystemConfigurationStorageProvider extends JsonStorageProvider
{

    /**
     * Initializes the storage provider by loading the configuration values from
     * the passed module configuration.
     *
     * @param \AppserverIo\Appserver\Core\Interfaces\SystemConfigurationInterface $systemConfiguration The system configuration
     * @param \AppserverIo\Server\Interfaces\ModuleConfigurationInterface         $moduleConfiguration The module configuration
     *
     * @throws \Exception Is thrown if the JSON can not be read
     */
    public function __construct(SystemConfigurationInterface $systemConfiguration, ModuleConfigurationInterface $moduleConfiguration)
    {

        // load the configuration values
        $defaultTtl = $moduleConfiguration->getParam(AbstractStorageProvider::DEFAULT_TTL);

        // query whether or not the default TTL is an integer
        if (!is_int($defaultTtl)) {
            throw new \Exception('Default TTL must be an integer.');
        }

        // declare the array for the DNS records
        $dnsRecords = array();

        // iterate over the system configuration and create DNS A records from the found virtual hosts
        foreach ($systemConfiguration->getContainers() as $containerNode) {
            foreach ($containerNode->getServers() as $serverNode) {
                foreach ($serverNode->getVirtualHosts() as $virtualHost) {
                    if (sizeof($dnsNames = explode(' ', $virtualHost->getName())) > 0) {
                        foreach ($dnsNames as $dnsName) {
                            // add the IPv4 + IPv6 address for localhost
                            $dnsRecords[$dnsName]['A'] = array('127.0.0.1');
                            $dnsRecords[$dnsName]['AAAA'] = array('::1');
                        }
                    }
                }
            }
        }

        // set the default TTL and the DNS records
        $this->dsTtl = $defaultTtl;
        $this->dnsRecords = $dnsRecords;
    }
}
