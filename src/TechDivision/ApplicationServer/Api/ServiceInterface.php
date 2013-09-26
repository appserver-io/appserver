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
     * @return \TechDivision\ApplicationServer\Configuration The system configuration
     */
    public function getSystemConfiguration();

    /**
     * Return's all container configurations.
     *
     * @return array<\stdClass> An array with all container configurations
     */
    public function findAll();

    /**
     * Returns the containers with the passed name.
     *
     * @param integer $id
     *            ID of the container to return
     * @return \stdClass The container with the ID passed as parameter
     */
    public function load($id);

    public function create($stdClass);

    public function update($stdClass);

    public function delete($id);
}