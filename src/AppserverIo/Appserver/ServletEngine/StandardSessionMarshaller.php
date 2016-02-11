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
     * Transforms the passed session instance into a JSON encoded string. If the data contains
     * objects, each of them will be serialized before store them to the persistence layer.
     *
     * @param \AppserverIo\Psr\Servlet\ServletSessionInterface $servletSession The session to be transformed
     *
     * @return string The JSON encoded session representation
     * @see \AppserverIo\Appserver\ServletEngine\SessionMarshaller::marshall()
     */
    public function marshall(ServletSessionInterface $servletSession)
    {

        // create the stdClass (that can easy be transformed into an JSON object)
        $stdClass = new \stdClass();

        // copy the values to the stdClass
        $stdClass->id = $servletSession->getId();
        $stdClass->name = $servletSession->getName();
        $stdClass->lifetime = $servletSession->getLifetime();
        $stdClass->maximumAge = $servletSession->getMaximumAge();
        $stdClass->domain = $servletSession->getDomain();
        $stdClass->path = $servletSession->getPath();
        $stdClass->secure = $servletSession->isSecure();
        $stdClass->httpOnly = $servletSession->isHttpOnly();

        // initialize the array for the session data
        $stdClass->data = array();

        // append the session data
        foreach ($servletSession->data as $key => $value) {
            $stdClass->data[$key] = serialize($value);
        }

        // returns the JSON encoded session instance
        return json_encode($stdClass);
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
     * @see \AppserverIo\Appserver\ServletEngine\SessionMarshaller::unmarshall()
     */
    public function unmarshall(ServletSessionInterface $servletSession, $marshalled)
    {

        // decode the string
        $decodedSession = json_decode($marshalled);

        // extract the values
        $id = $decodedSession->id;
        $name = $decodedSession->name;
        $lifetime = $decodedSession->lifetime;
        $maximumAge = $decodedSession->maximumAge;
        $domain = $decodedSession->domain;
        $path = $decodedSession->path;
        $secure = $decodedSession->secure;
        $httpOnly = $decodedSession->httpOnly;
        $data = $decodedSession->data;

        // initialize the instance
        $servletSession->init($id, $name, $lifetime, $maximumAge, $domain, $path, $secure, $httpOnly);

        // append the session data
        foreach ($data as $key => $value) {
            $servletSession->putData($key, unserialize($value));
        }
    }
}
