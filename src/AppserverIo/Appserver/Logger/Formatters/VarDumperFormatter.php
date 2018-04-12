<?php

/**
 * AppserverIo\Appserver\Logger\Formatters\VarDumperFormatter
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
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Logger\Formatters;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use AppserverIo\Logger\LogMessageInterface;
use AppserverIo\Logger\Formatters\StandardFormatter;

/**
 * The default formatter that uses the symfony/var-dumper package to format the message.
 *
 * The following arguments are available and passed to vsprintf()
 * method in the given order:
 *
 * 1. date (formatted with the date format passed to the constructor)
 * 2. hostname (queried by PHP gethostname() method)
 * 3. loglevel
 * 4. message
 * 5. context (always JSON encoded)
 *
 * If you want to change the order of the arguments have a look at
 * the sprintf() documentation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 * @see       http://php.net/manual/en/function.sprintf.php
 */
class VarDumperFormatter extends StandardFormatter
{

    /**
     * The message format to use, timestamp, id, line, message, context
     *
     * @var string
     */
    protected $messageFormat = '[%s] - %s (%s): %s %s';

    /**
     * The date format to use.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * The dumper instance.
     *
     * @var \Symfony\Component\VarDumper\Dumper\CliDumper
     */
    protected $dumper;

    /**
     * The cloner instance.
     *
     * @var \Symfony\Component\VarDumper\Cloner\VarCloner
     */
    protected $cloner;

    /**
     * Initializes the handler instance with channel name and log level.
     *
     * @param string|null $messageFormat The message format, MUST be valid for sprintf
     * @param string|null $dateFormat    The date format, valid for PHP date() function
     * @param boolean     $useColors     TRUE if the log output has to rendered with colors
     */
    public function __construct($messageFormat = null, $dateFormat = null, $useColors = true)
    {

        // initialize message and date format
        if ($messageFormat != null && is_string($messageFormat)) {
            $this->messageFormat = $messageFormat;
        }
        if ($dateFormat != null && is_string($dateFormat)) {
            $this->dateFormat = $dateFormat;
        }

        // intitialize the cloner/dumper instances
        $this->dumper = new CliDumper();
        $this->dumper->setColors($useColors);

        // initilize the cloner instance
        $this->cloner = new VarCloner();
        $this->cloner->setMaxItems(-1);
    }

    /**
     * Formats and returns a string representation of the passed log message.
     *
     * @param \AppserverIo\Logger\LogMessageInterface $logMessage The log message we want to format
     *
     * @return string The formatted string representation for the log messsage
     */
    public function format(LogMessageInterface $logMessage)
    {

        // initialize the parameters for the formatted message
        $params = array(
            date($this->dateFormat),
            gethostname(),
            $logMessage->getLevel(),
            $this->convertToString($logMessage),
            json_encode($logMessage->getContext())
        );

        // format, trim and return the message
        return trim(vsprintf($this->messageFormat, $params));
    }

    /**
     * Convert's the passed message into an string.
     *
     * @param \AppserverIo\Logger\LogMessageInterface $logMessage The log message we want to convert
     *
     * @return string The converted message
     */
    protected function convertToString(LogMessageInterface $logMessage)
    {

        // initialize the variable for the log output
        $output = '';

        // dump the log message
        $this->dumper->dump(
            $this->cloner->cloneVar($logMessage->getMessage()),
            function ($line, $depth) use (&$output) {
                // a negative depth means "end of dump"
                if ($depth >= 0) {
                    // adds a two spaces indentation to the line
                    $output .= str_repeat('  ', $depth) . $line . PHP_EOL;
                }
            }
        );

        // return the log output
        return rtrim($output, PHP_EOL);
    }
}
