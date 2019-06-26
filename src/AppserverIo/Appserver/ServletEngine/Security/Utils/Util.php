<?php

/**
 * AppserverIo\Appserver\ServletEngine\Security\Utils\Util
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

namespace AppserverIo\Appserver\ServletEngine\Security\Utils;

use AppserverIo\Lang\String;
use Doctrine\DBAL\DriverManager;
use AppserverIo\Collections\HashMap;
use AppserverIo\Appserver\ServletEngine\RequestHandler;
use AppserverIo\Appserver\Doctrine\Utils\ConnectionUtil;
use AppserverIo\Appserver\Naming\Utils\NamingDirectoryKeys;
use AppserverIo\Appserver\ServletEngine\Security\SimpleGroup;
use AppserverIo\Psr\Security\Auth\Spi\LoginModuleInterface;
use AppserverIo\Psr\Security\Auth\Login\LoginException;
use AppserverIo\Psr\Security\Auth\Login\FailedLoginException;
use AppserverIo\Psr\Naming\NamingException;

/**
 * Utility class for security purposes.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class Util
{

    /**
     * Contains the default group name.
     *
     * @var string
     */
    const DEFAULT_GROUP_NAME = 'Roles';

    /**
     * Key for base64 encoding.
     *
     * @var string
     */
    const BASE64_ENCODING = 'base64Encoding';

    /**
     * Creates and returns a hashed version of the passed password.
     *
     *
     * @param string                   $hashAlgorithm The hash algorithm to use
     * @param string                   $hashEncoding  The hash encoding to use
     * @param string                   $hashCharset   The hash charset to use
     * @param \AppserverIo\Lang\String $name          The login name
     * @param \AppserverIo\Lang\String $password      The password credential
     * @param mixed                    $callback      The callback providing some additional hashing functionality
     * @param string                   $hashSalt      The hash salt to use
     *
     * @return \AppserverIo\Lang\String The hashed password
     */
    public static function createPasswordHash($hashAlgorithm, $hashEncoding, $hashCharset, String $name, String $password, $callback, $hashSalt = null)
    {
        $newPassword = clone $password;
        switch ($hashAlgorithm) {
            case HashKeys::MD5:
                return $newPassword->md5($hashSalt);
            case HashKeys::SHA1:
                return $newPassword->sha1($hashSalt);
            case HashKeys::SHA256:
                return $newPassword->sha256($hashSalt);
            case HashKeys::SHA512:
                return $newPassword->sha512($hashSalt);
            case PASSWORD_BCRYPT:
                return $newPassword;
            case PASSWORD_DEFAULT:
                return $newPassword;
            case 'default':
                return $newPassword;
        }
    }

    /**
     * Execute the rolesQuery against the UserName to obtain the roles for the authenticated user.
     *
     * @param \AppserverIo\Lang\String                                $username   The username to load the roles for
     * @param \AppserverIo\Lang\String                                $lookupName The lookup name for the datasource
     * @param \AppserverIo\Lang\String                                $rolesQuery The query to load the roles
     * @param \AppserverIo\Psr\Security\Auth\Spi\LoginModuleInterface $aslm       The login module to add the roles to
     *
     * @return array An array of groups containing the sets of roles
     * @throws \AppserverIo\Psr\Security\Auth\Login\LoginException Is thrown if an error during login occured
     */
    public static function getRoleSets(String $username, String $lookupName, String $rolesQuery, LoginModuleInterface $aslm)
    {

        try {
            // initialize the map for the groups
            $setsMap = new HashMap();

            // load the application context
            $application = RequestHandler::getApplicationContext();

            /** @var \AppserverIo\Appserver\Core\Api\Node\DatabaseNode $databaseNode */
            $databaseNode = $application->getNamingDirectory()->search($lookupName)->getDatabase();

            // prepare the connection parameters and create the DBAL connection
            $connection = DriverManager::getConnection(ConnectionUtil::get($application)->fromDatabaseNode($databaseNode));

            // try to load the principal's roles from the database
            $statement = $connection->prepare($rolesQuery);
            $statement->bindParam(1, $username);
            $statement->execute();

            // query whether or not we've a password found or not
            $row = $statement->fetch(\PDO::FETCH_NUM);

            // query whether or not we've found at least one role
            if ($row == false) {
                // try load the unauthenticated identity
                if ($aslm->getUnauthenticatedIdentity() == null) {
                    throw new FailedLoginException('No matching username found in Roles');
                }

                // we're running with an unauthenticatedIdentity so create an empty roles set and return
                return array(new SimpleGroup(Util::DEFAULT_GROUP_NAME));
            }

            do {
                // load the found name and initialize the group name with a default value
                $name = $row[0];
                $groupName = Util::DEFAULT_GROUP_NAME;

                // query whether or not we've to initialize a default group
                if (isset($row[1])) {
                    $groupName = $row[1];
                }

                // query whether or not the group already exists in the set
                if ($setsMap->exists($groupName) === false) {
                    $group = new SimpleGroup(new String($groupName));
                    $setsMap->add($groupName, $group);
                } else {
                    $group = $setsMap->get($groupName);
                }

                try {
                    // add the user to the group
                    $group->addMember($aslm->createIdentity(new String($name)));
                    // log a message
                    $application
                        ->getNamingDirectory()
                        ->search(NamingDirectoryKeys::SYSTEM_LOGGER)
                        ->debug(sprintf('Assign user to role: %s', $name));
                } catch (\Exception $e) {
                    $application
                        ->getNamingDirectory()
                        ->search(NamingDirectoryKeys::SYSTEM_LOGGER)
                        ->error(sprintf('Failed to create principal: %s', $name));
                }

            // load one group after another
            } while ($row = $statement->fetch(\PDO::FETCH_OBJ));
        } catch (NamingException $ne) {
            throw new LoginException($ne->__toString());
        } catch (\PDOException $pdoe) {
            throw new LoginException($pdoe->__toString());
        }

        // close the prepared statement
        if ($statement != null) {
            try {
                $statement->closeCursor();
            } catch (\Exception $e) {
                $application
                    ->getNamingDirectory()
                    ->search(NamingDirectoryKeys::SYSTEM_LOGGER)
                    ->error($e->__toString());
            }
        }

        // close the DBAL connection
        if ($connection != null) {
            try {
                $connection->close();
            } catch (\Exception $e) {
                $application
                    ->getNamingDirectory()
                    ->search(NamingDirectoryKeys::SYSTEM_LOGGER)
                    ->error($e->__toString());
            }
        }

        // return the prepared groups
        return $setsMap->toArray();
    }

    /**
     * This is a utility class, so protect it against direct instantiation.
     */
    private function __construct()
    {
    }

    /**
     * This is a utility class, so protect it against cloning.
     *
     * @return void
     */
    private function __clone()
    {
    }
}
