<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\ProvisionNode
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
 * @copyright 2014 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * DTO to transfer a applications provision configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2014 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ProvisionNode extends AbstractNode
{

    /**
     * The node containing datasource information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\DatasourceNode
     * @AS\Mapping(nodeName="datasource", nodeType="AppserverIo\Appserver\Core\Api\Node\DatasourceNode")
     */
    protected $datasource;

    /**
     * The node containing installation information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\InstallationNode
     * @AS\Mapping(nodeName="installation", nodeType="AppserverIo\Appserver\Core\Api\Node\InstallationNode")
     */
    protected $installation;

    /**
     * Injects the datasource node.
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\DatasourceNode $datasource The datasource node to inject
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
     * @return \AppserverIo\Appserver\Core\Api\Node\DatasourceNode The node containing datasource information
     */
    public function getDatasource()
    {
        return $this->datasource;
    }

    /**
     * Returns the node containing installation information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\InstallationNode The node containing installation information
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

        // copy the datasource node temporarily
        $tmpDatasource = $this->datasource;

        // replace the variables
        ob_start();
        require $provisionFile;
        $this->initFromString(ob_get_clean());

        // re-attach the database node
        $this->datasource = $tmpDatasource;
    }

    /**
     * This method merges the installation steps of the passed provisioning node into the steps of
     * this instance. If a installation node with the same type already exists, the one of this
     * instance will be overwritten.
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\ProvisionNode $provisionNode The node with the installation steps we want to merge
     *
     * @return void
     */
    public function merge(ProvisionNode $provisionNode)
    {

        // inject the datasource node if available
        if ($datasource = $provisionNode->getDatasource()) {
            $this->injectDatasource($datasource);
        }

        // load the steps of this instance
        $localSteps = $this->getInstallation()->getSteps();

        // merge it with the ones found in the passed provisioning node
        foreach ($provisionNode->getInstallation()->getSteps() as $stepToMerge) {
            foreach ($localSteps as $key => $step) {
                if ($step->getType() === $stepToMerge->getType()) {
                    $localSteps[$key] = $stepToMerge;
                } else {
                    $localSteps[$stepToMerge->getUuid()] = $stepToMerge;
                }
            }
        }

        // set the installation steps
        $this->getInstallation()->setSteps($localSteps);
    }
}
