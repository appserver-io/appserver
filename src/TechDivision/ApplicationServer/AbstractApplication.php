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

/**
 * @package     TechDivision\ApplicationServer
 * @copyright  	Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 * @author      Johann Zelger <jz@techdivision.com>
 */
abstract class AbstractApplication implements ApplicationInterface
{
    /**
     * Path to the container's host configuration.
     * @var string
     */
    const CONTAINER_HOST = '/container/host';

    /**
     * Path to the container's base directory.
     * @var string
     */
    const CONTAINER_BASE_DIRECTORY = '/container/baseDirectory';

    /**
     * The unique application name.
     * @var string
     */
    protected $name;

    /**
     * The host configuration.
     * @var \TechDivision\ApplicationServer\Configuration
     */
    protected $configuration;

    /**
     * Passes the application name That has to be the class namespace.
     *
     * @param InitialContext $initialContext
     * @param type $name The application name
     */
    public function __construct($initialContext, $name) {
        $this->initialContext = $initialContext;
        $this->name = $name;
    }

    /**
     * Has been automatically invoked by the container after the application
     * instance has been created.
     *
     * @return \TechDivision\ApplicationServer\Interfaces\ApplicationInterface The connected application
     */
    abstract public function connect();

    /**
     * Returns the application name (that has to be the class namespace,
     * e. g. TechDivision\Example).
     *
     * @return string The application name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set's the host configuration.
     *
     * @param \TechDivision\ApplicationServer\Configuration $configuration The host configuration
     * @return \TechDivision\ServletContainer\Application The application instance
     */
    public function setConfiguration($configuration) {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * Returns the host configuration.
     *
     * @return \TechDivision\ApplicationServer\Configuration The host configuration
     */
    public function getConfiguration() {
        return $this->configuration;
    }

    /**
     * Returns the path to the appserver webapp base directory.
     *
     * @return string The path to the appserver webapp base directory
     */
    public function getAppBase() {
        $baseDir = $this->getConfiguration()->getChild(self::CONTAINER_BASE_DIRECTORY)->getValue();
        $appBase = $this->getConfiguration()->getChild(self::CONTAINER_HOST)->getAppBase();
        return $baseDir . $appBase;
    }

    /**
     * Return's the path to the web application.
     *
     * @return string The path to the web application
     */
    public function getWebappPath() {
        return $this->getAppBase() . DS . $this->getName();
    }

    /**
     * Creates a new instance of the passed class name and passes the
     * args to the instance constructor.
     *
     * @param string $className The class name to create the instance of
     * @param array $args The parameters to pass to the constructor
     * @return object The created instance
     */
    public function newInstance($className, array $args = array()) {
        return $this->initialContext->newInstance($className, $args);
    }

}