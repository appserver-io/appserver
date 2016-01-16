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
use AppserverIo\Configuration\Configuration;
use AppserverIo\Psr\Security\Auth\Subject;
use AppserverIo\Psr\Security\PrincipalInterface;
use AppserverIo\Psr\Security\Auth\Login\LoginContext;
use AppserverIo\Psr\Security\Auth\Login\LoginContextInterface;
use AppserverIo\Psr\Security\Auth\Callback\CallbackHandlerInterface;
use AppserverIo\Appserver\Core\Api\Node\SecurityDomainNodeInterface;

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
     * Initialize the security domain with the passed name.
     *
     * @param string $name The security domain's name
     */
    public function __construct($name)
    {
        $this->name = $name;
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
     * Finally tries to authenticate the user with the passed name.
     *
     * @param \AppserverIo\Lang\String                                         $username        The name of the user to authenticate
     * @param \AppserverIo\Psr\Security\Auth\Callback\CallbackHandlerInterface $callbackHandler The callback handler used to load the credentials
     *
     * @return \AppserverIo\Security\PrincipalInterface The authenticated user principal
     */
    public function authenticate(String $username, CallbackHandlerInterface $callbackHandler)
    {

        // initialize the subject and the configuration
        $subject = new Subject();
        $configuration = $this->getConfiguration();

        // initialize the LoginContext and try to login the user
        $loginContext = new LoginContext($subject, $callbackHandler, $configuration);
        $loginContext->login();

        // create and return a new Principal of the authenticated user
        return $this->createPrincipal($username, $loginContext->getSubject(), $loginContext);
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

        /*
        $roles = new ArrayList();
        $userPrincipal = null;

        // Scan the Principals for this Subject
        foreach ($subject->getPrincipals() as $principal) {

            $principalClass = principal.getClass().getName();

            if (userPrincipal == null && userClasses.contains(principalClass)) {
                userPrincipal = principal;
                if( log.isDebugEnabled() ) {
                    log.debug(sm.getString("jaasRealm.userPrincipalSuccess", principal.getName()));
                }
            }

            if (roleClasses.contains(principalClass)) {
                roles.add(principal.getName());
                if( log.isDebugEnabled() ) {
                    log.debug(sm.getString("jaasRealm.rolePrincipalAdd", principal.getName()));
                }
            }
        }

        // Print failure message if needed
        if (userPrincipal == null) {
            if (log.isDebugEnabled()) {
                log.debug(sm.getString("jaasRealm.userPrincipalFailure"));
                log.debug(sm.getString("jaasRealm.rolePrincipalFailure"));
            }
        } else {
            if (roles.size() == 0) {
                if (log.isDebugEnabled()) {
                    log.debug(sm.getString("jaasRealm.rolePrincipalFailure"));
                }
            }
        }

        // Return the resulting Principal for our authenticated user
        return new GenericPrincipal(username, null, roles, userPrincipal, loginContext);
        */
    }
}
