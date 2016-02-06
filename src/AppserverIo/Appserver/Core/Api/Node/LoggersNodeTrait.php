<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\LoggersNodeTrait
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
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * Abstract node that a contexts logger nodes.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
trait LoggersNodeTrait
{

    /**
     * The context's logger configuration.
     *
     * @var array
     * @AS\Mapping(nodeName="loggers/logger", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\LoggerNode")
     */
    protected $loggers = array();

    /**
     * Sets the context's logger configuration.
     *
     * @param array $loggers The context's logger configuration
     *
     * @return void
     */
    public function setLoggers($loggers)
    {
        $this->loggers = $loggers;
    }

    /**
     * Returns the context's logger configuration.
     *
     * @return array The context's logger configuration
     */
    public function getLoggers()
    {
        return $this->loggers;
    }
}
