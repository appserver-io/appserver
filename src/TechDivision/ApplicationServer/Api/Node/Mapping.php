<?php

/**
 * TechDivision\ApplicationServer\Api\Node\Mapping
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api\Node;

/**
 * DTO to transfer aliases.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class Mapping
{

    protected $name;

    protected $nodeType;

    protected $nodeName;

    protected $elementType;

    public function __construct(\stdClass $token)
    {
        $this->name = $token->name;

        foreach ($token->values as $member => $value) {
            $this->$member = $value;
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function getNodeType()
    {
        return $this->nodeType;
    }

    public function getNodeName()
    {
        return $this->nodeName;
    }

    public function getElementType()
    {
        return $this->elementType;
    }
}