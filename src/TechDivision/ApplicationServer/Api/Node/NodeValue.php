<?php

/**
 * TechDivision\ApplicationServer\Api\Node\NodeValue
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api\Node;

use TechDivision\ApplicationServer\Configuration;

/**
 * Represents a node's value.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class NodeValue implements ValueInterface
{

    /**
     * Some node's value.
     *
     * @var string
     */
    protected $value;

    /**
     * Initializes the node with the value.
     *
     * @param string $value The value to initialize the node with
     * @return void
     */
    public function initFromConfiguration(Configuration $configuration)
    {
        $this->value = $configuration->getValue();
    }
    
    /**
     * Set's the node's value.
     * 
     * @param string $value The value to set
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Return's the node value.
     *
     * @return string The node value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     *
     * @see \TechDivision\ApplicationServer\Api\Node\NodeValue::getValue()
     */
    public function __toString()
    {
        return $this->getValue();
    }
}
