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
use AppserverIo\Description\Configuration\AnnotationRegistryConfigurationInterface;

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
     * @param \AppserverIo\Description\Configuration\AnnotationRegistryConfigurationInterface $annotationRegistry The configuration node
     *
     * @return void
     * @throws \InvalidArgumentException Is thrown, if the file with the annotations that have to be registerd is not available
     */
    public function register(AnnotationRegistryConfigurationInterface $annotationRegistry)
    {
        if (is_file($filename = $annotationRegistry->getFile())) {
            AnnotationRegistry::registerFile($filename);
        } else {
            throw new \InvalidArgumentException(sprintf('Annotation Registry can\'t register file "%s" containing Doctrine Annotations', $filename));
        }
    }
}
