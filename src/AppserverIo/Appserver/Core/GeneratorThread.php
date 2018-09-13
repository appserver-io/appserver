<?php
/**
 * \AppserverIo\Appserver\Core\GeneratorThread
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH - <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core;

use AppserverIo\Doppelgaenger\Generator;
use Psr\Log\LogLevel;

/**
 * Simple thread for parallel creation of contract-enabled structure definitions.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH - <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class GeneratorThread extends \Thread
{

    /**
     * The default format for log messages.
     *
     * @var string
     */
    const LOG_FORMAT = '[%s] - %s (%s): %s [%s]';

    /**
     * Generator instance to use for creation
     *
     * @var \AppserverIo\Doppelgaenger\Generator $generator
     */
    protected $generator;

    /**
     * Array of structures we will be creating
     *
     * @var array<\AppserverIo\Doppelgaenger\Entities\Definitions\Structure> $structures
     */
    protected $structures;

    /**
     * Default constructor
     *
     * @param \AppserverIo\Doppelgaenger\Generator $generator  Our Doppelgaenger generator instance
     * @param array                                $structures List of structures to generate
     */
    public function __construct(Generator $generator, array $structures)
    {
        $this->generator = $generator;
        $this->structures = $structures;
    }

    /**
     * Run method
     *
     * @return void
     */
    public function run()
    {

        // register a shutdown function
        register_shutdown_function(array($this, 'shutdown'));

        // register the default autoloader
        require SERVER_AUTOLOADER;

        try {
            // iterate over all structures and generate them
            foreach ($this->structures as $structure) {
                $this->generator->create($structure);
            }

        } catch (\Exception $e) {
            $this->log(LogLevel::ERROR, $e->__toString());
        }
    }

    /**
     * The shutdown method implementation.
     *
     *@return void
     */
    public function shutdown()
    {

        // check if there was a fatal error caused shutdown
        if ($lastError = error_get_last()) {
            // initialize error type and message
            $type = 0;
            $message = '';
            // extract the last error values
            extract($lastError);
            // query whether we've a fatal/user error
            if ($type === E_ERROR || $type === E_USER_ERROR) {
                $this->log(LogLevel::ERROR, $message);
            }
        }
    }

    /**
     * Returns the default format for log messages.
     *
     * @return string The default log message format
     */
    public function getDefaultLogFormat()
    {
        return GeneratorThread::LOG_FORMAT;
    }

    /**
     * This is a very basic method to log some stuff by using the error_log() method of PHP.
     *
     * @param mixed  $level   The log level to use
     * @param string $message The message we want to log
     * @param array  $context The context we of the message
     *
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        error_log(sprintf($this->getDefaultLogFormat(), date('Y-m-d H:i:s'), gethostname(), $level, $message, json_encode($context)));
    }
}
