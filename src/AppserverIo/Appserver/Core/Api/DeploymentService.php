<?php
/**
 * AppserverIo\Appserver\Core\Api\DeploymentService
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

namespace AppserverIo\Appserver\Core\Api;

use AppserverIo\Appserver\Core\Api\AbstractService;
use AppserverIo\Appserver\Core\Api\Node\ContextNode;
use AppserverIo\Appserver\Core\Api\Node\DeploymentNode;
use AppserverIo\Appserver\Core\Api\ServiceInterface;
use AppserverIo\Appserver\Core\Interfaces\ContainerInterface;
use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * A service that handles deployment configuration data.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class DeploymentService extends AbstractService
{

    /**
     * Return's all deployment configurations.
     *
     * @return array An array with all deployment configurations
     * @see \AppserverIo\Appserver\Core\Api\ServiceInterface::findAll()
     */
    public function findAll()
    {
        $deploymentNodes = array();
        foreach ($this->getSystemConfiguration()->getContainers() as $container) {
            $deploymentNode = $container->getDeployment();
            $deploymentNodes[$deploymentNode->getUuid()] = $deploymentNode;
        }
        return $deploymentNodes;
    }

    /**
     * Returns the deployment with the passed UUID.
     *
     * @param integer $uuid UUID of the deployment to return
     *
     * @return DeploymentNode The deployment with the UUID passed as parameter
     * @see ServiceInterface::load()
     */
    public function load($uuid)
    {
        $deploymentNodes = $this->findAll();
        if (array_key_exists($uuid, $deploymentNodes)) {
            return $deploymentNodes[$uuid];
        }
    }

    /**
     * Creates the temporary directory for the webapp.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application to create the temporary directories for
     *
     * @return void
     */
    public function createTmpFolders(ApplicationInterface $application)
    {

        // create the directory we want to store the sessions in
        $tmpFolders = array(
            new \SplFileInfo($application->getTmpDir()),
            new \SplFileInfo($application->getCacheDir()),
            new \SplFileInfo($application->getSessionDir())
        );

        // create the applications temporary directories
        foreach ($tmpFolders as $tmpFolder) {
            $this->createDirectory($tmpFolder);
        }
    }

    /**
     * Clean up the the directories for the webapp, e. g. to delete cached stuff
     * that has to be recreated after a restart.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application to clean up the directories for
     *
     * @return void
     */
    public function cleanUpFolders($application)
    {

        // create the directory we want to store the sessions in
        $cleanUpFolders = array(new \SplFileInfo($application->getCacheDir()));

        // create the applications temporary directories
        foreach ($cleanUpFolders as $cleanUpFolder) {
            $this->cleanUpDir($cleanUpFolder);
        }
    }

    /**
     * Initializes the available application contexts and returns them.
     *
     * @param \AppserverIo\Appserver\Core\Interfaces\ContainerInterface $container The container we want to add the applications to
     *
     * @return array The array with the application contexts
     */
    public function loadContextInstancesByContainer(ContainerInterface $container)
    {

        // initialize the array for the context instances
        $contextInstances = array();

        // iterate over all applications and create the context configuration
        foreach (glob($container->getAppBase() . '/*', GLOB_ONLYDIR) as $webappPath) {

            // prepare the context path
            $contextPath = '/' . basename($webappPath);

            // load the default context configuration
            $context = new ContextNode();
            $context->initFromFile($this->getConfdDir('context.xml'));

            // try to load a context configuration (from appserver.xml) for the context path
            if ($contextToMerge = $container->getContainerNode()->getHost()->getContext($contextPath)) {
                $context->merge($contextToMerge);
            }

            // iterate through all context configurations (context.xml) and merge them
            foreach ($this->globDir($webappPath . '/META-INF/context.xml') as $contextFile) {

                // create a new context node instance
                $contextInstance = new ContextNode();
                $contextInstance->initFromFile($contextFile);

                // merge it into the default configuration
                $context->merge($contextInstance);
            }

            // attach the context to the context instance
            $contextInstances[$contextPath] = $context;
        }

        // return the array with the context instances
        return $contextInstances;
    }
}
