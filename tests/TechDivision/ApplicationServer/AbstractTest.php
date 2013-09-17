<?php

/**
 * TechDivision\ApplicationServer\AbstractReceiverTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\Mock\MockApplication;
use TechDivision\ApplicationServer\Mock\MockContainer;
use TechDivision\ApplicationServer\Configuration;
use TechDivision\ApplicationServer\InitialContext;

/**
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class AbstractTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Returns a dummy container configuration.
     *
     * @return \TechDivision\ApplicationServer\Configuration The dummy configuration
     */
    public function getContainerConfiguration()
    {
        $configuration = new Configuration();
        $configuration->initFromFile(__DIR__ . '/_files/appserver_container.xml');
        $configuration->addChildWithNameAndValue('baseDirectory', '/opt/appserver');
        return $configuration;
    }

    /**
     * Returns a mock application instance.
     *
     * @param string $applicationName
     *            The dummy application name
     * @return \TechDivision\ApplicationServer\MockApplication The initialized mock application
     */
    public function getMockApplication($applicationName)
    {
        $configuration = new Configuration();
        $configuration->initFromFile(__DIR__ . '/_files/appserver_initial_context.xml');
        $initialContext = new InitialContext($configuration);
        $application = new MockApplication($initialContext, $applicationName);
        $application->setConfiguration($this->getContainerConfiguration());
        return $application;
    }

    /**
     * Returns an array with mock applications.
     *
     * @param integer $size
     *            The number of mock applications to be returned
     * @return array Array with mock applications
     */
    public function getMockApplications($size = 1)
    {
        $applications = array();
        for ($i = 0; $i < $size; $i ++) {
            $application = $this->getMockApplication("application-$i");
            $applications[$application->getName()] = $application;
        }
        return $applications;
    }

    /**
     * Returns a new socket pair to simulate a real socket implementation.
     *
     * @throws \Exception Is thrown if the socket pair can't be craeted
     * @return array The socket pair
     */
    public function getSocketPair()
    {

        // initialize the array for the socket pair
        $sockets = array();

        // on Windows we need to use AF_INET
        $domain = (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? AF_INET : AF_UNIX);

        // setup and return a new socket pair
        if (socket_create_pair($domain, SOCK_STREAM, 0, $sockets) === false) {
            throw new \Exception("socket_create_pair failed. Reason: " . socket_strerror(socket_last_error()));
        }

        // return the array with the socket pair
        return $sockets;
    }
}