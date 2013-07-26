<?php

/**
 * TechDivision\ApplicationServer\Deployment
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\InitialContext;
use TechDivision\ApplicationServer\Interfaces\DeploymentInterface;

/**
 * @package     TechDivision\MessageQueue
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
abstract class AbstractDeployment implements DeploymentInterface {

    /**
     * Path to the container's base directory.
     * @var string
     */
    const CONTAINER_BASE_DIRECTORY = '/container/baseDirectory';

    /**
     * Path to the container's host configuration.
     * @var string
     */
    const CONTAINER_HOST = '/container/host';

    /**
     * The container thread
     * @var \TechDivision\ApplicationServer\ContainerThread
     */
    protected $containerThread;

    /**
     * Array with the initialized applications.
     * @var array
     */
    protected $applications;

    /**
     * Initializes the deployment with the container thread.
     *
     * @param \TechDivision\ApplicationServer\ContainerThread $containerThread
     */
    public function __construct($containerThread) {
        $this->containerThread = $containerThread;
    }

    /**
     * Returns the container thread.
     *
     * @return \TechDivision\ApplicationServer\ContainerThread The container thread
     */
    public function getContainerThread() {
        return $this->containerThread;
    }

    /**
     * Returns the deployed applications.
     *
     * @return array The deployed applications
     */
    public function getApplications() {
        return $this->applications;
    }

    /**
     * Creates a new instance of the passed class name and passes the
     * args to the instance constructor.
     *
     * @param string $className The class name to create the instance of
     * @param array $args The parameters to pass to the constructor
     * @return object The created instance
     */
    public function newInstance($className, array $args = array()) {
        return InitialContext::get()->newInstance($className, $args);
    }
}