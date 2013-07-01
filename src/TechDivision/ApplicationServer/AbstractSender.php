<?php

/**
 * TechDivision\ApplicationServer\AbstractSender
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\Interfaces\SenderInterface;

/**
 * @package     TechDivision\ApplicationServer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
abstract class AbstractSender implements SenderInterface {
    
    /**
     * The container instance.
     * @var \TechDivision\ApplicationServer\Interfaces\ContainerInterface
     */
    protected $container;
    
    /**
     * Sets the reference to the container instance.
     * 
     * @param \TechDivision\ApplicationServer\Interfaces\ContainerInterface $container The container instance
     */
    public function __construct($container) {
        $this->container = $container;
    }
    
    /**
     * Returns the refrence to the container instance.
     * 
     * @return \TechDivision\ApplicationServer\Interfaces\ContainerInterface The container instance
     */
    public function getContainer() {
        return $this->container;
    }
    
    /**
     * Returns the sender configuration.
     * 
     * @return \TechDivision\ApplicationServer\Interfaces\ContainerConfiguration
     */
    public function getConfiguration() {
        return $this->getContainer()->getConfiguration()->getSender();
    }
}