<?php

/**
 * \AppserverIo\Appserver\DependencyInjectionContainer\ParserFactoryInterface
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
 * Interface for all parser factories.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface ParserFactoryInterface
{

    /**
     * Creates and returns a new parser instance from the passed configuration.
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\ParserNodeInterface $parserNode The parser configuration
     * @param \AppserverIo\Psr\Application\ManagerInterface            $manager    The manager the parser is bound to
     *
     * @return \AppserverIo\Appserver\DependencyInjectionContainer\ParserInterface The parser instance
     */
    public function createParser(ParserNodeInterface $parserNode, ManagerInterface $manager);
}
