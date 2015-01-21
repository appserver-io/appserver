<?php

/**
 * AppserverIo\Appserver\Naming\ResourceIdentifier
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 */

namespace AppserverIo\Appserver\Naming;

use AppserverIo\Properties\PropertiesInterface;

/**
 * This is a resource identifier implementation to use a URL as a unique
 * identifier for lookup a enterprise bean from the container.
 *
 * Usually a normal URL will be enough, but as the container always uses
 * an application name we need some functionality to explode that from
 * the path.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 */
class ResourceIdentifier
{

    /**
     * The URL data of the resource identifier.
     *
     * @var array
     */
    protected $data = array();

    /**
     * Initializes the resource identifier with the data of the passed array.
     *
     * @param array $urlElements The data to initialize the identifier with
     */
    public function __construct(array $urlElements = array())
    {
        $this->data = $urlElements;
    }

    /**
     * Returns the value for the passed key, if available.
     *
     * @param string $key The key of the value to return
     *
     * @return mixed|null The requested value
     */
    protected function getValue($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
    }

    /**
     * Sets the value with the passed key, existing values
     * are overwritten.
     *
     * @param string $key   The key of the value
     * @param string $value The value to set
     *
     * @return void
     */
    protected function setValue($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Sets the URL scheme.
     *
     * @param string $scheme The URL scheme
     *
     * @return void
     */
    public function setScheme($scheme)
    {
        $this->setValue('scheme', $scheme);
    }

    /**
     * Returns the URL scheme.
     *
     * @return string|null The URL scheme
     */
    public function getScheme()
    {
        return $this->getValue('scheme');
    }

    /**
     * Sets the URL user.
     *
     * @param string $user The URL user
     *
     * @return void
     */
    public function setUser($user)
    {
        $this->setValue('user', $user);
    }

    /**
     * Returns the URL user.
     *
     * @return string|null The URL user
     */
    public function getUser()
    {
        return $this->getValue('user');
    }

    /**
     * Sets the URL password.
     *
     * @param string $pass The URL password
     *
     * @return void
     */
    public function setPass($pass)
    {
        $this->setValue('pass', $pass);
    }

    /**
     * Returns the URL password.
     *
     * @return string|null The URL password
     */
    public function getPass()
    {
        return $this->getValue('pass');
    }

    /**
     * Sets the URL host.
     *
     * @param string $host The URL host
     *
     * @return void
     */
    public function setHost($host)
    {
        $this->setValue('host', $host);
    }

    /**
     * Returns the URL host.
     *
     * @return string|null The URL host
     */
    public function getHost()
    {
        return $this->getValue('host');
    }

    /**
     * Sets the URL port.
     *
     * @param integer $port The URL port
     *
     * @return void
     */
    public function setPort($port)
    {
        $this->setValue('port', $port);
    }

    /**
     * Returns the URL port.
     *
     * @return integer|null The URL port
     */
    public function getPort()
    {
        return $this->getValue('port');
    }

    /**
     * Sets the URL path.
     *
     * @param string $path The URL path
     *
     * @return void
     */
    public function setPath($path)
    {
        $this->setValue('path', $path);
    }

    /**
     * Returns the URL path.
     *
     * @return string|null The URL path
     */
    public function getPath()
    {
        return $this->getValue('path');
    }

    /**
     * Sets the URL query.
     *
     * @param string $query The URL query
     *
     * @return void
     */
    public function setQuery($query)
    {
        $this->setValue('query', $query);
    }

    /**
     * Returns the URL query.
     *
     * @return string|null The URL query
     */
    public function getQuery()
    {
        return $this->getValue('query');
    }

    /**
     * Extracts and returns the path information from the path.
     *
     * @return string|null The path information
     */
    public function getPathInfo()
    {
        if ($path = $this->getPath()) {
            return str_replace($this->getFilename(), '', $path);
        }
    }

    /**
     * Extracts and returns the filename from the path.
     *
     * @return string|null The filename
     */
    public function getFilename()
    {

        // firs check if the resource identifier has a path
        if ($path = $this->getPath()) {
            // we're searching for the . => signals a file
            $foundDot = strpos($path, '.');

            // if we can't find one, we don't have a filename
            if ($foundDot === false) {
                return $path;
            }

            // after that look for the first slash => that'll be the end of the file extension
            $foundPathInfo = strpos($path, '/', $foundDot - 1);

            // if we can't find one, we don't have a path information to separate
            if ($foundPathInfo === false) {
                return $path;
            }

            // return the filename by cutting it out of the complete path
            return substr($path, 0, $foundPathInfo);
        }
    }

    /**
     * Returns the context name from the URL.
     *
     * @return string|null The context name
     */
    public function getContextName()
    {
        if ($filename = $this->getFilename()) {
            $filenameParts = explode('/', trim($filename, '/'));
            return reset($filenameParts);
        }
    }

    /**
     * Adds a query parameter to the resource identifier, usually this will be a session ID.
     *
     * @param string $keyValuePair The query parameter we want to add
     *
     * @return void
     */
    public function addQueryParam($keyValuePair)
    {
        if ($query = $this->getValue('query')) {
            $this->setQuery($query . '&' . $keyValuePair);
        } else {
            $this->setQuery($keyValuePair);
        }
    }

    /**
     * Creates a new resource identifer instance with the data extracted from the passed URL.
     *
     * @param string $url The URL to load the data from
     *
     * @return void
     */
    public function populateFromUrl($url)
    {
        foreach (parse_url($url) as $key => $value) {
            $this->setValue($key, $value);
        }
    }

    /**
     * create a new resource identifier with the URL parts from the passed properties.
     *
     * @param \AppserverIo\Properties\PropertiesInterface $properties The configuration properties
     *
     * @return \AppserverIo\Appserver\Naming\ResourceIdentifier The initialized instance
     */
    public static function createFromProperties(PropertiesInterface $properties)
    {
        return new ResourceIdentifier($properties->toIndexedArray());
    }
}
