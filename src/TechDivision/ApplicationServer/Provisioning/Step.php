<?php

/**
 * TechDivision\ApplicationServer\Provisioning\Step
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

/**
 * Interface for all step implementations.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Provisioning
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
interface Step
{

    /**
     * Executes the functionality for this step.
     *
     * @return void
     */
    public function execute();

    /**
     * Injects the step node with the configuration data for this step.
     *
     * @param \TechDivision\ApplicationServer\Api\Node\StepNode $stepNode The step node data
     *
     * @return void
     */
    public function injectStepNode(StepNode $stepNode);

    /**
     * Injects the datasource node found in the provisioning configuration.
     *
     * @param \TechDivision\ApplicationServer\Api\Node\DatasourceNode $datasourceNode The datasource node data
     *
     * @return void
     */
    public function injectDatasourceNode(DatasourceNode $datasourceNode);

    /**
     * Injects the absolute path to the appservers PHP executable.
     *
     * @param string $phpExecutable The absolute path to the appservers PHP executable
     *
     * @return void
     */
    public function injectPhpExecutable($phpExecutable);

    /**
     * Injects the absolute path to the applications folder.
     *
     * @param string $webappPath The absolute path to applications folder
     *
     * @return void
     */
    public function injectWebappPath($webappPath);
}
