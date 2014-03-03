<?php
/**
 * TechDivision\ApplicationServer\ContainerThread
 *
 * PHP version 5
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <tw@techdivision.com>
 * @author    Johann Zelger <jz@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace TechDivision\ApplicationServer;

/**
 * Class ContainerThread
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <tw@techdivision.com>
 * @author    Johann Zelger <jz@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
class ContainerThread extends AbstractContextThread
{

    /**
     * The container's to be deployed.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\ContainerNode
     */
    protected $containerNode;

    /**
     * Set's the unique container name to be started by this thread.
     *
     * @param string $containerNode The container node
     *
     * @return void
     */
    public function init($containerNode)
    {
        $this->containerNode = $containerNode;
    }

    /**
     * (non-PHPdoc)
     *
     * @return void
     * @see \TechDivision\ApplicationServer\AbstractContextThread::main()
     */
    public function main()
    {
        // deploy the applications and return them as array
        $applications = $this->getDeployment()
            ->deploy()
            ->getApplications();
        // synchronize container threads to avoid registring apps several times
        $this->synchronized(function () {
            $this->notify();
        });
        // load the container node
        $containerNode = $this->getContainerNode();
        // create the container instance
        $containerInstance = $this->newInstance($containerNode->getType(), array(
            $this->getInitialContext(),
            $containerNode,
            $applications
        ));
        // finally start the container instance
        $containerInstance->run();
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
        error_log($className);
        return $this->getInitialContext()->newService($className);
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
     * Return's the container node.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\ContainerNode The container node
     */
    public function getContainerNode()
    {
        return $this->containerNode;
    }

    /**
     * Returns the deployment interface for the container for
     * this container thread.
     *
     * @return \TechDivision\ApplicationServer\Interfaces\DeploymentInterface The deployment instance for this container thread
     */
    public function getDeployment()
    {
        $deploymentNode = $this->getContainerNode()->getDeployment();
        return $this->newInstance($deploymentNode->getType(), array(
            $this->getInitialContext(),
            $this->getContainerNode(),
            $deploymentNode
        ));
    }
}
