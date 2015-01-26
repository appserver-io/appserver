<?php

/**
 * AppserverIo\Appserver\Core\Provisioning\AbstractStep
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

namespace AppserverIo\Appserver\Core\Provisioning;

use AppserverIo\Appserver\Core\Api\Node\StepNode;
use AppserverIo\Appserver\Core\Api\Node\DatasourceNode;
use AppserverIo\Appserver\Core\Api\ServiceInterface;
use AppserverIo\Appserver\Core\InitialContext;

/**
 * Abstract base class for a step implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2014 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
abstract class AbstractStep extends \Thread implements Step
{

    /**
     * The provisioning service.
     *
     * @var \AppserverIo\Appserver\Core\Api\ServiceInterface;
     */
    protected $service;

    /**
     * The step node with the configuration data for this step.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\StepNode
     */
    protected $stepNode;

    /**
     * The datasource node found in the provisioning configuration.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\DatasourceNode
     */
    protected $datasourceNode;

    /**
     * The initial context.
     *
     * @var \AppserverIo\Appserver\Core\Interfaces\ContextInterface
     */
    protected $initialContext;

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
     * @param \AppserverIo\Appserver\Core\Api\ServiceInterface $service The provisioning service
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
     * @param \AppserverIo\Appserver\Core\Api\Node\StepNode $stepNode The step node data
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
     * @param \AppserverIo\Appserver\Core\Api\Node\DatasourceNode $datasourceNode The datasource node data
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
     * Injects the initial context.
     *
     * @param \AppserverIo\Appserver\Core\Interfaces\ContextInterface $initialContext The initial context instance
     *
     * @return void
     */
    public function injectInitialContext(InitialContext $initialContext)
    {
        $this->initialContext = $initialContext;
    }

    /**
     * Returns the provisioning service.
     *
     * @return \AppserverIo\Appserver\Core\Api\ServiceInterface The provisioning service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Returns the step node data.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\StepNode The step node data
     */
    public function getStepNode()
    {
        return $this->stepNode;
    }

    /**
     * Returns the datasource node found in the provisioning configuration.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DatasourceNode The datasource node data
     */
    public function getDatasourceNode()
    {
        return $this->datasourceNode;
    }

    /**
     * Returns the initial context instance.
     *
     * @return \AppserverIo\Appserver\Core\Interfaces\ContextInterface The initial context
     */
    public function getInitialContext()
    {
        return $this->initialContext;
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
