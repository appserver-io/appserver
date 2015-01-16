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

use AppserverIo\Appserver\Core\Api\ConfigurationTester;
use AppserverIo\Appserver\Core\Api\Node\ParamNode;
use AppserverIo\Appserver\Core\Utilities\DirectoryKeys;

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

// check if server.php has been started with -w and/or -c option
$arguments = getopt("$watch::$configTest::", array("$config::"));

// define a constant with the appserver base directory
define('APPSERVER_BP', __DIR__);

// load core functions to override in runtime environment
require __DIR__ . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'core_functions.php';

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

// substitude xincludes
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
$configurationTester = new ConfigurationTester();

// validate the configuration file with the schema
if ($configurationTester->validateXml($mergeDoc) === false) {

    foreach ($configurationTester->getErrorMessages() as $message) {

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

// if -w option has been passed, watch deployment directory only
if (array_key_exists($watch, $arguments)) {

    $server->watch();

} else {
    $server->start();
    $server->profile();
}
