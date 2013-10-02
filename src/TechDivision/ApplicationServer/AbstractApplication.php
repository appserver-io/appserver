<?php

/**
 * TechDivision\ApplicationServer\AbstractContainer
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\InitialContext;
use TechDivision\ApplicationServer\Interfaces\ApplicationInterface;
use JMS\Serializer\Context;
use TechDivision\ApplicationServer\Api\Node\AppNode;
use TechDivision\ApplicationServer\Api\Node\ContainerNode;

/**
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 * @author Johann Zelger <jz@techdivision.com>
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
     * @param TechDivision\ApplicationServer\InitialContext $initialContext
     *            The initial context instance
     * @param string $name
     *            The application name
     * @return void
     */
    public function __construct($initialContext, $containerNode, $name)
    {
        $this->initialContext = $initialContext;
        $this->containerNode = $containerNode;
        $this->name = $name;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\Interfaces\ApplicationInterface::connect()
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
     * @param \TechDivision\ApplicationServer\Api\Node\AppNode $appNode
     *            The app node the application is belonging to
     * @return void
     */
    public function setAppNode($appNode)
    {
        $this->appNode = $appNode;
    }

    /**
     * Return'sthe app node the application is belonging to.
     *
     * @return string The app node the application is belonging to
     */
    public function getAppNode()
    {
        return $this->appNode;
    }

    /**
     * Set's the app node the application is belonging to
     *
     * @param \TechDivision\ApplicationServer\Api\Node\AppNode $appNode
     *            The app node the application is belonging to
     * @return void
     */
    public function setContainerNode($containerNode)
    {
        $this->containerNode = $containerNode;
    }

    /**
     * Return'sthe app node the application is belonging to.
     *
     * @return string The app node the application is belonging to
     */
    public function getContainerNode()
    {
        return $this->containerNode;
    }

    /**
     * Returns the application name (that has to be the class namespace,
     * e.
     * g. TechDivision\Example).
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
     * @see \TechDivision\ApplicationServer\Api\ContainerService::getBaseDirectory()
     */
    public function getBaseDirectory($directoryToAppend = null)
    {
        return $this->newService('TechDivision\ApplicationServer\Api\ContainerService')->getBaseDirectory($directoryToAppend);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\Api\ApplicationService::getWebappPath()
     */
    public function getWebappPath()
    {
        return $this->getBaseDirectory($this->getAppBase() . DIRECTORY_SEPARATOR . $this->getName());
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\Api\ContainerService::getAppBase()
     */
    public function getAppBase()
    {
        return $this->getContainerNode()->getHost()->getAppBase();
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\Interfaces\ApplicationInterface::getDocumentRoot()
     */
    public function getDocumentRoot()
    {
        return $this->getBaseDirectory($this->getAppBase());
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\Api\ApplicationService::getServerSoftware()
     */
    public function getServerSoftware()
    {
        return $this->getContainerNode()->getHost()->getServerSoftware();
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\Api\ApplicationService::getServerAdmin()
     */
    public function getServerAdmin()
    {
        return $this->getContainerNode()->getHost()->getServerAdmin();
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\InitialContext::newInstance()
     */
    public function newInstance($className, array $args = array())
    {
        return $this->getInitialContext()->newInstance($className, $args);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\InitialContext::newService()
     */
    public function newService($className)
    {
        return $this->getInitialContext()->newService($className);
    }

    /**
     * Returns the initial context instance.
     *
     * @return \TechDivision\ApplicationServer\InitialContext The initial Context
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
     * Checks if the application is the VHost for the passed server name.
     *
     * @param string $serverName
     *            The server name to check the application being a VHost of
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
     * @see \TechDivision\ApplicationServer\Interfaces\ApplicationInterface::newAppNode()
     */
    public function newAppNode()
    {

        $appNode = $this->newInstance('TechDivision\ApplicationServer\Api\Node\AppNode');
        $appNode->setNodeName('application');
        $appNode->setName($this->getName());
        $appNode->setWebappPath($this->getWebappPath());
        $appNode->setParentUuid($this->getContainerNode()->getParentUuid());
        $appNode->setUuid($appNode->newUuid());

        $this->setAppNode($appNode);

        return $appNode;
    }
}