<?php

/**
 * \AppserverIo\Appserver\Core\Extractors\ExtractorFactoryInterface
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
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Extractors;

use AppserverIo\Appserver\Core\Api\Node\ExtractorNodeInterface;
use AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface;

/**
 * A factory to create new extractor instances.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface ExtractorFactoryInterface
{

    /**
     * Factory method to create a new extractor instance.
     *
     * @param \AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface $applicationServer The application server instance
     * @param \AppserverIo\Appserver\Core\Api\Node\ExtractorNodeInterface       $configuration     The extractor configuration
     *
     * @return void
     */
    public static function factory(ApplicationServerInterface $applicationServer, ExtractorNodeInterface $configuration);
}
