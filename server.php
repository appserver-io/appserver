#!/opt/appserver/bin/php
<?php

/**
 * server.php
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category  Server
 * @package   Appserver
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2014 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core;

use AppserverIo\Appserver\Core\Api\ConfigurationService;
use AppserverIo\Appserver\Core\Api\Node\AppserverNode;
use AppserverIo\Appserver\Core\Api\Node\ParamNode;
use AppserverIo\Appserver\Core\Utilities\DirectoryKeys;
use AppserverIo\Appserver\Core\InitialContext;
use AppserverIo\Appserver\Core\Utilities\FileSystem;
use Ratchet\Wamp\Exception;

declare (ticks = 1);

error_reporting(~E_NOTICE);
set_time_limit(0);

// set the session timeout to unlimited
ini_set('session.gc_maxlifetime', 0);
ini_set('zend.enable_gc', 0);
ini_set('max_execution_time', 0);

// set environmental variables in $_ENV globals per default
$_ENV = appserver_get_envs();

// define the available options
$watch = 'w';
$config = 'c';
$configTest = 't';
$setup = 's';

// check if server.php has been started with -w , -s and/or -c option
$arguments = getopt("$watch::$configTest::$setup:", array("$config::"));

// define a all constants appserver base directory
define('APPSERVER_BP', __DIR__);
// define install flag for setup mode install to check
define(
'IS_INSTALLED_FILE',
    __DIR__ . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . 'appserver' . DIRECTORY_SEPARATOR . '.is-installed'
);
define('IS_INSTALLED', is_file(IS_INSTALLED_FILE));

// bootstrap the application
require __DIR__ . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'bootstrap.php';

// query whether a configuration file has been specified or not
if (array_key_exists($config, $arguments) && file_exists($arguments[$config])) {
    // set the file passed as parameter
    $filename = $arguments[$config];
} elseif (file_exists(sprintf('%s/etc/appserver/appserver.xml', APPSERVER_BP))) {
    // try to load the default configuration file
    $filename = sprintf('%s/etc/appserver/appserver.xml', APPSERVER_BP);
} else {
    // throw an exception if we don't have a configuration file
    throw new \Exception('Can\'t find a configuration file');
}

// initialize configuration and schema file name
$configurationFileName = DirectoryKeys::realpath($filename);

// initialize the DOMDocument with the configuration file to be validated
$configurationFile = new \DOMDocument();
$configurationFile->load($configurationFileName);

// substitute xincludes
$configurationFile->xinclude(LIBXML_SCHEMA_CREATE);

// create a DOMElement with the base.dir configuration
$paramElement = $configurationFile->createElement('param', APPSERVER_BP);
$paramElement->setAttribute('name', DirectoryKeys::BASE);
$paramElement->setAttribute('type', ParamNode::TYPE_STRING);

// append the base.dir DOMElement
if ($paramsNode = $configurationFile->getElementsByTagName('params')->item(0)) {
    $paramsNode->appendChild($paramElement);
}

// create a new DOMDocument with the merge content => necessary because else, schema validation fails!!
$mergeDoc = new \DOMDocument();
$mergeDoc->loadXML($configurationFile->saveXML());

// get an instance of our configuration tester
$configurationService = new ConfigurationService(new InitialContext(new AppserverNode()));

// validate the configuration file with the schema
if ($configurationService->validateXml($mergeDoc) === false) {

    foreach ($configurationService->getErrorMessages() as $message) {

        // if we are here to test we will make a sane output instead of throwing an exception
        if (array_key_exists($configTest, $arguments)) {

            echo $message;
            exit;
        }
        throw new \Exception($message);
    }

} elseif (array_key_exists($configTest, $arguments)) {

    echo "Syntax OK\n";
    exit;
}

// initialize the SimpleXMLElement with the content XML configuration file
$configuration = new \AppserverIo\Configuration\Configuration();
$configuration->initFromString($mergeDoc->saveXML());

// create the server instance
$server = new Server($configuration);

// check if server.php -s is called for doing setup process
if (array_key_exists($setup, $arguments)) {
    try {
        // try to get setup mode
        $setupMode = $arguments[$setup];
        // init user and group vars
        $user = null;
        $group = null;

        $configurationUserReplacePattern = '/(<appserver[^>]+>[^<]+<params>.*<param name="user[^>]+>)([^<]+)/s';

        // check setup modes
        switch ($setupMode) {

            // prepares everything for developer mode
            case 'dev':
                // get current user to set permissions for
                $user = get_current_user();
                // get defined group from configuration
                $group = $server->getSystemConfiguration()->getParam('group');
                // replace user in configuration file
                file_put_contents($configurationFileName, preg_replace(
                    $configurationUserReplacePattern,
                    '${1}' . $user,
                    file_get_contents($configurationFileName)
                ));
                // add everyone write access to configuration files for dev mode
                FileSystem::recursiveChmod(APPSERVER_BP . DIRECTORY_SEPARATOR . 'etc', 0777, 0777);

                break;

            // prepares everything for production mode
            case 'prod':
                // get defined user and group from configuration
                $user = $server->getSystemConfiguration()->getParam('user');
                $group = $server->getSystemConfiguration()->getParam('group');
                // replace user to be same as group in configuration file
                file_put_contents($configurationFileName, preg_replace(
                    $configurationUserReplacePattern,
                    '${1}' . $group,
                    file_get_contents($configurationFileName)
                ));
                // set correct file permissions for configurations
                FileSystem::recursiveChmod(APPSERVER_BP . DIRECTORY_SEPARATOR . 'etc');

                break;

            // prepares everything for first installation which is default mode
            case 'install':
                // first check if install flag was set before
                if (IS_INSTALLED) {
                    echo "Nothing to do. Setup for mode '$setupMode' already done!" . PHP_EOL;
                    // exit normally
                    exit(0);
                }

                // create is installed flag for prevent further setup install mode calls
                touch(IS_INSTALLED_FILE);

                // get defined user and group from configuration
                $user = $server->getSystemConfiguration()->getParam('user');
                $group = $server->getSystemConfiguration()->getParam('group');

                // set correct file permissions for configurations
                FileSystem::recursiveChmod(APPSERVER_BP . DIRECTORY_SEPARATOR . 'etc');

                break;
            default:
                throw new \Exception('No valid setup mode given');

        }

        // check if user and group is set
        if (!is_null($user) && !is_null($group)) {
            // set needed files as accessable
            FileSystem::recursiveChown(APPSERVER_BP . DIRECTORY_SEPARATOR . 'var', $user, $group);
            FileSystem::recursiveChmod(APPSERVER_BP . DIRECTORY_SEPARATOR . 'var');
            FileSystem::recursiveChown(APPSERVER_BP . DIRECTORY_SEPARATOR . 'webapps', $user, $group);
            FileSystem::recursiveChmod(APPSERVER_BP . DIRECTORY_SEPARATOR . 'webapps');
            FileSystem::recursiveChown(APPSERVER_BP . DIRECTORY_SEPARATOR . 'deploy', $user, $group);
            FileSystem::recursiveChmod(APPSERVER_BP . DIRECTORY_SEPARATOR . 'deploy');
            FileSystem::recursiveChown(APPSERVER_BP . DIRECTORY_SEPARATOR . 'src', $user, $group);
            FileSystem::recursiveChown(APPSERVER_BP . DIRECTORY_SEPARATOR . 'vendor', $user, $group);

            echo "Setup for mode '$setupMode' done successfully!" . PHP_EOL;

        } else {
            throw new \Exception('No user or group given');
        }

        // exit normally
        exit(0);

    } catch (\Exception $e) {
        echo $e . PHP_EOL;
        exit($e->getCode());
    }
}

// if -w option has been passed, watch deployment directory only
if (array_key_exists($watch, $arguments)) {

    $server->watch();

} else {
    $server->start();
    $server->profile();
}
