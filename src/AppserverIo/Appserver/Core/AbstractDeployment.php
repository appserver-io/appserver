<?php

/**
 * AppserverIo\Appserver\Core\Deployment
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

namespace AppserverIo\Appserver\Core;

use AppserverIo\Appserver\Core\InitialContext;
use AppserverIo\Psr\Deployment\DeploymentInterface;
use AppserverIo\Appserver\Core\Interfaces\ContainerInterface;
use AppserverIo\Appserver\Application\Interfaces\ContextInterface;

/**
 * Abstract deployment implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
abstract class AbstractDeployment implements DeploymentInterface
{

    /**
     * The container instance.
     *
     * @var \AppserverIo\Appserver\Core\Interfaces\ContainerInterface
     */
    protected $container;

    /**
     * The deployment service instance.
     *
     * @var \AppserverIo\Appserver\Core\Api\DeploymentService
     */
    protected $deploymentService;

    /**
     * Injects the container instance.
     *
     * @param \AppserverIo\Appserver\Core\Interfaces\ContainerInterface $container The initial context instance
     *
     * @return void
     */
    public function injectContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns the container instance
     *
     * @return \AppserverIo\Appserver\Core\Interfaces\ContainerInterface The container instance
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Returns the deployment service instance.
     *
     * @return \AppserverIo\Appserver\Core\Api\DeploymentService The deployment service instance
     */
    public function getDeploymentService()
    {
        if ($this->deploymentService == null) {
            $this->deploymentService = $this->newService('AppserverIo\Appserver\Core\Api\DeploymentService');
        }
        return $this->deploymentService;
    }

    /**
     * (non-PHPdoc)
     *
     * @param string $className The fully qualified class name to return the instance for
     * @param array  $args      Arguments to pass to the constructor of the instance
     *
     * @return object The instance itself
     * @see \AppserverIo\Appserver\Core\InitialContext::newInstance()
     */
    public function newInstance($className, array $args = array())
    {
        return $this->getContainer()->getInitialContext()->newInstance($className, $args);
    }

    /**
     * Returns a new instance of the passed API service.
     *
     * @param string $className The API service class name to return the instance for
     *
     * @return \AppserverIo\Appserver\Core\Api\ServiceInterface The service instance
     */
    public function newService($className)
    {
        return $this->getContainer()->getInitialContext()->newService($className);
    }
}
