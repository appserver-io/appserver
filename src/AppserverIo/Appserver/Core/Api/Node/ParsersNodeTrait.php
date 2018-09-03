<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\ParsersNodeTrait
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

namespace AppserverIo\Appserver\Core\Api\Node;

use AppserverIo\Description\Annotations as DI;

/**
 * Abstract node that serves nodes having a parsers/parser child.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
trait ParsersNodeTrait
{

    /**
     * The parsers.
     *
     * @var array
     * @DI\Mapping(nodeName="parsers/parser", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\ParserNode")
     */
    protected $parsers = array();

    /**
     * Array with the parsers.
     *
     * @param array $parsers The parsers
     *
     * @return void
     */
    public function setParsers(array $parsers)
    {
        $this->parsers = $parsers;
    }

    /**
     * Array with the parsers.
     *
     * @return array
     */
    public function getParsers()
    {
        return $this->parsers;
    }
}
