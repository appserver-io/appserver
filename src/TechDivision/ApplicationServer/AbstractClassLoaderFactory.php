<?php

/**
 * TechDivision\ApplicationServer\AbstractClassLoaderFactory
 *
 * PHP version 5
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <tw@techdivision.com>
 * @author    Johann Zelger <jz@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace TechDivision\ApplicationServer;

use TechDivision\Storage\GenericStackable;
use TechDivision\Application\Interfaces\ApplicationInterface;
use \TechDivision\ApplicationServer\Api\Node\ClassLoaderNodeInterface;
use TechDivision\Application\Interfaces\ContextInterface;

/**
 * Abstract class loader factory used to create class loader instances.
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Bernhard Wick <b.wick@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
abstract class AbstractClassLoaderFactory extends \Thread
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
     * The class loader configuration.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\ClassLoaderNodeInterface
     */
    protected $configuration;

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
     * Injects the application instance.
     *
     * @param \TechDivision\Application\Interfaces\ApplicationInterface $application The application instance
     *
     * @return void
     */
    public function injectApplication(ApplicationInterface $application)
    {
        $this->application = $application;
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
     * Injects the class loader configuration.
     *
     * @param \TechDivision\ApplicationServer\Api\Node\ClassLoaderNodeInterface $configuration The class loader configuration
     *
     * @return void
     */
    public function injectClassLoaderConfiguration(ClassLoaderNodeInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Return the new class loader instance.
     *
     * @return object The class loader instance
     */
    public function newInstance()
    {
        return $this->synchronized(function ($self) {
            $instances = $self->instances;
            return $instances[sizeof($instances) - 1];
        }, $this);
    }
}
