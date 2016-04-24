<?php

/**
 * \AppserverIo\Appserver\Core\Utilities\Mock\AppEnvironmentHelperMock
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
 * @copyright 2016 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Utilities\Mock;

use AppserverIo\Appserver\Core\Utilities\AppEnvironmentHelper;
use AppserverIo\Appserver\Core\Utilities\ConfigurationKeys;
use AppserverIo\Appserver\Core\Utilities\DirectoryKeys;
use AppserverIo\Properties\Properties;

/**
 * Helper which provides static methods for handling different application environment settings
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2016 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class AppEnvironmentHelperMock extends AppEnvironmentHelper
{
    /**
     * Cached configuration properties
     *
     * @var \AppserverIo\Properties\Properties $cachedProperties
     */
    protected static $cachedGlobDirResult;

    /**
     * @param $value
     *
     * @throws \AppserverIo\Collections\InvalidKeyException
     * @throws \AppserverIo\Lang\NullPointerException
     */
    public static function setEnvironmentProperty($value)
    {
        self::$cachedProperties = Properties::create();
        self::$cachedProperties->add(ConfigurationKeys::APP_ENVIRONMENT, $value);
    }

    /**
     * (Pre-) sets the the globDir() method result as a means to stub the method
     *
     * @param array $result The prepared result
     *
     * @return void
     */
    public static function setGlobDirResult($result)
    {
        self::$cachedGlobDirResult = $result;
    }

    /**
     * Recursively parses and returns the directories that matches the passed
     * glob pattern.
     *
     * @param string  $pattern The glob pattern used to parse the directories
     * @param integer $flags   The flags passed to the glob function
     *
     * @return array The directories matches the passed glob pattern
     * @link http://php.net/glob
     */
    protected static function globDir($pattern, $flags = 0)
    {
        return self::$cachedGlobDirResult;
    }
}
