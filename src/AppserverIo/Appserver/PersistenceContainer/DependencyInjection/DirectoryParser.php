<?php

/**
 * \AppserverIo\Appserver\PersistenceContainer\DependencyInjection\DirectoryParser
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

namespace AppserverIo\Appserver\PersistenceContainer\DependencyInjection;

use AppserverIo\Psr\EnterpriseBeans\BeanContextInterface;

/**
 * Parser to parse a directory for annotated beans.
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
     * The bean context we want to parse the directories for.
     *
     * @var \AppserverIo\Psr\EnterpriseBeans\BeanContextInterface
     */
    protected $beanContext;

    /**
     * Inject the bean context instance.
     *
     * @param \AppserverIo\Psr\EnterpriseBeans\BeanContextInterface $beanContext The bean context instance
     *
     * @return void
     */
    public function injectBeanContext(BeanContextInterface $beanContext)
    {
        $this->beanContext = $beanContext;
    }

    /**
     * Returns the bean context instance.
     *
     * @return \AppserverIo\Psr\EnterpriseBeans\BeanContextInterface The bean context instance
     */
    public function getBeanContext()
    {
        return $this->beanContext;
    }

    /**
     * Returns the application context instance the bean context is bound to.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application context instance
     */
    public function getApplication()
    {
        return $this->getBeanContext()->getApplication();
    }

    /**
     * Parses the bean context's web application base directory for beans
     * that has to be registered in the object manager.
     *
     * @return void
     */
    public function parse()
    {

        // load the web application base directory
        $webappPath = $this->getBeanContext()->getWebappPath();

        // load the directories to be parsed
        $directories = array();

        // append the directory found in the servlet managers configuration
        /** @var \AppserverIo\Appserver\Core\Api\Node\DirectoryNode $directoryNode */
        foreach ($this->getBeanContext()->getDirectories() as $directoryNode) {
            // prepare the custom directory defined in the servlet managers configuration
            $customDir = $webappPath . DIRECTORY_SEPARATOR . ltrim($directoryNode->getNodeValue()->getValue(), DIRECTORY_SEPARATOR);

            // check if the directory exists
            if (is_dir($customDir)) {
                $directories[] = $customDir;
            }
        }

        // parse the directories for annotated servlets
        foreach ($directories as $directory) {
            $this->parseDirectory($directory);
        }
    }

    /**
     * Parses the passed directory for classes and instances that has to be registered
     * in the object manager.
     *
     * @param string $directory The directory to parse
     *
     * @return void
     */
    protected function parseDirectory($directory)
    {

        // check if we've found a valid directory
        if (is_dir($directory) === false) {
            return;
        }

        // load the object manager instance
        /** @var \AppserverIo\Psr\Di\ObjectManagerInterface $objectManager */
        $objectManager = $this->getApplication()->search('ObjectManagerInterface');

        // check directory for classes we want to register
        /** @var \AppserverIo\Appserver\Core\Api\DeploymentService $service */
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
                    /** \AppserverIo\Lang\Reflection\ClassInterface $reflectionClass */
                    $reflectionClass = $objectManager->getReflectionClass($className);

                    // load the descriptor class
                    $descriptorClass = $descriptor->getNodeValue()->getValue();

                    // load the object descriptor and add it to the object manager
                    /** \AppserverIo\Psr\Deployment\DescriptorInterface $objectDescriptor */
                    if (class_exists($descriptorClass)) {
                        if ($objectDescriptor = $descriptorClass::newDescriptorInstance()->fromReflectionClass($reflectionClass)) {
                            $objectManager->addObjectDescriptor($objectDescriptor);
                        }
                    }

                // if class can not be reflected continue with next class
                } catch (\Exception $e) {
                    // log an error message
                    $this->getApplication()->getInitialContext()->getSystemLogger()->error($e->__toString());

                    // proceed with the next bean
                    continue;
                }
            }
        }
    }
}
