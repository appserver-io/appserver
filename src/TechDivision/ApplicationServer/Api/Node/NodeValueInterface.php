<?php

/**
 * TechDivision\ApplicationServer\Api\Node\NodeValueInterface
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api\Node;

interface NodeValueInterface
{

    /**
     * Returns the configuration node's value.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\NodeValue The node's value
     */
    public function getNodeValue();
}
