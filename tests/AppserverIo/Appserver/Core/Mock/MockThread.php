<?php

/**
 * AppserverIo\Appserver\Core\Mock\MockThread
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace AppserverIo\Appserver\Core\Mock;

use AppserverIo\Appserver\Core\AbstractThread;

/**
 *
 * @package AppserverIo\Appserver\Core
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class MockThread extends AbstractThread
{

    /**
     * Soe instance to test.
     *
     * @var \stdClass
     */
    protected $someInstance;

    /**
     * TRUE if the thread has been executed, else FALSE.
     *
     * @var boolean
     */
    protected $executed = false;

    /**
     * (non-PHPdoc)
     *
     * @see \AppserverIo\Appserver\Core\AbstractThread::main()
     * @throws \Exception Is thrown to be cached by PHPUnit framework
     */
    public function main()
    {
        $this->executed = true;
        ;
    }

    /**
     * Helper method to check if the threads main
     * method has been excecuted.
     *
     * @return boolean TRUE if the thread has been executed
     */
    public function hasExcecuted()
    {
        return $this->executed;
    }

    /**
     * Returns some instance.
     *
     * @return stdClass Some instance
     */
    public function getSomeInstance()
    {
        return $this->someInstance;
    }

    /**
     * Method to initialze the thread with the constructor
     * params without the initial context.
     *
     * @param stcClass $someInstance
     *            Some instance to test
     * @return void
     */
    public function init($someInstance)
    {
        $this->someInstance = $someInstance;
    }
}