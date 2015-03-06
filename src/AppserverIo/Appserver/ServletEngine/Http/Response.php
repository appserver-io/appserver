<?php

/**
 * AppserverIo\Appserver\ServletEngine\Http\Response
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
use AppserverIo\Http\HttpException;
use AppserverIo\Http\HttpResponseStates;
use AppserverIo\Psr\HttpMessage\CookieInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;

/**
 * A servlet response implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class Response implements HttpServletResponseInterface
{

    /**
     * Initialises the response object to default properties.
     *
     * @return void
     */
    public function init()
    {

        // init body stream
        $this->bodyStream = '';

        // init exception
        $this->exception = null;

        // init default response properties
        $this->statusCode = 200;
        $this->version = 'HTTP/1.1';
        $this->statusReasonPhrase = "OK";
        $this->mimeType = "text/plain";
        $this->state = HttpResponseStates::INITIAL;

        // initialize cookies and headers
        $this->cookies = array();
        $this->headers = array();

        // initialize the default headers
        $this->initDefaultHeaders();
    }

    /**
     * Initializes the response with the default headers.
     *
     * @return void
     */
    protected function initDefaultHeaders()
    {
        // add this header to prevent .php request to be cached
        $this->addHeader(HttpProtocol::HEADER_EXPIRES, '19 Nov 1981 08:52:00 GMT');
        $this->addHeader(HttpProtocol::HEADER_CACHE_CONTROL, 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->addHeader(HttpProtocol::HEADER_PRAGMA, 'no-cache');

        // set per default text/html mimetype
        $this->addHeader(HttpProtocol::HEADER_CONTENT_TYPE, 'text/html');
    }

    /**
     * Adds a cookie.
     *
     * @param \AppserverIo\Psr\HttpMessage\CookieInterface $cookie The cookie instance to add
     *
     * @return void
     */
    public function addCookie(CookieInterface $cookie)
    {

        // check if this cookie has already been set
        if ($this->hasCookie($name = $cookie->getName())) {
            // then check if we've already one cookie header available
            if (is_array($cookieValue = $this->getCookie($name))) {
                $cookieValue[] = $cookie;
            } else {
                $cookieValue = array($cookieValue, $cookie);
            }

            // add the cookie array
            $this->cookies[$name] = $cookieValue;

        } else {
            // when add it the first time, simply add it
            $this->cookies[$name] = $cookie;
        }
    }

    /**
     * Returns TRUE if the response already has a cookie with the passed
     * name, else FALSE.
     *
     * @param string $cookieName Name of the cookie to be checked
     *
     * @return boolean TRUE if the response already has the cookie, else FALSE
     */
    public function hasCookie($cookieName)
    {
        return isset($this->cookies[$cookieName]);
    }

    /**
     * Returns the cookie with the a cookie
     *
     * @param string $cookieName Name of the cookie to be checked
     *
     * @return mixed The cookie instance or an array of cookie instances
     * @throws \AppserverIo\Http\HttpException Is thrown if the requested header is not available
     */
    public function getCookie($cookieName)
    {
        if ($this->hasCookie($cookieName) === false) {
            throw new HttpException("Cookie '$cookieName' not found");
        }
        return $this->cookies[$cookieName];
    }

    /**
     * Returns the cookies.
     *
     * @return \ArrayAccess The cookies
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * Return content
     *
     * @return string $content
     */
    public function getBodyContent()
    {
        return $this->bodyStream;
    }

    /**
     * Reset the body stream
     *
     * @return void
     */
    public function resetBodyStream()
    {
        $this->bodyStream = '';
    }

    /**
     * Returns the body stream as a resource.
     *
     * @return resource The body stream
     */
    public function getBodyStream()
    {
        return $this->bodyStream;
    }

    /**
     * Appends the content.
     *
     * @param string $content The content to append
     *
     * @return void
     */
    public function appendBodyStream($content)
    {
        $this->bodyStream .= $content;
    }

    /**
     * Copies a source stream to body stream.
     *
     * @param resource $sourceStream The file pointer to source stream
     * @param integer  $maxlength    The max length to read from source stream
     * @param integer  $offset       The offset from source stream to read
     *
     * @return integer The total number of bytes copied
     */
    public function copyBodyStream($sourceStream, $maxlength = null, $offset = 0)
    {

        // check if a stream has been passed
        if (is_resource($sourceStream)) {
            if ($offset && $maxlength) {
                $this->bodyStream = stream_get_contents($sourceStream, $maxlength, $offset);
            }
            if (!$offset && $maxlength) {
                $this->bodyStream = stream_get_contents($sourceStream, $maxlength);
            }
            if (!$offset && !$maxlength) {
                $this->bodyStream = stream_get_contents($sourceStream);
            }
        } else {
            // if not, copy the string
            $this->bodyStream = substr($sourceStream, $offset, $maxlength);
        }

        // return the string length
        return strlen($this->bodyStream);
    }

    /**
     * Resetss the stream resource pointing to body content.
     *
     * @param resource $bodyStream The body content stream resource
     *
     * @return void
     */
    public function setBodyStream($bodyStream)
    {
        $this->copyBodyStream($bodyStream);
    }

    /**
     * Resets all headers by given array
     *
     * @param array $headers The headers array
     *
     * @return void
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * Returns all headers as array
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Returns the headers as string
     *
     * @return string
     */
    public function getHeadersAsString()
    {
        $headerString = '';
        foreach ($this->getHeaders() as $name => $header) {
            // build up a header string
            $headerString .= $name . ': ' . $header . PHP_EOL;
        }

        return $headerString;
    }

    /**
     * Adds a header information got from connection. We've to take care that headers
     * like Set-Cookie header can exist multiple times. To support this create an
     * array that keeps the multiple header values.
     *
     * @param string  $name   The header name
     * @param string  $value  The headers value
     * @param boolean $append If TRUE and a header with the passed name already exists, the value will be appended
     *
     * @return void
     */
    public function addHeader($name, $value, $append = false)
    {
        // normalize header names in case of 'Content-type' into 'Content-Type'
        $name = str_replace(' ', '-', ucwords(str_replace('-', ' ', $name)));

        // check if we've a Set-Cookie header to process
        if ($this->hasHeader($name) && $append === true) {
            // then check if we've already one cookie header available
            if (is_array($headerValue = $this->getHeader($name))) {
                $headerValue[] = $value;
            } else {
                $headerValue = array($headerValue, $value);
            }

            // if no cookie header simple add it
            $this->headers[$name] = $headerValue;

        } else {
            $this->headers[$name] = $value;
        }
    }

    /**
     * Returns header by given name.
     *
     * @param string $name The header name to get
     *
     * @return mixed Usually a string, but can also be an array if we request the Set-Cookie header
     * @throws \AppserverIo\Http\HttpException Is thrown if the requested header is not available
     */
    public function getHeader($name)
    {
        if (isset($this->headers[$name]) === false) {
            throw new HttpException("Response header '$name' not found");
        }
        return $this->headers[$name];
    }

    /**
     * Returns the http version of the response
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Removes the header with the passed name.
     *
     * @param string $name Name of the header to remove
     *
     * @return void
     */
    public function removeHeader($name)
    {
        if (isset($this->headers[$name])) {
            unset($this->headers[$name]);
        }
    }

    /**
     * Check's if header exists by given name
     *
     * @param string $name The header name to check
     *
     * @return boolean
     */
    public function hasHeader($name)
    {
        return isset($this->headers[$name]);
    }

    /**
     * Sets the http response status code
     *
     * @param int $code The status code to set
     *
     * @return void
     */
    public function setStatusCode($code)
    {
        // set status code
        $this->statusCode = $code;

        // lookup reason phrase by code and set
        $this->setStatusReasonPhrase(HttpProtocol::getStatusReasonPhraseByCode($code));
    }

    /**
     * Returns the response status code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Sets the status reason phrase
     *
     * @param string $statusReasonPhrase The reason phrase
     *
     * @return void
     */
    public function setStatusReasonPhrase($statusReasonPhrase)
    {
        $this->statusReasonPhrase = $statusReasonPhrase;
    }

    /**
     * Returns the status phrase based on the status code
     *
     * @return string
     */
    public function getStatusReasonPhrase()
    {
        return $this->statusReasonPhrase;
    }

    /**
     * Sets state of response
     *
     * @param int $state The state value
     *
     * @return void
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * Returns the current state
     *
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Compares current state with given state
     *
     * @param int $state The state to compare with
     *
     * @return bool Whether state is equal (true) or not (false)
     */
    public function hasState($state)
    {
        return ($this->state === $state);
    }

    /**
     * Redirects to the passed URL by adding a 'Location' header and
     * setting the appropriate status code, by default 301.
     *
     * @param string  $url  The URL to forward to
     * @param integer $code The status code to set
     *
     * @return void
     */
    public function redirect($url, $code = 301)
    {
        $this->setStatusCode($code);
        $this->addHeader(HttpProtocol::HEADER_LOCATION, $url);
    }

    /**
     * Queries whether the response contains an exception or not.
     *
     * @return boolean TRUE if an exception has been attached, else FALSE
     */
    public function hasException()
    {
        return $this->exception instanceof \Exception;
    }

    /**
     * Returns the exception bound to the response.
     *
     * @return \Exception|null The exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Binds the exception to the response.
     *
     * @param \Exception $exception The exception to bind.
     *
     * @return void
     */
    public function setException(\Exception $exception)
    {
        $this->exception = $exception;
    }
}
