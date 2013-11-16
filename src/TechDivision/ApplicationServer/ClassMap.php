<?php

/**
 * TechDivision\ApplicationServer\ClassMap
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ApplicationServer;

/**
 * The class map to store already loaded files when autoloading via SplClassLoader.
 *
 * @package TechDivision\ServletContainer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class ClassMap extends \Stackable
{

    /**
     * (non-PHPdoc)
     *
     * @see \Stackable::run()
     */
    public function run() {}
}