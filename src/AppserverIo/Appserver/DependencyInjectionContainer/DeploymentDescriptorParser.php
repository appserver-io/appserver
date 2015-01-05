<?php

/**
 * AppserverIo\Appserver\DependencyInjectionContainer\DeploymentDescriptorParser
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage DependencyInjectionContainer
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\DependencyInjectionContainer;

use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Lang\Reflection\ReflectionClass;

/**
 * Parser to parse a deployment descriptor for beans or servlets.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage DependencyInjectionContainer
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */
class DeploymentDescriptorParser
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
     * Parses the passed deployment descriptor file for classes and instances that has
     * to be registered in the object manager.
     *
     * @param string      $deploymentDescriptor The deployment descriptor we want to parse
     * @param string|null $xpath                The XPath expression used to parse the deployment descriptor
     *
     * @return void
     */
    public function parse($deploymentDescriptor, $xpath = '/')
    {

        // query whether we found epb.xml deployment descriptor file
        if (file_exists($deploymentDescriptor) === false) {
            return;
        }

        // load the object manager instance
        $objectManager = $this->getApplication()->search('ObjectManagerInterface');

        // load the application config
        $config = new \SimpleXMLElement(file_get_contents($deploymentDescriptor));

        // intialize the session beans by parsing the nodes
        foreach ($config->xpath($xpath) as $node) {

            // iterate over all configured descriptors and try to load object description
            foreach ($objectManager->getConfiguredDescriptors() as $descriptor) {

                try {

                    // load the descriptor class
                    $descriptorClass = $descriptor->getNodeValue()->getValue();

                    // load the object descriptor and add it to the object manager
                    if ($objectDescriptor = $descriptorClass::newDescriptorInstance()->fromDeploymentDescriptor($node)) {
                        $objectManager->addObjectDescriptor($objectDescriptor, true);
                    }

                    // proceed with the next descriptor
                    continue;

                } catch (\Exception $e) { // if class can not be reflected continue with next class

                    // log an error message
                    $this->getApplication()->getInitialContext()->getSystemLogger()->error($e->__toString());

                    // proceed with the next descriptor
                    continue;
                }
            }
        }
    }
}
