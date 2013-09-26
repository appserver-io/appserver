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
    const XPATH_APPLICATION = '/container/applications/application';

    /**
     * Returns all deployed applications.
     *
     * @return array<\stdClass> An array with all deployed applications
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::findAll()
     */
    public function findAll()
    {
        $applications = array();
        $applicationIds = 1;
        $containerIds = 1;
        foreach ($this->getSystemConfiguration()->getChilds(self::XPATH_CONTAINERS) as $containerConfiguration) {
            foreach ($containerConfiguration->getChilds(self::XPATH_APPLICATION) as $applicationConfiguration) {

                $applicationConfiguration->setData(self::PRIMARY_KEY, $applicationIds ++);

                $application = $this->normalize($applicationConfiguration);

                if (array_key_exists($application->name, $applications)) {
                    $applications[$application->name]->container_ids[] = $containerIds;
                } else {
                    $applications[$application->name] = $application;
                }
            }
            $containerIds ++;
        }
        return array(
            'apps' => array_values($applications)
        );
    }

    /**
     * Returns the application with the passed name.
     *
     * @param string $id
     *            ID of the application to return
     * @return \stdClass The application with the name passed as parameter
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::load($id)
     */
    public function load($id)
    {
        $applications = $this->findAll();
        foreach ($applications['applications'] as $application) {
            if ($application->{self::PRIMARY_KEY} == $id) {
                return array(
                    'app' => $application
                );
            }
        }
    }

    /**
     * Creates a new application based on the passed information.
     *
     * @param \stdClass $stdClass
     *            The data with the information for the application to be created
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::create($id)
     */
    public function create($stdClass)
    {
        $configuration = $this->newInstance('TechDivision\ApplicationServer\Configuration');
        $configuration->setNodeName('application');
        $configuration->setData('name', $stdClass->name);

        $applicationsConfiguration = $this->newInstance('TechDivision\ApplicationServer\Configuration');
        $applicationsConfiguration->setNodeName('applications');
        $applicationsConfiguration->addChild($configuration);

        $applications = array();

        foreach ($this->getSystemConfiguration()->getChilds(self::XPATH_CONTAINERS) as $containerConfiguration) {
            foreach ($containerConfiguration->getChilds(self::XPATH_APPLICATION) as $applicationConfiguration) {
                if (array_key_exists($applicationConfiguration->getName(), $applications) === false) {
                    $applications[$applicationConfiguration->getName()] = $applicationConfiguration;
                }
            }
        }

        foreach ($applications as $config) {
            $applicationsConfiguration->addChild($config);
        }

        $this->mergeInSystemConfiguration($applicationsConfiguration);
    }

    /**
     * Merge the passed application configurations into the system configuration
     * and refreshes it in the initial context, to make it available to the API.
     *
     * @param \TechDivision\ApplicationServer\Configuration $applicationConfigurations
     *            The application configurations to merge into the system configuration
     */
    public function mergeInSystemConfiguration(Configuration $applicationConfiguration)
    {
        $systemConfiguration = $this->getSystemConfiguration();
        foreach ($systemConfiguration->getChilds(self::XPATH_CONTAINERS) as $containerConfiguration) {
            $containerConfiguration->removeChilds('/container/applications');
            $containerConfiguration->addChild($applicationConfiguration);
        }

        $this->getInitialContext()->setSystemConfiguration($systemConfiguration);
    }

    /**
     * Updates the application with the passed data.
     *
     * @param \stdClass $stdClass
     *            The application data to update
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::update($id)
     */
    public function update($stdClass)
    {}

    /**
     * Deletes the application with passed ID.
     *
     * @param string $id
     *            The ID of the application to be deleted
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::delete($id)
     */
    public function delete($id)
    {}
}