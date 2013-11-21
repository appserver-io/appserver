<?php

/**
 * TechDivision\ApplicationServer\InitialContext
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer;

use Herrera\Annotations\Tokens;
use Herrera\Annotations\Tokenizer;
use Herrera\Annotations\Convert\ToArray;
use TechDivision\ApplicationServer\InitialContext\StorageInterface;
use TechDivision\ApplicationServer\InitialContext\ContextKeys;
use TechDivision\ApplicationServer\Api\Node\NodeInterface;
use TechDivision\ApplicationServer\SplClassLoader;

/**
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class InitialContext
{

    /**
     * The storage instance
     *
     * @var \TechDivision\ApplicationServer\InitialContext\StorageInterface
     */
    protected $storage;

    /**
     * Array containing the available bean annotations.
     *
     * @var array
     */
    protected $beanAnnotations = array(
        'entity',
        'singleton',
        'statefull',
        'stateless'
    );

    /**
     * Initializes the context with the connection to the storage backend.
     *
     * @return void
     */
    public function __construct(NodeInterface $systemConfiguration)
    {
        // initialize the storage
        $initialContextNode = $systemConfiguration->getInitialContext();
        $storageNode = $initialContextNode->getStorage();
        $reflectionClass = $this->newReflectionClass($storageNode->getType());
        $this->setStorage($reflectionClass->newInstanceArgs(array(
            $storageNode
        )));
        
        // initialize the class loader instance
        $classLoaderNode = $initialContextNode->getClassLoader();
        $reflectionClass = $this->newReflectionClass($classLoaderNode->getType());
        $this->setClassLoader($reflectionClass->newInstanceArgs(array($this)));

        // attach the system configuration to the initial context
        $this->setSystemConfiguration($systemConfiguration);
    }

    /**
     * Returns the storage instance.
     *
     * @return \TechDivision\ApplicationServer\InitialContext\StorageInterface The storage instance
     */
    public function setStorage(StorageInterface $storage)
    {
        return $this->storage = $storage;
    }

    /**
     * Returns the storage instance.
     *
     * @return \TechDivision\ApplicationServer\InitialContext\StorageInterface The storage instance
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Set's the initial context's class loader.
     *
     * @param \TechDivision\ApplicationServer\SplClassLoader $classLoader
     *            The class loader used
     * @return void
     */
    public function setClassLoader(SplClassLoader $classLoader)
    {
        $this->classLoader = $classLoader;
    }

    /**
     * Return's the initial context's class loader.
     *
     * @return \TechDivision\ApplicationServer\ClassLoader The class loader used
     */
    public function getClassLoader()
    {
        return $this->classLoader;
    }

    /**
     * Adds the system configuration to the inital context.
     *
     * @param \TechDivision\ApplicationServer\Configuration $systemConfiguration
     *            The system configuration
     * @return void
     */
    public function setSystemConfiguration($systemConfiguration)
    {
        $this->setAttribute(ContextKeys::SYSTEM_CONFIGURATION, $systemConfiguration);
    }

    /**
     * Returns the system configuration.
     *
     * @return \TechDivision\ApplicationServer\Configuration The system configuration
     */
    public function getSystemConfiguration()
    {
        return $this->getAttribute(ContextKeys::SYSTEM_CONFIGURATION);
    }

    /**
     * Stores the passed key value pair in the initial context.
     *
     * @param string $key
     *            The key to store the value under
     * @param mixed $value
     *            The value to add to the inital context
     * @return void
     */
    public function setAttribute($key, $value)
    {
        $this->storage->set($key, $value);
    }

    /**
     * Returns the value with the passed key from the initial context.
     *
     * @param string $key
     *            The key of the value to return
     * @return mixed The value stored in the initial context
     */
    public function getAttribute($key)
    {
        return $this->storage->get($key);
    }

    /**
     * Removes the attribute with the passed key from the initial context.
     *
     * @param string $key
     *            The key of the value to delete
     * @return void
     */
    public function removeAttribute($key)
    {
        $this->storage->remove($key);
    }

    /**
     * Returns a reflection class intance for the passed class name.
     *
     * @param string $className
     *            The class name to return the reflection instance for
     * @return \ReflectionClass The reflection instance
     */
    public function newReflectionClass($className)
    {
        return new \ReflectionClass($className);
    }

    /**
     * Returns a new instance of the passed class name.
     *
     * @param string $className
     *            The fully qualified class name to return the instance for
     * @param array $args
     *            Arguments to pass to the constructor of the instance
     * @return object The instance itself
     * @todo Has to be refactored to avoid registering autoloader on every call
     */
    public function newInstance($className, array $args = array())
    {
        // create and return a new instance
        $reflectionClass = $this->newReflectionClass($className);
        return $reflectionClass->newInstanceArgs($args);
    }

    /**
     * Returns a new instance of the passed API service.
     *
     * @param string $className
     *            The API service class name to return the instance for
     * @return \TechDivision\ApplicationsServer\Api\ServiceInterface The service instance
     */
    public function newService($className)
    {
        return $this->newInstance($className, array(
            $this
        ));
    }

    /**
     * Returns the bean annotation for the passed reflection class, that can be
     * one of Entity, Stateful, Stateless, Singleton.
     *
     * @param \ReflectionClass $reflectionClass
     *            The class to return the annotation for
     * @throws \Exception Is thrown if the class has NO bean annotation
     * @return string The found bean annotation
     */
    public function getBeanAnnotation($reflectionClass)
    {

        // load the class name to get the annotation for
        $className = $reflectionClass->getName();

        // check if an array with the bean types has already been registered
        $beanTypes = $this->getAttribute('beanTypes');
        if (is_array($beanTypes)) {
            if (array_key_exists($className, $beanTypes)) {
                return $beanTypes[$className];
            }
        } else {
            $beanTypes = array();
        }

        // initialize the annotation tokenizer
        $tokenizer = new Tokenizer();
        $tokenizer->ignore(array(
            'author',
            'package',
            'license',
            'copyright'
        ));
        $aliases = array();

        // parse the doc block
        $parsed = $tokenizer->parse($reflectionClass->getDocComment(), $aliases);

        // convert tokens and return one
        $tokens = new Tokens($parsed);
        $toArray = new ToArray();

        // iterate over the tokens
        foreach ($toArray->convert($tokens) as $token) {
            $tokeName = strtolower($token->name);
            if (in_array($tokeName, $this->beanAnnotations)) {
                $beanTypes[$className] = $tokeName;
                $this->setAttribute('beanTypes', $beanTypes);
                return $tokeName;
            }
        }

        // throw an exception if the requested class
        throw new \Exception(sprintf("Missing enterprise bean annotation for %s", $reflectionClass->getName()));
    }

    /**
     * Run's a lookup for the session bean with the passed class name and
     * session ID.
     * If the passed class name is a session bean an instance
     * will be returned.
     *
     * @param string $className
     *            The name of the session bean's class
     * @param string $sessionId
     *            The session ID
     * @param array $args
     *            The arguments passed to the session beans constructor
     * @return object The requested session bean
     * @throws \Exception Is thrown if passed class name is no session bean or is a entity bean (not implmented yet)
     */
    public function lookup($className, $sessionId, array $args = array())
    {

        // get the reflection class for the passed class name
        $reflectionClass = $this->newReflectionClass($className);

        switch ($this->getBeanAnnotation($reflectionClass)) {

            case 'entity':
                throw new \Exception("Entity beans are not implemented yet");

            case 'stateful':

                // load the session's from the initial context
                $session = $this->getAttribute($sessionId);

                // if an instance exists, load and return it
                if (is_array($session)) {
                    if (array_key_exists($className, $session)) {
                        return $session[$className];
                    }
                } else {
                    $session = array();
                }

                // if not, initialize a new instance, add it to the container and return it
                $instance = $this->newInstance($className, $args);
                $session[$className] = $instance;
                $this->setAttribute($sessionId, $session);
                return $instance;

            case 'singleton':

                // check if an instance is available
                if ($this->getAttribute($className)) {
                    return $this->getAttribute($className);
                }

                // if not create a new instance and return it
                $instance = $this->newInstance($className, $args);
                $this->setAttribute($className, $instance);
                return $instance;

            default: // @Stateless

                return $this->newInstance($className, $args);
        }

        // if the class is no session bean, throw an exception
        throw new \Exception("Can\'t find session bean with class name '$className'");
    }
}