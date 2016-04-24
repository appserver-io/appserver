<?php

/**
 * AppserverIo\Appserver\Core\Scanner\AbstractScannerTest
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

namespace AppserverIo\Appserver\Core\Scanner;

use AppserverIo\Appserver\Core\AbstractTest;
use AppserverIo\Appserver\Core\Scanner\Mock\MockAbstractScanner;

/**
 * Test for the abstract scanner
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class AbstractScannerTest extends AbstractTest
{

    /**
     * Data provider for the testGetRestartCommand() method.
     *
     * @return array
     */
    public function getRestartCommandDataProvider()
    {
        return array(
            array('Debian' . DeploymentScanner::LINUX, '8.3', DeploymentScanner::SYSTEMD_INIT_STRING),
            array('Debian' . DeploymentScanner::LINUX, '7.1', DeploymentScanner::SYSTEMV_INIT_STRING),
            array('Fedora' . DeploymentScanner::LINUX, '21', DeploymentScanner::SYSTEMD_INIT_STRING),
            array('CentOS' . DeploymentScanner::LINUX, '6.4', DeploymentScanner::SYSTEMV_INIT_STRING),
            array('CentOS' . DeploymentScanner::LINUX, '7.0.1406', DeploymentScanner::SYSTEMD_INIT_STRING),
            array('Ubuntu' . DeploymentScanner::LINUX, '13.04', DeploymentScanner::SYSTEMV_INIT_STRING),
            array('Ubuntu' . DeploymentScanner::LINUX, '15.10', DeploymentScanner::SYSTEMD_INIT_STRING),
            array(DeploymentScanner::WINDOWS_NT, null, DeploymentScanner::WIN_NT_INIT_STRING)
        );
    }

    /**
     * Tests the getDistributionVersion() method.
     *
     * @param string      $distribution The OS to return the restart command for
     * @param string|null $distVersion  Version of the operating system to get the restart command for
     * @param string      $command      The command expected as result
     *
     * @return void
     *
     * @dataProvider getRestartCommandDataProvider
     */
    public function testGetRestartCommand($distribution, $distVersion, $command)
    {

        // mock the configuration
        $mockScanner = $this->getMockForAbstractClass('AppserverIo\Appserver\Core\Scanner\AbstractScanner', array($this->getMockInitialContext(), 'TestScanner'));
        // check that the cache directory has been initialized successfully
        $this->assertSame($command, $mockScanner->getRestartCommand($distribution, $distVersion));
    }

    /**
     * Data provider for the testGetDistributionVersion() method.
     *
     * @return array
     */
    public function getDistributionVersionDataProvider()
    {
        return array(
            array('Debian', '8.3'),
            array('Fedora', '20'),
            array('CentOS', '6.4')
        );
    }

    /**
     * Tests the getDistributionVersion() method.
     *
     * @param string      $distribution The OS to return the restart command for
     * @param string|null $distVersion  Version of the operating system to get the restart command for
     *
     * @return void
     *
     * @dataProvider getDistributionVersionDataProvider
     */
    public function testGetDistributionVersion($distribution, $distVersion)
    {
        // mock the configuration
        $mockScanner = new MockAbstractScanner($this->getMockInitialContext(), 'TestScanner');

        // check that the cache directory has been initialized successfully
        $this->assertSame($distVersion, $mockScanner->testableGetDistributionVersion($distribution));
    }
}
