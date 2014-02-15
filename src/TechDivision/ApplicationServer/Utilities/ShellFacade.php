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
 * Shell facade to wrap away the exec() shell interface so certain things like forbidden commands can be implemented.
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
     * @var array Commands which are prohibited to pass the facade
     */
    protected $prohibitedCommands;
    
    /**
     * Default constructor
     * 
     * @param array $prohibitedCommands Initially prohibited commands
     */
    public function __construct($prohibitedCommands = array()) 
    {
        $this->prohibitedCommands = $prohibitedCommands;
    }
    
    /**
     * Will execute a shell command using exec() function.
     *
     * @param string $command The command to execute over the shell
     *
     * @return mixed
     */
    public function exec($command)
    {
        // Check for prohibited commands
        foreach ($this->prohibitedCommands as $prohibitedCommand) {
            
            if (strpos(trim($command), $prohibitedCommand) !== false) {
                
                throw new Exception('The shell command ' . $command . ' is prohibited');
            }
        }
        
        return exec($command);
    }
    
    /**
     * Getter for the list of prohibited commands
     * 
     * @return array
     */
    public function getProhibitedCommands()
    {
        return $this->prohibitedCommands;
    }
    
    /**
     * Setter for the list of prohibited commands
     * 
     * @param array $prohibitedCommands The list of prohibited commands
     */
    public function setProhibitedCommands(array $prohibitedCommands)
    {
        $this->prohibitedCommands = $prohibitedCommands;
    }
}
