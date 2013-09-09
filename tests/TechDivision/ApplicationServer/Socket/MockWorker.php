<?php

/**
 * TechDivision\ApplicationServer\Socket\MockWorker
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Socket;

use TechDivision\ApplicationServer\AbstractWorker;

/**
 * The mock request implementation.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class MockWorker extends AbstractWorker
{

    /**
     * @see \TechDivision\ApplicationServer\AbstractWorker::getResourceClass()
     */
    protected function getResourceClass()
    {
        return 'TechDivision\Socket\MockSocket';
    }
    
    /**
     * (non-PHPdoc)
     * @see \TechDivision\ApplicationServer\AbstractWorker::main()
     */
    public function main()
    {
        return;
    }
}