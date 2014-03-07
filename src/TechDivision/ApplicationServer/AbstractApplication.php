<?php
/**
 * TechDivision\ApplicationServer\AbstractApplication
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

use TechDivision\ApplicationServer\InitialContext;
use TechDivision\ApplicationServer\Interfaces\ApplicationInterface;
use TechDivision\ApplicationServer\Api\ServiceInterface;
use JMS\Serializer\Context;
use TechDivision\ApplicationServer\Api\Node\AppNode;
use TechDivision\ApplicationServer\Api\Node\ContainerNode;

/**
 * Implements abstract application functionality
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <tw@techdivision.com>
 * @author    Johann Zelger <jz@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
abstract class AbstractApplication implements ApplicationInterface
{

    /**
     * The app node the application is belonging to.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\AppNode
     */
    protected $appNode;

    /**
     * The app node the application is belonging to.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\ContainerNode
     */
    protected $containerNode;

    /**
     * The unique application name.
     *
     * @var string
     */
    protected $name;

    /**
     * Array with available VHost configurations.
     *
     * @var array
     */
    protected $vhosts = array();

    /**
     * The datasources the app might use.
     *
     * @var array
     */
    protected $datasources;

    /**
     * The host configuration.
     *
     * @var \TechDivision\ApplicationServer\Configuration
     */
    protected $configuration;

    /**
     * The initial context instance.
     *
     * @var \TechDivision\ApplicationServer\InitialContext
     */
    protected $initialContext;

    /**
     * Passes the application name That has to be the class namespace.
     *
     * @param InitialContext $initialContext The initial context instance
     * @param ContainerNode  $containerNode  The container node the deployment is for
     * @param string         $name           The application name
     * @param array          $datasources    The datasources the app might use
     *
     * @return void
     */
    public function __construct($initialContext, $containerNode, $name, array $datasources = array())
    {
        $this->initialContext = $initialContext;
        $this->containerNode = $containerNode;
        $this->datasources = $datasources;
        $this->name = $name;
    }

    /**
     * (non-PHPdoc)
     *
     * @return AbstractApplication
     * @see ApplicationInterface::connect()
     */
    public function connect()
    {

        // load the containers vhosts
        $vhosts = $this->getContainerNode()->getHost()->getVhosts();

        // prepare the VHost configurations
        foreach ($vhosts as $vhost) {

            // check if vhost configuration belongs to application
            if ($this->getName() == ltrim($vhost->getAppBase(), '/')) {

                // prepare the aliases if available
                $aliases = array();
                foreach ($vhost->getAliases() as $alias) {
                    $aliases[] = $alias->getNodeValue();
                }

                // initialize VHost classname and parameters
                $vhostClassname = '\TechDivision\ApplicationServer\Vhost';
                $vhostParameter = array(
                    $vhost->getName(),
                    $vhost->getAppBase(),
                    $aliases
                );

                // register VHost in array with app base folder
                $this->vhosts[] = $this->newInstance($vhostClassname, $vhostParameter);
            }
        }

        // return the instance itself
        return $this;
    }

    /**
     * Set's the app node the application is belonging to
     *
     * @param AppNode $appNode The app node the application is belonging to
     *
     * @return void
     */
    public function setAppNode($appNode)
    {
        $this->appNode = $appNode;
    }

    /**
     * Return's the app node the application is belonging to.
     *
     * @return AppNode The app node the application is belonging to
     */
    public function getAppNode()
    {
        return $this->appNode;
    }

    /**
     * Set's the app node the application is belonging to
     *
     * @param ContainerNode $containerNode The container node the application is belonging to
     *
     * @return void
     */
    public function setContainerNode($containerNode)
    {
        $this->containerNode = $containerNode;
    }

    /**
     * Return's the app node the application is belonging to.
     *
     * @return ContainerNode The app node the application is belonging to
     */
    public function getContainerNode()
    {
        return $this->containerNode;
    }

    /**
     * Returns the application name (that has to be the class namespace, e.g. TechDivision\Example)
     *
     * @return string The application name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * (non-PHPdoc)
     *
     * @param string $directoryToAppend The directory to append to the base directory
     *
     * @return string The base directory with appended dir if given
     * @see ContainerService::getBaseDirectory()
     */
    public function getBaseDirectory($directoryToAppend = null)
    {
        return $this
            ->newService('TechDivision\ApplicationServer\Api\ContainerService')
            ->getBaseDirectory($directoryToAppend);
    }

    /**
     * (non-PHPdoc)
     *
     * @return string The path to the webapps folder
     * @see ApplicationService::getWebappPath()
     */
    public function getWebappPath()
    {
        return $this->getBaseDirectory($this->getAppBase() . DIRECTORY_SEPARATOR . $this->getName());
    }

    /**
     * (non-PHPdoc)
     *
     * @return string The app base
     * @see ContainerService::getAppBase()
     */
    public function getAppBase()
    {
        return $this->getContainerNode()->getHost()->getAppBase();
    }

    /**
     * (non-PHPdoc)
     *
     * @return string The servers software definition
     * @seeApplicationService::getServerSoftware()
     */
    public function getServerSoftware()
    {
        return $this->getContainerNode()->getHost()->getServerSoftware();
    }

    /**
     * (non-PHPdoc)
     *
     * @return string The servers admin definition
     * @see ApplicationService::getServerAdmin()
     */
    public function getServerAdmin()
    {
        return $this->getContainerNode()->getHost()->getServerAdmin();
    }

    /**
     * (non-PHPdoc)
     *
     * @param string $className The fully qualified class name to return the instance for
     * @param array  $args      Arguments to pass to the constructor of the instance
     *
     * @return object The instance itself
     * @see InitialContext::newInstance()
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
     * @return ServiceInterface The service instance
     * @see InitialContext::newService()
     */
    public function newService($className)
    {
        return $this->getInitialContext()->newService($className);
    }

    /**
     * Returns the initial context instance.
     *
     * @return InitialContext The initial Context
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * Return's the applications available VHost configurations.
     *
     * @return array The available VHost configurations
     */
    public function getVhosts()
    {
        return $this->vhosts;
    }

    /**
     * Sets the application's usable datasources.
     *
     * @param array $datasources The available datasources
     *
     * @return void
     */
    public function setDatasources($datasources)
    {
        $this->datasources = $datasources;
    }

    /**
     * Returns the application's usable datasources.
     *
     * @return array The available datasources
     */
    public function getDatasources()
    {
        return $this->datasources;
    }

    /**
     * Checks if the application is the VHost for the passed server name.
     *
     * @param string $serverName The server name to check the application being a VHost of
     *
     * @return boolean TRUE if the application is the VHost, else FALSE
     */
    public function isVhostOf($serverName)
    {

        // check if the application is VHost for the passed server name
        foreach ($this->getVhosts() as $vhost) {

            // compare the VHost name itself
            if (strcmp($vhost->getName(), $serverName) === 0) {
                return true;
            }

            // then compare all aliases
            if (in_array($serverName, $vhost->getAliases())) {
                return true;
            }
        }

        return false;
    }

    /**
     * (non-PHPdoc)
     *
     * @return AppNode The node representation of the application
     * @see ApplicationInterface::newAppNode()
     */
    public function newAppNode()
    {
        // create a new AppNode and initialize it with the values from this instance
        $appNode = $this->newInstance('TechDivision\ApplicationServer\Api\Node\AppNode');
        $appNode->setNodeName('application');
        $appNode->setName($this->getName());
        $appNode->setWebappPath($this->getWebappPath());
        $appNode->setDatasources($this->getDatasources());
        $appNode->setParentUuid($this->getContainerNode()->getParentUuid());
        $appNode->setUuid($appNode->newUuid());
        // set the AppNode in the instance itself and return it
        $this->setAppNode($appNode);

        return $appNode;
    }
}
