<?php

/**
 * AppserverIo\Appserver\Meta\Composer\Script\Setup
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

namespace AppserverIo\Appserver\Meta\Composer\Script;

use Composer\Script\Event;

/**
 * Class that provides functionality that'll be executed by composer
 * after installation or update of the application server.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class Setup
{

    /**
     * appserver.io written in ASCI art.
     *
     * @var string
     */
    protected static $logo = '                                                    _
  ____ _____  ____  ________  ______   _____  _____(_)___
 / __ `/ __ \/ __ \/ ___/ _ \/ ___/ | / / _ \/ ___/ / __ \
/ /_/ / /_/ / /_/ (__  )  __/ /   | |/ /  __/ /  / / /_/ /
\__,_/ .___/ .___/____/\___/_/    |___/\___/_(_)/_/\____/
    /_/   /_/

';

    /**
     * The array with the merged and os specific template variables.
     *
     * @var array
     */
    protected static $mergedProperties = array();

    /**
     * The available properties we used for parsing the template.
     *
     * @var array
     */
    protected static $defaultProperties = array(
        SetupKeys::PHP_VERSION                                   => PHP_VERSION,
        SetupKeys::ADMIN_EMAIL                                   => 'info@appserver.io',
        SetupKeys::CONTAINER_SERVER_WORKER_ACCEPT_MIN            => 3,
        SetupKeys::CONTAINER_SERVER_WORKER_ACCEPT_MAX            => 8,
        SetupKeys::CONTAINER_HTTP_WORKER_NUMBER                  => 16,
        SetupKeys::CONTAINER_HTTP_HOST                           => SetupKeys::DEFAULT_HOST,
        SetupKeys::CONTAINER_HTTP_PORT                           => 9080,
        SetupKeys::CONTAINER_HTTPS_WORKER_NUMBER                 => 16,
        SetupKeys::CONTAINER_HTTPS_HOST                          => SetupKeys::DEFAULT_HOST,
        SetupKeys::CONTAINER_HTTPS_PORT                          => 9443,
        SetupKeys::CONTAINER_MESSAGE_QUEUE_WORKER_NUMBER         => 4,
        SetupKeys::CONTAINER_MESSAGE_QUEUE_HOST                  => SetupKeys::DEFAULT_HOST,
        SetupKeys::CONTAINER_MESSAGE_QUEUE_PORT                  => 8587,
        SetupKeys::PHP_FPM_PORT                                  => 9010,
        SetupKeys::PHP_FPM_HOST                                  => SetupKeys::DEFAULT_HOST,
        SetupKeys::UMASK                                         => '0002',
        SetupKeys::USER                                          => 'nobody',
        SetupKeys::GROUP                                         => 'nobody'
    );

    /**
     * The OS specific configuration properties.
     *
     * @var array
     */
    protected static $osProperties = array(
        SetupKeys::OS_DARWIN  => array(SetupKeys::OS_FAMILY => SetupKeys::OS_FAMILY_DARWIN, SetupKeys::OS_ARCHITECTURE => 'x86_64', SetupKeys::GROUP => 'staff',    SetupKeys::USER => '_www'),
        SetupKeys::OS_DEBIAN  => array(SetupKeys::OS_FAMILY => SetupKeys::OS_FAMILY_LINUX,  SetupKeys::OS_ARCHITECTURE => 'x86_64', SetupKeys::GROUP => 'www-data', SetupKeys::USER => 'www-data'),
        SetupKeys::OS_UBUNTU  => array(SetupKeys::OS_FAMILY => SetupKeys::OS_FAMILY_LINUX,  SetupKeys::OS_ARCHITECTURE => 'x86_64', SetupKeys::GROUP => 'www-data', SetupKeys::USER => 'www-data'),
        SetupKeys::OS_FEDORA  => array(SetupKeys::OS_FAMILY => SetupKeys::OS_FAMILY_LINUX,  SetupKeys::OS_ARCHITECTURE => 'x86_64', ),
        SetupKeys::OS_REDHAT  => array(SetupKeys::OS_FAMILY => SetupKeys::OS_FAMILY_LINUX,  SetupKeys::OS_ARCHITECTURE => 'x86_64', ),
        SetupKeys::OS_CENTOS  => array(SetupKeys::OS_FAMILY => SetupKeys::OS_FAMILY_LINUX,  SetupKeys::OS_ARCHITECTURE => 'x86_64', ),
        SetupKeys::OS_WINDOWS => array(
            SetupKeys::OS_FAMILY                                     => SetupKeys::OS_FAMILY_WINDOWS,
            SetupKeys::OS_ARCHITECTURE                               => 'x86',
            SetupKeys::CONTAINER_HTTP_WORKER_NUMBER                  => 8,
            SetupKeys::CONTAINER_HTTPS_WORKER_NUMBER                 => 8
        )
    );

    /**
     * Returns the Linux distribution we're running on.
     *
     * @return string The Linux distribution we're running on
     */
    public static function getLinuxDistro()
    {

        // declare Linux distros (extensible list).
        $distros = array(
            SetupKeys::OS_ARCH   => 'arch-release',
            SetupKeys::OS_DEBIAN => 'debian_version',
            SetupKeys::OS_FEDORA => 'fedora-release',
            SetupKeys::OS_UBUNTU => 'lsb-release',
            SetupKeys::OS_REDHAT => 'redhat-release',
            SetupKeys::OS_CENTOS => 'centos-release'
        );

        // get everything from /etc directory.
        $etcList = scandir('/etc');

        // loop through /etc results...
        $distro = '';

        // iterate over all found files
        foreach ($etcList as $entry) {
            // loop through list of distros..
            foreach ($distros as $distroReleaseFile) {
                // match was found.
                if ($distroReleaseFile === $entry) {
                    // find distros array key (i.e. distro name) by value (i.e. distro release file)
                    $distro = array_search($distroReleaseFile, $distros);
                    break 2; // break inner and outer loop.
                }
            }
        }

        // return the found distro string
        return $distro;
    }

    /**
     * Merge the properties based on the passed OS.
     *
     * @param string $os                The OS we want to merge the properties for
     * @param array  $contextProperties The properties to merge
     *
     * @return void
     */
    public static function prepareProperties($os, array $contextProperties)
    {

        // merge all properties
        Setup::$mergedProperties = array_merge(
            $contextProperties,
            Setup::$defaultProperties,
            Setup::$osProperties[$os]
        );

        // prepare the properties for the OS identifier, e. g. iron-horse_debian_x86_64
        Setup::$mergedProperties[SetupKeys::OS_IDENTIFIER] = sprintf(
            '%s_%s_%s',
            str_replace(' ', '-', strtolower(Setup::$mergedProperties[SetupKeys::RELEASE_NAME])),
            $os,
            Setup::$mergedProperties[SetupKeys::OS_ARCHITECTURE]
        );

        // prepare the properties for the software identifier, e. g. appserver/1.0.0 (debian) PHP/5.5.21
        Setup::$mergedProperties[SetupKeys::SOFTWARE_IDENTIFIER] = sprintf(
            'appserver/%s (%s) PHP/%s',
            Setup::$mergedProperties[SetupKeys::VERSION],
            Setup::$mergedProperties[SetupKeys::OS_FAMILY],
            Setup::$mergedProperties[SetupKeys::PHP_VERSION]
        );
    }

    /**
     * Prepares the context by given event or without event for other usage
     *
     * @param null|string $installDir The install dir to check for info files
     * @param null|Event  $event      The event instance or null
     *
     * @return void
     */
    public static function prepareContext($installDir = null, $event = null)
    {
        // initialize the installation directory
        if (is_null($installDir)) {
            $installDir = getcwd();
        }

        // check if we've a file with the actual version number
        if (file_exists($filename = $installDir .'/etc/appserver/.release-version')) {
            $version = file_get_contents($filename);
        } else {
            // load the version (GIT) of this package as fallback if called with event instance
            if (!is_null($event)) {
                $version = $event->getComposer()->getPackage()->getPrettyVersion();
            }
        }

        // check if we've a file with the actual release name
        if (file_exists($filename = $installDir .'/etc/appserver/.release-name')) {
            $releaseName = file_get_contents($filename);
        } else {
            // set the release name to 'Unknown' if not
            $releaseName = 'Unknown';
        }

        // prepare the context properties
        $contextProperties = array(
            SetupKeys::VERSION => $version,
            SetupKeys::INSTALL_DIR => $installDir,
            SetupKeys::RELEASE_NAME => $releaseName
        );

        // load the OS signature => sscanf is necessary to detect Windows, e. g. Windows NT for Windows 7
        list($os, ) = sscanf(strtolower(php_uname('s')), '%s %s');

        // check what OS we are running on
        switch ($os) {

            // installation running on Linux
            case SetupKeys::OS_FAMILY_LINUX:

                // get the distribution
                $distribution = Setup::getLinuxDistro();
                // if we cant find one of the supported systems
                if ($distribution == null) {
                    // set debian as default
                    $distribution = SetupKeys::OS_DEBIAN;

                    // write a message to the console if called with event instance
                    if (!is_null($event)) {
                        $event->getIo()->write(
                            sprintf(
                                '<warning>Unknown Linux distribution found, use Debian default values: ' .
                                'Please check user/group configuration in etc/appserver/appserver.xml</warning>'
                            )
                        );
                    }
                }

                // merge the properties for the found Linux distribution
                Setup::prepareProperties($distribution, $contextProperties);

                break;

            // installation running on Mac OS X
            case SetupKeys::OS_FAMILY_DARWIN:

                // merge the properties for Mac OS X
                Setup::prepareProperties($os, $contextProperties);
                break;

            // installation running on Windows
            case SetupKeys::OS_FAMILY_WINDOWS:

                // merge the properties for Windows
                Setup::prepareProperties($os, $contextProperties);
                break;

            // all other OS are NOT supported actually
            default:
                break;
        }
    }

    /**
     * This method will be invoked by composer after a successfull installation and creates
     * the application server configuration file under etc/appserver/appserver.xml.
     *
     * @param \Composer\Script\Event $event The event that invokes this method
     *
     * @return void
     */
    public static function postInstall(Event $event)
    {
        // initialize the installation directory
        $override = false;
        $installDir = getcwd();

        // check the arguments for an installation directory
        foreach ($event->getArguments() as $arg) {
            // extract arguments
            list ($key, ) = explode('=', $arg);
            // query we want to override files
            if ($key === SetupKeys::ARG_OVERRIDE) {
                $override = true;
            }
            // query for a custom installation directory
            if ($key === SetupKeys::ARG_INSTALL_DIR) {
                $installDir = str_replace("$key=", '', $arg);
            }
        }

        Setup::prepareContext($installDir, $event);

        // process and move the configuration files their target directory
        Setup::processTemplate('etc/appserver/appserver.xml', $override);

        // write a message to the console
        $event->getIo()->write(
            sprintf(
                '%s<info>Successfully invoked appserver.io Composer post-install-cmd script ...</info>',
                Setup::$logo
            )
        );
    }

    /**
     * Returns the configuration value with the passed key.
     *
     * @param string $key The key to get value for
     *
     * @return mixed|null The configuration value
     */
    public static function getValue($key)
    {
        if (array_key_exists($key, Setup::$mergedProperties)) {
            return Setup::$mergedProperties[$key];
        }
    }

    /**
     * Copies the passed OS specific resource file to the target directory.
     *
     * @param string  $os       The OS we want to copy the files for
     * @param string  $resource The resource file we want to copy
     * @param boolean $override TRUE if the file should be overwritten if exists, else FALSE
     * @param integer $mode     The mode of the target file
     *
     * @return void
     */
    public static function copyOsSpecificResource($os, $resource, $override = false, $mode = 0644)
    {

        // we need the installation directory
        $installDir = Setup::getValue(SetupKeys::INSTALL_DIR);

        // prepare source and target directory
        $source = Setup::prepareOsSpecificPath(sprintf('%s/resources/os-specific/%s/%s', $installDir, $os, $resource));
        $target = Setup::prepareOsSpecificPath(sprintf('%s/%s', $installDir, $resource));

        // query whether we've to override the file
        if ($override === false && is_file($target) === true) {
            return;
        }

        // prepare the target directory
        Setup::prepareDirectory($target);

        // copy the file to the target directory
        copy($source, $target);

        // set the correct mode for the file
        Setup::changeFilePermissions($target, $mode);
    }

    /**
     * Processes the OS specific template and replace the properties with the OS specific values.
     *
     * @param string  $os       The OS we want to process the template for
     * @param string  $template The path to the template
     * @param boolean $override TRUE if the file should be overwritten if exists, else FALSE
     * @param integer $mode     The mode of the target file
     *
     * @return void
     */
    public static function processOsSpecificTemplate($os, $template, $override = false, $mode = 0644)
    {

        // prepare the target filename
        $targetFile = Setup::prepareOsSpecificPath($template);

        // query whether we've to override the file
        if ($override === false && is_file($targetFile) === true) {
            return;
        }

        // prepare the target directory
        Setup::prepareDirectory($template);

        // process the template and store the result in the passed file
        ob_start();
        include Setup::prepareOsSpecificPath(sprintf('resources/templates/os-specific/%s/%s.phtml', $os, $template));
        file_put_contents($targetFile, ob_get_clean());

        // set the correct mode for the file
        Setup::changeFilePermissions($template, $mode);
    }

    /**
     * Processes the template and replace the properties with the OS specific values.
     *
     * @param string  $template The path to the template
     * @param boolean $override TRUE if the file should be overwritten if exists, else FALSE
     * @param integer $mode     The mode of the target file
     *
     * @return void
     */
    public static function processTemplate($template, $override = false, $mode = 0644)
    {

        // prepare the target filename
        $targetFile = Setup::prepareOsSpecificPath($template);

        // query whether we've to override the file
        if ($override === false && is_file($targetFile) === true) {
            return;
        }

        // prepare the target directory
        Setup::prepareDirectory($template);

        // process the template and store the result in the passed file
        ob_start();
        include Setup::prepareOsSpecificPath(sprintf('resources/templates/%s.phtml', $template));
        file_put_contents($targetFile, ob_get_clean());

        // set the correct mode for the file
        Setup::changeFilePermissions($template, $mode);
    }

    /**
     * Sets the passed mode for the file if NOT on Windows.
     *
     * @param string  $filename The filename to set the mode for
     * @param integer $mode     The mode to set
     *
     * @return void
     */
    public static function changeFilePermissions($filename, $mode = 0644)
    {

        // make the passed filename OS compliant
        $toBeChanged = Setup::prepareOsSpecificPath($filename);

        // change the mode, if we're not on Windows
        if (SetupKeys::OS_FAMILY_WINDOWS !== strtolower(php_uname('s'))) {
            chmod($toBeChanged, $mode);
        }
    }

    /**
     * Prepares the passed directory if necessary.
     *
     * @param string  $directory The directory to prepare
     * @param integer $mode      The mode of the directory
     *
     * @return void
     */
    public static function prepareDirectory($directory, $mode = 0775)
    {

        // make the passed directory OS compliant
        $toBePreapared = Setup::prepareOsSpecificPath($directory);

        // make sure the directory exists
        if (is_dir(dirname($toBePreapared)) === false) {
            mkdir(dirname($toBePreapared), $mode, true);
        }
    }

    /**
     * Prepares the passed path to work on the actual OS.
     *
     * @param string $path The path we want to perpare
     *
     * @return string The prepared path
     */
    public static function prepareOsSpecificPath($path)
    {
        return str_replace('/', DIRECTORY_SEPARATOR, $path);
    }
}
