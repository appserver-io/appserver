<?php

/**
 * \AppserverIo\Appserver\Core\AbstractContextThread
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core;

/**
 * An abstraction context layer for Threads.
 * It will automatically register the intitialContext object.
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
abstract class AbstractContextThread extends AbstractThread
{

    /**
     * The initial context instance containing the system configuration.
     *
     * @var \AppserverIo\Appserver\Application\Interfaces\ContextInterface
     */
    protected $initialContext;

    /**
     * Constructor sets initialContext object per default and calls
     * init function to pass other args.
     *
     * @param \AppserverIo\Appserver\Application\Interfaces\ContextInterface $initialContext The initial context instance
     */
    public function __construct($initialContext)
    {
        // get function args
        $functionArgs = func_get_args();
        // shift first arg to initialContext which should be a stackable implementation.
        $this->initialContext = array_shift($functionArgs);
        // call parent
        call_user_func_array(array('parent', '__construct'), $functionArgs);
    }

    /**
     * (non-PHPdoc)
     *
     * @return void
     * @see \AppserverIo\Appserver\Core\AbstractThread::run()
     */
    public function run()
    {
        // register the class loader again, because in a Thread the context has been lost maybe
        require SERVER_AUTOLOADER;

        // call the parent run method to start the thread
        parent::run();
    }

    /**
     * Returns the initialContext object
     *
     * @return \AppserverIo\Appserver\Application\Interfaces\ContextInterface
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * Creates a new instance of the passed class name and passes the
     * args to the instance constructor.
     *
     * @param string $className The class name to create the instance of
     * @param array  $args      The parameters to pass to the constructor
     *
     * @return object The created instance
     */
    public function newInstance($className, array $args = array())
    {
        return $this->getInitialContext()->newInstance($className, $args);
    }
}
