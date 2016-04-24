<?php

/**
 * AppserverIo\Appserver\Core\AbstractDeploymentTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
namespace AppserverIo\Appserver\Core;

use AppserverIo\Appserver\Core\Utilities\Mock\AppEnvironmentHelperMock;

/**
 * Test for the app environment helper
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class AppEnvironmentHelperTest extends AbstractTest
{

    /**
     * Data provider for the getEnvironmentAwareFile test
     *
     * @return array
     */
    public function getEnvironmentAwareFilePathDataProvider()
    {
        $testAppBase = '/webapps/test';
        return array(
            array(
                $testAppBase,
                '*-ds',
                'xml',
                array(),
                'dev',
                $testAppBase . DIRECTORY_SEPARATOR . '*-ds.xml'
            ),
            array(
                $testAppBase,
                'pointcuts',
                'json',
                array(),
                'dev',
                $testAppBase . DIRECTORY_SEPARATOR . 'pointcuts.json'
            ),
            array(
                $testAppBase,
                '*-ds',
                'xml',
                array('found'),
                'staging',
                $testAppBase . DIRECTORY_SEPARATOR . '*-ds.staging.xml'
            ),
            array(
                $testAppBase,
                '{META-INF,WEB-INF,common}/containers',
                'xml',
                array('found'),
                'dev',
                $testAppBase . DIRECTORY_SEPARATOR . '{META-INF,WEB-INF,common}/containers.dev.xml'
            ),
            array(
                $testAppBase,
                '{META-INF,WEB-INF,common}/containers',
                'xml',
                array(),
                'dev',
                $testAppBase . DIRECTORY_SEPARATOR . '{META-INF,WEB-INF,common}/containers.xml'
            ),
            array(
                $testAppBase,
                '{META-INF,WEB-INF,common}/containers',
                'xml',
                array('found'),
                '',
                $testAppBase . DIRECTORY_SEPARATOR . '{META-INF,WEB-INF,common}/containers.xml'
            )
        );
    }

    /**
     * Checks if the getEnvironmentAwareFilePath() method works as expected.
     *
     * @param string $appBase       The base file path to the application
     * @param string $fileGlob      The intermediate path (or glob pattern) from app base path to file extension
     * @param string $fileExtension The extension of the file
     * @param array  $globDirResult The result of the internal call to globDir()
     * @param string $modifier      The modifier we need to test paths through our code
     * @param string $result        The expected result to test against
     *
     * @return void
     *
     * @dataProvider getEnvironmentAwareFilePathDataProvider
     */
    public function testGetEnvironmentAwareFilePath($appBase, $fileGlob, $fileExtension, $globDirResult, $modifier, $result)
    {
        // set the needed result of the internal globDir() method
        AppEnvironmentHelperMock::setGlobDirResult($globDirResult);
        // set the modifier as we need it
        AppEnvironmentHelperMock::setEnvironmentProperty($modifier);
        $this->assertSame($result, AppEnvironmentHelperMock::getEnvironmentAwareFilePath($appBase, $fileGlob, $fileExtension));
    }
}
