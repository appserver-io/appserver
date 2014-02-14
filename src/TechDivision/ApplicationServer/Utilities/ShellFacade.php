<?php
/**
 * File containing the ShellFacade class
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Utilities
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php
 *             Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

namespace TechDivision\ApplicationServer\Utilities;

/**
 * TechDivision\ApplicationServer\Utilities\ShellFacade
 *
 * ShellFacade to wrap away the exec() shell interface so certain things like forbidden commands can be implemented.
 * It also helps mocking any results coming from the shell.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Utilities
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php
 *             Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
class ShellFacade
{
    /**
     * Will execute a shell command using exec() function.
     *
     * @param string $command The command to execute over the shell
     *
     * @return mixed
     */
    public function exec($command)
    {

    }
}
