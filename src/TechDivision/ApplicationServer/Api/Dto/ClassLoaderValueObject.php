<?php

/**
 * TechDivision\ApplicationServer\Api\Dto\ClassLoaderValueObject
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api\Dto;

/**
 * DTO to transfer a app.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class ClassLoaderValueObject extends AbstractValueObject
{

    /**
     * The class loader class name.
     * @var string
     */
    protected $type;
}