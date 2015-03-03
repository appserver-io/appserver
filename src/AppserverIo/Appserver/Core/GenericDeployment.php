<?php

/**
 * \AppserverIo\Appserver\Core\GenericDeployment
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

namespace AppserverIo\Appserver\Core;

/**
 * Generic deployment implementation for web applications.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class GenericDeployment extends AbstractDeployment
{

    /**
     * Initializes the available applications and adds them to the container.
     *
     * @return void
     * @see \AppserverIo\Psr\Deployment\DeploymentInterface::deploy()
     */
    public function deploy()
    {

        // load the container and initial context instance
        $container = $this->getContainer();

        // load the context instances for this container
        $contextInstances = $this->getDeploymentService()->loadContextInstancesByContainer($container);

        // gather all the deployed web applications
        foreach ($contextInstances as $context) {
            // try to load the application factory
            if ($applicationFactory = $context->getFactory()) {
                // use the factory if available
                $applicationFactory::visit($container, $context);
            } else {
                // if not, try to instantiate the application directly
                $applicationType = $context->getType();
                $container->addApplication(new $applicationType($context));
            }
        }
    }
}
