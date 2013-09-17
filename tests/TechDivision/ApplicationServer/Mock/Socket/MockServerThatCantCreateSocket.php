<?php

/**
 * TechDivision\ApplicationServer\Mock\Socket\MockServerThatCantCreateSocket
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Socket;

use TechDivision\Socket\Server;
use TechDivision\SocketException;

/**
 * The mock request implementation.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class MockServerThatCantCreateSocket extends MockServer
{

    /**
     * (non-PHPdoc)
     * @see \TechDivision\Socket\Server::start()
     */
    public function start()
    {
        $this->resource = socket_create(AF_UNIX, SOCK_STREAM, 0);
        throw new SocketException('Address already in use');
    }
}