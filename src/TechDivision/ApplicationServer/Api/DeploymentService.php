<?php
/**
 * TechDivision\ApplicationServer\Api\DeploymentService
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

namespace TechDivision\ApplicationServer\Api;

use TechDivision\ApplicationServer\Api\AbstractService;
use TechDivision\ApplicationServer\Api\Node\DeploymentNode;
use TechDivision\ApplicationServer\Api\ServiceInterface;

/**
 * A service that handles deployment configuration data.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class DeploymentService extends AbstractService
{

    /**
     * Return's all deployment configurations.
     *
     * @return array An array with all deployment configurations
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::findAll()
     */
    public function findAll()
    {
        $deploymentNodes = array();
        foreach ($this->getSystemConfiguration()->getContainers() as $container) {
            $deploymentNode = $container->getDeployment();
            $deploymentNodes[$deploymentNode->getUuid()] = $deploymentNode;
        }
        return $deploymentNodes;
    }

    /**
     * Returns the deployment with the passed UUID.
     *
     * @param integer $uuid UUID of the deployment to return
     *
     * @return DeploymentNode The deployment with the UUID passed as parameter
     * @see ServiceInterface::load()
     */
    public function load($uuid)
    {
        $deploymentNodes = $this->findAll();
        if (array_key_exists($uuid, $deploymentNodes)) {
            return $deploymentNodes[$uuid];
        }
    }
}
