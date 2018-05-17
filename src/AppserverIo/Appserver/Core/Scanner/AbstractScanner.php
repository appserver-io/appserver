<?php

/**
 * \AppserverIo\Appserver\Core\Scanner\AbstractScanner
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
 * @copyright 2015 TechDivision GmbH - <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.techdivision.com/
 */

namespace AppserverIo\Appserver\Core\Scanner;

use AppserverIo\Appserver\Core\AbstractContextThread;
use AppserverIo\Appserver\Core\Interfaces\ScannerInterface;
use AppserverIo\Psr\ApplicationServer\ContextInterface;

/**
 * Abstract scanner which provides basic functionality to its children.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH - <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.techdivision.com/
 */
abstract class AbstractScanner extends AbstractContextThread implements ScannerInterface
{
    /**
     * OS signature when calling php_uname('s') on Mac OS x 10.8.x/10.9.x.
     *
     * @var string
     */
    const DARWIN = 'Darwin';

    /**
     * OS signature when calling php_uname('s') on Linux Debian/Ubuntu/Fedora and CentOS.
     *
     * @var string
     */
    const LINUX = 'Linux';

    /**
     * OS signature when calling php_uname('s') on Windows NT.
     *
     * @var string
     */
    const WINDOWS_NT = 'Windows NT';

    /**
     * There are some major init systems which are re-used within different OSs
     *
     * @var string
     */
    const LAUNCHD_INIT_STRING = '/sbin/appserverctl restart';
    const SYSTEMV_INIT_STRING = '/etc/init.d/appserver restart > /dev/null';
    const SYSTEMD_INIT_STRING = 'systemctl restart appserver';
    const WIN_NT_INIT_STRING = 'net stop "appserver" && net start "appserver" || net start "appserver"';

    /**
     * The mapping of Linux distributions to their release file's name
     *
     * @link http://linuxmafia.com/faq/Admin/release-files.html
     *
     * @var array $distroMapping
     */
    protected $distroMapping;

    /**
     * The API service used to load the deployment directory.
     *
     * @var \AppserverIo\Appserver\Core\Api\ContainerService
     */
    protected $service;

    /**
     * Array that contains the available startup scripts.
     *
     * @var array
     */
    protected $restartCommands;

    /**
     * The unique scanner name.
     *
     * @var string
     */
    protected $name;

    /**
     * Constructor sets initialContext object per default and calls
     * init function to pass other args.
     *
     * @param \AppserverIo\Psr\ApplicationServer\ContextInterface $initialContext The initial context instance
     * @param string                                              $name           The unique scanner name from the configuration
     */
    public function __construct(ContextInterface $initialContext, $name)
    {

        // invoke parent constructor
        parent::__construct($initialContext);

        // set the unique scanner name
        $this->name = $name;
    }

    /**
     * Initalizes the scanner with the necessary service instance.
     *
     * @return void
     * @see \AppserverIo\Appserver\Core\AbstractThread::init()
     */
    public function init()
    {
        // initialize the service class
        $this->service = $this->newService('AppserverIo\Appserver\Core\Api\ContainerService');

        //We will check the distribution type by it's release file, as we have problems doing so using php_uname('s').
        //These mappings are for the most common platforms. If others are needed see the link below
        //@link http://linuxmafia.com/faq/Admin/release-files.html
        $this->distroMapping = array(
            "Arch" => "arch-release",
            "Debian" => "debian_version",
            "Fedora" => "fedora-release",
            "Ubuntu" => "lsb-release",
            'Redhat' => 'redhat-release',
            'CentOS' => 'centos-release'
        );

        // initialize the available restart commands
        $this->restartCommands = array(
            DeploymentScanner::DARWIN => DeploymentScanner::LAUNCHD_INIT_STRING,
            DeploymentScanner::WINDOWS_NT => DeploymentScanner::WIN_NT_INIT_STRING,
            'Debian' . DeploymentScanner::LINUX => array(
                6 => DeploymentScanner::SYSTEMV_INIT_STRING,
                7 => DeploymentScanner::SYSTEMV_INIT_STRING,
                'default' => DeploymentScanner::SYSTEMD_INIT_STRING
            ),
            'Ubuntu' . DeploymentScanner::LINUX => array(
                12 => DeploymentScanner::SYSTEMV_INIT_STRING,
                13 => DeploymentScanner::SYSTEMV_INIT_STRING,
                14 => DeploymentScanner::SYSTEMV_INIT_STRING,
                'default' => DeploymentScanner::SYSTEMD_INIT_STRING
            ),
            'CentOS' . DeploymentScanner::LINUX => array(
                5 => DeploymentScanner::SYSTEMV_INIT_STRING,
                6 => DeploymentScanner::SYSTEMV_INIT_STRING,
                'default' => DeploymentScanner::SYSTEMD_INIT_STRING
            ),
            'Fedora' . DeploymentScanner::LINUX => DeploymentScanner::SYSTEMD_INIT_STRING
        );
    }

    /**
     * Returns The API service, e. g. to load the deployment directory.
     *
     * @return \AppserverIo\Appserver\Core\Api\ContainerService The API service instance
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * The system logger to use.
     *
     * @return \Psr\Log\LoggerInterface The system logger instance
     */
    public function getSystemLogger()
    {
        return $this->getInitialContext()->getSystemLogger();
    }

    /**
     * (non-PHPdoc)
     *
     * @param string $className The API service class name to return the instance for
     *
     * @return \AppserverIo\Psr\ApplicationServer\ServiceInterface The service instance
     * @see \AppserverIo\Appserver\Core\InitialContext::newService()
     */
    public function newService($className)
    {
        return $this->getInitialContext()->newService($className);
    }

    /**
     * Returns the restart command for the passed OS
     * if available.
     *
     * @param string      $os          The OS to return the restart command for
     * @param string|null $distVersion Version of the operating system to get the restart command for
     *
     * @return string The restart command
     * @throws \Exception Is thrown if the restart command for the passed OS is can't found
     */
    public function getRestartCommand($os, $distVersion = null)
    {

        // check if the restart command is registered
        if (array_key_exists($os, $this->restartCommands)) {
            // check whether or not we got an array, if so we have to check for the version
            $command = $this->restartCommands[$os];
            if (is_array($command)) {
                // if we do have a certain version we have to determine the command
                if (!is_null($distVersion)) {
                    // floor the dist version for comparison
                    $distVersion = (int) floor((float) $distVersion);
                    foreach ($command as $version => $potentialCommand) {
                        if ($distVersion === $version) {
                            $command = $potentialCommand;
                            break;
                        }
                    }
                }

                // if we did not find anything we have to take the default command
                if (is_array($command)) {
                    $command = $command['default'];
                }
            }

            // for Mac OS X the base directory has to be appended
            if ($os === DeploymentScanner::DARWIN) {
                $command = $this->getService()->realpath($command);
            }

            // return the command
            return $command;
        }

        // throw an exception if the restart command is not available
        throw new \Exception("Can't find restart command for OS $os");
    }

    /**
     * Restart the appserver using the appserverctl file in the sbin folder.
     *
     * @return void
     */
    public function restart()
    {

        // load the OS signature
        $os = php_uname('s');

        // log the found OS
        $this->getSystemLogger()->debug(
            "Found operating system: $os"
        );

        // check what OS we are running on
        switch ($os) {

            // If we got a Linux distribution we have to check which one
            case DeploymentScanner::LINUX:

                // Get the distribution
                $distribution = $this->getLinuxDistribution();
                // If we did not get anything
                if (!$distribution) {
                    // Log the error
                    $this->getSystemLogger()->error(
                        "The used Linux distribution could not be determined, it might not be supported."
                    );

                    // End here
                    return;
                }

                // determine the version of the found distribution
                $distVersion = $this->getDistributionVersion($distribution);

                // log the found distribution
                $this->getSystemLogger()->debug(
                    "Found Linux distribution: $distribution"
                );

                // Execute the restart command for the distribution
                exec($this->getRestartCommand($distribution . $os, $distVersion));
                break;

            // Restart with the Mac or Windows command
            case DeploymentScanner::DARWIN:
            case DeploymentScanner::WINDOWS_NT:
                exec($this->getRestartCommand($os));
                break;

            // all other OS are NOT supported actually
            default:
                $this->getSystemLogger()->error(
                    "OS $os does not support auto restart"
                );
                break;
        }
    }

    /**
     * This method will check for the Linux release file normally stored in /etc and will return
     * the corresponding distribution
     *
     * @param array $etcList List of already collected AND flipped release files we need to filter
     *
     * @return string|boolean
     */
    protected function getLinuxDistribution($etcList = array())
    {
        // Get everything from /etc directory and flip the result for faster search,
        // but only if there is no list provided already
        $etcDir = '/etc';
        if (empty($etcList)) {
            $etcList = scandir($etcDir);
            $etcList = array_flip($etcList);
        }

        // Loop through our mapping and look if we have a match
        $distributionCandidates = array();
        foreach ($this->distroMapping as $distribution => $releaseFile) {
            // Do we have a match which is not just a soft link on the actual file? If so collect the distro
            if (isset($etcList[$releaseFile]) && !is_link($etcDir . DIRECTORY_SEPARATOR . $releaseFile)) {
                $distributionCandidates[$releaseFile] = $distribution;
            }
        }

        // If we have several matches we might have to resort
        if (count($distributionCandidates) === 1) {
            return array_pop($distributionCandidates);

        } elseif (count($distributionCandidates) > 1) {
            // the file lsb-release might be existent in several Linux systems, filter it out
            if (isset($distributionCandidates['lsb-release'])) {
                unset($distributionCandidates['lsb-release']);
            }

        } else {
            // It does not make sense to check any further
            return false;
        }

        // Recursively filter the found files
        return $this->getLinuxDistribution($distributionCandidates);
    }

    /**
     * Returns the systems configuration root directory aka "etc"
     *
     * @return string
     */
    protected function getEtcDir()
    {
        return '/etc';
    }

    /**
     * This method will check for the Linux release file normally stored in /etc and will return
     * the version of the distribution
     *
     * @param string|null $distribution Distribution to search a version for
     * @param array       $etcList      List of already collected AND flipped release files we need to filter
     *
     * @return string|boolean
     */
    protected function getDistributionVersion($distribution = null, $etcList = array())
    {
        // Get everything from /etc directory and flip the result for faster search,
        // but only if there is no list provided already
        $etcDir = $this->getEtcDir();
        if (empty($etcList)) {
            $etcList = scandir($etcDir);
            $etcList = array_flip($etcList);
        }

        // check if we got a distribution to specifically look for, if not determine it first
        if (is_null($distribution)) {
            $distribution = $this->getLinuxDistribution($etcList);
        }

        // check if the distribution was properly provided/found, if not return FALSE
        if (!isset($this->distroMapping[$distribution])) {
            return false;
        }

        // loop through our mapping and look if we have a match
        $releaseFile = $this->distroMapping[$distribution];

        // do we have a match which is not just a soft link on the actual file? If so collect the file content
        $potentialVersion = '';
        if (isset($etcList[$releaseFile]) && !is_link($etcDir . DIRECTORY_SEPARATOR . $releaseFile)) {
            // retrieve the version string and try to determine the actual version from it
            $potentialVersion = file_get_contents($etcDir . DIRECTORY_SEPARATOR . $releaseFile);
            $matches = array();
            preg_match('/(\d+\.*)+/', $potentialVersion, $matches);
            // filter our findings
            if (!isset($matches[0])) {
                return false;
            }
            $potentialVersion = $matches[0];
        }

        // return what we got
        return $potentialVersion;
    }

    /**
     * Returns the time when the contents of the file were changed. The time
     * returned is a UNIX timestamp.
     *
     * If the file doesn't exists, the method returns 0 to signal that the no
     * successfull depolyment has been processed so far, e. g. the server has
     * been installed and not been started yet.
     *
     * @param string $file The deployment directory
     *
     * @return integer The UNIX timestamp with the last successfully deployment date or 0 if no successful
     *      deployment has been processed
     */
    protected function getLastFileTouch($file)
    {
        // initialize the file's mtime to 0
        $mtime = 0;

        // clear the stat cache to get real mtime if changed
        clearstatcache();

        // return the change date (last successful deployment date)
        if (is_file($file)) {
            $mtime = filemtime($file);
        }

        // return the file's mtime
        return $mtime;
    }

    /**
     * Returns an array with file extensions that are used
     * to create the directory hash.
     *
     * @return array The array with the file extensions
     * @see \AppserverIo\Appserver\Core\Scanner\AbstractScanner::getDirectoryHash()
     */
    abstract protected function getExtensionsToWatch();

    /**
     * Calculates an hash value for all files with certain extensions.
     * This is used to test if the hash value changed, so if
     * it changed, the appserver can react accordingly.
     *
     * @param \SplFileInfo $directory The directory to watch
     *
     * @return string The hash value build out of the found filenames
     */
    protected function getDirectoryHash(\SplFileInfo $directory)
    {

        // prepare the array
        $files = new \ArrayObject();

        // prepare the array with the file extensions of the files used to build the hash
        $extensionsToWatch = $this->getExtensionsToWatch();

        // clear the cache
        clearstatcache();

        // add all files with the found extensions to the array
        foreach (glob($directory . '/*.{' . implode(',', $extensionsToWatch) . '}', GLOB_BRACE) as $filename) {
            $files->append($filename);
        }

        // calculate and return the hash value for the array
        return md5($files->serialize());
    }

    /**
     * Returns the unique scanner name.
     *
     * @return string The scanner name
     * @see \AppserverIo\Appserver\Core\Interfaces\ScannerInterface::getName()
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Stop's the scanner.
     *
     * @return void
     * @see \AppserverIo\Appserver\Core\Interfaces\ScannerInterface::stop()
     */
    public function stop()
    {
        throw new \Exception(sprintf('%s not implemented yet', __METHOD__));
    }
}
