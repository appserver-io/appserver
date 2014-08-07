<?php

/**
 * TechDivision\ApplicationServer\DatasourceProvisioner
 *
 * PHP version 5
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace TechDivision\ApplicationServer;

/**
 * Standard provisioning functionality.
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
class DatasourceProvisioner extends AbstractProvisioner
{

    /**
     * Provision all datasources recursively found in the web application directory.
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
}
