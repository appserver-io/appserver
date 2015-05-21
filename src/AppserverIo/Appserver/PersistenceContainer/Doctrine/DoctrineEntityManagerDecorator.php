<?php

/**
 * \AppserverIo\Appserver\PersistenceContainer\Doctrine\DoctrineEntityManagerDecorator
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

namespace AppserverIo\Appserver\PersistenceContainer\Doctrine;

use Doctrine\ORM\Decorator\EntityManagerDecorator;

/**
 * Decorator for the Doctrine entity manager instance.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class DoctrineEntityManagerDecorator extends EntityManagerDecorator
{

    /**
     * Sleep method to unset the PDO connection before serializing
     * the entity manager instance.
     *
     * @return array The array with the properties to serialize
     */
    public function __sleep()
    {

        // close the connection
        $this->getWrapped()->getConnection()->close();

        // we want to serialize NOTHING
        return array();
    }

    /**
     * Closes the Doctrine connection if a wrapped
     * instance is available
     */
    public function __destruct()
    {

        // query whether we've a wrapped instance
        if ($wrapped = $this->getWrapped()) {
            $this->getWrapped()->getConnection()->close();
        }
    }

    /**
     * Returns the wrapped entity manager instance.
     *
     * @return \Doctrine\ORM\EntityManagerInterface The wrapped entity manager instance
     */
    public function getWrapped()
    {
        return $this->wrapped;
    }
}
