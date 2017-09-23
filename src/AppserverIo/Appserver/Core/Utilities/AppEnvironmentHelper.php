<?php

/**
 * \AppserverIo\Appserver\Core\Utilities\AppEnvironmentHelper
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
 * @author    Hans Höchtl <hhoechtl@1drop.de>
 * @copyright 2016 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Utilities;

use AppserverIo\Properties\Properties;

/**
 * Helper which provides static methods for handling different application environment settings
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @author    Hans Höchtl <hhoechtl@1drop.de>
 * @copyright 2016 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class AppEnvironmentHelper
{

    /**
     * Returns the name of the configuration file
     *
     * @var string CONFIGURATION_FILE
     */
    const CONFIGURATION_FILE = 'build.properties';

    /**
     * Cached configuration properties
     *
     * @var \AppserverIo\Properties\Properties $cachedProperties
     */
    protected static $cachedProperties;

    /**
     * Get the environment modifier (if any) which helps to switch the configuration environment
     *
     * @param string $appBase The base of the application we are dealing with
     *
     * @return string
     *
     * @throws \AppserverIo\Properties\PropertyFileNotFoundException
     * @throws \AppserverIo\Properties\PropertyFileParseException
     */
    public static function getEnvironmentModifier($appBase)
    {
        // check if we got the properties cached already, if not load them anew
        $properties = null;
        if (!is_null(self::$cachedProperties)) {
            $properties = self::$cachedProperties;

        } else {
            // load the properties from file
            $propertiesFile = DirectoryKeys::realpath(
                sprintf('%s/%s', $appBase, self::CONFIGURATION_FILE)
            );

            // load the properties from the configuration file
            if (file_exists($propertiesFile)) {
                $properties = Properties::create()->load($propertiesFile);
            }
        }

        // load the properties from the configuration file
        $result = '';
        // get the actual property if it exists
        if (!is_null($properties) && $properties->exists(ConfigurationKeys::APP_ENVIRONMENT)) {
            $result = $properties->get(ConfigurationKeys::APP_ENVIRONMENT);
        }
        // ENV variable always wins
        if (defined(ConfigurationKeys::APP_ENVIRONMENT)) {
            $result = getenv(ConfigurationKeys::APP_ENVIRONMENT);
        }

        return $result;
    }

    /**
     * Will take a segmented path to a file (which might contain glob type wildcards) and return it fixed to the currently active environment modifier.
     * E.g.
     * AppEnvironmentHelper::getEnvironmentAwareFilePath('webapps/example', 'META-INF/*-ds') => 'webapps/example/META-INF/*-ds.dev.xml'
     *
     * @param string  $appBase       The base file path to the application
     * @param string  $fileGlob      The intermediate path (or glob pattern) from app base path to file extension
     * @param integer $flags         The flags passed to the glob function
     * @param string  $fileExtension The extension of the file, will default to 'xml'
     *
     * @return string
     */
    public static function getEnvironmentAwareGlobPattern($appBase, $fileGlob, $flags = 0, $fileExtension = 'xml')
    {
        // get the file path modifier
        $modifier = static::getEnvironmentModifier($appBase);

        // as we default to a not modified path we have to be careful about the "two dots" schema .$modifier.$extension
        $defaultFilePath = $appBase . DIRECTORY_SEPARATOR . $fileGlob . '.' . $fileExtension;
        if (empty($modifier)) {
            // if we do not have a modifier we do not need to act upon anything, so we return the default
            return $defaultFilePath;
        } else {
            // we got a modifier we have to check if there is something reachable under the modified path, if not we will also return the default
            $modifiedPath = $appBase . DIRECTORY_SEPARATOR . $fileGlob . '.' . $modifier . '.' . $fileExtension;
            $potentialFiles = static::globDir($modifiedPath, $flags);
            if (!empty($potentialFiles)) {
                return $modifiedPath;
            }
            return $defaultFilePath;
        }
    }

    /**
     * Parses and returns the directories and files that matches
     * the passed glob pattern in a recursive way (if wanted).
     *
     * @param string  $pattern   The glob pattern used to parse the directories
     * @param integer $flags     The flags passed to the glob function
     * @param boolean $recursive Whether or not to parse directories recursively
     *
     * @return array The directories matches the passed glob pattern
     * @link http://php.net/glob
     */
    protected static function globDir($pattern, $flags = 0, $recursive = true)
    {
        return FileSystem::globDir($pattern, $flags, $recursive);
    }
}
