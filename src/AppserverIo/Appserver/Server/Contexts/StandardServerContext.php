<?php

/**
 * \AppserverIo\Appserver\Server\Contexts\StandardServerContext
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

namespace AppserverIo\Appserver\Server\Contexts;

use Psr\Log\LoggerInterface;
use AppserverIo\Psr\Naming\NamingException;
use AppserverIo\Server\Contexts\ServerContext;
use AppserverIo\Server\Exceptions\ServerException;

/**
 * This is a customized server context implementation that used the naming directory
 * of the application server to lookup logger instances instead of the injected ones.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class StandardServerContext extends ServerContext
{

    /**
     * Queries if the requested logger type is registered or not.
     *
     * @param string $loggerType The logger type we want to query
     *
     * @return boolean TRUE if the logger is registered, else FALSE
     */
    public function hasLogger($loggerType)
    {
        try {
            $this->getContainer()->getNamingDirectory()->search(sprintf('php:global/log/%s', $loggerType));
            return true;
        } catch (NamingException $ne) {
            return false;
        }
    }

    /**
     * Returns the logger instance with the passed type.
     *
     * @param string $loggerType The requested logger's type
     *
     * @return \Psr\Log\LoggerInterface The logger instance
     * @throws \AppserverIo\Server\Exceptions\ServerException
     */
    public function getLogger($loggerType = self::DEFAULT_LOGGER_TYPE)
    {
        try {
            return $this->getContainer()->getNamingDirectory()->search(sprintf('php:global/log/%s', $loggerType));
        } catch (NamingException $ne) {
            throw new ServerException("Logger name '$loggerType' does not exist.", 500);
        }
    }
}
