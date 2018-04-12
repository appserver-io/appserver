<?php

/**
 * \AppserverIo\Appserver\PersistenceContainer\Doctrine\DoctrineEntityManagerProxy
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

use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManagerInterface;
use AppserverIo\RemoteMethodInvocation\SessionInterface;
use AppserverIo\RemoteMethodInvocation\RemoteMethodCall;
use AppserverIo\RemoteMethodInvocation\RemoteMethodInterface;
use AppserverIo\RemoteMethodInvocation\RemoteObjectInterface;

/**
 * Decorator for the Doctrine entity manager instance.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class DoctrineEntityManagerProxy implements RemoteObjectInterface, EntityManagerInterface
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
        return new DoctrineEntityManagerProxy($className);
    }

    /**
     * Gets the database connection object used by the EntityManager.
     *
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection()
    {
        return $this->__call('getConnection', array());
    }

    /**
     * Gets an ExpressionBuilder used for object-oriented construction of query expressions.
     *
     * Example:
     *
     * <code>
     *     $qb = $em->createQueryBuilder();
     *     $expr = $em->getExpressionBuilder();
     *     $qb->select('u')->from('User', 'u')
     *         ->where($expr->orX($expr->eq('u.id', 1), $expr->eq('u.id', 2)));
     * </code>
     *
     * @return \Doctrine\ORM\Query\Expr
     */
    public function getExpressionBuilder()
    {
        return $this->__call('getExpressionBuilder', array());
    }

    /**
     * Starts a transaction on the underlying database connection.
     *
     * @return void
     */
    public function beginTransaction()
    {
        return $this->__call('beginTransaction', array());
    }

    /**
     * Executes a function in a transaction.
     *
     * The function gets passed this EntityManager instance as an (optional) parameter.
     *
     * {@link flush} is invoked prior to transaction commit.
     *
     * If an exception occurs during execution of the function or flushing or transaction commit,
     * the transaction is rolled back, the EntityManager closed and the exception re-thrown.
     *
     * @param callable $func The function to execute transactionally.
     *
     * @return mixed The non-empty value returned from the closure or true instead.
     */
    public function transactional($func)
    {
        return $this->__call('transactional', array($func));
    }

    /**
     * Commits a transaction on the underlying database connection.
     *
     * @return void
     */
    public function commit()
    {
        return $this->__call('commit', array());
    }

    /**
     * Performs a rollback on the underlying database connection.
     *
     * @return void
     */
    public function rollback()
    {
        return $this->__call('rollback', array());
    }

    /**
     * Creates a new Query object.
     *
     * @param string $dql The DQL string.
     *
     * @return \Doctrine\ORM\Query
     */
    public function createQuery($dql = '')
    {
        return $this->__call('createQuery', array($dql));
    }

    /**
     * Creates a Query from a named query.
     *
     * @param string $name The query name
     *
     * @return \Doctrine\ORM\Query
     */
    public function createNamedQuery($name)
    {
        return $this->__call('createNamedQuery', array($name));
    }

    /**
     * Creates a native SQL query.
     *
     * @param string           $sql The SQL command
     * @param ResultSetMapping $rsm The ResultSetMapping to use
     *
     * @return \Doctrine\ORM\NativeQuery The query instance
     */
    public function createNativeQuery($sql, ResultSetMapping $rsm)
    {
        return $this->__call('createNativeQuery', array($sql, $rsm));
    }

    /**
     * Creates a NativeQuery from a named native query.
     *
     * @param string $name The query name
     *
     * @return \Doctrine\ORM\NativeQuery The query instance
     */
    public function createNamedNativeQuery($name)
    {
        return $this->__call('createNamedNativeQuery', array($name));
    }

    /**
     * Create a QueryBuilder instance
     *
     * @return \Doctrine\ORM\QueryBuilder The query builder instance
     */
    public function createQueryBuilder()
    {
        return $this->__call('createQueryBuilder', array());
    }

    /**
     * Gets a reference to the entity identified by the given type and identifier
     * without actually loading it, if the entity is not yet loaded.
     *
     * @param string $entityName The name of the entity type.
     * @param mixed  $id         The entity identifier.
     *
     * @return object The entity reference.
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function getReference($entityName, $id)
    {
        return $this->__call('getReference', array($entityName, $id));
    }

    /**
     * Gets a partial reference to the entity identified by the given type and identifier
     * without actually loading it, if the entity is not yet loaded.
     *
     * The returned reference may be a partial object if the entity is not yet loaded/managed.
     * If it is a partial object it will not initialize the rest of the entity state on access.
     * Thus you can only ever safely access the identifier of an entity obtained through
     * this method.
     *
     * The use-cases for partial references involve maintaining bidirectional associations
     * without loading one side of the association or to update an entity without loading it.
     * Note, however, that in the latter case the original (persistent) entity data will
     * never be visible to the application (especially not event listeners) as it will
     * never be loaded in the first place.
     *
     * @param string $entityName The name of the entity type.
     * @param mixed  $identifier The entity identifier.
     *
     * @return object The (partial) entity reference.
     */
    public function getPartialReference($entityName, $identifier)
    {
        return $this->__call('getPartialReference', array($entityName, $identifier));
    }

    /**
     * Closes the EntityManager. All entities that are currently managed
     * by this EntityManager become detached. The EntityManager may no longer
     * be used after it is closed.
     *
     * @return void
     */
    public function close()
    {
        return $this->__call('close', array());
    }

    /**
     * Creates a copy of the given entity. Can create a shallow or a deep copy.
     *
     * @param object  $entity The entity to copy.
     * @param boolean $deep   FALSE for a shallow copy, TRUE for a deep copy.
     *
     * @return object The new entity.
     *
     * @throws \BadMethodCallException
     */
    public function copy($entity, $deep = false)
    {
        return $this->__call('copy', array($entity, $deep));
    }

    /**
     * Acquire a lock on the given entity.
     *
     * @param object       $entity      The entity to be locked
     * @param integer      $lockMode    The lock mode
     * @param integer|null $lockVersion The lock version
     *
     * @return void
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\PessimisticLockException
     */
    public function lock($entity, $lockMode, $lockVersion = null)
    {
        return $this->__call('lock', array($lockMode, $lockVersion));
    }

    /**
     * Finds an Entity by its identifier.
     *
     * @param string       $entityName  The class name of the entity to find.
     * @param mixed        $id          The identity of the entity to find.
     * @param integer|null $lockMode    One of the \Doctrine\DBAL\LockMode::* constants
     *                                  or NULL if no specific lock mode should be used
     *                                  during the search.
     * @param integer|null $lockVersion The version of the entity to find when using
     *                                  optimistic locking.
     *
     * @return object|null The entity instance or NULL if the entity can not be found.
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\TransactionRequiredException
     * @throws \Doctrine\ORM\ORMException
     */
    public function find($entityName, $id, $lockMode = null, $lockVersion = null)
    {
        return $this->__call('find', array($entityName, $id, $lockMode, $lockVersion));
    }

    /**
     * Flushes all changes to objects that have been queued up to now to the database.
     * This effectively synchronizes the in-memory state of managed objects with the
     * database.
     *
     * If an entity is explicitly passed to this method only this entity and
     * the cascade-persist semantics + scheduled inserts/removals are synchronized.
     *
     * @param null|object|array $entity The entity to be synchronized
     *
     * @return void
     *
     * @throws \Doctrine\ORM\OptimisticLockException If a version check on an entity that
     *         makes use of optimistic locking fails.
     */
    public function flush($entity = null)
    {
        return $this->__call('flush', array($entity));
    }

    /**
     * Gets the EventManager used by the EntityManager.
     *
     * @return \Doctrine\Common\EventManager The event manager
     */
    public function getEventManager()
    {
        return $this->__call('getEventManager', array());
    }

    /**
     * Gets the Configuration used by the EntityManager.
     *
     * @return \Doctrine\ORM\Configuration The configuration
     */
    public function getConfiguration()
    {
        return $this->__call('getConfiguration', array());
    }

    /**
     * Check if the Entity manager is open or closed.
     *
     * @return boolean TRUE if the EM is open
     */
    public function isOpen()
    {
        return $this->__call('isOpen', array());
    }

    /**
     * Gets the UnitOfWork used by the EntityManager to coordinate operations.
     *
     * @return \Doctrine\ORM\UnitOfWork The unit of work
     */
    public function getUnitOfWork()
    {
        return $this->__call('getUnitOfWork', array());
    }

    /**
    * Gets a hydrator for the given hydration mode.
    *
    * This method caches the hydrator instances which is used for all queries that don't
    * selectively iterate over the result.
    *
    * @param int $hydrationMode The hydration mode to use
    *
    * @return \Doctrine\ORM\Internal\Hydration\AbstractHydrator
    * @deprecated
    */
    public function getHydrator($hydrationMode)
    {
        return $this->__call('getHydrator', array($hydrationMode));
    }

    /**
     * Create a new instance for the given hydration mode.
     *
    * @param integer $hydrationMode The hydration mode to use
     *
     * @return \Doctrine\ORM\Internal\Hydration\AbstractHydrator
     * @throws \Doctrine\ORM\ORMException
     */
    public function newHydrator($hydrationMode)
    {
        return $this->__call('newHydtrator', array($hydrationMode));
    }

    /**
     * Gets the proxy factory used by the EntityManager to create entity proxies.
     *
     * @return \Doctrine\ORM\Proxy\ProxyFactory The proxy factory
     */
    public function getProxyFactory()
    {
        return $this->__call('getProxyFactory', array());
    }

    /**
     * Gets the enabled filters.
     *
     * @return \Doctrine\ORM\Query\FilterCollection The active filter collection
     */
    public function getFilters()
    {
        return $this->__call('getFilters', array());
    }

    /**
     * Checks whether the state of the filter collection is clean.
     *
     * @return boolean TRUE, if the filter collection is clean
     */
    public function isFiltersStateClean()
    {
        return $this->__call('isFiltersStateClean', array());
    }

    /**
     * Checks whether the Entity Manager has filters.
     *
     * @return boolean TRUE, if the EM has a filter collection
     */
    public function hasFilters()
    {
        return $this->__call('hasFilters', array());
    }

    /**
     * Returns the cache API for managing the second level cache regions or NULL if the cache is not enabled.
     *
     * @return \Doctrine\ORM\Cache|null
     */
    public function getCache()
    {
        return $this->__call('getCache', array());
    }

    /**
     * Tells the ObjectManager to make an instance managed and persistent.
     *
     * The object will be entered into the database as a result of the flush operation.
     *
     * NOTE: The persist operation always considers objects that are not yet known to
     * this ObjectManager as NEW. Do not pass detached objects to the persist operation.
     *
     * @param object $object The instance to make managed and persistent.
     *
     * @return void
     */
    public function persist($object)
    {
        return $this->__call('persist', array($object));
    }

    /**
     * Removes an object instance.
     *
     * A removed object will be removed from the database as a result of the flush operation.
     *
     * @param object $object The object instance to remove.
     *
     * @return void
     */
    public function remove($object)
    {
        return $this->__call('remove', array($object));
    }

    /**
     * Merges the state of a detached object into the persistence context
     * of this ObjectManager and returns the managed copy of the object.
     * The object passed to merge will not become associated/managed with this ObjectManager.
     *
     * @param object $object The object to be merged
     *
     * @return object The merge instance
     */
    public function merge($object)
    {
        return $this->__call('merge', array($object));
    }

    /**
     * Clears the ObjectManager. All objects that are currently managed
     * by this ObjectManager become detached.
     *
     * @param string|null $objectName if given, only objects of this type will get detached.
     *
     * @return void
     */
    public function clear($objectName = null)
    {
        return $this->__call('clear', array($objectName));
    }

    /**
     * Detaches an object from the ObjectManager, causing a managed object to
     * become detached. Unflushed changes made to the object if any
     * (including removal of the object), will not be synchronized to the database.
     * Objects which previously referenced the detached object will continue to
     * reference it.
     *
     * @param object $object The object to detach.
     *
     * @return void
     */
    public function detach($object)
    {
        return $this->__call('detach', array($object));
    }

    /**
     * Refreshes the persistent state of an object from the database,
     * overriding any local changes that have not yet been persisted.
     *
     * @param object $object The object to refresh.
     *
     * @return void
     */
    public function refresh($object)
    {
        return $this->__call('refresh', array($object));
    }

    /**
     * Gets the repository for a class.
     *
     * @param string $className The class name to return the repository for
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getRepository($className)
    {
        return $this->__call('getRepository', array($className));
    }

    /**
     * Returns the ClassMetadata descriptor for a class.
     *
     * The class name must be the fully-qualified class name without a leading backslash
     * (as it is returned by get_class($obj)).
     *
     * @param string $className The class name to return the metadata for
     *
     * @return \Doctrine\Common\Persistence\Mapping\ClassMetadata
     */
    public function getClassMetadata($className)
    {
        return $this->__call('getClassMetadata', array($className));
    }

    /**
     * Gets the metadata factory used to gather the metadata of classes.
     *
     * @return \Doctrine\Common\Persistence\Mapping\ClassMetadataFactory
     */
    public function getMetadataFactory()
    {
        return $this->__call('getMetadataFactory', array());
    }

    /**
     * Helper method to initialize a lazy loading proxy or persistent collection.
     *
     * This method is a no-op for other objects.
     *
     * @param object $obj The object to be initialized
     *
     * @return void
     */
    public function initializeObject($obj)
    {
        return $this->__call('initializeObject', array($obj));
    }

    /**
     * Checks if the object is part of the current UnitOfWork and therefore managed.
     *
     * @param object $object The object to check
     *
     * @return bool
     */
    public function contains($object)
    {
        return $this->__call('contains', array($object));
    }
}
