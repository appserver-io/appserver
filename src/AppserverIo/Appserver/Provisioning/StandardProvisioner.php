<?php

/**
 * \AppserverIo\Appserver\Provisioning\StandardProvisioner
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

namespace AppserverIo\Appserver\Provisioning;

use AppserverIo\Appserver\Core\Api\Node\ProvisionNode;
use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * Standard provisioning functionality.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class StandardProvisioner extends AbstractProvisioner
{

    /**
     * Path the to appserver's PHP executable on UNIX systems.
     *
     * @var string
     */
    const PHP_EXECUTABLE_UNIX = '/bin/php';

    /**
     * Path the to appserver's PHP executable on Windows systems.
     *
     * @var string
     */
    const PHP_EXECUTABLE_WIN = '/php/php.exe';

    /**
     * Provisions all web applications.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     */
    public function provision(ApplicationInterface $application)
    {

        // check if the webapps directory exists
        if (is_dir($webappPath = $application->getWebappPath())) {
            // load the service instance
            $service = $this->getService();

            // prepare the glob expression with the application's directories to parse
            $applicationDirectories = sprintf('%s/{WEB-INF,META-INF}/provision.xml', $webappPath);

            // iterate through all provisioning files (provision.xml), validate them and attach them to the configuration
            $configurationService = $this->getInitialContext()->newService('AppserverIo\Appserver\Core\Api\ConfigurationService');
            foreach ($service->globDir($applicationDirectories, GLOB_BRACE) as $provisionFile) {
                // validate the file, but skip it if validation fails
                if (!$configurationService->validateFile($provisionFile, null)) {
                    $errorMessages = $configurationService->getErrorMessages();
                    $systemLogger = $this->getInitialContext()->getSystemLogger();
                    $systemLogger->error(reset($errorMessages));
                    $systemLogger->critical(sprintf('Will skip reading provisioning steps in %s, provisioning might not have been done.', $provisionFile));
                    continue;
                }

                // load the path of web application
                $webappPath = new \SplFileInfo(dirname(dirname($provisionFile)));

                // load the provisioning configuration
                $provisionNode = new ProvisionNode();
                $provisionNode->initFromFile($provisionFile);

                // query whether we've a datasource configured or not
                if ($datasource = $provisionNode->getDatasource()) {
                    // try to load the datasource from the system configuration
                    $datasourceNode = $service->findByName($datasource->getName());
                    // try to inject the datasource node if available
                    if ($datasourceNode != null) {
                        $provisionNode->injectDatasource($datasourceNode);
                    }
                }

                /* Re-provision the provision.xml (reinitialize).
                 *
                 * ATTENTION: The re-provisioning is extremely important, because
                 * this allows dynamic replacement of placeholders by using the
                 * XML file as a template that will reinterpreted with the PHP
                 * interpreter!
                 */
                $provisionNode->reprovision($provisionFile);

                // execute the provisioning workflow
                $this->executeProvision($application, $provisionNode, $webappPath);
            }
        }
    }

    /**
     * Executes the passed applications provisioning workflow.
     *
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface  $application   The application instance
     * @param \AppserverIo\Appserver\Core\Api\Node\ProvisionNode $provisionNode The file with the provisioning information
     * @param \SplFileInfo                                       $webappPath    The path to the webapp folder
     *
     * @return void
     */
    protected function executeProvision(ApplicationInterface $application, ProvisionNode $provisionNode, \SplFileInfo $webappPath)
    {

        // load the steps from the configuration
        $stepNodes = $provisionNode->getInstallation()->getSteps();
        if (!is_array($stepNodes)) {
            return;
        }

        // execute all steps found in the configuration
        foreach ($stepNodes as $stepNode) {
            try {
                // create a new reflection class of the step
                $reflectionClass = new \ReflectionClass($stepNode->getType());
                $step = $reflectionClass->newInstance();

                // try to inject the datasource node if available
                if ($datasourceNode = $provisionNode->getDatasource()) {
                    $step->injectDataSourceNode($datasourceNode);
                }

                // inject all other information
                $step->injectStepNode($stepNode);
                $step->injectApplication($application);
                $step->injectService($this->getService());
                $step->injectWebappPath($webappPath->getPathname());
                $step->injectInitialContext($this->getInitialContext());
                $step->injectPhpExecutable($this->getAbsolutPathToPhpExecutable());

                // execute the step finally
                $step->start(PTHREADS_INHERIT_NONE|PTHREADS_INHERIT_CONSTANTS);
                $step->join();

            } catch (\Exception $e) {
                $this->getInitialContext()->getSystemLogger()->error($e->__toString());
            }
        }
    }

    /**
     * Returns the absolute path to the appservers PHP executable.
     *
     * @return string The absolute path to the appserver PHP executable
     */
    public function getAbsolutPathToPhpExecutable()
    {
        $executable = StandardProvisioner::PHP_EXECUTABLE_UNIX;
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // we have a different executable on Windows systems
            $executable = StandardProvisioner::PHP_EXECUTABLE_WIN;
        }
        return $this->getService()->realpath($executable);
    }
}
