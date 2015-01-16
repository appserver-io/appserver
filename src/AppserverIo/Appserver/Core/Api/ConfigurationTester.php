<?php

/**
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
 * @subpackage Core
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2015 TechDivision GmbH - <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io/
 */

namespace AppserverIo\Appserver\Core\Api;

/**
 * AppserverIo\Appserver\Core\Api\ConfigurationTester
 *
 * This class clan be used to validate configuration files against known schemas
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Core
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2015 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class ConfigurationTester
{

    /**
     * The default schema to validate against if no specific one is found.
     * Path is relative to installation directory
     *
     * @var string DEFAULT_XML_SCHEMA
     */
    const DEFAULT_XML_SCHEMA = 'resources/schema/appserver.xsd';

    /**
     * Array of schema files indexed with the configuration file name they can validate
     *
     * @var array $schemaFiles
     */
    protected $errors = array();

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
     */
    public function __construct()
    {
        $this->init();
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
     * Will return recently found errors already formatted for output
     *
     * @return array
     */
    public function getErrorMessages()
    {
        $errorMessages = array();
        foreach ($this->getErrors() as $error) {

            $errorMessages[] = sprintf(
                "Found a schema validation error on line %s with code %s and message %s when validating configuration file %s, see error dump below: %s",
                $error->line,
                $error->code,
                $error->message,
                $error->file,
                var_export($error, true)
            );
        }

        return $errorMessages;
    }

    /**
     * Getter for the errors produced in the last run
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
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
     * @param string       $fileName     Name of the file to validate
     * @param string|null  $schemaFile   The specific schema file to validate against (optional)
     * @param boolean      $failOnErrors If the validation should fail on error (optional)
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

        // check by the files extension
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);

        // are we able to validate the file?
        $result = null;
        switch ($extension)
        {
            case 'xml':

                $domDocument = new \DOMDocument();
                $domDocument->load($fileName);
                $result = $this->validateXml($domDocument, $schemaFile, $failOnErrors);
                break;

            default:

                throw new \Exception(sprintf('Could not find a validation method for file %s as the extension %s is not supported.', $fileName, $extension));
                break;
        }

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

        // activate internal error handling, necessary to catch errors with libxml_get_errors()
        libxml_use_internal_errors(true);

        // validate the configuration file with the schema
        $result = true;
        if ($domDocument->schemaValidate($schemaFileName) === false) {

            // collect the errors and return as a failure
            $this->errors = libxml_get_errors();
            $result = false;
        }

        // if we have to fail on errors we might do so here
        if ($failOnErrors && !$result) {

            $errorMessages = $this->getErrorMessages();
            throw new InvalidConfigurationException(reset($errorMessages));
        }

        return $result;
    }
}
