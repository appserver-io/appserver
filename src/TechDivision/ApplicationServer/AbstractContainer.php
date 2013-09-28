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
     * Path to the container's receiver configuration.
     *
     * @var string
     */
    const XPATH_CONFIGURATION_RECEIVER = '/container/receiver';

    /**
     * Path to the receiver's worker.
     *
     * @var string
     */
    const XPATH_CONFIGURATION_WORKER = '/container/receiver/worker';

    /**
     * Path to the receiver's worker.
     *
     * @var string
     */
    const XPATH_CONFIGURATION_THREAD = '/container/receiver/thread';

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
     * The container's unique ID.
     *
     * @var string
     */
    protected $id;

    /**
     * TRUE if the container has been started, else FALSE.
     * @var boolean
     */
    protected $started = false;

    /**
     * Initializes the container with the initial context, the unique container ID
     * and the deployed applications.
     *
     * @param \TechDivision\ApplicationServer\InitialContext $initialContext
     *            The initial context instance
     * @param string $id
     *            The unique container ID
     * @todo Application deployment only works this way because of Thread compatibilty
     * @return void
     */
    public function __construct($initialContext, $id, $applications)
    {
        $this->initialContext = $initialContext;
        $this->id = $id;
        $this->setApplications($applications);
    }

    /**
     * The unique container ID.
     *
     * @return string The unique container ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @see \Stackable::run()
     */
    public function run()
    {
        $this->setStarted($this->getReceiver()->start());
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
        $receiverService = $this->newService('TechDivision\ApplicationServer\Api\ContainerService');
        return $receiverService->getReceiverType($this->getId());
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
}