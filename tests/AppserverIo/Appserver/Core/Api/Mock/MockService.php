<?php

/**
 * AppserverIo\Appserver\Core\Api\Mack\MockService
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Mock;

use AppserverIo\Appserver\Core\Api\AbstractService;

/**
 * Unit tests for our abstract service implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class MockService extends AbstractService
{

    /**
     * (non-PHPdoc)
     *
     * @see \AppserverIo\Psr\ApplicationServer\ServiceInterface::findAll()
     */
    public function findAll()
    {}

    /**
     * (non-PHPdoc)
     *
     * @see \AppserverIo\Psr\ApplicationServer\ServiceInterface::load()
     */
    public function load($id)
    {}

    /**
     * (non-PHPdoc)
     *
     * @see \AppserverIo\Psr\ApplicationServer\ServiceInterface::create()
     */
    public function create(\stdClass $stdClass)
    {}

    /**
     * (non-PHPdoc)
     *
     * @see \AppserverIo\Psr\ApplicationServer\ServiceInterface::update()
     */
    public function update(\stdClass $stdClass)
    {}

    /**
     * (non-PHPdoc)
     *
     * @see \AppserverIo\Psr\ApplicationServer\ServiceInterface::delete()
     */
    public function delete($id)
    {}
}