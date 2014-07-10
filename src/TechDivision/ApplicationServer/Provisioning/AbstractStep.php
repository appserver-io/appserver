<?php

/**
 * TechDivision\ApplicationServer\Provisioning\AbstractStep
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Provisioning
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Provisioning;

use TechDivision\ApplicationServer\Api\Node\StepNode;
use TechDivision\ApplicationServer\Api\Node\DatasourceNode;
use TechDivision\ApplicationServer\Api\ServiceInterface;

/**
 * Abstract base class for a step implementation.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Provisioning
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
abstract class AbstractStep extends \Thread implements Step
{

    /**
     * The provisioning service.
     *
     * @var \TechDivision\ApplicationServer\Api\ServiceInterface;
     */
    protected $service;

    /**
     * The step node with the configuration data for this step.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\StepNode
     */
    protected $stepNode;

    /**
     * The datasource node found in the provisioning configuration.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\DatasourceNode
     */
    protected $datasourceNode;

    /**
     * The absolute path to the appserver PHP executable.
     *
     * @var string
     */
    protected $phpExecutable;

    /**
     * The absolute path to the applications folder.
     *
     * @var string
     */
    protected $webappPath;

    /**
     * Injects the provisioning service.
     *
     * @param \TechDivision\ApplicationServer\Api\ServiceInterface $service The provisioning service
     *
     * @return void
     */
    public function injectService(ServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Injects the step node with the configuration data for this step.
     *
     * @param \TechDivision\ApplicationServer\Api\Node\StepNode $stepNode The step node data
     *
     * @return void
     */
    public function injectStepNode(StepNode $stepNode)
    {
        $this->stepNode = $stepNode;
    }

    /**
     * Injects the datasource node found in the provisioning configuration.
     *
     * @param \TechDivision\ApplicationServer\Api\Node\DatasourceNode $datasourceNode The datasource node data
     *
     * @return void
     */
    public function injectDatasourceNode(DatasourceNode $datasourceNode)
    {
        $this->datasourceNode = $datasourceNode;
    }

    /**
     * Injects the absolute path to the appservers PHP executable.
     *
     * @param string $phpExecutable The absolute path to the appservers PHP executable
     *
     * @return void
     */
    public function injectPhpExecutable($phpExecutable)
    {
        $this->phpExecutable = $phpExecutable;
    }

    /**
     * Injects the absolute path to the applications folder.
     *
     * @param string $webappPath The absolute path to applications folder
     *
     * @return void
     */
    public function injectWebappPath($webappPath)
    {
        $this->webappPath = $webappPath;
    }

    /**
     * Returns the provisioning service.
     *
     * @return \TechDivision\ApplicationServer\Api\ServiceInterface The provisioning service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Returns the step node data.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\StepNode The step node data
     */
    public function getStepNode()
    {
        return $this->stepNode;
    }

    /**
     * Returns the datasource node found in the provisioning configuration.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\DatasourceNode The datasource node data
     */
    public function getDatasourceNode()
    {
        return $this->datasourceNode;
    }

    /**
     * Returns the absolute path to the appservers PHP executable.
     *
     * @return string The absolute path to the appservers PHP executable
     */
    public function getPhpExecutable()
    {
        return $this->phpExecutable;
    }

    /**
     * Returns the absolute path to the applications folder.
     *
     * @return string The applications folder
     */
    public function getWebappPath()
    {
        return $this->webappPath;
    }

    /**
     * Executes the steps functionality in a separate context.
     *
     * @return void
     */
    public function run()
    {
        $this->execute();
    }
}
