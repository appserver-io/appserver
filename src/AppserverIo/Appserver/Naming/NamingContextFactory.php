<?php

/**
 * AppserverIo\Appserver\Naming\NamingContextFactory
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Naming;

use AppserverIo\Properties\Properties;
use AppserverIo\Psr\Naming\InitialContext;
use AppserverIo\Appserver\Core\Utilities\DirectoryKeys;
use AppserverIo\Appserver\Application\Interfaces\ManagerSettingsAwareInterface;

/**
 * Factory implementation for the naming context.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class NamingContextFactory
{

    /**
     * The name of the naming context configuration properties file.
     *
     * @var string
     */
    const CONFIGURATION_FILE = 'epb-client.properties';

    /**
     * The parameter key for the configuration base directory.
     *
     * @var string
     */
    const BASE_DIRECTORY = 'baseDirectory';

    /**
     * Creates a new naming context and add's it to the passed manager.
     *
     * @param \AppserverIo\Psr\Application\ManagerInterface $manager The manager to add the naming context to
     *
     * @return void
     */
    public static function visit(ManagerSettingsAwareInterface $manager)
    {

        // load the path to the web application
        $application = $manager->getApplication();
        $webappPath = $application->getWebappPath();

        // initialize the variable for the properties
        $properties = null;

        // load the configuration base directory
        if ($baseDirectory = $manager->getManagerSettings()->getBaseDirectory()) {
            // look for naming context properties in the manager's base directory
            $propertiesFile = DirectoryKeys::realpath(
                sprintf('%s/%s/%s', $webappPath, $baseDirectory, NamingContextFactory::CONFIGURATION_FILE)
            );

            // load the properties from the configuration file
            if (file_exists($propertiesFile)) {
                $properties = Properties::create()->load($propertiesFile);
            }
        }

        // create the initial context instance
        $initialContext = new InitialContext($properties);
        $initialContext->injectApplication($application);

        // set the initial context in the manager
        $manager->injectInitialContext($initialContext);
    }
}
