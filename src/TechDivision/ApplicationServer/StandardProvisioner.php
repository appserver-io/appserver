<?php

/**
 * TechDivision\ApplicationServer\StandardProvisioner
 *
 * PHP version 5
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Johann Zelger <j.zelger@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\Api\Node\ProvisionNode;
use TechDivision\ApplicationServer\Interfaces\ProvisionerInterface;

/**
 * Standard provisioning functionality.
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Johann Zelger <j.zelger@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
class StandardProvisioner implements ProvisionerInterface
{

    /**
     * Path the to appservers PHP executable.
     *
     * @var string
     */
    const PHP_EXECUTABLE = '/bin/php';

    /**
     * The containers base directory.
     *
     * @var string
     */
    protected $service;

    /**
     * The initial context instance.
     *
     * @var \TechDivision\ApplicationServer\InitialContext
     */
    protected $initialContext;

    /**
     * Contructor
     *
     * @param \TechDivision\ApplicationServer\InitialContext $initialContext The initial context instance
     */
    public function __construct($initialContext)
    {
        // add initialContext
        $this->initialContext = $initialContext;
        // init API service to use
        $this->service = $this->newService('TechDivision\ApplicationServer\Api\ProvisioningService');
    }

    /**
     * Provisions all web applications.
     *
     * @return void
     */
    public function provision()
    {
        // check if deploy dir exists
        if (is_dir($this->getWebappsDir())) {

            // init file iterator on webapps directory
            $directory = new \RecursiveDirectoryIterator($this->getWebappsDir());
            $iterator = new \RecursiveIteratorIterator($directory);

            // Iterate through all provisioning files (provision.xml) and attach them to the configuration
            foreach (new \RegexIterator($iterator, '/^.*\/(META-INF|WEB-INF)\/provision.xml$/') as $provisionFile) {

                // if we don't find a provisioning file
                if ($provisionFile->isFile() === false) {
                    continue;
                }

                // create the webapp path info
                $webappPath = new \SplFileInfo(dirname($provisionFile->getPath()));

                // execute the provisioning workflow
                $this->executeProvision($provisionFile, $webappPath);
            }
        }
    }

    /**
     * Executes the passed applications provisioning workflow.
     *
     * @param \SplFileInfo $provisionFile The file with the provisioning information
     * @param \SplFileInfo $webappPath    The path to the webapp folder
     *
     * @return void
     */
    protected function executeProvision(\SplFileInfo $provisionFile, \SplFileInfo $webappPath)
    {

        // load the service instance
        $service = $this->getService();

        // load the provisioning configuration
        $provisionNode = new ProvisionNode();
        $provisionNode->initFromFile($provisionFile->getPathname());

        // try to load the datasource from the system configuration
        $datasourceNode = $service->findByName(
            $provisionNode->getDatasource()->getName()
        );

        // try to inject the datasource node if available
        if ($datasourceNode != null) {
            $provisionNode->injectDatasource($datasourceNode);
        }

        /* Reprovision the provision.xml (reinitialize).
         *
         * ATTENTION: The reprovisioning is extremely important, because
         * this allows dynamic replacment of placeholders by using the
         * XML file as a template that will reinterpreted with the PHP
         * interpreter!
         */
        $provisionNode->reprovision($provisionFile->getPathname());

        // load the steps from the configuration
        $stepNodes = $provisionNode->getInstallation()->getSteps();

        // execute all steps found in the configuration
        foreach ($stepNodes as $stepNode) {

            try {

                // create a new reflection class of the step
                $reflectionClass = new \ReflectionClass($stepNode->getType());
                $step = $reflectionClass->newInstance();

                // try to inject the datasource node if available
                if ($datasourceNode != null) {
                    $step->injectDataSourceNode($datasourceNode);
                }

                // inject all other information
                $step->injectService($service);
                $step->injectStepNode($stepNode);
                $step->injectWebappPath($webappPath);
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
     * Returns the servers deploy directory
     *
     * @return string
     */
    public function getWebappsDir()
    {
        return $this->getService()->getWebappsDir();
    }

    /**
     * Returns the absolute path to the appservers PHP executable.
     *
     * @return string The absolute path to the appserver PHP executable
     */
    public function getAbsolutPathToPhpExecutable()
    {
        return $this->getService()->realpath(StandardProvisioner::PHP_EXECUTABLE);
    }

    /**
     * (non-PHPdoc)
     *
     * @param string $className The API service class name to return the instance for
     *
     * @return \TechDivision\ApplicationServer\Api\ServiceInterface The service instance
     * @see \TechDivision\ApplicationServer\InitialContext::newService()
     */
    public function newService($className)
    {
        return $this->getInitialContext()->newService($className);
    }

    /**
     * Returns the inital context instance.
     *
     * @return \TechDivision\ApplicationServer\InitialContext The initial context instance
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * Returns the service instance to use.
     *
     * @return \TechDivision\ApplicationServer\Api\ServiceInterface $service The service to use
     */
    public function getService()
    {
        return $this->service;
    }
}
