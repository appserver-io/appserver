<?php

/**
 * \AppserverIo\Appserver\DependencyInjectionContainer\ParserFactory
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

use AppserverIo\Lang\Reflection\ReflectionClass;
use AppserverIo\Psr\Application\ManagerInterface;
use AppserverIo\Appserver\Core\Api\Node\ParserNodeInterface;

/**
 * Generic factory to create new parser instances from the passed parser configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ParserFactory implements ParserFactoryInterface
{

    /**
     * Creates and returns a new parser instance from the passed configuration.
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\ParserNodeInterface $parserNode The parser configuration
     * @param \AppserverIo\Psr\Application\ManagerInterface            $manager    The manager the parser is bound to
     *
     * @return \AppserverIo\Appserver\DependencyInjectionContainer\ParserInterface The parser instance
     */
    public function createParser(ParserNodeInterface $parserNode, ManagerInterface $manager)
    {

        // create a reflection class from the configured parser type
        $reflectionClass = new ReflectionClass($parserNode->getType());

        // create a new parser instance and pass the configuration and manager to the constructor
        return $reflectionClass->newInstanceArgs(array($parserNode, $manager));
    }
}
