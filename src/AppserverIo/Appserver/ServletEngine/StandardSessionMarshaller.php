<?php

/**
 * \AppserverIo\Appserver\ServletEngine\StandardSessionMarshaller
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

namespace AppserverIo\Appserver\ServletEngine;

use AppserverIo\Psr\Servlet\ServletSessionInterface;

/**
 * This valve will check if the actual request needs authentication.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class StandardSessionMarshaller implements SessionMarshallerInterface
{

    /**
     * The key for the session attribute 'id'.
     *
     * @var string
     */
    const ID = 'id';

    /**
     * The key for the session attribute 'name'.
     *
     * @var string
     */
    const NAME = 'name';

    /**
     * The key for the session attribute 'lifetime'.
     *
     * @var string
     */
    const LIFETIME = 'lifetime';

    /**
     * The key for the session attribute 'maximumAge'.
     *
     * @var string
     */
    const MAXIMUM_AGE = 'maximumAge';

    /**
     * The key for the session attribute 'domain'.
     *
     * @var string
     */
    const DOMAIN = 'domain';

    /**
     * The key for the session attribute 'path'.
     *
     * @var string
     */
    const PATH = 'path';

    /**
     * The key for the session attribute 'secure'.
     *
     * @var string
     */
    const SECURE = 'secure';

    /**
     * The key for the session attribute 'httpOnly'.
     *
     * @var string
     */
    const HTTP_ONLY = 'httpOnly';

    /**
     * The key for the session attribute 'lastActivityTimestamp'.
     *
     * @var string
     */
    const LAST_ACTIVITY_TIMESTAMP = 'lastActivityTimestamp';

    /**
     * The key for the session attribute 'data'.
     *
     * @var string
     */
    const DATA = 'data';

    /**
     * Transforms the passed session instance into a JSON encoded string. If the data contains
     * objects, each of them will be serialized before store them to the persistence layer.
     *
     * @param \AppserverIo\Psr\Servlet\ServletSessionInterface $servletSession The session to be transformed
     *
     * @return string The JSON encoded session representation
     * @throws \AppserverIo\Appserver\ServletEngine\DataNotSerializableException Is thrown, if the session can't be encoded
     * @see \AppserverIo\Appserver\ServletEngine\SessionMarshallerInterface::marshall()
     */
    public function marshall(ServletSessionInterface $servletSession)
    {

        // create the stdClass (that can easy be transformed into an JSON object)
        $sessionData = array();

        // copy the values to the stdClass
        $sessionData[StandardSessionMarshaller::ID] = $servletSession->getId();
        $sessionData[StandardSessionMarshaller::NAME] = $servletSession->getName();
        $sessionData[StandardSessionMarshaller::LIFETIME] = $servletSession->getLifetime();
        $sessionData[StandardSessionMarshaller::MAXIMUM_AGE] = $servletSession->getMaximumAge();
        $sessionData[StandardSessionMarshaller::DOMAIN] = $servletSession->getDomain();
        $sessionData[StandardSessionMarshaller::PATH] = $servletSession->getPath();
        $sessionData[StandardSessionMarshaller::SECURE] = $servletSession->isSecure();
        $sessionData[StandardSessionMarshaller::HTTP_ONLY] = $servletSession->isHttpOnly();
        $sessionData[StandardSessionMarshaller::LAST_ACTIVITY_TIMESTAMP] = $servletSession->getLastActivityTimestamp();

        // initialize the array for the session data
        $sessionData[StandardSessionMarshaller::DATA] = array();

        // append the session data
        foreach ($servletSession->data as $key => $value) {
            $sessionData[StandardSessionMarshaller::DATA][$key] = serialize($value);
        }

        // JSON encode the session instance
        $encodedSession = json_encode($sessionData);

        // query whether or not the session data has been decoded successfully
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new SessionDataNotReadableException(json_last_error_msg());
        }

        // return the JSON encoded session data
        return $encodedSession;
    }

    /**
     * Initializes the session instance from the passed JSON string. If the encoded
     * data contains objects, they will be un-serialized before reattached to the
     * session instance.
     *
     * @param \AppserverIo\Psr\Servlet\ServletSessionInterface $servletSession The empty session instance we want the un-marshaled data be added to
     * @param string                                           $marshalled     The marshaled session representation
     *
     * @return \AppserverIo\Psr\Servlet\ServletSessionInterface The decoded session instance
     * @throws \AppserverIo\Appserver\ServletEngine\SessionDataNotReadableException Is thrown, if the session data can not be unmarshalled
     * @see \AppserverIo\Appserver\ServletEngine\SessionMarshallerInterface::unmarshall()
     */
    public function unmarshall(ServletSessionInterface $servletSession, $marshalled)
    {

        // try to decode the string
        $decodedSession = json_decode($marshalled, true);

        // query whether or not the session data has been encoded successfully
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new SessionDataNotReadableException(json_last_error_msg());
        }

        // extract the values
        $id = $decodedSession[StandardSessionMarshaller::ID];
        $name = $decodedSession[StandardSessionMarshaller::NAME];
        $lifetime = $decodedSession[StandardSessionMarshaller::LIFETIME];
        $maximumAge = $decodedSession[StandardSessionMarshaller::MAXIMUM_AGE];
        $domain = $decodedSession[StandardSessionMarshaller::DOMAIN];
        $path = $decodedSession[StandardSessionMarshaller::PATH];
        $secure = $decodedSession[StandardSessionMarshaller::SECURE];
        $httpOnly = $decodedSession[StandardSessionMarshaller::HTTP_ONLY];
        $data = $decodedSession[StandardSessionMarshaller::DATA];
        $lastActivityTimestamp = $decodedSession[StandardSessionMarshaller::LAST_ACTIVITY_TIMESTAMP];

        // initialize the instance
        $servletSession->init($id, $name, $lifetime, $maximumAge, $domain, $path, $secure, $httpOnly, $lastActivityTimestamp);

        // append the session data
        foreach ($data as $key => $value) {
            $servletSession->putData($key, unserialize($value));
        }
    }
}
