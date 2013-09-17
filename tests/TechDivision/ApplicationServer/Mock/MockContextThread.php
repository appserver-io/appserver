<?php

/**
 * TechDivision\ApplicationServer\Mock\MockContextThread
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Mock;

use TechDivision\ApplicationServer\AbstractContextThread;

/**
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class MockContextThread extends AbstractContextThread
{

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\AbstractThread::main()
     */
    public function main()
    {}

    /**
     * Method to initialze the thread with the constructor
     * params without the initial context.
     *
     * @return void
     */
    public function init()
    {}
}