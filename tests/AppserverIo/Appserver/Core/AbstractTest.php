<?php

/**
 * AppserverIo\Appserver\Core\AbstractReceiverTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core;

use AppserverIo\Configuration\Configuration;
use AppserverIo\Appserver\Core\Api\Node\AppserverNode;
use AppserverIo\Appserver\Core\Api\Node\ContainerNode;
use AppserverIo\Appserver\Core\Api\Node\DeploymentNode;
use AppserverIo\Appserver\Core\Mock\MockInitialContext;
use org\bovigo\vfs\vfsStream;

/**
 * Abstract base class for appserver related tests
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Will clear all files from the temporary directory
     *
     * @return boolean
     */
    protected function clearTmpDir()
    {
        return $this->deleteTree($this->getTmpDir());
    }

    /**
     * Will recursively delete the content of a directory
     *
     * @param string $dir Path to the directory
     *
     * @return boolean
     */
    protected function deleteTree($dir)
    {
        $files = array_diff(scandir($dir), array('.','..', '.gitignore'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->deleteTree("$dir/$file") : unlink("$dir/$file");
        }

        return rmdir($dir);
    }

    /**
     * Returns the system configuration.
     *
     * @return \AppserverIo\Configuration\Configuration The system configuration
     */
    public function getAppserverConfiguration()
    {
        $configuration = new Configuration();
        $configuration->initFromFile(__DIR__ . '/../../../_files/appserver.xml');
        return $configuration;
    }

    /**
     * Returns a dummy container configuration.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node The dummy configuration
     */
    public function getContainerConfiguration()
    {
        $configuration = new Configuration();
        $configuration->initFromFile(__DIR__ . '/../../../_files/appserver_container.xml');
        return $configuration;
    }

    /**
     * Returns a dummy deployment configuration.
     *
     * @return \AppserverIo\Configuration\Configuration A dummy deployment configuration
     */
    public function getDeploymentConfiguration()
    {
        $configuration = new Configuration();
        $configuration->initFromFile(__DIR__ . '/../../../_files/appserver_container_deployment.xml');
        return $configuration;
    }

    /**
     * Returns a appserver node initialized with a mock system configuration.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\AppserverNode The requested appserver node
     */
    public function getAppserverNode()
    {
        $appserverNode = new AppserverNode();
        $appserverNode->initFromConfiguration($this->getAppserverConfiguration());
        return $appserverNode;
    }

    /**
     * Returns a container node initialized with a mock container configuration.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\ContainerNode The requested container node
     */
    public function getContainerNode()
    {
        $containerNode = new ContainerNode();
        $containerNode->initFromConfiguration($this->getContainerConfiguration());
        return $containerNode;
    }

    /**
     * Returns a deployment node initialized with a mock deployment configuration.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DeplyomentNode The requested deployment node
     */
    public function getDeploymentNode()
    {
        $deploymentNode = new DeploymentNode();
        $deploymentNode->initFromConfiguration($this->getDeploymentConfiguration());
        return $deploymentNode;
    }

    /**
     * Returns a initial context instance with a mock configuration.
     *
     * @return \AppserverIo\Appserver\Core\InitialContext Initial context with mock configuration
     */
    public function getMockInitialContext()
    {
        return new MockInitialContext($this->getAppserverNode());
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

    /**
     * Path to a real, existing directory which might be used for not-mockable filesystem operations (e.g. chown)
     *
     * @return string
     */
    public function getTmpDir()
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR  . md5(__CLASS__);
    }

    /**
     * Will set up a virtual stream wrapper to mock file system operations below a configured root path
     * Usage:
     *  $rootDir = vfsStreamWrapper::getRoot();
     *
     * @param string $rootDir Root directory path of your mocked file system path
     *
     * @return \org\bovigo\vfs\vfsStreamDirectory
     *
     * @see https://phpunit.de/manual/3.7/en/test-doubles.html#test-doubles.mocking-the-filesystem.examples.ExampleTest2.php
     * @see https://github.com/mikey179/vfsStream/wiki
     * @see http://tech.vg.no/2011/03/09/mocking-the-file-system-using-phpunit-and-vfsstream/
     */
    public function setUpFilesystemMock($rootDir)
    {
        return vfsStream::setup($rootDir);
    }
}