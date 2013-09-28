<?php

/**
 * TechDivision\ApplicationServer\Api\Dto\ContainerValueObject
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api\Dto;

/**
 * DTO to transfer a container.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class ContainerValueObject extends AbstractValueObject
{

    /**
     * The container's name.
     * @var string
     */
    protected $name;

    /**
     * The container's class name.
     * @var string
     */
    protected $type;

    /**
     * The thread class name that start's the container.
     * @var string
     */
    protected $threadType;

    /**
     *
     * @var  \TechDivision\ApplicationServer\Api\Dto\NodeValue
     */
    protected $description;

    /**
     *
     * @var \TechDivision\ApplicationServer\Api\Dto\ReceiverValueObject
     */
    protected $receiver;

    /**
     *
     * @var \TechDivision\ApplicationServer\Api\Dto\HostValueObject
     */
    protected $host;

    /**
     *
     * @var \TechDivision\ApplicationServer\Api\Dto\DeploymentValueObject
     */
    protected $deployment;

    /**
     *
     * @var array<\TechDivision\ApplicationServer\Api\Dto\AppsValueObject>
     */
    protected $apps = array();
}