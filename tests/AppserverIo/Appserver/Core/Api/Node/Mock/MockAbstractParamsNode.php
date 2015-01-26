<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\Mock\MockAbstractParamsNode
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
namespace AppserverIo\Appserver\Core\Api\Node\Mock;

use AppserverIo\Appserver\Core\Api\Node\AbstractNode;
use AppserverIo\Appserver\Core\Api\Node\ParamsNodeTrait;

/**
 * A mock class that allows us to instanciate an AbstractParamsNode instance.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class MockAbstractParamsNode extends AbstractNode
{
    // We use several traits which give us the possibility to have collections of the child nodes mentioned in the
    // corresponding trait name
    use ParamsNodeTrait;


    /**
     * The params to test.
     *
     * @param array $params The params to test
     */
    public function __construct(array $params = array())
    {
        $this->params = $params;
    }
}