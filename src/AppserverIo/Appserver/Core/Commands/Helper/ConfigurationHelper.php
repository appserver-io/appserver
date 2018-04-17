<?php

/**
 * AppserverIo\Appserver\Core\Commands\Helper\ConfigurationHelper
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

namespace AppserverIo\Appserver\Core\Commands\Helper;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\OutputWriter;
use Doctrine\DBAL\Migrations\Configuration\XmlConfiguration;
use Doctrine\DBAL\Migrations\Configuration\YamlConfiguration;
use Doctrine\DBAL\Migrations\Configuration\ArrayConfiguration;
use Doctrine\DBAL\Migrations\Configuration\JsonConfiguration;
use Symfony\Component\Console\Input\InputInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Core\Utilities\DirectoryKeys;

/**
 * The doctrine migrations command implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ConfigurationHelper extends \Doctrine\DBAL\Migrations\Tools\Console\Helper\ConfigurationHelper
{

    /**
     * The application instance.
     *
     * @var \AppserverIo\Psr\Application\ApplicationInterface
     */
    protected $application;

    /**
     * The DBAL connection to use.
     *
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    /**
     * Initialize the helper with the passed application.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application to load the default migration configuration for
     * @param \Doctrine\DBAL\Connection                         $connection  The DBAL connection to initialize the helper with
     */

    public function __construct(ApplicationInterface $application, Connection $connection)
    {
        $this->connection = $connection;
        $this->application = $application;
    }

    /**
     * Initialize's and return's the configuration, either from the specified parameter or by loading
     * the configuration from a default file, if available.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input        The input to read arguments/parameters from
     * @param \Doctrine\DBAL\Migrations\OutputWriter          $outputWriter The output writer instance
     *
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface The configuration instance
     */
    public function getMigrationConfig(InputInterface $input, OutputWriter $outputWriter)
    {

        // if a configuration option is passed to the command line, use that configuration instead of any other one
        if ($input->getOption('configuration')) {
            $outputWriter->write("Loading configuration from command option: " . $input->getOption('configuration'));
            return $this->loadConfig($input->getOption('configuration'), $outputWriter);
        }

        //f no any other config has been found, look for default config file in the path
        $defaultConfigs = [
            'migrations.xml',
            'migrations.yml',
            'migrations.yaml',
            'migrations.json',
            'migrations.php',
        ];

        // try to locate one of the default configuration files in the application's root directory
        foreach ($defaultConfigs as $defaultConfig) {
            $config = DirectoryKeys::realpath(sprintf('%s/%s', $this->application->getWebappPath(), $defaultConfig));
            if ($this->configExists($config)) {
                $outputWriter->write("Loading configuration from file: $config");
                return $this->loadConfig($config, $outputWriter);
            }
        }
    }

    /**
     * Load's and initializes the configuration from the passed file.
     *
     * @param string                                 $config       The name of the file to load the configuration from
     * @param \Doctrine\DBAL\Migrations\OutputWriter $outputWriter The output writer instance
     *
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface The configuration instance
     * @throws \InvalidArgumentException Is thrown, if the given file type is not supported
     */
    private function loadConfig($config, OutputWriter $outputWriter)
    {

        // initialize a mapping from file suffix to configuration class
        $map = [
            'xml'   => XmlConfiguration::class,
            'yaml'  => YamlConfiguration::class,
            'yml'   => YamlConfiguration::class,
            'php'   => ArrayConfiguration::class,
            'json'  => JsonConfiguration::class,
        ];

        // load the file information
        $info = pathinfo($config);

        // check we can support this file type
        if (empty($map[$info['extension']])) {
            throw new \InvalidArgumentException('Given config file type is not supported');
        }

        // initialize and return the configuration instance
        $class = $map[$info['extension']];
        $configuration = new $class($this->connection, $outputWriter);
        $configuration->load($config);
        return $configuration;
    }

    /**
     * Query whether or not the file with the passed name exists.
     *
     * @param string $config The file to query for
     *
     * @return boolean TRUE if the file exists, else FALSE
     */
    private function configExists($config)
    {
        return file_exists($config);
    }
}
