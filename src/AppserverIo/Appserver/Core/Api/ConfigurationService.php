<?php

/**
 * \AppserverIo\Appserver\Core\Api\ConfigurationService
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
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api;

use AppserverIo\Configuration\ConfigurationUtils;
use AppserverIo\Appserver\Core\InitialContext;
use AppserverIo\Appserver\Core\Api\Node\ParamNode;
use AppserverIo\Appserver\Core\Utilities\DirectoryKeys;

/**
 * This class can be used to validate configuration files against known schemas.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ConfigurationService extends AbstractService
{

    /**
     * The default schema to validate against if no specific one is found.
     * Path is relative to installation directory
     *
     * @var string DEFAULT_XML_SCHEMA
     */
    const DEFAULT_XML_SCHEMA = 'resources/schema/appserver.xsd';

    /**
     * The path of the schema file to use for the validation
     *
     * @var array $schemaFile
     */
    protected $schemaFile = '';

    /**
     * Array of schema files indexed with the configuration file name they can validate
     *
     * @var array $schemaFiles
     */
    protected $schemaFiles = array();

    /**
     * Default constructor
     *
     * @param \AppserverIo\Appserver\Core\InitialContext $initialContext The initial context instance
     */
    public function __construct(InitialContext $initialContext)
    {
        parent::__construct($initialContext);

        $this->init();
    }

    /**
     * Returns all nodes.
     *
     * @return array An array with all nodes
     */
    public function findAll()
    {
        // TODO: Implement findAll() method.
    }

    /**
     * Will try to find the appropriate schema file for the file to validate
     *
     * @param string $fileName Name of the file to find the schema for
     *
     * @return null
     */
    protected function findSchemaFile($fileName)
    {
        // check if we got a specific schema file we have to use, otherwise use the default one
        $this->schemaFile = realpath(__DIR__ . '/../../../../../') . DIRECTORY_SEPARATOR . self::DEFAULT_XML_SCHEMA;
        $fileName = pathinfo($fileName, PATHINFO_FILENAME);
        if (isset($this->schemaFiles[$fileName])) {
            $this->schemaFile = $this->schemaFiles[$fileName];
        }
    }

    /**
     * Initializes the configuration tester.
     * Will reset traces of any former usage
     *
     * @return null
     */
    public function init()
    {
        $this->errors = array();
        $this->schemaFile = realpath(__DIR__ . '/../../../../../') . DIRECTORY_SEPARATOR . self::DEFAULT_XML_SCHEMA;
        $this->schemaFiles = array();
    }

    /**
     * Returns the node with the passed UUID.
     *
     * @param integer $uuid UUID of the node to return
     *
     * @return \AppserverIo\Configuration\Interfaces\NodeInterface The node with the UUID passed as parameter
     */
    public function load($uuid)
    {
        // TODO: Implement load() method.
    }

    /**
     * Will set the schema file for the next validation
     *
     * @param string $fileName Path of the schema file to use for coming validation
     *
     * @return null
     */
    public function setSchemaFile($fileName)
    {
        $this->schemaFile = $fileName;
    }

    /**
     * Will validate a given file against a schema.
     * This method supports several validation mechanisms for different file types.
     * Will return true if validation passes, false otherwise.
     * A specific schema file to use might be passed as well, if none is given the tester tries to choose the right one
     *
     * @param string      $fileName     Name of the file to validate
     * @param string|null $schemaFile   The specific schema file to validate against (optional)
     * @param boolean     $failOnErrors If the validation should fail on error (optional)
     *
     * @return boolean
     *
     * @throws \Exception If aren't able to validate this file type
     */
    public function validateFile($fileName, $schemaFile = null, $failOnErrors = false)
    {

        // if we did not get a schema file we have to check if we know which one to use
        if (is_null($schemaFile)) {
            $this->findSchemaFile($fileName);
            $schemaFile = $this->schemaFile;
        }

        // validate the passed configuration file
        ConfigurationUtils::singleton()->validateFile($fileName, $schemaFile, $failOnErrors);

        // return TRUE if validation has been successfull
        return $result;
    }

    /**
     * Will validate a DOM document against a schema file.
     * Will return true if validation passes, false otherwise.
     * A specific schema file to use might be passed as well, if none is given the tester tries to choose the right one
     *
     * @param \DOMDocument $domDocument  DOM document to validate
     * @param string|null  $schemaFile   The specific schema file to validate against (optional)
     * @param boolean      $failOnErrors If the validation should fail on error (optional)
     *
     * @return boolean
     *
     * @throws \AppserverIo\Appserver\Core\Api\InvalidConfigurationException If $failOnErrors is set to true an exception will be thrown on errors
     */
    public function validateXml(\DOMDocument $domDocument, $schemaFile = null, $failOnErrors = false)
    {
        // if we got a specific schema file we will use it, otherwise we will use the one we got globally
        $schemaFileName = $this->schemaFile;
        if (!is_null($schemaFile)) {
            $schemaFileName = $schemaFile;
        }

        // validate the passed DOM document
        ConfigurationUtils::singleton()->validateXml($domDocument, $schemaFileName, $failOnErrors);

        // return TRUE if validation has been successfull
        return true;
    }
}
