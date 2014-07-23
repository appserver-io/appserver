<?php
/**
 * TechDivision\ApplicationServer\Deployment
 *
 * PHP version 5
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace TechDivision\ApplicationServer;

use TechDivision\Application\Interfaces\ApplicationInterface;
use TechDivision\ApplicationServer\InitialContext;
use TechDivision\ApplicationServer\Interfaces\DeploymentInterface;

/**
 * Class AbstractDeployment
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
abstract class AbstractDeployment implements DeploymentInterface
{

    /**
     * The initial context instance.
     *
     * @var \TechDivision\ApplicationServer\InitialContext
     */
    protected $initialContext;

    /**
     * The deployment service instance.
     *
     * @var \TechDivision\ApplicationServer\Api\DeploymentService
     */
    protected $deploymentService;

    /**
     * Initializes the deployment with the container thread.
     *
     * @param \TechDivision\ApplicationServer\InitialContext $initialContext The initial context instance
     */
    public function __construct(InitialContext $initialContext)
    {

        // set the initial context instance
        $this->initialContext = $initialContext;

        // create a deployment service instance
        $this->deploymentService = $this->newService('TechDivision\ApplicationServer\Api\DeploymentService');
    }

    /**
     * Returns the initialContext instance
     *
     * @return \TechDivision\ApplicationServer\InitialContext The initial context instance
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * Returns the deployment service instance.
     *
     * @return \TechDivision\ApplicationServer\Api\DeploymentService The deployment service instance
     */
    public function getDeploymentService()
    {
        return $this->deploymentService;
    }

    /**
     * (non-PHPdoc)
     *
     * @param string $className The fully qualified class name to return the instance for
     * @param array  $args      Arguments to pass to the constructor of the instance
     *
     * @return object The instance itself
     * @see \TechDivision\ApplicationServer\InitialContext::newInstance()
     */
    public function newInstance($className, array $args = array())
    {
        return $this->getInitialContext()->newInstance($className, $args);
    }

    /**
     * Returns a new instance of the passed API service.
     *
     * @param string $className The API service class name to return the instance for
     *
     * @return \TechDivision\ApplicationServer\Api\ServiceInterface The service instance
     */
    public function newService($className)
    {
        return $this->getInitialContext()->newService($className);
    }
}
