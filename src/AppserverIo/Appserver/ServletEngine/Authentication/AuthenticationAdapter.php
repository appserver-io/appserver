<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authentication\AuthenticationAdapter
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Florian Sydekum <fs@techdivision.com>
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\ServletEngine\Authentication;

use AppserverIo\Psr\Servlet\Servlet;

/**
 * Abstract class for authentication adapters.
 *
 * @author    Florian Sydekum <fs@techdivision.com>
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
abstract class AuthenticationAdapter
{

    /**
     * Necessary options for specific adapter.
     *
     * @var array
     */
    protected $options;

    /**
     * The path to the actual web application (to resolve relative URLs).
     *
     * @var string
     */
    protected $webappPath;

    /**
     * The filename of the htdigest file.
     *
     * @var string
     */
    protected $filename;

    /**
     * Instantiates an authentication adapter.
     *
     * @param array $securedUrlConfig The security configuration matching this adapter.
     */
    public function __construct($securedUrlConfig)
    {
        // initialize the options and the webapp path
        $this->setOptions($securedUrlConfig['auth']['options']);

        // set the absolute path to the filename containing the users + passwords
        $this->setFilename($securedUrlConfig['webapp-path'] . DIRECTORY_SEPARATOR . $this->options['file']);

        // initialize the adapter implementations
        $this->init();
    }

    /**
     * Initializes the adapter.
     *
     * @return void
     */
    abstract public function init();

    /**
     * Sets htdigest filename.
     *
     * @param string $filename The filename
     *
     * @return void
     */
    protected function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Returns htdigest filename.
     *
     * @return string The filename
     */
    protected function getFilename()
    {
        return $this->filename;
    }

    /**
     * Sets authentication options.
     *
     * @param array $options The options
     *
     * @return void
     */
    protected function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * Returns authenication options.
     *
     * @return array The authentication options
     */
    protected function getOptions()
    {
        return $this->options;
    }
}
