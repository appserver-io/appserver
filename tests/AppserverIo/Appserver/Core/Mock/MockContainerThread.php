<?php

/**
 * AppserverIo\Appserver\Core\Mock\ContainerThread
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @author    Johann Zelger <jw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
namespace AppserverIo\Appserver\Core\Mock;

/**
 * This is a mock object for running PHPUnit testing purposes only.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @author    Johann Zelger <jw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
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