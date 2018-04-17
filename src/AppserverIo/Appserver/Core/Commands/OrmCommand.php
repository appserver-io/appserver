<?php

/**
 * AppserverIo\Appserver\Core\Commands\OrmCommand
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

namespace AppserverIo\Appserver\Core\Commands;

use Symfony\Component\Console\Output\BufferedOutput;

/**
 * The Doctrine ORM CLI implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class OrmCommand extends AbstractCommand
{

    /**
     * The unique command name.
     *
     * @var string
     */
    const COMMAND = 'orm';

    /**
     * Executes the command.
     *
     * @param array $params The arguments passed to the command
     *
     * @return mixed|null The result of the command
     * @see \AppserverIo\Appserver\Core\Commands\CommandInterface::execute()
     */
    public function execute(array $params = array())
    {
        $this->doOrm($params);
    }

    /**
     * Execute the Doctrine ORM CLI tool.
     *
     * @param array $command The Doctrine command to be executed
     *
     * @return string The commands output
     */
    protected function doOrm(array $command = array())
    {

        try {
            // the first arguement has to be the application name
            $applicationName = array_shift($command);

            // try to load the application
            /** \AppserverIo\Psr\Application\ApplicationInterface $application */
            $application = $this->getNamingDirectory()->search(sprintf('php:global/combined-appserver/%s/ApplicationInterface', $applicationName));

            // register the applications class loaders
            $application->registerClassLoaders();

            // try to load the application's default entity manager
            /** \Doctrine\ORM\EntityManagerInterface $entityManager */
            $entityManager = $this->loadDefaultEntityManager($application);

            // initialize the helper set with the entity manager instance
            /** \Symfony\Component\Console\Helper\HelperSet $helperSet */
            $helperSet = \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);

            // create the Symfony Console application
            /** \Symfony\Component\Console\Application $app */
            $app = \Doctrine\ORM\Tools\Console\ConsoleRunner::createApplication($helperSet);
            $app->setAutoExit(false);

            // as Doctrine CLI uses Symfony Console component, we've to simulate the commandline args
            $argv = array(OrmCommand::COMMAND);
            $argv = array_merge($argv, $command);

            // create a new instance of the commandline args
            $argvInput = new \Symfony\Component\Console\Input\ArgvInput($argv);

            // run the Symfony Console application
            $app->run($argvInput, $buffer = new BufferedOutput());

            // log a debug message with the output
            $this->getSystemLogger()->debug($result = $buffer->fetch());

            // write the result to the output
            $this->write("$result\$\n");

        } catch (\Exception $e) {
            // log the exception
            $this->getSystemLogger()->error($e->__toString());
            // write the error message to the output
            $this->write("{$e->__toString()}ERROR\n");
        }
    }
}
