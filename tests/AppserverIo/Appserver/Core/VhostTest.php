<?php

/**
 * AppserverIo\Appserver\Core\VhostTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace AppserverIo\Appserver\Core;

/**
 *
 * @package AppserverIo\Appserver\Core
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class VhostTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test if the VHost has successfully been initialized.
     *
     * @return void
     */
    public function testConstructor()
    {
        $vhost = new Vhost($name = 'foo.bar', $appBase = '/foo.bar', $aliases = array('www.foo.bar', 'test.foo.bar'));
        $this->assertSame($name, $vhost->getName());
        $this->assertSame($appBase, $vhost->getAppBase());
        $this->assertSame($aliases, $vhost->getAliases());
    }
}