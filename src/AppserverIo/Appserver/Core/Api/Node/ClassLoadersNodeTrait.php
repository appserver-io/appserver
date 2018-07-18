<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\ClassLoadersNodeTrait
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

use AppserverIo\Description\Annotations as DI;

/**
 *
 * Abstract node that a contexts class loader nodes.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
trait ClassLoadersNodeTrait
{

    /**
     * The contexts class loader configuration.
     *
     * @var array
     * @DI\Mapping(nodeName="classLoaders/classLoader", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\ClassLoaderNode")
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
