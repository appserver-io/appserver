<?php

/**
 * AppserverIo\Appserver\Core\StandardProvisioner
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

use AppserverIo\Appserver\Core\Api\Node\ProvisionNode;

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
     * @return void
     */
    public function provision()
    {

        // check if the webapps directory exists
        if (is_dir($webappsPath = $this->getWebappsDir())) {
            // load the service instance
            $service = $this->getService();

            // iterate through all provisioning files (provision.xml), validate them and attach them to the configuration
            $configurationService = $this->getInitialContext()->newService('AppserverIo\Appserver\Core\Api\ConfigurationService');
            foreach ($service->globDir($webappsPath . '/*/{WEB-INF,META-INF}/provision.xml', GLOB_BRACE) as $provisionFile) {
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

                // try to load the datasource from the system configuration
                $datasourceNode = $service->findByName(
                    $provisionNode->getDatasource()->getName()
                );

                // try to inject the datasource node if available
                if ($datasourceNode != null) {
                    $provisionNode->injectDatasource($datasourceNode);
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
                $this->executeProvision($provisionNode, $webappPath);
            }
        }
    }

    /**
     * Executes the passed applications provisioning workflow.
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\ProvisionNode $provisionNode The file with the provisioning information
     * @param \SplFileInfo                                       $webappPath    The path to the webapp folder
     *
     * @return void
     */
    protected function executeProvision(ProvisionNode $provisionNode, \SplFileInfo $webappPath)
    {

        // load the steps from the configuration
        $stepNodes = $provisionNode->getInstallation()->getSteps();

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
                $step->injectService($this->getService());
                $step->injectWebappPath($webappPath->getPathname());
                $step->injectInitialContext($this->getInitialContext());
                $step->injectPhpExecutable($this->getAbsolutPathToPhpExecutable());

                // execute the step finally
                $step->start();
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
