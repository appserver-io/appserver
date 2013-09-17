<?php

/**
 * TechDivision\ApplicationServer\Mock\Socket\MockRequest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Mock\Socket;

use TechDivision\Socket\Server;

/**
 * The mock request implementation.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class MockServer extends Server
{

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\Socket\Server::start()
     */
    public function start()
    {}

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\Socket::getResource()
     */
    public function getResource()
    {
        return true;
    }
}