<?php

/**
 * RoboFile.php
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
 * @link      http://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

use Robo\Robo;
use AppserverIo\RoboTasks\AbstractRoboFile;
use AppserverIo\RoboTasks\ConfigurationKeys;

/**
 * Defines the available appserver.io build tasks.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @SuppressWarnings(PHPMD)
 */
class RoboFile extends AbstractRoboFile
{

    /**
     * Load the appserver.io base tasks.
     *
     * @var \AppserverIo\RoboTasks\Base\loadTasks
     */
    use AppserverIo\RoboTasks\Base\loadTasks;

    /**
     * Initializes the default configuration.
     */
    public function __construct()
    {

        // call parent constructor
        parent::__construct();

        // initialize the default configuration
        Robo::config()->setDefault(sprintf('%s.%s', ConfigurationKeys::DIRS, 'dist'), sprintf('%s/dist', getcwd()));
    }

    /**
     * Run's the composer install command.
     *
     * @return void
     */
    public function composerInstall()
    {
        // optimize autoloader with custom path
        $this->taskComposerInstall()
             ->preferDist()
             ->optimizeAutoloader()
             ->run();
    }

    /**
     * Run's the composer update command.
     *
     * @return void
     */
    public function composerUpdate()
    {
        // optimize autoloader with custom path
        $this->taskComposerUpdate()
             ->preferDist()
             ->optimizeAutoloader()
             ->run();
    }

    /**
     * Clean up the environment for a new build.
     *
     * @return void
     */
    public function clean()
    {
        $this->taskDeleteDir($this->getTargetDir())->run();
    }

    /**
     * Prepare's the environment for a new build.
     *
     * @return void
     */
    public function prepare()
    {
        $this->taskFileSystemStack()
             ->mkdir($this->getDistDir())
             ->mkdir($this->getTargetDir())
             ->mkdir($this->getReportsDir())
             ->run();
    }

    /**
     * Run's the PHPMD.
     *
     * @return void
     */
    public function runMd()
    {

        // run the mess detector
        $this->_exec(
            sprintf(
                '%s/bin/phpmd %s xml phpmd.xml --reportfile %s/reports/pmd.xml --ignore-violations-on-exit || exit 0',
                $this->getVendorDir(),
                $this->getSrcDir(),
                $this->getTargetDir()
            )
        );
    }

    /**
     * Run's the PHPCPD.
     *
     * @return void
     */
    public function runCpd()
    {

        // run the copy past detector
        $this->_exec(
            sprintf(
                '%s/bin/phpcpd --names-exclude=DirectoryParser.php,EntityManagerFactory.php,QueueManager.php %s --log-pmd %s/reports/pmd-cpd.xml',
                $this->getVendorDir(),
                $this->getSrcDir(),
                $this->getTargetDir()
            )
        );
    }

    /**
     * Run's the PHPCodeSniffer.
     *
     * @return void
     */
    public function runCs()
    {

        // run the code sniffer
        $this->_exec(
            sprintf(
                '%s/bin/phpcs -n --report-full --extensions=php --standard=phpcs.xml --report-checkstyle=%s/reports/phpcs.xml %s',
                $this->getVendorDir(),
                $this->getTargetDir(),
                $this->getSrcDir()
            )
        );
    }

    /**
     * Run's the PHPUnit tests.
     *
     * @return void
     */
    public function runTests()
    {

        // run PHPUnit
        $this->taskPHPUnit(sprintf('%s/bin/phpunit', $this->getVendorDir()))
             ->configFile('phpunit.xml')
             ->run();
    }

    /**
     * The complete build process.
     *
     * @return void
     */
    public function build()
    {
        $this->clean();
        $this->prepare();
        $this->runCs();
        $this->runCpd();
        $this->runMd();
        $this->runTests();
    }

    /**
     * Returns the distribution directory.
     *
     * @return string The distribution directory
     */
    protected function getDistDir()
    {
        return Robo::config()->get(sprintf('%s.%s', ConfigurationKeys::DIRS, 'dist'));
    }
}
