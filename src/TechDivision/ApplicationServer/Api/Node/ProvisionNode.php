<?php
/**
 * TechDivision\ApplicationServer\Api\Node\ProvisionNode
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Api\Node;

/**
 * DTO to transfer a applications provision configuration.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class ProvisionNode extends AbstractNode
{
    
    /**
     * The node containing datasource information.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\DatasourceNode
     * @AS\Mapping(nodeName="datasource", nodeType="TechDivision\ApplicationServer\Api\Node\DatasourceNode")
     */
    protected $datasource;

    /**
     * The node containing installation information.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\InstallationNode
     * @AS\Mapping(nodeName="installation", nodeType="TechDivision\ApplicationServer\Api\Node\InstallationNode")
     */
    protected $installation;

    /**
     * Injects the datasource node.
     *
     * @param \TechDivision\ApplicationServer\Api\Node\DatasourceNode $datasource The datasource node to inject
     *
     * @return void
     */
    public function injectDatasource(DatasourceNode $datasource)
    {
        $this->datasource = $datasource;
    }
    
    /**
     * Returns the node containing datasource information.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\DatasourceNode The node containing datasource information
     */
    public function getDatasource()
    {
        return $this->datasource;
    }

    /**
     * Returns the node containing installation information.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\InstallationNode The node containing installation information
     */
    public function getInstallation()
    {
        return $this->installation;
    }
    
    /**
     * This method reprovisions the provision node with the data from the file passed as parameter.
     *
     * Before reinitializing the provisioning node, the file will be reinterpreted with be invoking
     * the PHP parser again, what again gives you the possibility to replace content by calling the
     * PHP methods of this class.
     *
     * @param string $provisionFile The absolute pathname of the file to reprovision from
     *
     * @return void
     */
    public function reprovision($provisionFile)
    {
        ob_start();
        require $provisionFile;
        $this->initFromString($content = ob_get_clean());
    }
}
