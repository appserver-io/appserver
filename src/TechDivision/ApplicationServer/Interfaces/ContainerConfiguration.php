<?php

/**
 * TechDivision\ApplicationServer\Interfaces\ContainerConfiguration
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ApplicationServer\Interfaces;

/**
 * @package     TechDivision\ApplicationServer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
interface ContainerConfiguration {
    
    /**
     * Checks if the passed configuration is equal. If yes, the method
     * returns TRUE, if not FALSE.
     * 
     * @param \TechDivision\ApplicationServer\Interfaces\ContainerConfiguration $configuration The configuration to compare to
     * @return boolean TRUE if the configurations are equal, else FALSE
     * @todo Actually it's not possible to add interfaces as type hints for method parameters, this results in an infinite loop 
     */
    public function equals($configuration);
}