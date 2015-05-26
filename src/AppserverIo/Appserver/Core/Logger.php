<?php

/**
 * AppserverIo\Appserver\Core\Logger
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core;

/**
 * Simple logger implementation for execution service usage
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class Logger
{

    /**
     * Defines log formats as array
     *
     * @var array
     */
    public $logFormats;

    /**
     * Defines log streams as array
     *
     * @var array
     */
    public $logStreams;

    /**
     * Registeres given logstream with logformat
     *
     * @param string $name      The streams name
     * @param mixed  $logStream The streamhandle to use
     * @param string $logFormat The log formate to use
     *
     * @return void
     */
    public function attachLogstream($name, $logStream, $logFormat = "%s\r\n")
    {
        $this->logFormats[$name] = $logFormat;
        $this->logStreams[$name] = $logStream;
    }

    /**
     * Simple logger method that writes the passed log messages
     * asynchronously to a stream.
     *
     * @param string $message The message to log
     *
     * @return void
     * @Asynchronous
     */
    public function log($message)
    {
        foreach ($this->logStreams as $name => $logStream) {
            $logHandle = @fopen($logStream, 'rw');
            if (is_resource($logHandle)) {
                fwrite($logHandle, sprintf($this->logFormats[$name], $message));
                fclose($logHandle);
            }
        }
    }
}
