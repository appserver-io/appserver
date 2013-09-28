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
     * Returns the used normalizer instance.
     *
     * @return \TechDivision\ApplicationServer\Api\NormalizerInterface The normalizer instance
     */
    public function getNormalizer();

    /**
     * Returns the system configuration.
     *
     * @return \TechDivision\ApplicationServer\Configuration The system configuration
     */
    public function getSystemConfiguration();

    /**
     * Return's all entity configurations.
     *
     * @return array<\stdClass> An array with all entity configurations
     */
    public function findAll();

    /**
     * Returns the containers with the passed ID.
     *
     * @param integer $id
     *            ID of the entity to return
     * @return \stdClass The container with the ID passed as parameter
     */
    public function load($id);

    /**
     * Creates a new entity from the passed class information.
     *
     * @param \stdClass $stdClass
     *            The class information to create the entity from
     * @return string The new ID of the created entity
     * @return void
     */
    public function create(\stdClass $stdClass);

    /**
     * Updates the entity with the passed class information.
     *
     * @param \stdClass $stdClass
     *            The entity with the passed class information.
     * @return void
     */
    public function update(\stdClass $stdClass);

    /**
     * Deletes the entity with the passed ID.
     *
     * @param string $id
     *            ID of the entity to remove
     */
    public function delete($id);
}