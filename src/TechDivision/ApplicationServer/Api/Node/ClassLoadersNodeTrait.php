<?php

/**
 * TechDivision\ApplicationServer\Api\Node\ClassLoadersNodeTrait
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

namespace TechDivision\ApplicationServer\Api\Node;

/**
 *
 * Abstract node that a contexts class loader nodes.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
trait ClassLoadersNodeTrait
{

    /**
     * The contexts class loader configuration.
     *
     * @var array
     * @AS\Mapping(nodeName="classLoaders/classLoader", nodeType="array", elementType="TechDivision\ApplicationServer\Api\Node\ClassLoaderNode")
     */
    protected $classLoaders = array();

    /**
     * Sets the contexts class loader configuration.
     *
     * @param array $classLoaders The contexts class loader configuration
     *
     * @return void
     */
    public function setClassLoaders($classLoaders)
    {
        $this->classLoaders = $classLoaders;
    }

    /**
     * Returns the contexts class loader configuration.
     *
     * @return array The contexts class loader configuration
     */
    public function getClassLoaders()
    {
        return $this->classLoaders;
    }
}
