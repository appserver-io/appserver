<?php

/**
 * TechDivision\ApplicationServer\DatasourceProvisioner
 *
 * PHP version 5
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Johann Zelger <j.zelger@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\Api\Node\ProvisionNode;
use TechDivision\ApplicationServer\Interfaces\ProvisionerInterface;

/**
 * Standard provisioning functionality.
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Johann Zelger <j.zelger@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
class DatasourceProvisioner implements ProvisionerInterface
{

    /**
     * The containers base directory.
     *
     * @var string
     */
    protected $service;

    /**
     * The initial context instance.
     *
     * @var \TechDivision\ApplicationServer\InitialContext
     */
    protected $initialContext;

    /**
     * Contructor
     *
     * @param \TechDivision\ApplicationServer\InitialContext $initialContext The initial context instance
     */
    public function __construct($initialContext)
    {
        // add initialContext
        $this->initialContext = $initialContext;
        // init API service to use
        $this->service = $this->newService('TechDivision\ApplicationServer\Api\DatasourceService');
    }

    /**
     * Provisions all web applications.
     *
     * @return void
     */
    public function provision()
    {

        // check if deploy dir exists
        if (is_dir($this->getWebappsDir())) {

            // init file iterator on webapps directory
            $directory = new \RecursiveDirectoryIterator($this->getWebappsDir());
            $iterator = new \RecursiveIteratorIterator($directory);

            // Iterate through all provisioning files (provision.xml) and attach them to the configuration
            foreach (new \RegexIterator($iterator, '/^.*\\-ds.xml$/') as $datasourceFile) {

                // load the database configuration
                $datasourceNodes = $this->getService()->initFromFile($datasourceFile);

                // store the datasource in the system configuration
                foreach ($datasourceNodes as $datasourceNode) {
                    $this->getService()->attachDatasource($datasourceNode);
                }
            }
        }
    }

    /**
     * Returns the servers deploy directory
     *
     * @return string
     */
    public function getWebappsDir()
    {
        return $this->getService()->getWebappsDir();
    }

    /**
     * (non-PHPdoc)
     *
     * @param string $className The API service class name to return the instance for
     *
     * @return \TechDivision\ApplicationServer\Api\ServiceInterface The service instance
     * @see \TechDivision\ApplicationServer\InitialContext::newService()
     */
    public function newService($className)
    {
        return $this->getInitialContext()->newService($className);
    }

    /**
     * Returns the inital context instance.
     *
     * @return \TechDivision\ApplicationServer\InitialContext The initial context instance
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * Returns the service instance to use.
     *
     * @return \TechDivision\ApplicationServer\Api\ServiceInterface $service The service to use
     */
    public function getService()
    {
        return $this->service;
    }
}
