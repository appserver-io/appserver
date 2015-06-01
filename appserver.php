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

use AppserverIo\Storage\GenericStackable;
use AppserverIo\Appserver\Naming\NamingDirectory;
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
$setup = 's';

// check if server.php has been started with -w , -s and/or -c option
$arguments = getopt("$watch::$configTest::$setup:", array("$config::"));

// define a all constants appserver base directory
define('APPSERVER_BP', __DIR__);

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

// create and initialize the naming directory
$namingDirectory = new NamingDirectory();
$namingDirectory->setScheme('php');

// create a directory for the services
$namingDirectory->createSubdirectory('php:env');
$namingDirectory->createSubdirectory('php:global');
$namingDirectory->createSubdirectory('php:global/log');
$namingDirectory->createSubdirectory('php:services');

// create the default subdirectories
foreach (array_keys(ApplicationServer::$runlevels) as $runlevel) {
    $namingDirectory->createSubdirectory(sprintf('php:services/%s', $runlevel));
}

// set the path to the default configuration filenames
$namingDirectory->bind('php:env/configurationFilename', DirectoryKeys::realpath($filename));
$namingDirectory->bind(
    'php:env/bootstrapConfigurationFilename',
    DirectoryKeys::realpath(sprintf('%s/etc/appserver/conf.d/bootstrap.xml', APPSERVER_BP))
);

// add the storeage containers for the runlevels
$runlevels = new GenericStackable();
foreach (ApplicationServer::$runlevels as $runlevel) {
    $runlevels[$runlevel] = new GenericStackable();
}

// initialize and start the application server
$applicationServer = new ApplicationServer($namingDirectory, $runlevels);
$applicationServer->start();

// we've to wait for shutdown
while ($applicationServer->keepRunning()) {
    sleep(1);
}

// wait until all threads have been stopped
$applicationServer->join();
