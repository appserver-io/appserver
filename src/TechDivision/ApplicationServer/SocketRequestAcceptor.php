<?php

/**
 * TechDivision\ServletContainer\SocketRequestAcceptor
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\Interfaces\ContainerInterface;
use TechDivision\SplClassLoader;

/**
 * The thread implementation that handles the request.
 *
 * @package     TechDivision\ServletContainer
 * @copyright  	Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Johann Zelger <jz@techdivision.com>
 */
class SocketRequestAcceptor extends \Thread {

    /**
     * Holds the container implementation
     *
     * @var ContainerInterface
     */
    public $container;

    /**
     * Holds the main socket resource
     *
     * @var resource
     */
    public $resource;

    /**
     * The thread implementation classname
     *
     * @var string
     */
    public $threadType;

    /**
     * Init acceptor with container and acceptable socket resource
     * and threadType class
     *
     * @param ContainerInterface $container A container implementation
     * @param resource $resource The client socket instance
     * @param string $threadType The thread type class to instantiate
     * @return \TechDivision\ApplicationServer\SocketRequestAcceptor
     */
    public function __construct(ContainerInterface $container, $resource, $threadType) {
        $this->container = $container;
        $this->resource = $resource;
        $this->threadType = $threadType;
    }

    /**
     * @see \Thread::run()
     */
    public function run() {
        // register class loader again, because we are in a thread
        $classLoader = new SplClassLoader();
        $classLoader->register();
        // start acceptor loop
        while (true) {
            // accept client connection
            if ($clientSocket = socket_accept($this->resource)) {
                // init new thread type instance
                $request = new $this->threadType($this->container, $clientSocket);
                // start thread
                $request->start();
            }
        }
    }

}