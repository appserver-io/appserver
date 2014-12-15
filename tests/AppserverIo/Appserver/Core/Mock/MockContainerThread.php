<?php

/**
 * AppserverIo\Appserver\Core\Mock\ContainerThread
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace AppserverIo\Appserver\Core\Mock;

/**
 * This is a mock object for running PHPUnit testing purposes only.
 *
 * @package AppserverIo\Appserver\Core
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 * @author Johann Zelger <jz@appserver.io>
 * @coversNothing
 */
class MockContainerThread
{

    /**
     *@see \AppserverIo\Appserver\Core\ContainerThread::__construct()
     */
    public function __construct($initialContext, $id)
    {}

    /**
     * @see \Thread::start()
     */
    public function start()
    {}

    /**
     * @see \Thread::join()
     */
    public function join()
    {}
    
    /**
     * @see \Thread::synchronized()
     */
    public function synchronized(\Closure $block)
    {}
}