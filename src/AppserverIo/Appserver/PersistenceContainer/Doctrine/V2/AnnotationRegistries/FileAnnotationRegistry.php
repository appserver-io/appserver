<?php

/**
 * AppserverIo\Appserver\PersistenceContainer\Doctrine\V2\AnnotationRegistries\FileAnnotationRegistry
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
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/rmi
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\PersistenceContainer\Doctrine\V2\AnnotationRegistries;

use Doctrine\Common\Annotations\AnnotationRegistry;
use AppserverIo\Appserver\Core\Api\Node\AnnotationRegistryNodeInterface;

/**
 * An annotation registry using exactly one file to register annotation classes.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/rmi
 * @link      http://www.appserver.io
 */
class FileAnnotationRegistry implements AnnotationRegistryInterface
{

    /**
     * Register's the annotation driver for the passed configuration.
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\AnnotationRegistryNodeInterface $annotationRegistry The configuration node
     *
     * @return void
     */
    public function register(AnnotationRegistryNodeInterface $annotationRegistry)
    {
        AnnotationRegistry::registerFile($annotationRegistry->getFile());
    }
}