<?php

/**
 * TechDivision\ApplicationServer\Interfaces\ClassLoaderInterface
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Interfaces
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Interfaces;

use TechDivision\Application\Interfaces\ApplicationInterface;
use TechDivision\ApplicationServer\Api\Node\ClassLoaderNodeInterface;

/**
 * Interface for all class loaders that can be registered in the application server.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Interfaces
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
interface ClassLoaderInterface
{

    /**
     * Installs this class loader on the SPL autoload stack.
     *
     * @param bool $throw   If register should throw an exception or not
     * @param bool $prepend If register should prepend
     *
     * @return void
     */
    public function register($throw = true, $prepend = false);
}
