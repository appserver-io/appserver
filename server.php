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
use AppserverIo\Appserver\Core\Utilities\Runlevels;

declare (ticks = 1);

error_reporting(~E_NOTICE);
set_time_limit(0);

// set the session timeout to unlimited
ini_set('session.gc_maxlifetime', 0);
ini_set('zend.enable_gc', 0);
ini_set('max_execution_time', 0);

// query whether the sockets extension is available or not
if (extension_loaded('sockets') === false) {
    throw new \Exception('Extension sockets has to be loaded');
}

// query whether the pthreads extension is available or not
if (extension_loaded('pthreads') === false) {
    throw new \Exception('Extension pthreads (https://github.com/appserver-io-php/pthreads) > 2.0.10 has to be loaded');
}

// query whether the appserver extension is available or not
if (version_compare(PHP_VERSION, '7.0.0', '<') && extension_loaded('appserver') === false) {
    throw new \Exception('Extension appserver (https://github.com/appserver-io-php/php-ext-appserver) > 1.0.1 has to be loaded');
}

// define a all constants appserver base directory
define('APPSERVER_BP', __DIR__);

// bootstrap the application
require __DIR__ . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'bootstrap.php';

// define the available options
$setup = 's';
$watch = 'w'; // compatibility mode for old server version
$config = 'c';
$bootstrap = 'b';
$configTest = 't';

// check if server.php has been started with -s, -w, -c or -b option
$arguments = getopt("$setup:$configTest::$watch::$config::$bootstrap::");

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

// query whether a bootstrap file has been specified or not
if (array_key_exists($bootstrap, $arguments) && file_exists($arguments[$bootstrap])) {
    // set the file passed as parameter
    $bootstrapFilename = $arguments[$bootstrap];
} elseif ((array_key_exists($setup, $arguments) || array_key_exists($configTest, $arguments)) && file_exists(sprintf('%s/etc/appserver/conf.d/bootstrap-commands.xml', APPSERVER_BP))) {
    // set the default commands boostrap file
    $bootstrapFilename = sprintf('%s/etc/appserver/conf.d/bootstrap-commands.xml', APPSERVER_BP);
} elseif (array_key_exists($watch, $arguments) && file_exists(sprintf('%s/etc/appserver/conf.d/bootstrap-watcher.xml', APPSERVER_BP))) {
    // set the default watcher boostrap file
    $bootstrapFilename = sprintf('%s/etc/appserver/conf.d/bootstrap-watcher.xml', APPSERVER_BP);
} elseif (file_exists(sprintf('%s/etc/appserver/conf.d/bootstrap.xml', APPSERVER_BP))) {
    // try to load the default bootstrap file
    $bootstrapFilename = sprintf('%s/etc/appserver/conf.d/bootstrap.xml', APPSERVER_BP);
} else {
    // throw an exception if we don't have a bootstrap file
    throw new \Exception('Can\'t find a bootstrap file');
}

// create and initialize the naming directory
$namingDirectory = new NamingDirectory();
$namingDirectory->setScheme('php');

// create a directory for the services
$namingDirectory->createSubdirectory('php:env');
$namingDirectory->createSubdirectory('php:env/args');
$namingDirectory->createSubdirectory('php:global');
$namingDirectory->createSubdirectory('php:global/log');
$namingDirectory->createSubdirectory('php:services');

// create the default subdirectories
foreach (array_keys(Runlevels::singleton()->getRunlevels()) as $runlevel) {
    $namingDirectory->createSubdirectory(sprintf('php:services/%s', $runlevel));
}

// bind the command line arguments to the naming directory
foreach ($arguments as $name => $value) {
    $namingDirectory->bind(sprintf('php:env/args/%s', $name), empty($value) ? true : $value);
}

// bind the current user to the naming directory
$namingDirectory->bind('php:env/currentUser', isset($_SERVER['SUDO_USER']) ? $_SERVER['SUDO_USER'] : get_current_user());

// bind the path to the default configuration and bootstrap filenames
$namingDirectory->bind('php:env/configurationFilename', DirectoryKeys::realpath($filename));
$namingDirectory->bind('php:env/bootstrapConfigurationFilename', DirectoryKeys::realpath($bootstrapFilename));

// add the storeage containers for the runlevels
$runlevels = new GenericStackable();
foreach (Runlevels::singleton()->getRunlevels() as $runlevel) {
    $runlevels[$runlevel] = new GenericStackable();
}

// initialize and start the application server
$applicationServer = ApplicationServer::singleton($namingDirectory, $runlevels);

// we've to wait for shutdown
while ($applicationServer->keepRunning()) {
    sleep(1);
}

// wait until all threads have been stopped
$applicationServer->join();
