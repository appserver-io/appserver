<?php

/**
 * AppserverIo\Appserver\DependencyInjectionContainer\DirectoryParser
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

namespace AppserverIo\Appserver\DependencyInjectionContainer;

use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * Parser to parse a directory for annotated beans or servlets.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class DirectoryParser
{

    /**
     * Inject the application instance.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     */
    public function injectApplication(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * Returns the application instance.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application instance
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Parses the passed directory for classes and instances that has to be registered
     * in the object manager.
     *
     * @param string $directory The directory to parse
     *
     * @return void
     */
    public function parse($directory)
    {

        // check if we've found a valid directory
        if (is_dir($directory) === false) {
            return;
        }

        // load the object manager instance
        $objectManager = $this->getApplication()->search('ObjectManagerInterface');

        // check directory for classes we want to register
        $service = $this->getApplication()->newService('AppserverIo\Appserver\Core\Api\DeploymentService');
        $phpFiles = $service->globDir($directory . DIRECTORY_SEPARATOR . '*.php');

        // iterate all php files
        foreach ($phpFiles as $phpFile) {
            // iterate over all configured descriptors and try to load object description
            foreach ($objectManager->getConfiguredDescriptors() as $descriptor) {
                try {
                    // cut off the META-INF directory and replace OS specific directory separators
                    $relativePathToPhpFile = str_replace(DIRECTORY_SEPARATOR, '\\', str_replace($directory, '', $phpFile));

                    // now cut off the .php extension
                    $className = substr($relativePathToPhpFile, 0, -4);

                    // we need a reflection class to read the annotations
                    $reflectionClass = $objectManager->getReflectionClass($className);

                    // load the descriptor class
                    $descriptorClass = $descriptor->getNodeValue()->getValue();

                    // load the object descriptor and add it to the object manager
                    if ($objectDescriptor = $descriptorClass::newDescriptorInstance()->fromReflectionClass($reflectionClass)) {
                        $objectManager->addObjectDescriptor($objectDescriptor);
                    }

                // if class can not be reflected continue with next class
                } catch (\Exception $e) {
                    // log an error message
                    $this->getApplication()->getInitialContext()->getSystemLogger()->error($e->__toString());

                    // proceed with the nexet bean
                    continue;
                }
            }
        }
    }
}
