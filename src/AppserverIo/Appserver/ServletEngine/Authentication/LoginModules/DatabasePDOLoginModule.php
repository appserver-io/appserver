<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authentication\LoginModules\DatabasePDOLoginModule
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
use AppserverIo\Appserver\ServletEngine\Authentication\LoginModules\Utilities\ParamKeys;
use AppserverIo\Appserver\ServletEngine\RequestHandler;
use AppserverIo\Appserver\Doctrine\Utils\ConnectionUtil;
use Doctrine\DBAL\DriverManager;
use AppserverIo\Appserver\ServletEngine\Authentication\LoginModules\Utilities\SharedStateKeys;

/**
 * This valve will check if the actual request needs authentication.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class DatabasePDOLoginModule extends AbstractLoginModule
{

    /**
     * The datasource name used to lookup in the naming directory.
     *
     * @var string
     */
    protected $lookupName;

    /**
     * The database query used to load the user's roles.
     *
     * @var string
     */
    protected $rolesQuery;

    /**
     * The database query used to load the user.
     *
     * @var string
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
     * @param \AppserverIo\Collections\MapInterface $sharedState A Map shared between all configured login module instances
     * @param \AppserverIo\Collections\MapInterface $params      The parameters passed to the login module
     */
    public function initialize(MapInterface $sharedState, MapInterface $params)
    {

        // call the parent method
        parent::initialize($sharedState, $params);

        // load the parameters from the map
        $this->lookupName = $params->get(ParamKeys::LOOKUP_NAME);
        $this->rolesQuery = $params->get(ParamKeys::ROLES_QUERY);
        $this->principalsQuery = $params->get(ParamKeys::PRINCIPALS_QUERY);
    }

    /**
     * Called by login() to acquire the username and password strings for
     * authentication. This method does no validation of either.
     *
     * @return array Array with username and password, e. g. array(0 => $username, 1 => $password)
     * @throws \AppserverIo\Appserver\ServletEngine\Authentication\LoginModules\LoginException Is thrown if username and password can't be loaded
     */
    public function getUsersPassword()
    {

        // load the application context
        $application = RequestHandler::getApplicationContext();

        /** @var \AppserverIo\Appserver\Core\Api\Node\DatabaseNode $databaseNode */
        $databaseNode = $application->search($this->lookupName)->getDatasourceNode()->getDatabaseNode();

        // prepare the connection parameters and create the DBAL connection
        $connection = DriverManager::getConnection(ConnectionUtil::get($application)->fromDatasourceNode($databaseNode));

        $statement = $connection->prepare($this->principalsQuery);
        $statement->bindParam(1, $this->sharedState->get(SharedStateKeys::LOGIN_NAME));
        $statement->execute();

        if ($password = $statement->fetch()) {
            // do something here
        } else {
            throw new LoginException('No matching username found in principals');
        }
    }
}
