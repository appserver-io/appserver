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

use AppserverIo\Psr\Application\ManagerInterface;
use AppserverIo\Appserver\Core\Api\Node\ParserNodeInterface;

/**
 * Abstract parser implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
abstract class AbstractParser implements ParserInterface
{

    /**
     * The object manager we want to parse the deployment descriptor for.
     *
     * @var \AppserverIo\Psr\Application\ManagerInterface
     */
    protected $manager;

    /**
     * The parser configuration.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ParserNodeInterface
     */
    protected $configuration;

    /**
     * Initializes the instance with the parser configuration and manager instance.
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\ParserNodeInterface $configuration The parser configuration
     * @param \AppserverIo\Psr\Application\ManagerInterface            $manager       The object manager instance
     */
    public function __construct(ParserNodeInterface $configuration, ManagerInterface $manager)
    {
        $this->manager = $manager;
        $this->configuration = $configuration;
    }

    /**
     * Returns the manager instance.
     *
     * @return \AppserverIo\Psr\Application\ManagerInterface The manager instance
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * The parser configuration.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\ParserNodeInterface The parser configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Returns the application context instance the bean context is bound to.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application context instance
     */
    public function getApplication()
    {
        return $this->getManager()->getApplication();
    }

    /**
     * Returns the configured directories.
     *
     * @return array The directories
     */
    public function getDirectories()
    {
        return $this->getConfiguration()->getDirectoriesAsArray();
    }

    /**
     * Returns the configured descriptor classes.
     *
     * @return array The descriptors
     */
    public function getDescriptors()
    {
        return $this->getManager()->getDescriptors();
    }
}
