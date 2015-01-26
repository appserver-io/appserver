<?php

/**
 * AppserverIo\Appserver\Core\Provisioning\Step
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

namespace AppserverIo\Appserver\Core\Provisioning;

use AppserverIo\Appserver\Core\Api\Node\StepNode;
use AppserverIo\Appserver\Core\Api\Node\DatasourceNode;

/**
 * Interface for all step implementations.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
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
     * @param \AppserverIo\Appserver\Core\Api\Node\StepNode $stepNode The step node data
     *
     * @return void
     */
    public function injectStepNode(StepNode $stepNode);

    /**
     * Injects the datasource node found in the provisioning configuration.
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\DatasourceNode $datasourceNode The datasource node data
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
