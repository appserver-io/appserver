<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\SecurityNode
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2015 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 * @deprecated Since 1.2.0
 */

namespace AppserverIo\Appserver\Core\Api\Node;

use AppserverIo\Configuration\ConfigurationException;

/**
 * DTO to transfer a security information.
 *
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2015 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 * @deprecated Since 1.2.0
 */
class SecurityNode extends AbstractNode implements SecurityNodeInterface
{

    /**
     * The available authentication types.
     *
     * @var string
     */
    protected $authenticationTypes = array(
        'Basic'  => '\AppserverIo\Http\Authentication\BasicAuthentication',
        'Digest' => '\AppserverIo\Http\Authentication\DigestAuthentication'
    );

    /**
     * The URL pattern information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\UrlPatternNode
     * @AS\Mapping(nodeName="url-pattern", nodeType="AppserverIo\Appserver\Core\Api\Node\UrlPatternNode")
     */
    protected $urlPattern;

    /**
     * The authentication information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\AuthNode
     * @AS\Mapping(nodeName="auth", nodeType="AppserverIo\Appserver\Core\Api\Node\AuthNode")
     */
    protected $auth;

    /**
     * Returns the authentication type class name for the passed shortname.
     *
     * @param string $shortname The shortname of the requested authentication type class name
     *
     * @return string The requested authentication type class name
     * @throws ConfigurationException
     */
    public function mapAuthenticationType($shortname)
    {

        // query whether or not an authentication type is available or not
        if (isset($this->authenticationTypes[$shortname])) {
            return $this->authenticationTypes[$shortname];
        }

        // throw an exception if the can't find an matching authentication type
        throw new ConfigurationException(sprintf('Can\t find authentication type %s', $shortname));
    }

    /**
     * Return's the URL pattern information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\UrlPatternNode The URL pattern
     */
    public function getUrlPattern()
    {
        return $this->urlPattern;
    }

    /**
     * Return's the authentication information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\AuthNode The authentication information
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * Returns the security information as associative array.
     *
     * @param string $webappPath The webapp directory
     *
     * @return array The array with the security information
     */
    public function getOptionsAsArray($webappPath)
    {
        return array(
            'type'         => $this->mapAuthenticationType($this->getAuth()->getAuthType()->__toString()),
            'realm'        => $this->getAuth()->getRealm()->__toString(),
            'adapter-type' => $this->getAuth()->getAdapterType()->__toString(),
            'file'         => $webappPath . DIRECTORY_SEPARATOR . $this->getAuth()->getOptions()->getFile()->__toString()
        );
    }
}
