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
use AppserverIo\RemoteMethodInvocation\RemoteObjectInterface;
use Doctrine\ORM\EntityManagerInterface;
use AppserverIo\RemoteMethodInvocation\SessionInterface;
use AppserverIo\RemoteMethodInvocation\RemoteMethodInterface;
use AppserverIo\RemoteMethodInvocation\RemoteMethodCall;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Decorator for the Doctrine entity manager instance.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class DoctrineEntityManagerDecorator implements RemoteObjectInterface, EntityManagerInterface
{

    /**
     * Holds the ContextSession for this proxy.
     *
     * @var \AppserverIo\RemoteMethodInvocation\SessionInterface
     */
    protected $session = null;

    /**
     * The class name to proxy.
     *
     * @var string $className
     */
    protected $className = null;

    /**
     * Initializes the proxy with the class name to proxy.
     *
     * @param mixed $className The name of the class to create the proxy for
     */
    public function __construct($className = 'AppserverIo\Appserver\Core\InitialContext')
    {
        $this->className = $className;
    }

    /**
     * The name of the original object.
     *
     * @return string The name of the original object
     * @see \AppserverIo\RemoteMethodInvocation\RemoteObjectInterface::__getClassName()
     */
    public function __getClassName()
    {
        return $this->className;
    }

    /**
     * Sets the session with the connection instance.
     *
     * @param \AppserverIo\RemoteMethodInvocation\SessionInterface $session The session instance to use
     *
     * @return \AppserverIo\RemoteMethodInvocation\RemoteObjectInterface The instance itself
     */
    public function __setSession(SessionInterface $session)
    {
        $this->session = $session;
        return $this;
    }

    /**
     * Returns the session instance.
     *
     * @return \AppserverIo\RemoteMethodInvocation\SessionInterface The session instance
     * @see \AppserverIo\RemoteMethodInvocation\RemoteObjectInterface::__getSession()
     */
    public function __getSession()
    {
        return $this->session;
    }

    /**
     * Invokes the remote execution of the passed remote method.
     *
     * @param string $method The remote method to call
     * @param array  $params The parameters for the method call
     *
     * @return mixed The result of the remote method call
     */
    public function __call($method, $params)
    {
        $methodCall = new RemoteMethodCall($this->__getClassName(), $method, $this->__getSession()->getSessionId());
        foreach ($params as $key => $value) {
            $methodCall->addParameter($key, $value);
        }
        return $this->__invoke($methodCall, $this->__getSession());
    }

    /**
     * Invokes the remote execution of the passed remote method.
     *
     * @param \AppserverIo\RemoteMethodInvocation\RemoteMethodInterface $methodCall The remote method call instance
     * @param \AppserverIo\RemoteMethodInvocation\SessionInterface      $session    The session with the connection instance to use
     *
     * @return mixed The result of the remote method call
     */
    public function __invoke(RemoteMethodInterface $methodCall, SessionInterface $session)
    {
        return $this->__setSession($session)->__getSession()->send($methodCall);
    }

    /**
     * Factory method to create a new instance of the requested proxy implementation.
     *
     * @param string $className The name of the class to create the proxy for
     *
     * @return \AppserverIo\RemoteMethodInvocation\RemoteObjectInterface The proxy instance
     */
    public static function __create($className)
    {
        return new DoctrineEntityManagerDecorator($className);
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        return $this->__call('getConnection', array());
    }

    /**
     * {@inheritdoc}
     */
    public function getExpressionBuilder()
    {
        return $this->__call('getExpressionBuilder', array());
    }

    /**
     * {@inheritdoc}
     */
    public function beginTransaction()
    {
        return $this->__call('beginTransaction', array());
    }

    /**
     * {@inheritdoc}
     */
    public function transactional($func)
    {
        return $this->__call('transactional', array($func));
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        return $this->__call('commit', array());
    }

    /**
     * {@inheritdoc}
     */
    public function rollback()
    {
        return $this->__call('rollback', array());
    }

    /**
     * {@inheritdoc}
     */
    public function createQuery($dql = '')
    {
        return $this->__call('createQuery', array($dql));
    }

    /**
     * {@inheritdoc}
     */
    public function createNamedQuery($name)
    {
        return $this->__call('createNamedQuery', array($name));
    }

    /**
     * {@inheritdoc}
     */
    public function createNativeQuery($sql, ResultSetMapping $rsm)
    {
        return $this->__call('createNativeQuery', array($sql, $rsm));
    }

    /**
     * {@inheritdoc}
     */
    public function createNamedNativeQuery($name)
    {
        return $this->__call('createNamedNativeQuery', array($name));
    }

    /**
     * {@inheritdoc}
     */
    public function createQueryBuilder()
    {
        return $this->__call('createQueryBuilder', array());
    }

    /**
     * {@inheritdoc}
     */
    public function getReference($entityName, $id)
    {
        return $this->__call('getReference', array($entityName, $id));
    }

    /**
     * {@inheritdoc}
     */
    public function getPartialReference($entityName, $identifier)
    {
        return $this->__call('getPartialReference', array($entityName, $identifier));
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        return $this->__call('close', array());
    }

    /**
     * {@inheritdoc}
     */
    public function copy($entity, $deep = false)
    {
        return $this->__call('copy', array($entity, $deep));
    }

    /**
     * {@inheritdoc}
     */
    public function lock($entity, $lockMode, $lockVersion = null)
    {
        return $this->__call('lock', array($lockMode, $lockVersion));
    }

    /**
     * {@inheritdoc}
     */
    public function find($entityName, $id, $lockMode = null, $lockVersion = null)
    {
        return $this->__call('find', array($entityName, $id, $lockMode, $lockVersion));
    }

    /**
     * {@inheritdoc}
     */
    public function flush($entity = null)
    {
        return $this->__call('flush', array($entity));
    }

    /**
     * {@inheritdoc}
     */
    public function getEventManager()
    {
        return $this->__call('getEventManager', array());
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return $this->__call('getConfiguration', array());
    }

    /**
     * {@inheritdoc}
     */
    public function isOpen()
    {
        return $this->__call('isOpen', array());
    }

    /**
     * {@inheritdoc}
     */
    public function getUnitOfWork()
    {
        return $this->__call('getUnitOfWork', array());
    }

    /**
     * {@inheritdoc}
     */
    public function getHydrator($hydrationMode)
    {
        return $this->__call('getHydrator', array($hydrationMode));
    }

    /**
     * {@inheritdoc}
     */
    public function newHydrator($hydrationMode)
    {
        return $this->__call('newHydtrator', array($hydrationMode));
    }

    /**
     * {@inheritdoc}
     */
    public function getProxyFactory()
    {
        return $this->__call('getProxyFactory', array());
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return $this->__call('getFilters', array());
    }

    /**
     * {@inheritdoc}
     */
    public function isFiltersStateClean()
    {
        return $this->__call('isFiltersStateClean', array());
    }

    /**
     * {@inheritdoc}
     */
    public function hasFilters()
    {
        return $this->__call('hasFilters', array());
    }

    /**
     * {@inheritdoc}
     */
    public function getCache()
    {
        return $this->__call('getCache', array());
    }

    /**
     * {@inheritdoc}
     */
    public function persist($object)
    {
        return $this->__call('persist', array($object));
    }

    /**
     * {@inheritdoc}
     */
    public function remove($object)
    {
        return $this->__call('remove', array($object));
    }

    /**
     * {@inheritdoc}
     */
    public function merge($object)
    {
        return $this->__call('merge', array($object));
    }

    /**
     * {@inheritdoc}
     */
    public function clear($objectName = null)
    {
        return $this->__call('clear', array($objectName));
    }

    /**
     * {@inheritdoc}
     */
    public function detach($object)
    {
        return $this->__call('detach', $object);
    }

    /**
     * {@inheritdoc}
     */
    public function refresh($object)
    {
        return $this->__call('refresh', array($object));
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository($className)
    {
        return $this->__call('getRepository', array($className));
    }

    /**
     * {@inheritdoc}
     */
    public function getClassMetadata($className)
    {
        return $this->__call('getClassMetadata', array($className));
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataFactory()
    {
        return $this->__call('getMetadataFactory', array());
    }

    /**
     * {@inheritdoc}
     */
    public function initializeObject($obj)
    {
        return $this->__call('initializeObject', array($obj));
    }

    /**
     * {@inheritdoc}
     */
    public function contains($object)
    {
        return $this->__call('contains', array($object));
    }
}
