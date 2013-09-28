<?php

/**
 * TechDivision\ApplicationServer\Api\Dto\AppserverValueObject
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api\Dto;

/**
 * DTO to transfer the application server's complete configuration.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class AppserverValueObject extends AbstractValueObject
{

    /**
     *
     * @var \TechDivision\ApplicationServer\Api\Dto\BaseDirectoryValueObject
     */
    protected $initialContext;

    /**
     *
     * @var \TechDivision\ApplicationServer\Api\Dto\InitialContextValueObject
     */
    protected $initialContext;

    /**
     *
     * @var \TechDivision\ApplicationServer\Api\Dto\SystemLoggerValueObject
     */
    protected $systemLogger;
}