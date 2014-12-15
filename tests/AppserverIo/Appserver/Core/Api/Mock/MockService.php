<?php

/**
 * AppserverIo\Appserver\Core\Api\Mock\MockService
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace AppserverIo\Appserver\Core\Api\Mock;

use AppserverIo\Appserver\Core\Api\AbstractService;

/**
 *
 * @package AppserverIo\Appserver\Core
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class MockService extends AbstractService
{

    /**
     * (non-PHPdoc)
     *
     * @see \AppserverIo\Appserver\Core\Api\ServiceInterface::findAll()
     */
    public function findAll()
    {}

    /**
     * (non-PHPdoc)
     *
     * @see \AppserverIo\Appserver\Core\Api\ServiceInterface::load()
     */
    public function load($id)
    {}

    /**
     * (non-PHPdoc)
     *
     * @see \AppserverIo\Appserver\Core\Api\ServiceInterface::create()
     */
    public function create(\stdClass $stdClass)
    {}

    /**
     * (non-PHPdoc)
     *
     * @see \AppserverIo\Appserver\Core\Api\ServiceInterface::update()
     */
    public function update(\stdClass $stdClass)
    {}

    /**
     * (non-PHPdoc)
     *
     * @see \AppserverIo\Appserver\Core\Api\ServiceInterface::delete()
     */
    public function delete($id)
    {}
}