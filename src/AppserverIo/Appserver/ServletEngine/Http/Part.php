<?php

/**
 * AppserverIo\Appserver\ServletEngine\Http
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

namespace AppserverIo\Appserver\ServletEngine\Http;

use AppserverIo\Http\HttpProtocol;
use AppserverIo\Psr\HttpMessage\PartInterface;

/**
 * A http part implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class Part implements PartInterface
{

    /**
     * Holds input stream file pointer
     *
     * @var resource a file pointer resource on success, or false on error.
     */
    protected $inputStream;

    /**
     * The name of the part
     *
     * @var string
     */
    protected $name;

    /**
     * Hold the orig filename given in multipart header
     *
     * @var string
     */
    protected $filename;

    /**
     * Hold the templary filename.
     *
     * @var string
     */
    protected $tmpFilename;

    /**
     * Holds the header information as array
     *
     * @var array
     */
    protected $headers = array();

    /**
     * Holds  the number of bytes written to inputStream
     *
     * @var int
     */
    protected $size = 0;

    /**
     * Creates a new servlet part instance with the data from the HTTP part.
     *
     * @param \AppserverIo\Psr\HttpMessage\PartInterface $httpPart The HTTP part we want to copy
     *
     * @return \AppserverIo\Appserver\ServletEngine\Http\Part The initialized servlet part
     */
    public static function fromHttpRequest(PartInterface $httpPart)
    {

        // create a temporary filename
        $httpPart->write($tmpFilename = tempnam(ini_get('upload_tmp_dir'), 'tmp_'));

        // initialize the servlet part instance
        $servletPart = new Part();
        $servletPart->setName($httpPart->getName());
        $servletPart->setFilename($httpPart->getFilename());
        $servletPart->setTmpFilename($tmpFilename);

        // return the servlet part instance
        return $servletPart;
    }

    /**
     * Initiates a http form part object
     *
     * @param string $streamWrapper The stream wrapper to use per default temp stream wrapper
     * @param long   $maxMemory     Maximum memory in bytes per default to 5 MB
     *
     * @throws \Exception
     * @return void
     */
    public function init($streamWrapper = self::STREAM_WRAPPER_TEMP, $maxMemory = 5242880)
    {

        // weather we've alread set a filename open the input stream
        if ($tmpFilename = $this->getTmpFilename()) {
            if (!$this->inputStream = fopen($tmpFilename, 'r+')) {
                throw new \Exception(sprintf('Can\'t open input temporary filename %s', $tmpFilename));
            }
        } else {
            if (!$this->inputStream = fopen($streamWrapper . '/maxmemory:' . $maxMemory, 'r+')) {
                throw new \Exception(sprintf('Can\'t open stream wrapper %s', $streamWrapper));
            }
        }
    }

    /**
     * Puts content to input stream.
     *
     * @param string $content The content as string
     *
     * @return void
     */
    public function putContent($content)
    {
        // write to io stream
        $this->size = fwrite($this->inputStream, $content);
        // rewind file pointer
        rewind($this->inputStream);
    }

    /**
     * Gets the content of this part as an InputStream
     *
     * @return resource The content of this part as an InputStream
     */
    public function getInputStream()
    {
        return $this->inputStream;
    }

    /**
     * Gets the content type of this part.
     *
     * @return string The content type of this part.
    */
    public function getContentType()
    {
        return $this->getHeader(HttpProtocol::HEADER_CONTENT_TYPE);
    }

    /**
     * Sets the orig form filename.
     *
     * @param string $filename The file's name
     *
     * @return void
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Gets the original form filename.
     *
     * @return string The file's name
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Sets the temporary filename.
     *
     * @param string $tmpFilename The temporary filename
     *
     * @return void
     */
    public function setTmpFilename($tmpFilename)
    {
        $this->tmpFilename = $tmpFilename;
    }

    /**
     * Returns the temporary filename.
     *
     * @return string The temporary filename
     */
    public function getTmpFilename()
    {
        return $this->tmpFilename;
    }

    /**
     * Sets the name of the part
     *
     * @param string $name The part's name
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Adds header information to the part
     *
     * @param string $name  The header name
     * @param string $value The header value for given name
     *
     * @return void
     */
    public function addHeader($name, $value)
    {
        $this->headers[$name] = $value;
    }

    /**
     * Gets the name of this part
     *
     * @return string The name of this part as a String
    */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the size of this file.
     *
     * @return int The size of this part, in bytes.
    */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * A convenience method to write this uploaded item to disk.
     *
     * @param string $fileName The name of the file to which the stream will be written.
     *
     * @return integer
    */
    public function write($fileName)
    {
        return file_put_contents(
            $fileName,
            $this->getInputStream()
        );
    }

    /**
     * Deletes the underlying storage for a file item, including deleting any associated temporary disk file.
     *
     * @return void
    */
    public function delete()
    {
        fclose($this->inputStream);
    }

    /**
     * Returns the value of the specified mime header as a String.
     * If the Part did not include a header of the specified name, this method returns null.
     * If there are multiple headers with the same name, this method returns the first header in the part.
     * The header name is case insensitive. You can use this method with any request header.
     *
     * @param string $name a String specifying the header name
     *
     * @return string The headers value for given name
     */
    public function getHeader($name)
    {
        if (array_key_exists($name, $this->headers)) {
            return $this->headers[$name];
        }

        return '';
    }

    /**
     * Gets the values of the Part header with the given name.
     *
     * @param string $name the header name whose values to return
     *
     * @return array
    */
    public function getHeaders($name = null)
    {
        if (is_null($name)) {
            return $this->headers;
        } else {
            return $this->getHeader($name);
        }
    }

    /**
     * Gets the header names of this Part.
     *
     * @return array
    */
    public function getHeaderNames()
    {
        return array_keys($this->headers);
    }
}
