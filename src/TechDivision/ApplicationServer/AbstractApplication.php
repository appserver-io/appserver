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
     * Path to the container's host configuration.
     *
     * @var string
     */
    const XPATH_CONTAINER_HOST = '/container/host';

    /**
     * Path to the container's base directory.
     *
     * @var string
     */
    const XPATH_CONTAINER_BASE_DIRECTORY = '/container/baseDirectory';

    /**
     * The unique application ID.
     *
     * @var string
     */
    protected $id;

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
    public function __construct($initialContext, $name)
    {
        $this->initialContext = $initialContext;
        $this->name = $name;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\Interfaces\ApplicationInterface::connect()
     */
    public function connect()
    {

        $vhostService = $this->newService('TechDivision\ApplicationServer\Api\VhostService');
        $vhosts = $vhostService->findAllByAppBase($this->getAppBase())->vhosts;

        // prepare the VHost configurations
        foreach ($vhosts as $vhost) {

            // check if vhost configuration belongs to application
            if ($this->getName() == ltrim($vhost->app_base, '/')) {

                // prepare the aliases if available
                $aliases = array();
                foreach ($vhost->getChilds(Vhost::XPATH_CONTAINER_ALIAS) as $alias) {
                    $aliases[] = $alias->getValue();
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
     * Set's the unique application ID from the system configuration.
     *
     * @param string $id
     *            The unique application ID from the system configuration
     * @return \TechDivision\ServletContainer\Application The application instance
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Return's the unique application ID from the system configuration.
     *
     * @return string The unique application ID from the system configuration
     */
    public function getId()
    {
        return $this->id;
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
     * Returns the host configuration.
     *
     * @return \TechDivision\ApplicationServer\Configuration The host configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\Api\ApplicationService::getAppBase()
     */
    public function getAppBase()
    {
        return $this->newService('TechDivision\ApplicationServer\Api\ApplicationService')->getAppBase($this->getId());
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\Api\ApplicationService::getWebappPath()
     */
    public function getWebappPath()
    {
        return $this->newService('TechDivision\ApplicationServer\Api\ApplicationService')->getWebappPath($this->getId());
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\Api\ApplicationService::getServerSoftware()
     */
    public function getServerSoftware()
    {
        return $this->newService('TechDivision\ApplicationServer\Api\ApplicationService')->getServerSoftware($this->getId());
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\Api\ApplicationService::getServerAdmin()
     */
    public function getServerAdmin()
    {
        return $this->newService('TechDivision\ApplicationServer\Api\ApplicationService')->getServerAdmin($this->getId());
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
     * @see \TechDivision\ApplicationServer\Interfaces\ApplicationInterface::toStdClass()
     */
    public function toStdClass()
    {
        $stdClass = new \stdClass();
        $stdClass->id = $this->getId();
        $stdClass->name = $this->getName();
        return $stdClass;
    }
}