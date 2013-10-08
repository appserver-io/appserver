<?php

/**
 * TechDivision\ApplicationServer\Api\Node\Mock\MockAbstractParamsNode
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api\Node\Mock;

use TechDivision\ApplicationServer\Api\Node\AbstractParamsNode;

/**
 * A mock class that allows us to instanciate an AbstractParamsNode instance.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class MockAbstractParamsNode extends AbstractParamsNode
{

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