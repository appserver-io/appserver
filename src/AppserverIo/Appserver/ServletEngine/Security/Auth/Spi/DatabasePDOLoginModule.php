<?php

/**
 * AppserverIo\Appserver\ServletEngine\Security\Auth\Spi\DatabasePDOLoginModule
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

namespace AppserverIo\Appserver\ServletEngine\Security\Auth\Spi;

use AppserverIo\Lang\String;
use Doctrine\DBAL\DriverManager;
use AppserverIo\Collections\MapInterface;
use AppserverIo\Appserver\ServletEngine\RequestHandler;
use AppserverIo\Appserver\Doctrine\Utils\ConnectionUtil;
use AppserverIo\Psr\Security\Auth\Subject;
use AppserverIo\Psr\Security\Auth\Login\LoginException;
use AppserverIo\Psr\Security\Auth\Callback\CallbackHandlerInterface;
use AppserverIo\Appserver\ServletEngine\Security\Utils\Util;
use AppserverIo\Appserver\ServletEngine\Security\Utils\ParamKeys;

/**
 * This valve will check if the actual request needs authentication.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class DatabasePDOLoginModule extends UsernamePasswordLoginModule
{

    /**
     * The datasource name used to lookup in the naming directory.
     *
     * @var \AppserverIo\Lang\String
     */
    protected $lookupName;

    /**
     * The database query used to load the user's roles.
     *
     * @var \AppserverIo\Lang\String
     */
    protected $rolesQuery;

    /**
     * The database query used to load the user.
     *
     * @var \AppserverIo\Lang\String
     */
    protected $principalsQuery;

    /**
     * Initialize the login module. This stores the subject, callbackHandler and sharedState and options
     * for the login session. Subclasses should override if they need to process their own options. A call
     * to parent::initialize() must be made in the case of an override.
     *
     * The following parameters can by default be passed from the configuration.
     *
     * lookupName:      The datasource name used to lookup in the naming directory
     * rolesQuery:      The database query used to load the user's roles
     * principalsQuery: The database query used to load the user
     *
     * @param \AppserverIo\Psr\Security\Auth\Subject                           $subject         The Subject to update after a successful login
     * @param \AppserverIo\Psr\Security\Auth\Callback\CallbackHandlerInterface $callbackHandler The callback handler that will be used to obtain the user identity and credentials
     * @param \AppserverIo\Collections\MapInterface                            $sharedState     A map shared between all configured login module instances
     * @param \AppserverIo\Collections\MapInterface                            $params          The parameters passed to the login module
     */
    public function initialize(Subject $subject, CallbackHandlerInterface $callbackHandler, MapInterface $sharedState, MapInterface $params)
    {

        // call the parent method
        parent::initialize($subject, $callbackHandler, $sharedState, $params);

        // load the parameters from the map
        $this->lookupName = new String($params->get(ParamKeys::LOOKUP_NAME));
        $this->rolesQuery = new String($params->get(ParamKeys::ROLES_QUERY));
        $this->principalsQuery = new String($params->get(ParamKeys::PRINCIPALS_QUERY));
    }

    /**
     * Returns the password for the user from the sharedMap data.
     *
     * @return \AppserverIo\Lang\String The user's password
     * @throws \AppserverIo\Psr\Security\Auth\Login\LoginException Is thrown if password can't be loaded
     */
    protected function getUsersPassword()
    {

        // load the application context
        $application = RequestHandler::getApplicationContext();

        /** @var \AppserverIo\Appserver\Core\Api\Node\DatabaseNode $databaseNode */
        $databaseNode = $application->getNamingDirectory()->search($this->lookupName)->getDatabase();

        // prepare the connection parameters and create the DBAL connection
        $connection = DriverManager::getConnection(ConnectionUtil::get($application)->fromDatabaseNode($databaseNode));

        // try to load the principal's credential from the database
        $statement = $connection->prepare($this->principalsQuery);
        $statement->bindParam(1, $this->getUsername());
        $statement->execute();

        // query whether or not we've a password found or not
        if ($row = $statement->fetch(\PDO::FETCH_NUM)) {
            return new String($row[0]);
        } else {
            throw new LoginException('No matching username found in principals');
        }
    }

    /**
     * Execute the rolesQuery against the lookupName to obtain the roles for the authenticated user.
     *
     * @return array Array containing the sets of roles
     * @throws \AppserverIo\Psr\Security\Auth\Login\LoginException Is thrown if password can't be loaded
     */
    protected function getRoleSets()
    {
        return Util::getRoleSets($this->getUsername(), new String($this->lookupName), new String($this->rolesQuery), $this);
    }
}
