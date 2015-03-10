<?php

/**
 * \AppserverIo\Appserver\Core\AbstractNormalizer
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

namespace AppserverIo\Appserver\Core\Api;

use AppserverIo\Appserver\Core\InitialContext;

/**
 * Normalizes configuration nodes to \stdClass instances.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
abstract class AbstractNormalizer implements NormalizerInterface
{

    /**
     * The initial context instance.
     *
     * @var \AppserverIo\Appserver\Core\InitialContext;
     */
    protected $initialContext;

    /**
     * Initializes the normalizer with the initial context.
     *
     * @param InitialContext   $initialContext The initial context instance
     * @param ServiceInterface $service        The service to normalize for
     */
    public function __construct(InitialContext $initialContext, ServiceInterface $service)
    {
        $this->initialContext = $initialContext;
        $this->service = $service;
    }

    /**
     * (non-PHPdoc)
     *
     * @return InitialContext
     * @see NormalizerInterface::getInitialContext()
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * (non-PHPdoc)
     *
     * @return ServiceInterface
     * @see NormalizerInterface::getService()
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * (non-PHPdoc)
     *
     * @param string $className The fully qualified class name to return the instance for
     * @param array  $args      Arguments to pass to the constructor of the instance
     *
     * @return InitialContext
     * @see InitialContext::newInstance()
     */
    public function newInstance($className, array $args = array())
    {
        return $this->getInitialContext()->newInstance($className, $args);
    }
}
