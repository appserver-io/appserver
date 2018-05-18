<?php

/**
 * \AppserverIo\Appserver\Provisioning\AbstractProvisioner
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

namespace AppserverIo\Appserver\Provisioning;

use AppserverIo\Psr\Application\ProvisionerInterface;
use AppserverIo\Psr\ApplicationServer\ContextInterface;
use AppserverIo\Appserver\Core\Api\Node\ProvisionerNodeInterface;

/**
 * Abstract base class that provides basic provisioning functionality.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
abstract class AbstractProvisioner implements ProvisionerInterface
{

    /**
     * The initial context instance.
     *
     * @var \AppserverIo\Psr\ApplicationServer\ContextInterface
     */
    protected $initialContext;

    /**
     * The provisioning service instance.
     *
     * @var \AppserverIo\Psr\ApplicationServer\ServiceInterface
     */
    protected $service;

    /**
     * The provisioner node configuration data.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ProvisionerNodeInterface
     */
    protected $provisionerNode;

    /**
     * Contructor to initialize the provisioner instance with the initial context
     * and the provision node configuration data.
     *
     * @param \AppserverIo\Psr\ApplicationServer\ContextInterface           $initialContext  The initial context instance
     * @param \AppserverIo\Appserver\Core\Api\Node\ProvisionerNodeInterface $provisionerNode The provisioner node configuration data
     */
    public function __construct(ContextInterface $initialContext, ProvisionerNodeInterface $provisionerNode)
    {

        // add initial context and provisioner node configuration data
        $this->initialContext = $initialContext;
        $this->provisionerNode = $provisionerNode;
        // init API service to use
        $this->service = $this->newService('AppserverIo\Appserver\Core\Api\ProvisioningService');
    }

    /**
     * (non-PHPdoc)
     *
     * @param string $className The API service class name to return the instance for
     *
     * @return \AppserverIo\Psr\ApplicationServer\ServiceInterface The service instance
     * @see \AppserverIo\Appserver\Core\InitialContext::newService()
     */
    public function newService($className)
    {
        return $this->getInitialContext()->newService($className);
    }

    /**
     * Returns the inital context instance.
     *
     * @return \AppserverIo\Psr\ApplicationServer\ContextInterface The initial context instance
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * Returns the service instance to use.
     *
     * @return \AppserverIo\Appserver\Core\Api\ProvisioningService $service The service to use
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Returns the provisioner node configuration data.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\ProvisionerNodeInterface The provisioner node configuration data
     */
    public function getProvisionerNode()
    {
        return $this->provisionerNode;
    }
}
