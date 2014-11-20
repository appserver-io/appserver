<?php
/**
 * AppserverIo\Appserver\Core\Api\AppService
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

use TechDivision\Configuration\Interfaces\NodeInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Core\Api\Node\AppNode;
use AppserverIo\Appserver\Core\Interfaces\ExtractorInterface;
use AppserverIo\Appserver\Core\Extractors\PharExtractor;

/**
 * This services provides access to the deplyed applications and allows
 * to deploy new applications or remove a deployed one.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class AppService extends AbstractService
{

    /**
     * The unique XML configuration node name for a app node.
     *
     * @var string
     */
    const NODE_NAME = 'application';

    /**
     * Creates a new app node for the passed application and attaches
     * it to the system configuration.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application to create a new AppNode for
     *
     * @return void
     */
    public function newFromApplication(ApplicationInterface $application)
    {

        // load the system configuration
        $systemConfiguration = $this->getInitialContext()->getSystemConfiguration();

        // create a new AppNode and initialize it with the values from this instance
        $appNode = new AppNode();
        $appNode->setNodeName(AppService::NODE_NAME);
        $appNode->setName($application->getName());
        $appNode->setWebappPath($application->getWebappPath());
        $appNode->setParentUuid($systemConfiguration->getUuid());
        $appNode->setUuid($appNode->newUuid());

        // persist the AppNode instance
        $this->persist($appNode);
    }

    /**
     * Returns all deployed applications.
     *
     * @return array All deployed applications
     * @see ServiceInterface::findAll()
     */
    public function findAll()
    {
        $appNodes = array();
        foreach ($this->getSystemConfiguration()->getApps() as $appNode) {
            $appNodes[$appNode->getPrimaryKey()] = $appNode;
        }
        return $appNodes;
    }

    /**
     * Returns the applications with the passed name.
     *
     * @param string $name Name of the application to return
     *
     * @return array The applications with the name passed as parameter
     */
    public function findAllByName($name)
    {
        $appNodes = array();
        foreach ($this->findAll() as $appNode) {
            if ($appNode->getName() == $name) {
                $appNodes[$appNode->getPrimaryKey()] = $appNode;
            }
        }
        return $appNodes;
    }

    /**
     * Returns the application with the passed UUID.
     *
     * @param string $uuid UUID of the application to return
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\AppNode|null The application with the UUID passed as parameter
     * @see ServiceInterface::load()
     */
    public function load($uuid)
    {
        foreach ($this->findAll() as $appNode) {
            if ($appNode->getPrimaryKey() == $uuid) {
                return $appNode;
            }
        }
    }

    /**
     * Returns the application with the passed webapp path.
     *
     * @param string $webappPath webapp path of the application to return
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\AppNode|null The application with the webapp path
     *                                                               passed as parameter
     */
    public function loadByWebappPath($webappPath)
    {
        foreach ($this->findAll() as $appNode) {
            if ($appNode->getWebappPath() == $webappPath) {
                return $appNode;
            }
        }
    }

    /**
     * Persists the system configuration.
     *
     * @param \TechDivision\Configuration\Interfaces\NodeInterface $appNode The application node object
     *
     * @return void
     */
    public function persist(NodeInterface $appNode)
    {
        $systemConfiguration = $this->getSystemConfiguration();
        $systemConfiguration->attachApp($appNode);
        $this->setSystemConfiguration($systemConfiguration);
    }

    /**
     * Soaks the passed archive into from a location in the filesystem
     * to the deploy directory.
     *
     * @param \SplFileInfo $archive The archive to soak
     *
     * @return void
     */
    public function soak(\SplFileInfo $archive)
    {
        $p = new PharExtractor($this->getInitialContext());
        $p->soakArchive($archive);
    }

    /**
     * Adds the .dodeploy flag file in the deploy folder, therefore the
     * app will be deployed with the next restart.
     *
     * @param \TechDivision\Configuration\Interfaces\NodeInterface $appNode The application node object
     *
     * @return void
     */
    public function deploy(NodeInterface $appNode)
    {
        // prepare file name
        $fileName = $appNode->getName() . PharExtractor::EXTENSION_SUFFIX;

        // load the file info
        $archive = new \SplFileInfo($this->getDeployDir() . DIRECTORY_SEPARATOR . $fileName);

        // flag the archiv => deploy it with the next restart
        $extractor = new PharExtractor($this->getInitialContext());
        $extractor->flagArchive($archive, ExtractorInterface::FLAG_DODEPLOY);
    }

    /**
     * Removes the .deployed flag file from the deploy folder, therefore the
     * app will be undeployed with the next restart.
     *
     * @param string $uuid UUID of the application to delete
     *
     * @return void
     * @todo Add functionality to delete the deployed app
     */
    public function undeploy($uuid)
    {

        // try to load the app node with the passe UUID
        if ($appNode = $this->load($uuid)) {

            // prepare file name
            $extractor = new PharExtractor($this->getInitialContext());
            $fileName = $appNode->getName() . $extractor->getExtensionSuffix();

            // load the file info
            $archive = new \SplFileInfo($this->getDeployDir() . DIRECTORY_SEPARATOR . $fileName);

            // unflag the archiv => undeploy it with the next restart
            $extractor = new PharExtractor($this->getInitialContext());
            $extractor->unflagArchive($archive);
        }
    }

    /**
     * Returns an new app node instance.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application to create a new app node instance from
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\AppNode The app node representation of the application
     */
    protected function create(ApplicationInterface $application)
    {

        // create a new AppNode and initialize it with the values from this instance
        $appNode = new AppNode();
        $appNode->setNodeName(AppService::NODE_NAME);
        $appNode->setUuid($appNode->newUuid());
        $appNode->setName($application->getName());
        $appNode->setWebappPath($application->getWebappPath());
        $appNode->setDatasources($application->getDatasources());

        // return the AppNode instance
        return $appNode;
    }
}
