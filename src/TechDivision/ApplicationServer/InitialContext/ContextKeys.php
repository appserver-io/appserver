<?php
/**
 * TechDivision\ApplicationServer\ContextKeys
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage InitialContext
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\InitialContext;

/**
 * Class ContextKeys
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage InitialContext
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class ContextKeys
{

    /**
     * Key the system configuration is available in the initial context.
     *
     * @var string
     */
    const SYSTEM_CONFIGURATION = 'context_keys_system_configuration';

    /**
     * This is a utility, so don't allow direct instantiation
     */
    final private function __construct()
    {
    }

    /**
     * This is a utility, so don't allow direct instantiation
     *
     * @return void
     */
    final private function __clone()
    {
    }
}
