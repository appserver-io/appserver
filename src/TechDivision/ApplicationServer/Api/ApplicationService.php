<?php

/**
 * TechDivision\ApplicationServerApi\ApplicationService
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api;

use TechDivision\ApplicationServer\Api\AbstractService;
use TechDivision\ApplicationServer\Api\ContainerService;
use TechDivision\ApplicationServer\Configuration;

/**
 * This services provides access to the deplyed applications and allows
 * to deploy new applications or remove a deployed one.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class ApplicationService extends AbstractService
{

    /**
     * XPath expression for the container configurations.
     *
     * @var string
     */
    const XPATH_CONTAINERS = '/appserver/containers/container';

    /**
     * XPath expression for the application configurations.
     *
     * @var string
     */
    const XPATH_APPLICATIONS = '/container/apps';

    /**
     * XPath expression for the application configurations.
     *
     * @var string
     */
    const XPATH_APPLICATION = '/container/apps/app';

    const XPATH_APPLICATIONS_APPLICATION = '/apps/app';

    const NODE_NAME_APPLICATION = 'app';

    const NODE_NAME_APPLICATIONS = 'apps';

    /**
     * Returns all deployed applications.
     *
     * @return array<\stdClass> All deployed applications
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::findAll()
     */
    public function findAll()
    {
        $applications = array();
        $applicationIds = 1;
        $containerIds = 1;

        $containerService = $this->newService('TechDivision\ApplicationServer\Api\ContainerService');
        $hostService = $this->newService('TechDivision\ApplicationServer\Api\HostService');

        $result = new \stdClass();

        foreach ($this->getSystemConfiguration()->getChilds(self::XPATH_CONTAINERS) as $containerConfiguration) {

            $host = $hostService->loadByContainerId($containerIds);

            foreach ($containerConfiguration->getChilds(self::XPATH_APPLICATION) as $applicationConfiguration) {

                $applicationConfiguration->setData(self::PRIMARY_KEY, $applicationIds);
                $application = $this->normalize($applicationConfiguration);

                // if the application is in the same base directory AND has the same name, then it's the same application
                $uniqueKey = $host->host->app_base . DIRECTORY_SEPARATOR . $application->app->name;

                if (array_key_exists($uniqueKey, $applications)) {
                    $applications[$uniqueKey]->app->container_ids[] = $containerIds;
                } else {
                    $applications[$uniqueKey] = $application;
                    $applications[$uniqueKey]->app->container_ids = array();
                    $applications[$uniqueKey]->app->container_ids[] = $containerIds;
                    $applicationIds ++;
                }
            }
            $containerIds ++;
        }

        $result->apps = array_values($applications);

        return $result;
    }

    /**
     * Returns all deployed applications having the passed application base directory.
     *
     * @param string $appBase The application base directory to return the applications for
     * @return array<\stdClass> The deployed applications having the passed application base directory
     */
    public function findAllByAppBase($appBase)
    {

        $applications = array();
        $result = \stdClass();

        foreach ($this->findAll()->apps as $application) {
            if ($this->getAppBase($application->app->id) == $appBase) {
                $applications[] = $application;
            }
        }

        $result->apps = $applications;

        return $result;
    }

    /**
     * Returns the application with the passed id.
     *
     * @param string $id
     *            ID of the application to return
     * @return \stdClass The application with the ID passed as parameter
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::load($id)
     */
    public function load($id)
    {
        $result = new \stdClass();
        foreach ($this->findAll()->apps as $application) {
            if ($application->app->{self::PRIMARY_KEY} == $id) {
                return $application;
            }
        }
    }

    /**
     * Returns the application with the passed name.
     *
     * @param string $id
     *            ID of the application to return
     * @return \stdClass The application with the name passed as parameter
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::load($id)
     */
    public function loadByName($name)
    {
        $result = new \stdClass();
        foreach ($this->findAll()->apps as $application) {
            if ($application->app->name == $name) {
                return $application;
            }
        }
    }

    /**
     * Creates a new application based on the passed information.
     *
     * @param \stdClass $stdClass
     *            The data with the information for the application to be created
     * @return void
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::create(\stdClass $stdClass)
     */
    public function create(\stdClass $stdClass)
    {
        $systemConfiguration = $this->getSystemConfiguration();

        foreach ($systemConfiguration->getChilds(self::XPATH_CONTAINERS) as $containerConfiguration) {

            // initialize the array that contains the existing applications
            $applications = array();

            $applicationsConfiguration = $this->newApplicationsForContainer($containerConfiguration);

            foreach ($applicationsConfiguration->getChilds(self::XPATH_APPLICATIONS_APPLICATION) as $applicationConfiguration) {
                if (in_array($applicationConfiguration->getId(), $applications) === false) {
                    $applications[] = $applicationConfiguration->getId();
                }
            }

            // sort the application IDs and create a new unique ID
            asort($applications);
            $id = end($applications) + 1;

            $application = $this->newApplication($id, $stdClass->name);
            $applicationsConfiguration->addChild($application);
        }

        $this->getInitialContext()->setSystemConfiguration($systemConfiguration);
    }

    /**
     * Creates a applications configuration node for the container with the passed ID
     * is NOT yet available.
     *
     * @param \TechDivision\ApplicationServer\Configuration $containerConfiguration
     *            The container to create the applications node for
     */
    public function newApplicationsForContainer($containerConfiguration)
    {
        if ($containerConfiguration->getChilds(self::XPATH_APPLICATIONS) == null) {
            $containerConfiguration->addChild($this->newApplications());
        }
        return $containerConfiguration->getChild(self::XPATH_APPLICATIONS);
    }

    /**
     * Creates a new application configuration node with the application data and
     * returns it.
     *
     * @param string $id
     *            The unique application ID to be used
     * @param string $name
     *            The unique application name to be used
     * @return \TechDivision\ApplicationServer\Configuration The application configuration instance
     */
    public function newApplication($id, $name)
    {
        $data = array(
            self::PRIMARY_KEY => $id,
            'name' => $name
        );
        return $this->newConfiguration(self::NODE_NAME_APPLICATION, $data);
    }

    /**
     * Creates a new applications configuration node and returns it.
     *
     * @return \TechDivision\ApplicationServer\Configuration The applications configuration instance
     */
    public function newApplications()
    {
        return $this->newConfiguration(self::NODE_NAME_APPLICATIONS);
    }

    /**
     * Updates the application with the passed data.
     *
     * @param \stdClass $stdClass
     *            The application data to update
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::update(\stdClass $stdClass)
     */
    public function update(\stdClass $stdClass)
    {}

    /**
     * (non-PHPdoc)
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::delete()
     */
    public function delete($id)
    {}

    /**
     * Returns the base directory for the application with
     * the passed ID.
     *
     * @param string $id The ID of the application to return the base directory for
     * @return string The application's base directory, /opt/appserver/webapps by default
     * @throws \Exception Is thrown if the passed application ID is NOT valid
     */
    public function getAppBase($id)
    {

        // load the container service and the application with the passed ID
        $containerService = $this->newService('TechDivision\ApplicationServer\Api\ContainerService');
        $application = $this->load($id)->app;

        // iterate over the application's container and return the base directory of the first container found
        foreach ($application->container_ids as $containerId) {
            return $containerService->getAppBase($containerId);
        }

        throw new \Exception("Can't find application base directory for application with ID: $id");
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\Api\ContainerService::getServerSoftware()
     */
    public function getServerSoftware($id)
    {

        // load the container service and the application with the passed ID
        $containerService = $this->newService('TechDivision\ApplicationServer\Api\ContainerService');
        $application = $this->load($id)->app;

        // iterate over the application's container and return the base directory of the first container found
        foreach ($application->container_ids as $containerId) {
            return $containerService->getServerSoftware($containerId);
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\Api\ContainerService::getServerAdmin()
     */
    public function getServerAdmin($id)
    {

        // load the container service and the application with the passed ID
        $containerService = $this->newService('TechDivision\ApplicationServer\Api\ContainerService');
        $application = $this->load($id)->app;

        // iterate over the application's container and return the base directory of the first container found
        foreach ($application->container_ids as $containerId) {
            return $containerService->getServerAdmin($containerId);
        }
    }

    /**
     * Return's the path to the web application directory for the
     * application with the passed ID.
     *
     * @param string $id The ID of the application to return the web application directory for
     * @return string The path to the web application
     */
    public function getWebappPath($id)
    {
        $application = $this->load($id)->app;
        return $this->getAppBase($id) . DIRECTORY_SEPARATOR . $application->name;
    }
}