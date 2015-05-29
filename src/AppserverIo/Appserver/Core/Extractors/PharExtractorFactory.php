<?php
/**
 * \AppserverIo\Appserver\Core\Extractors\PharExtractorFactory
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
 * A factory for the PHAR extractor instances.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class PharExtractorFactory
{

    /**
     * Factory method to create a new PHAR extractor instance.
     *
     * @param \AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface $applicationServer The application server instance
     * @param \AppserverIo\Appserver\Core\Api\Node\ExtractorNodeInterface       $configuration     The extractor configuration
     *
     * @return void
     */
    public static function factory(ApplicationServerInterface $applicationServer, ExtractorNodeInterface $configuration)
    {

        // create a new reflection class instance
        $reflectionClass = new \ReflectionClass($configuration->getType());

        // initialize the container configuration with the base directory and pass it to the thread
        $params = array($applicationServer->getInitialContext(), $configuration);

        // create and append the thread instance to the internal array
        return $reflectionClass->newInstanceArgs($params);
    }
}
