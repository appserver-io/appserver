<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authentication\Subject
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

namespace AppserverIo\Appserver\ServletEngine\Authentication;

use AppserverIo\Collections\ArrayList;
use AppserverIo\Collections\CollectionInterface;

/**
 * A subject represents a grouping of related information for a single entity, such
 * as a person. Such information includes the subject's identities as well as its
 * security-related attributes (passwords and cryptographic keys, for example).
 *
 * Subjects may potentially have multiple identities. Each identity is represented
 * as a principal within the subject. Principals simply bind names to a subject.
 * For example, a subject that happens to be a person, Alice, might have
 * two principals: one which binds "Alice Bar", the name on her driver license,
 * to the subject, and another which binds, "999-99-9999", the number on her student
 * identification card, to the subject. Both principals refer to the same subject
 * even though each has a different name.
 *
 * A subject may also own security-related attributes, which are referred to as
 * credentials. Sensitive credentials that require special protection, such as
 * private cryptographic keys, are stored within the private credentials.
 * Credentials intended to be shared, such as public key certificates or Kerberos
 * server tickets are stored within the public credentials. Different permissions
 * are required to access and modify the different credential maps.
 *
 * To retrieve all the principals associated with a subject, invoke the
 * getPrincipals() method. To retrieve invoke the getPublicCredentials() method or
 * getPrivateCredentials() method, respectively. To modify the returned map of
 * principals and credentials, use the methods defined in the map class. For example:
 *
 * <pre>
 *      $subject = new Subject();
 *      $principal = new Principal();
 *      $credential = new String();
 *
 *      // add a principal and credential to the subject
 *      $subject->getPrincipals()->add($principal);
 *      $subject->getPublicCredentials()->add($credential);
 * </pre>
 *
 * This subject class implements the Serializable interface. While the principals
 * associated with the subject are serialized, the credentials associated with the
 * subject are not.
 *
 * @see \AppserverIo\Appserver\ServletEngine\Authentication\PrincipalInterface
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class Subject implements \Serializable
{

    /**
     * Whether this subject is read-only or not.
     *
     * @var boolean
     */
    protected $readOnly = false;

    /**
     * A map that provides a view of all of this subject's principals.
     *
     * @var \AppserverIo\Collections\CollectionInterface
     */
    protected $principals;

    /**
     * A map that provide a view of private credentials of this subject's credentials.
     *
     * @var \AppserverIo\Collections\CollectionInterface
     */
    protected $privateCredentials;

    /**
     * A map that provide a view of public credentials of this subject's credentials.
     *
     * @var \AppserverIo\Collections\CollectionInterface
     */
    protected $publicCredentials;

    /**
     * Initialize the subject with the passed data.
     *
     * @param \AppserverIo\Collections\CollectionInterface $principals         A map with subject's principals
     * @param \AppserverIo\Collections\CollectionInterface $publicCredentials  A map with the public credentials
     * @param \AppserverIo\Collections\CollectionInterface $privateCredentials A map with the private credentials
     * @param boolean                                      $readOnly           Whether this subject is read-only or not
     */
    public function __construct(
        CollectionInterface $principals = null,
        CollectionInterface $publicCredentials = null,
        CollectionInterface $privateCredentials = null,
        $readOnly = false)
    {

        // initialize the principals
        if ($principals == null) {
            $this->principals = new ArrayList();
        } else {
            $this->principals = $principals;
        }

        // initialize the public credentials
        if ($publicCredentials == null) {
            $this->publicCredentials = new ArrayList();
        } else {
            $this->publicCredentials = $publicCredentials;
        }

        // initialize the private credentials
        if ($privateCredentials == null) {
            $this->privateCredentials = new ArrayList();
        } else {
            $this->privateCredentials = $privateCredentials;
        }

        // mark the subject read-only or not
        $this->readOnly = $readOnly;
    }

    /**
     * Set this subject to be read-only.
     *
     * Modifications (additions and removals) to this subject's
     * principals and credentials will be disallowed.
     *
     * The destroy operation on this subject's credentials will
     * still be permitted.
     *
     * Subsequent attempts to modify the subject's principals and
     * credentials will result in an >IllegalStateException being
     * thrown. Also, once a subject is read-only, it can not be
     * reset to being writable again.
     *
     * @return void
     */
    public function setReadOnly()
    {
        $this->readOnly = true;
    }

    /**
     * Query whether this subject is read-only.
     *
     * @return boolean TRUE if this subject is read-only, FALSE otherwise
     */
    public function isReadOnly()
    {
        return $this->readOnly;
    }

    /**
     * Return's the subject's principals.
     *
     * @return \AppserverIo\Collections\CollectionInterface The principals
     */
    public function getPrincipals()
    {
        return $this->principals;
    }

    /**
     * Return's the subject's public credentials.
     *
     * @return \AppserverIo\Collections\CollectionInterface The public credentials
     */
    public function getPublicCredentials()
    {
        return $this->publicCredentials;
    }

    /**
     * Return's the subject's private credentials.
     *
     * @return \AppserverIo\Collections\CollectionInterface The private credentials
     */
    public function getPrivateCredentials()
    {
        return $this->privateCredentials;
    }

    /**
     * Return's a string representation of the subject and the principals.
     *
     * @return string|null The subject as string representation
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize($this->principals);
    }

    /**
     * Called during unserialization of the object.
     *
     * @param string $data The subject as string representation
     *
     * @return void
     * @see \Serializable::unserialize()
     */
    public function unserialize($data)
    {
        $this->principals = unserialize($data);
    }
}
