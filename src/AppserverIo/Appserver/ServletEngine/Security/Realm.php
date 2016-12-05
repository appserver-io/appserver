<?php

/**
 * AppserverIo\Appserver\ServletEngine\Security\Realm
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

namespace AppserverIo\Appserver\ServletEngine\Security;

use AppserverIo\Lang\String;
use AppserverIo\Collections\ArrayList;
use AppserverIo\Configuration\Configuration;
use AppserverIo\Psr\Auth\RealmInterface;
use AppserverIo\Psr\Security\Auth\Subject;
use AppserverIo\Psr\Security\PrincipalInterface;
use AppserverIo\Psr\Security\Acl\GroupInterface;
use AppserverIo\Psr\Security\Auth\Login\LoginContext;
use AppserverIo\Psr\Security\Auth\Login\LoginContextInterface;
use AppserverIo\Psr\Security\Auth\Callback\CallbackHandlerInterface;
use AppserverIo\Appserver\Naming\Utils\NamingDirectoryKeys;
use AppserverIo\Appserver\ServletEngine\Security\Utils\Util;
use AppserverIo\Appserver\Core\Api\Node\SecurityDomainNodeInterface;
use AppserverIo\Appserver\ServletEngine\Security\Auth\Callback\SecurityAssociationHandler;
use AppserverIo\Psr\Auth\AuthenticationManagerInterface;

/**
 * Security domain implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class Realm implements RealmInterface
{

    /**
     * The security domain's name.
     *
     * @var string
     */
    protected $name;

    /**
     * The security domain's login modules.
     *
     * @var \AppserverIo\Appserver\ServletEngine\Security\SecurityDomainInterface
     */
    protected $configruation;

    /**
     * The authentication manager instance.
     *
     * @var \AppserverIo\Appserver\ServletEngine\Security\AuthenticationManagerInterface
     */
    protected $authenticationManager;

    /**
     * A stack with the exception throwed during authentication.
     *
     * @var \AppserverIo\Collections\ArrayList
     */
    protected $exceptionStack;

    /**
     * Initialize the security domain with the passed name.
     *
     * @param \AppserverIo\Psr\Auth\AuthenticationManagerInterface $authenticationManager The authentication manager instance
     * @param string                                               $name                  The security domain's name
     */
    public function __construct(AuthenticationManagerInterface $authenticationManager, $name)
    {

        // set the passed parameters
        $this->name = $name;
        $this->authenticationManager = $authenticationManager;

        // initialize the exception stack
        $this->exceptionStack = new ArrayList();
    }

    /**
     * Return's the name of the security domain.
     *
     * @return string The security domain's name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Inject the realm's configuration.
     *
     * @param \AppserverIo\Appserver\ServletEngine\Security\SecurityDomainInterface $configuration The realm's configuration
     *
     * @return void
     */
    public function injectConfiguration(SecurityDomainNodeInterface $configuration)
    {
        $this->configruation = $configuration;
    }

    /**
     * Return's the realm's configuration.
     *
     * @return \AppserverIo\Appserver\ServletEngine\Security\SecurityDomainInterface The realm's configuration
     */
    public function getConfiguration()
    {
        return $this->configruation;
    }

    /**
     * Return's the authentication manager instance.
     *
     * @return \AppserverIo\Appserver\ServletEngine\Security\AuthenticationManagerInterface The authentication manager instance
     */
    public function getAuthenticationManager()
    {
        return $this->authenticationManager;
    }

    /**
     * Return's the exception stack.
     *
     * @return \AppserverIo\Collections\ArrayList The exception stack
     */
    public function getExceptionStack()
    {
        return $this->exceptionStack;
    }

    /**
     * Finally tries to authenticate the user with the passed name.
     *
     * @param \AppserverIo\Lang\String                                         $username        The name of the user to authenticate
     * @param \AppserverIo\Psr\Security\Auth\Callback\CallbackHandlerInterface $callbackHandler The callback handler used to load the credentials
     *
     * @return \AppserverIo\Security\PrincipalInterface|null The authenticated user principal
     */
    public function authenticateByUsernameAndCallbackHandler(String $username, CallbackHandlerInterface $callbackHandler)
    {

        try {
            // initialize the subject and the configuration
            $subject = new Subject();
            $configuration = $this->getConfiguration();

            // initialize the LoginContext and try to login the user
            $loginContext = new LoginContext($subject, $callbackHandler, $configuration);
            $loginContext->login();

            // create and return a new Principal of the authenticated user
            return $this->createPrincipal($username, $subject, $loginContext);

        } catch (\Exception $e) {
            // add the exception to the stack
            $this->getExceptionStack()->add($e);
            // load the system logger and debug log the exception
            /** @var \Psr\Log\LoggerInterface $systemLogger */
            if ($systemLogger = $this->getAuthenticationManager()->getApplication()->getNamingDirectory()->search(NamingDirectoryKeys::SYSTEM_LOGGER)) {
                $systemLogger->error($e->__toString());
            }
        }
    }

    /**
     * Finally tries to authenticate the user with the passed name.
     *
     * @param \AppserverIo\Lang\String $username The name of the user to authenticate
     * @param \AppserverIo\Lang\String $password The password used for authentication
     *
     * @return \AppserverIo\Security\PrincipalInterface|null The authenticated user principal
     */
    public function authenticate(String $username, String $password)
    {

        // prepare the callback handler
        $callbackHandler = new SecurityAssociationHandler(new SimplePrincipal($username), $password);

        // authenticate the passed username/password combination
        return $this->authenticateByUsernameAndCallbackHandler($username, $callbackHandler);
    }

    /**
     * Identify and return an instance implementing the PrincipalInterface that represens the
     * authenticated user for the specified Subject. The Principal is constructed by scanning
     * the list of Principals returned by the LoginModule. The first Principal object that
     * matches one of the class names supplied as a "user class" is the user Principal. This
     * object is returned to the caller. Any remaining principal objects returned by the
     * LoginModules are mapped to roles, but only if their respective classes match one of the
     * "role class" classes. If a user Principal cannot be constructed, return NULL.
     *
     * @param \AppserverIo\Lang\String                                   $username     The associated user name
     * @param \AppserverIo\Psr\Security\Auth\Subject                     $subject      The Subject representing the logged-in user
     * @param \AppserverIo\Psr\Security\Auth\Login\LoginContextInterface $loginContext Associated with the Principal so {@link LoginContext#logout()} can be called later
     *
     * @return \AppserverIo\Security\PrincipalInterface the principal object
     */
    protected function createPrincipal(String $username, Subject $subject, LoginContextInterface $loginContext)
    {

        // initialize the roles and the user principal
        $roles = new ArrayList();
        $userPrincipal = null;

        // scan the Principals for this Subject
        foreach ($subject->getPrincipals() as $principal) {
            // query whether or not the principal found is a group principal
            if ($principal instanceof GroupInterface && $principal->getName()->equals(new String(Util::DEFAULT_GROUP_NAME))) {
                // if yes, add the role name
                foreach ($principal->getMembers() as $role) {
                    $roles->add($role->getName());
                }

            // query whether or not the principal found is a user principal
            } elseif ($userPrincipal == null && $principal instanceof PrincipalInterface) {
                $userPrincipal = $principal;
            } else {
                // do nothing, because we've no principal or group to deal with
            }
        }

        // return the resulting Principal for our authenticated user
        return new GenericPrincipal($username, null, $roles, $userPrincipal, $loginContext);
    }
}
