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
use TechDivision\ApplicationServer\Interfaces\ContainerInterface;

/**
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 * @author Johann Zelger <jz@techdivision.com>
 */
abstract class AbstractContainer extends \Stackable implements ContainerInterface
{

    /**
     * Array with deployed applications.
     *
     * @var array
     */
    protected $applications = array();

    /**
     * The server instance.
     *
     * @var \TechDivision\ApplicationServer\Server
     */
    protected $server;

    /**
     * The container node.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\ContainerNode
     */
    protected $containerNode;

    /**
     * The container's base directory.
     *
     * @var string
     */
    protected $baseDirectory;

    /**
     * TRUE if the container has been started, else FALSE.
     *
     * @var boolean
     */
    protected $started = false;

    /**
     * @var InitialContext
     */
    protected $initialContext;

    /**
     * Initializes the container with the initial context, the unique container ID
     * and the deployed applications.
     *
     * @param \TechDivision\ApplicationServer\InitialContext $initialContext
     *            The initial context instance
     * @param \TechDivision\ApplicationServer\Api\Node\ContainerNode $containerNode
     *            The container's UUID
     * @todo Application deployment only works this way because of Thread compatibilty
     * @return void
     */
    public function __construct($initialContext, $containerNode, $applications)
    {
        $this->initialContext = $initialContext;
        $this->baseDirectory = $this->newService('TechDivision\ApplicationServer\Api\ContainerService')->getBaseDirectory();

        $this->setContainerNode($containerNode);
        $this->setApplications($applications);
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
     * Return's the container node.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\ContainerNode The container node
     */
    public function getContainerNode()
    {
        return $this->containerNode;
    }

    /**
     *
     * @see \Stackable::run()
     */
    public function run()
    {
        $this->setStarted();
        $this->getReceiver()->start();
    }

    /**
     *
     * @see \TechDivision\ApplicationServer\Interfaces\ContainerInterface::getReceiver()
     */
    public function getReceiver()
    {
        return $this->newInstance($this->getReceiverType(), array(
            $this->initialContext,
            $this
        ));
    }

    /**
     * Sets an array with the deployed applications.
     *
     * @param array $applications
     *            Array with the deployed applications
     * @return \TechDivision\ServletContainer\Container The container instance itself
     */
    public function setApplications($applications)
    {
        $this->applications = $applications;
        return $this;
    }

    /**
     * Returns an array with the deployed applications.
     *
     * @return array The array with applications
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * Return's the class name of the container's receiver type.
     *
     * @return string The class name of the container's receiver type
     */
    public function getReceiverType()
    {
        return $this->getContainerNode()
            ->getReceiver()
            ->getType();
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\InitialContext::newInstance()
     */
    public function newInstance($className, array $args = array())
    {
        return $this->initialContext->newInstance($className, $args);
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
     * Returns the inital context instance.
     *
     * @return \TechDivision\ApplicationServer\InitialContext The initial context instance
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * Marks the container as started.
     *
     * @return void
     */
    public function setStarted()
    {
        $this->started = true;
    }

    /**
     * Returns TRUE if the container has been started, else FALSE.
     *
     * @return boolean TRUE if the container has been started, else FALSE
     */
    public function isStarted()
    {
        return $this->started;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\Api\ContainerService::getBaseDirectory()
     */
    public function getBaseDirectory($directoryToAppend = null)
    {
        if ($directoryToAppend != null) {
            return $this->baseDirectory . $directoryToAppend;
        }
        return $this->baseDirectory;
    }
}