<?php

/**
 * TechDivision\ApplicationServer\Stream\Receiver
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Stream;

use TechDivision\ApplicationServer\AbstractReceiver;

/**
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Johann Zelger <jz@techdivision.com>
 */
class Receiver extends AbstractReceiver
{

    /**
     * @see \TechDivision\ApplicationServer\AbstractReceiver
     */
    protected function getResourceClass()
    {
        return 'TechDivision\Stream\Server';
    }
}