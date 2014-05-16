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

use TechDivision\ApplicationServer\InitialContext;
use TechDivision\ApplicationServer\Interfaces\DeploymentInterface;
use TechDivision\ApplicationServer\Interfaces\ApplicationInterface;

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
     * The container node the deployment is for.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\ContainerNode
     */
    protected $containerNode;

    /**
     * Array with the initialized applications.
     *
     * @var array
     */
    protected $applications = array();
    
    /**
     * The initial context instance.
     * 
     * @var \TechDivision\ApplicationServer\InitialContext
     */
    protected $initialContext;

    /**
     * Initializes the deployment with the container thread.
     *
     * @param \TechDivision\ApplicationServer\InitialContext         $initialContext The initial context instance
     * @param \TechDivision\ApplicationServer\Api\Node\ContainerNode $containerNode  The container node the deployment is for
     */
    public function __construct(InitialContext $initialContext, $containerNode)
    {
        $this->initialContext = $initialContext;
        $this->containerNode = $containerNode;
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
     * Returns the container node the deployment is for.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\ContainerNode The container node
     */
    public function getContainerNode()
    {
        return $this->containerNode;
    }
    
    /**
     * Connects the passed application to the system configuration.
     * 
     * @param \TechDivision\ApplicationServer\Interfaces\ApplicationInterface $application The application to be prepared
     * 
     * @return void
     */
    protected function addApplicationToSystemConfiguration(ApplicationInterface $application)
    {

        // create a new API app service instance
        $appService = $this->newService('TechDivision\ApplicationServer\Api\AppService');
        $appNode = $appService->loadByWebappPath($application->getWebappPath());
        
        // check if the application has already been attached to the container
        if ($appNode == null) {
            $application->newAppNode($this->getContainerNode());
        } else {
            $application->setAppNode($appNode);
        }
        
        // persist the application
        $appService->persist($application->getAppNode());
        
        // connect the application to the container
        $application->connect();
    }

    /**
     * Append the deployed application to the deployment instance
     * and registers it in the system configuration.
     *
     * @param ApplicationInterface $application The application to append
     *
     * @return void
     */
    public function addApplication(ApplicationInterface $application)
    {

        // adds the application to the system configuration
        $this->addApplicationToSystemConfiguration($application);
        
        // register the application in this instance
        $this->applications[$application->getName()] = $application;
        
        // log a message that the app has been started
        $this->getInitialContext()->getSystemLogger()->debug(
            sprintf(
                'Successfully started app %s in container %s',
                $application->getName(),
                $application->getWebappPath(),
                $application->getContainerNode()->getName()
            )
        );
    }

    /**
     * Return's the deployed applications.
     *
     * @return array The deployed applications
     */
    public function getApplications()
    {
        return $this->applications;
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
     * (non-PHPdoc)
     *
     * @param string|null $directoryToAppend Append this directory to the base directory before returning it
     *
     * @return string The base directory
     * @see \TechDivision\ApplicationServer\Api\ContainerService::getBaseDirectory()
     */
    public function getBaseDirectory($directoryToAppend = null)
    {
        return $this->newService('TechDivision\ApplicationServer\Api\ContainerService')
            ->getBaseDirectory($directoryToAppend);
    }

    /**
     * (non-PHPdoc)
     *
     * @return string The application base directory for this container
     * @see \TechDivision\ApplicationServer\Api\ContainerService::getAppBase()
     */
    public function getAppBase()
    {
        return $this->getContainerNode()->getHost()->getAppBase();
    }
}
