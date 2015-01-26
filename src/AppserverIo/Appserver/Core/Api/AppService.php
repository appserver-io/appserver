<?php

/**
 * AppserverIo\Appserver\Core\Api\AppService
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

namespace AppserverIo\Appserver\Core\Api;

use AppserverIo\Configuration\Interfaces\NodeInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Core\Api\Node\AppNode;
use AppserverIo\Appserver\Core\Interfaces\ExtractorInterface;
use AppserverIo\Appserver\Core\Extractors\PharExtractor;

/**
 * This services provides access to the deployed applicatio
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class AppService extends AbstractService
{

    /**
     * The name of the default extractor we will look for in our configuration
     *
     * @var string DEFAULT_EXTRACTOR_NAME
     */
    const DEFAULT_EXTRACTOR_NAME = 'phar';

    /**
     * The extractor instance to handle archive operations with
     *
     * @var \AppserverIo\Appserver\Core\Interfaces\ExtractorInterface $extractor
     */
    protected $extractor;

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
        // create a new AppNode and initialize it
        $appNode = new AppNode();
        $appNode->initFromApplication($application);
        $appNode->setParentUuid($this->getSystemConfiguration()->getUuid());

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
     * Getter for this service's extractor
     *
     * @return \AppserverIo\Appserver\Core\Interfaces\ExtractorInterface|null
     */
    public function getExtractor()
    {
        // we will have a phar extractor by default
        if (!isset($this->extractor)) {
            $configuration = $this->getSystemConfiguration()->getExtractors();
            if (isset($configuration[self::DEFAULT_EXTRACTOR_NAME])) {
                // create a new extractor with the default configuration
                $this->extractor = new PharExtractor($this->getInitialContext(), $configuration[self::DEFAULT_EXTRACTOR_NAME]);

            } else {
                $this->getInitialContext()->getSystemLogger()->warning(sprintf(
                    'Did not find configuration for default extractor %s nor was an extractor injected.',
                    self::DEFAULT_EXTRACTOR_NAME
                ));
                $this->extractor = null;
            }
        }

        return $this->extractor;
    }

    /**
     * Will inject a certain extractor to be used
     *
     * @param \AppserverIo\Appserver\Core\Interfaces\ExtractorInterface $extractor The extractor instance to inject
     *
     * @return null
     */
    public function injectExtractor(ExtractorInterface $extractor)
    {
        $this->extractor = $extractor;
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
     * @param \AppserverIo\Configuration\Interfaces\NodeInterface $appNode The application node object
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
        $extractor = $this->getExtractor();
        $extractor->soakArchive($archive);
    }

    /**
     * Adds the .dodeploy flag file in the deploy folder, therefore the
     * app will be deployed with the next restart.
     *
     * @param \AppserverIo\Configuration\Interfaces\NodeInterface $appNode The application node object
     *
     * @return void
     */
    public function deploy(NodeInterface $appNode)
    {
        // prepare file name
        $extractor = $this->getExtractor();
        $fileName = $appNode->getName() . $extractor->getExtensionSuffix();

        // load the file info
        $archive = new \SplFileInfo($this->getDeployDir() . DIRECTORY_SEPARATOR . $fileName);

        // flag the archive => deploy it with the next restart
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
            $extractor = $this->getExtractor();
            $fileName = $appNode->getName() . $extractor->getExtensionSuffix();

            // load the file info
            $archive = new \SplFileInfo($this->getDeployDir() . DIRECTORY_SEPARATOR . $fileName);

            // un-flag the archiv => un-deploy it with the next restart
            $extractor->unflagArchive($archive);
        }
    }
}
