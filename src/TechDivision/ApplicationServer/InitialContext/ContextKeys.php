<?php

/**
 * TechDivision\ApplicationServer\ContextKeys
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\InitialContext;

/**
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class ContextKeys
{

    /**
     * Key the system configuration is available in the initial context.
     *
     * @var string
     */
    const SYSTEM_CONFIGURATION = 'context_keys_system_configuration';

    /**
     * This is a utility, so don't allow direct instanciation
     *
     * @return void
     */
    private final function __construct()
    {}

    /**
     * This is a utility, so don't allow direct instanciation
     *
     * @return void
     */
    private final function __clone()
    {}
}