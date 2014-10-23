<?php

/**
 * TechDivision\ApplicationServer\AbstractManagerFactory
 *
 * PHP version 5
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Bernhard Wick <b.wick@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace TechDivision\ApplicationServer;

use TechDivision\Storage\GenericStackable;
use TechDivision\Application\Interfaces\ContextInterface;
use TechDivision\Application\Interfaces\ApplicationInterface;
use TechDivision\Application\Interfaces\ManagerConfigurationInterface;

/**
 * Abstract manager factory used to create a application manager instances.
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Bernhard Wick <b.wick@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
abstract class AbstractManagerFactory extends \Thread
{

    /**
     * The container for the instances.
     *
     * @var \TechDivision\Storage\GenericStackable
     */
    protected $instances;

    /**
     * The application instance.
     *
     * @var \TechDivision\Application\Interfaces\ApplicationInterface
     */
    protected $application;

    /**
     * The manager configuration.
     *
     * @var \TechDivision\Application\Interfaces\ManagerConfigurationInterface
     */
    protected $managerConfiguration;

    /**
     * The initial context instance.
     *
     * @var \TechDivision\Application\Interfaces\ContextInterface
     */
    protected $initialContext;

    /**
     * Injects the container for the instances.
     *
     * @param \TechDivision\Storage\GenericStackable $instances The container for the instances
     *
     * @return void
     */
    public function injectInstances(GenericStackable $instances)
    {
        $this->instances = $instances;
    }

    /**
     * Injects the container for the instances.
     *
     * @param \TechDivision\Application\Interfaces\ApplicationInterface $application The container for the instances
     *
     * @return void
     */
    public function injectApplication(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * Injects the container for the instances.
     *
     * @param \TechDivision\Application\Interfaces\ManagerConfigurationInterface $managerConfiguration The container for the instances
     *
     * @return void
     */
    public function injectManagerConfiguration(ManagerConfigurationInterface $managerConfiguration)
    {
        $this->managerConfiguration = $managerConfiguration;
    }

    /**
     * Injects the initial context instance.
     *
     * @param \TechDivision\Application\Interfaces\ContextInterface $initialContext The initial context instance
     *
     * @return void
     */
    public function injectInitialContext(ContextInterface $initialContext)
    {
        $this->initialContext = $initialContext;
    }

    /**
     * Return the new instance.
     *
     * @return \TechDivision\ServletEngine\StandardSessionManager The instance
     */
    public function newInstance()
    {
        return $this->synchronized(function ($self) {
            $instances = $self->instances;
            return $instances[sizeof($instances) - 1];
        }, $this);
    }
}
