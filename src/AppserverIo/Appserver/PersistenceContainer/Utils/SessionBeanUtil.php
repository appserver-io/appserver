<?php
/**
 * \AppserverIo\Appserver\PersistenceContainer\Utils\SessionBeanUtil
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

namespace AppserverIo\Appserver\PersistenceContainer\Utils;

/**
 * Utility class with some session bean utilities.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class SessionBeanUtil
{

    /**
     * The tie to concatenate the session-ID and the class name
     *
     * @var string
     */
    protected static $tie = '_';

    /**
     * Creates a unqiue identifier for the passed session-ID and class name
     *
     * @param string $sessionId The session-ID to create the identifier for
     * @param string $className The class name to create the identifier for
     *
     * @return string The unique identifier
     */
    public static function createIdentifier($sessionId, $className)
    {
        return sprintf('%s%s%s', SessionBeanUtil::$tie, $sessionId, $className);
    }
}
