<?php

/**
 * TechDivision\ApplicationServer\AbstractProvisioner
 *
 * PHP version 5
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace TechDivision\ApplicationServer;

use TechDivision\Application\Interfaces\ContextInterface;
use TechDivision\ApplicationServer\Interfaces\ProvisionerInterface;
use TechDivision\ApplicationServer\Api\Node\ProvisionerNodeInterface;

/**
 * Abstract base class that provides basic provisioning functionality.
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
abstract class AbstractProvisioner implements ProvisionerInterface
{

    /**
     * The initial context instance.
     *
     * @var \TechDivision\Application\Interfaces\ContextInterface
     */
    protected $initialContext;

    /**
     * The provisioning service instance.
     *
     * @var \TechDivision\ApplicationServer\Api\ServiceInterface
     */
    protected $service;

    /**
     * The provisioner node configuration data.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\ProvisionerNodeInterface
     */
    protected $provisionerNode;

    /**
     * Contructor to initialize the provisioner instance with the initial context
     * and the provision node configuration data.
     *
     * @param \TechDivision\Application\Interfaces\ContextInterface           $initialContext  The initial context instance
     * @param \TechDivision\ApplicationServer\Api\Node\ExtractorNodeInterface $provisionerNode The provisioner node configuration data
     */
    public function __construct(ContextInterface $initialContext, ProvisionerNodeInterface $provisionerNode)
    {

        // add initial context and provisioner node configuration data
        $this->initialContext = $initialContext;
        $this->provisionerNode = $provisionerNode;
        // init API service to use
        $this->service = $this->newService('TechDivision\ApplicationServer\Api\ProvisioningService');
    }

    /**
     * Returns the servers web application directory.
     *
     * @return string The web application directory
     */
    public function getWebappsDir()
    {
        return $this->getService()->getWebappsDir();
    }

    /**
     * (non-PHPdoc)
     *
     * @param string $className The API service class name to return the instance for
     *
     * @return \TechDivision\ApplicationServer\Api\ServiceInterface The service instance
     * @see \TechDivision\ApplicationServer\InitialContext::newService()
     */
    public function newService($className)
    {
        return $this->getInitialContext()->newService($className);
    }

    /**
     * Returns the inital context instance.
     *
     * @return \TechDivision\Application\Interfaces\ContextInterface The initial context instance
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * Returns the service instance to use.
     *
     * @return \TechDivision\ApplicationServer\Api\ServiceInterface $service The service to use
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Returns the provisioner node configuration data.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\ProvisionerNodeInterface The provisioner node configuration data
     */
    public function getProvisionerNode()
    {
        return $this->provisionerNode;
    }
}
