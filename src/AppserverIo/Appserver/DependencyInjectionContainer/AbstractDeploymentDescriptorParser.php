<?php

/**
 * \AppserverIo\Appserver\DependencyInjectionContainer\AbstractParser
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

use AppserverIo\Appserver\Core\Utilities\AppEnvironmentHelper;

/**
 * Abstract deployment descriptor parser implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
abstract class AbstractDeploymentDescriptorParser extends AbstractParser
{

    /**
     * Loads the environment aware deployment descriptors for the given manager.
     *
     * @return string[] The array with the deployment descriptors
     */
    protected function loadDeploymentDescriptors()
    {

        // load the web application base directory
        $webappPath = $this->getApplication()->getWebappPath();

        // load the deployment service
        /** @var \AppserverIo\Appserver\Core\Api\DeploymentService $deploymentService */
        $deploymentService = $this->getApplication()->newService('AppserverIo\Appserver\Core\Api\DeploymentService');

        // prepare the array with the deployment descriptor with the fallback deployment descriptor
        $deploymentDescriptors = array($deploymentService->getConfdDir(sprintf('%s.xml', $descriptorName = $this->getConfiguration()->getDescriptorName())));

        // try to locate deployment descriptors in the configured directories
        foreach ($this->getDirectories() as $directory) {
            // make sure we've a path, relative to the webapp directory
            $strippedDirectory = ltrim(str_replace($webappPath, '', $directory), DIRECTORY_SEPARATOR);

            // add the environment aware deployment descriptor to the array
            array_push(
                $deploymentDescriptors,
                AppEnvironmentHelper::getEnvironmentAwareGlobPattern($webappPath, $strippedDirectory . DIRECTORY_SEPARATOR . $descriptorName)
            );
        }


        // return the loaded deployment descriptors
        return $deploymentDescriptors;
    }
}
