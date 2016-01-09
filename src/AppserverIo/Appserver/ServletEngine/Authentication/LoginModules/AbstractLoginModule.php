<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authentication\LoginModules\AbstractLoginModule
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

namespace AppserverIo\Appserver\ServletEngine\Authentication\LoginModules;

use AppserverIo\Collections\MapInterface;

/**
 * An abstract login module implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
abstract class AbstractLoginModule implements LoginModuleInterface
{

    /**
     * The login module parameters.
     *
     * @var \AppserverIo\Collections\MapInterface
     */
    protected $params;

    /**
     * Used the share the login state between multiple modules.
     *
     * @var \AppserverIo\Collections\MapInterface
     */
    protected $sharedState;

    /**
     * Initialize the login module. This stores the subject, callbackHandler and sharedState and options
     * for the login session. Subclasses should override if they need to process their own options. A call
     * to parent::initialize() must be made in the case of an override.
     *
     * The following parameters can by default be passed from the configuration.
     *
     * password-stacking:       If this is set to "useFirstPass", the login identity will be taken from the
     *                          appserver.security.auth.login.name value of the sharedState map, and the proof
     *                          of identity from the appserver.security.auth.login.password value of the sharedState map
     * principalClass:          A Principal implementation that support a constructor taking a string argument for the princpal name
     * unauthenticatedIdentity: The name of the principal to asssign and authenticate when a null username and password are seen
     *
     * @param \AppserverIo\Collections\MapInterface $sharedState A Map shared between all configured login module instances
     * @param \AppserverIo\Collections\MapInterface $params      The parameters passed to the login module
     */
    public function initialize(MapInterface $sharedState, MapInterface $params)
    {
        $this->params = $params;
        $this->sharedState = $sharedState;
    }
}
