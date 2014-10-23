<?php

/**
 * TechDivision\ApplicationServer\PbcClassLoaderFactory
 *
 * PHP version 5
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Bernhard Wick <b.wick@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace TechDivision\ApplicationServer;

use TechDivision\PBC\Config;

/**
 * A factory for the PBC class loader instances.
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Bernhard Wick <b.wick@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
class PbcClassLoaderFactory extends AbstractClassLoaderFactory
{

    /**
     * The main method that creates new instances in a separate context.
     *
     * @return void
     */
    public function run()
    {

        while (true) { // we never stop

            $this->synchronized(function ($self) {

                // make instances local available
                $instances = $self->instances;
                $application = $self->application;
                $configuration = $self->configuration;
                $initialContext = $self->initialContext;

                // register the default class loader
                $initialContext->getClassLoader()->register(true, true);

                // load the web application path we want to register the class loader for
                $webappPath = $application->getWebappPath();

                // initialize the class path and the enforcement directories
                $classPath = array();
                $enforcementDirs = array();

                // add the possible class path if folder is available
                foreach ($configuration->getDirectories() as $directory) {
                    if (is_dir($webappPath . $directory->getNodeValue())) {
                        array_push($classPath, $webappPath . $directory->getNodeValue());
                        if ($directory->isEnforced()) {
                            array_push($enforcementDirs, $webappPath . $directory->getNodeValue());
                        }
                    }
                }

                // initialize the arrays of different omit possibilities
                $omittedEnforcement = array();
                $omittedAutoLoading = array();

                // iterate over all namespaces and check if they are omitted in one or the other way
                foreach ($configuration->getNamespaces() as $namespace) {

                    // is the enforcement omitted for this namespace?
                    if ($namespace->omitEnforcement()) {

                        $omittedEnforcement[] = $namespace->getNodeValue()->__toString();
                    }

                    // is the autoloading omitted for this namespace?
                    if ($namespace->omitAutoLoading()) {

                        $omittedAutoLoading[] = $namespace->getNodeValue()->__toString();
                    }
                }

                // initialize the class loader configuration
                $config = new Config();

                // set the environment mode we want to use
                $config->setValue('environment', $configuration->getEnvironment());

                // set the cache directory
                $config->setValue('cache/dir', $application->getCacheDir());

                // set the default autoloader values
                $config->setValue('autoloader/dirs', $classPath);

                // collect the omitted namespaces (if any)
                $config->setValue('autoloader/omit', $omittedAutoLoading);
                $config->setValue('enforcement/omit', $omittedEnforcement);

                // set the default enforcement configuration values
                $config->setValue('enforcement/dirs', $enforcementDirs);
                $config->setValue('enforcement/enforce-default-type-safety', $configuration->getTypeSafety());
                $config->setValue('enforcement/processing', $configuration->getProcessing());
                $config->setValue('enforcement/level', $configuration->getEnforcementLevel());
                $config->setValue('enforcement/logger', $application->getInitialContext()->getSystemLogger());

                // create the autoloader instance and fill the structure map
                $autoLoader = new PbcClassLoader($config);

                // add the class loader instance to the application
                $instances[] = $autoLoader;

                // wait for the next instance to be created
                $self->wait();

            }, $this);
        }
    }
}
