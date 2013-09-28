<?php

/**
 * TechDivision\ApplicationServer\Api\Dto\ReceiverValueObject
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api\Dto;

/**
 * DTO to transfer a receiver.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class ReceiverValueObject extends AbstractValueObject
{

    /**
     * The receiver's class name.
     *
     * @var string
     */
    protected $type;

    /**
     *
     * @var \TechDivision\ApplicationServer\Api\Dto\ThreadValueObject
     */
    protected $thread;

    /**
     *
     * @var \TechDivision\ApplicationServer\Api\Dto\WorkerValueObject
     */
    protected $worker;

    /**
     *
     * @var array<\TechDivision\ApplicationServer\Api\Dto\ParamsValueObject>
     */
    protected $params = array();
}