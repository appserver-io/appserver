<?php

/**
 * TechDivision\ApplicationServer\GenericStackable
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ApplicationServer;

/**
 * A generic stackable implementation that can be used as data container
 * in a thread context.
 *
 * @package TechDivision\ServletContainer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class GenericStackable extends \Stackable
{

    /**
     * (non-PHPdoc)
     *
     * @see \Stackable::run()
     */
    public function run() {}
}