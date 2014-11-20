<?php

/**
 * AppserverIo\Appserver\Core\Utilities\ClassLoaderKeys
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Utilities;

/**
 * Utility class that contains keys for class loader configuration.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class ClassLoaderKeys
{

    /**
     * Key for the 'environment'.
     *
     * @var string
     */
    const ENVIRONMENT = 'environment';

    /**
     * Key to check 'typeSafety' has been activated.
     *
     * @var string
     */
    const TYPE_SAFETY = 'typeSafety';

    /**
     * Key for the 'processing' level.
     *
     * @var string
     */
    const PROCESSING = 'processing';

    /**
     * Key for the 'enforcementLevel' we're using.
     *
     * @var string
     */
    const ENFORCEMENT_LEVEL = 'enforcementLevel';

    /**
     * This is a utility class, so protect it against direct
     * instantiation.
     */
    private function __construct()
    {
    }

    /**
     * This is a utility class, so protect it against cloning.
     *
     * @return void
     */
    private function __clone()
    {
    }
}
