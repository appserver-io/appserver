<?php

/**
 * TechDivision\ApplicationServer\Api\Node\ValueInterface
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api\Node;

interface ValueInterface
{

    /**
     * Return's the node value.
     *
     * @return string The node value
     */
    public function getValue();
}
