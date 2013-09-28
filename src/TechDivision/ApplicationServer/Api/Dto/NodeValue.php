<?php

/**
 * TechDivision\ApplicationServer\Api\Dto\StringValue
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api\Dto;

/**
 * Represents a node's value.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class StringValue
{

    /**
     * Some node's value.
     *
     * @var string
     */
    protected $value;

    /**
     * The value's type.
     *
     * @var string
     */
    protected $type;
}