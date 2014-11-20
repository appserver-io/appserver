<?php
/**
 * AppserverIo\Appserver\Core\Deployment
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core;

use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Core\InitialContext;
use AppserverIo\Appserver\Core\Interfaces\DeploymentInterface;

/**
 * Class AbstractDeployment
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
abstract class AbstractDeployment implements DeploymentInterface
{

    /**
     * The initial context instance.
     *
     * @var \AppserverIo\Appserver\Core\InitialContext
     */
    protected $initialContext;

    /**
     * The deployment service instance.
     *
     * @var \AppserverIo\Appserver\Core\Api\DeploymentService
     */
    protected $deploymentService;

    /**
     * Initializes the deployment with the container thread.
     *
     * @param \AppserverIo\Appserver\Core\InitialContext $initialContext The initial context instance
     */
    public function __construct(InitialContext $initialContext)
    {

        // set the initial context instance
        $this->initialContext = $initialContext;

        // create a deployment service instance
        $this->deploymentService = $this->newService('AppserverIo\Appserver\Core\Api\DeploymentService');
    }

    /**
     * Returns the initialContext instance
     *
     * @return \AppserverIo\Appserver\Core\InitialContext The initial context instance
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * Returns the deployment service instance.
     *
     * @return \AppserverIo\Appserver\Core\Api\DeploymentService The deployment service instance
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
     * @see \AppserverIo\Appserver\Core\InitialContext::newInstance()
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
     * @return \AppserverIo\Appserver\Core\Api\ServiceInterface The service instance
     */
    public function newService($className)
    {
        return $this->getInitialContext()->newService($className);
    }
}
