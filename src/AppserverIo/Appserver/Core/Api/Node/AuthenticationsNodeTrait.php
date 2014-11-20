<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * AppserverIo\Appserver\Core\Api\Node\AuthenticationsNodeTrait
 *
 * This trait is used to give any node class the possibility to manage authentication nodes
 * which might be child elements of it.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
trait AuthenticationsNodeTrait
{
    /**
     * The authentications specified within the parent node
     *
     * @var array
     * @AS\Mapping(nodeName="authentications/authentication", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\AuthenticationNode")
     */
    protected $authentications = array();

    /**
     * Will return the authentications array.
     *
     * @return array The array with the authentications
     */
    public function getAuthentications()
    {
        return $this->authentications;
    }

    /**
     * Will return the authentication node with the specified definition and if nothing could
     * be found we will return false.
     *
     * @param string $uri The URI of the authentication in question
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\AuthenticationNode|boolean The requested authentication node
     */
    public function getAuthentication($uri)
    {
        // Iterate over all authentications
        foreach ($this->getAuthentications() as $authenticationNode) {

            // If we found one with a matching URI we will return it
            if ($authenticationNode->getUri() === $uri) {

                return $authenticationNode;
            }
        }

        // Still here? Seems we did not find anything
        return false;
    }

    /**
     * Returns the authentications as an associative array.
     *
     * @return array The array with the sorted authentications
     */
    public function getAuthenticationsAsArray()
    {
        // Iterate over the authentication nodes and sort them into an array
        $authentications = array();
        foreach ($this->getAuthentications() as $authenticationNode) {

            // Restructure to an array
            $authentications[] = array(
                'uri' => $authenticationNode->getUri(),
                'params' => $authenticationNode->getParamsAsArray()
            );
        }

        // Return what we got
        return $authentications;
    }
}
