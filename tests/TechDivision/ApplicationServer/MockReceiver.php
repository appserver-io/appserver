<?php

/**
 * TechDivision\ApplicationServer\MockReceiver
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\AbstractReceiver;

/**
 * The mock receiver implementation.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class MockReceiver extends AbstractReceiver
{
	
	/**
	 * (non-PHPdoc)
	 * @see \TechDivision\ApplicationServer\AbstractReceiver::getResourceClass()
	 */
	public function getResourceClass()
	{
	}
	
	/**
     * @see TechDivision\ApplicationServer\Interfaces\ReceiverInterface::start()
	 */
	public function start()
	{
		$this->getContainer()->setStarted();
	}
}