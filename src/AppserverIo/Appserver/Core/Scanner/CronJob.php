<?php

/**
 * \AppserverIo\Appserver\Core\Scanner\CronScanner
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

namespace AppserverIo\Appserver\Core\Scanner;

use AppserverIo\Appserver\Core\Api\Node\JobNodeInterface;

/**
 * The executor thread for a CRON job.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class CronJob extends \Thread
{

    /**
     * Initializes the CRON job with the job information and starts immediately.
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\JobNodeInterface $jobNode The job information
     */
    public function __construct(JobNodeInterface $jobNode)
    {

        // initialize the job information
        $this->jobNode = $jobNode;

        // immediately start the job
        $this->start();
    }

    /**
     * Main method that executes the CRON job in a separate thread.
     *
     * @return void
     */
    public function run()
    {

        try {
            // register shutdown handler
            register_shutdown_function(array(&$this, "shutdown"));

            // store the actual directory
            $actualDir = getcwd();

            // load the node data with the command information
            $executeNode = $this->jobNode->getExecute();

            // try to load the script from the configuration
            if ($script = $executeNode->getScript()) {
                // change the working directory
                if ($execDir = $executeNode->getDirectory()) {
                    chdir($execDir);
                }

                // check if the configured script is a file
                if (is_file($script) && is_executable($script)) {
                    // initialize the exec params
                    $output = array();
                    $returnVar = 0;

                    // execute the script on the command line
                    exec($executeNode, $output, $returnVar);

                    // restore the old directory
                    if ($execDir && $actualDir) {
                        chdir($actualDir);
                    }

                    // query whether the script has been executed or not
                    if ($returnVar !== 0) {
                        throw new \Exception(implode(PHP_EOL, $output));
                    } else {
                        error_log(implode(PHP_EOL, $output));
                    }

                } else {
                    throw new \Exception(sprintf('Script %s is not a file or not executable', $script));
                }

            } else {
                throw new \Exception(sprintf('Can\t find a script configured in job', $this->jobNode->getName()));
            }

        } catch (\Exception $e) {
            // restore the old directory
            chdir($actualDir);
            // log the exception
            error_log($e->__toString());
        }
    }

    /**
     * Does shutdown logic for request handler if something went wrong and
     * produces a fatal error for example.
     *
     * @return void
     */
    public function shutdown()
    {

        // check if there was a fatal error caused shutdown
        if ($lastError = error_get_last()) {
            // initialize type + message
            $type = 0;
            $message = '';
            // extract the last error values
            extract($lastError);
            // query whether we've a fatal/user error
            if ($type === E_ERROR || $type === E_USER_ERROR) {
                error_log($message);
            }
        }
    }
}
