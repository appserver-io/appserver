<?php

/**
 * TechDivision\ApplicationServer\Provisioning\ExecCliStep
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Provisioning
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Provisioning;

/**
 * An step implementation that executes a PHP script defined in configuration.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Provisioning
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class ExecCliStep extends AbstractStep
{

    /**
     * Executes the functionality for this step, in this case the execution of
     * the PHP script defined in the step configuration.
     *
     * @return void
     * @throws \Exception Is thrown if the script can't be executed
     * @see \TechDivision\ApplicationServer\Provisioning\Step::execute()
     */
    public function execute()
    {

        // try to load the script from the configuration
        if ($script = $this->getStepNode()->getExecute()->getScript()) {

            // prepare script by prepending the webapp directory
            $script = new \SplFileInfo(
                $this->getWebappPath() . DIRECTORY_SEPARATOR . ltrim($script, DIRECTORY_SEPARATOR)
            );

            // check if the configured script is a file
            if ($script->isFile() === false) {
                throw new \Exception(sprintf('Script %s is not a file', $script));
            }

            // prepare the scripts arguments
            $args = '';
            if ($params = $this->getStepNode()->getExecute()->getArgs()) {
                $args .= ' -- ';
                foreach ($params as $param) {
                    $args .= ' --' . $param->getName() . ' ' . $param->castToType();
                }
            }

            // prepare the PHP executable, the script and the arguments
            $toExecute = $this->getPhpExecutable() . ' -f ' . $script . $args;

            // execute the script on the command line
            exec($toExecute, $output, $returnVar);

            // check if script has been executed successfully
            if ($returnVar !== 0) { // if not, throw an exception
                throw new \Exception(implode(PHP_EOL, $output));
            }
        }
    }
}
