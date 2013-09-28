<?php

/**
 * TechDivision\ApplicationServer\Api\Dto\HostValueObject
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api\Dto;

/**
 * DTO to transfer a host.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class HostValueObject extends AbstractValueObject
{

    /**
     * The host name.
     *
     * @var string
     */
    protected $name;

    /**
     * The application base directory.
     *
     * @var string
     */
    protected $appBase;

    /**
     * The server admin mail address.
     *
     * @var string
     */
    protected $serverAdmin;

    /**
     * The server software string.
     *
     * @var string
     */
    protected $serverSoftware;

    /**
     *
     * @var array<\TechDivision\ApplicationServer\Api\Dto\VhostsValueObject>
     */
    protected $vhosts = array();
}