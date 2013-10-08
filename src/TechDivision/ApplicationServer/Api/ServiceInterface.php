<?php

/**
 * TechDivision\ApplicationServer\Api\ServiceInterface
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api;

use Symfony\Component\Config\Definition\NodeInterface;
/**
 * This interface defines the basic method each API service has
 * to provide.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
interface ServiceInterface
{

    /**
     * Returns the initial context instance.
     *
     * @return \TechDivision\ApplicationServer\InitialContext The initial Context
     */
    public function getInitialContext();

    /**
     * Returns the system configuration.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\AppserverNode The system configuration
     */
    public function setSystemConfiguration($systemConfiguration);

    /**
     * Returns the system configuration.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\AppserverNode The system configuration
     */
    public function getSystemConfiguration();

    /**
     * Return's all nodes.
     *
     * @return array<\TechDivision\ApplicationServer\Api\Node\NodeInterface> An array with all nodes
     */
    public function findAll();

    /**
     * Returns the node with the passed UUID.
     *
     * @param integer $uuid
     *            UUID of the node to return
     * @return \TechDivision\ApplicationServer\Api\Node\NodeInterface The node with the UUID passed as parameter
     */
    public function load($uuid);
}