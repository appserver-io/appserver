<?php

/**
 * \AppserverIo\Appserver\Core\LoggerFactory
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

/**
 * Logger factory implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class LoggerFactory
{

    /**
     * Creates a new logger instance based on the passed logger configuration.
     *
     * @param object $loggerNode The logger configuration
     *
     * @return object The logger instance
     */
    public static function factory($loggerNode)
    {

        // initialize the processors
        $processors = array();
        /**
         * @var \AppserverIo\Appserver\Core\Api\Node\ProcessorNode $processorNode
         */
        foreach ($loggerNode->getProcessors() as $processorNode) {
            $reflectionClass = new \ReflectionClass($processorNode->getType());
            $processors[] = $reflectionClass->newInstanceArgs($processorNode->getParamsAsArray());
        }

        // initialize the handlers
        $handlers = array();
        /**
         * @var \AppserverIo\Appserver\Core\Api\Node\HandlerNode $handlerNode
         */
        foreach ($loggerNode->getHandlers() as $handlerNode) {
            // initialize the handler node
            $reflectionClass = new \ReflectionClass($handlerNode->getType());
            $handler = $reflectionClass->newInstanceArgs($handlerNode->getParamsAsArray());

            // if we've a formatter, initialize the formatter also
            if ($formatterNode = $handlerNode->getFormatter()) {
                $reflectionClass = new \ReflectionClass($formatterNode->getType());
                $handler->setFormatter($reflectionClass->newInstanceArgs($formatterNode->getParamsAsArray()));
            }

            // add the handler
            $handlers[] = $handler;
        }

        // prepare the logger params
        $loggerParams = array($loggerNode->getChannelName(), $handlers, $processors);
        $loggerParams = array_merge($loggerParams, $loggerNode->getParamsAsArray());

        // initialize the logger instance itself
        $reflectionClass = new \ReflectionClass($loggerNode->getType());
        $loggerInstance = $reflectionClass->newInstanceArgs($loggerParams);

        // return the instance
        return $loggerInstance;
    }
}
