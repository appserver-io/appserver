<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\WebResourceCollectionNode
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

namespace AppserverIo\Appserver\Core\Api\Node;

use AppserverIo\Description\Annotations as DI;
use AppserverIo\Description\Api\Node\AbstractNode;
use AppserverIo\Configuration\Interfaces\NodeValueInterface;

/**
 * DTO to transfer a security constraint.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class WebResourceCollectionNode extends AbstractNode implements WebResourceCollectionNodeInterface
{

    /**
     * The web resource name information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\WebResourceNameNode
     * @DI\Mapping(nodeName="web-resource-name", nodeType="AppserverIo\Appserver\Core\Api\Node\WebResourceNameNode")
     */
    protected $webResourceName;

    /**
     * The description information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\DescriptionNode
     * @DI\Mapping(nodeName="description", nodeType="AppserverIo\Appserver\Core\Api\Node\DescriptionNode")
     */
    protected $description;

    /**
     * The URL pattern information.
     *
     * @var array
     * @DI\Mapping(nodeName="url-pattern", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\UrlPatternNode")
     */
    protected $urlPatterns = array();

    /**
     * The HTTP method information.
     *
     * @var array
     * @DI\Mapping(nodeName="http-method", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\HttpMethodNode")
     */
    protected $httpMethods = array();

    /**
     * The HTTP method omission information.
     *
     * @var array
     * @DI\Mapping(nodeName="http-method-omission", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\HttpMethodOmissionNode")
     */
    protected $httpMethodOmissions = array();

    /**
     * Initializes the node with the passed values.
     *
     * @param \AppserverIo\Configuration\Interfaces\NodeValueInterface $webResourceName     The web resource name information
     * @param \AppserverIo\Configuration\Interfaces\NodeValueInterface $description         The description information
     * @param array                                                    $urlPatterns         The array with the URL pattern information
     * @param array                                                    $httpMethods         The array with the HTTP method information
     * @param array                                                    $httpMethodOmissions The array with the HTTP method omission information
     */
    public function __construct(
        NodeValueInterface $webResourceName = null,
        NodeValueInterface $description = null,
        array $urlPatterns = array(),
        array $httpMethods = array(),
        array $httpMethodOmissions = array()
    ) {
        $this->webResourceName = $webResourceName;
        $this->description = $description;
        $this->urlPatterns = $urlPatterns;
        $this->httpMethods = $httpMethods;
        $this->httpMethodOmissions = $httpMethodOmissions;
    }

    /**
     * Return's the web resource name information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\WebResourceNameNode The web resource name information
     */
    public function getWebResourceName()
    {
        return $this->webResourceName;
    }

    /**
     * Return's the description information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DescriptionNode The description information
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Return's the URL pattern information.
     *
     * @return array The URL pattern information
     */
    public function getUrlPatterns()
    {
        return $this->urlPatterns;
    }

    /**
     * Return's the HTTP method information.
     *
     * @return array The HTTP method information
     */
    public function getHttpMethods()
    {
        return $this->httpMethods;
    }

    /**
     * Return's the HTTP method omission information.
     *
     * @return array The HTTP method omission information
     */
    public function getHttpMethodOmissions()
    {
        return $this->httpMethodOmissions;
    }

    /**
     * Returns the URL patterns as an associative array
     *
     * @return array The array with the sorted URL patterns
     */
    public function getUrlPatternsAsArray()
    {

        // initialize the array for the URL patterns
        $urlPatterns = array();

        // prepare the URL patterns
        /** @var \AppserverIo\Appserver\Core\Api\Node\UrlPatternNode $urlPatternNode */
        foreach ($this->getUrlPatterns() as $urlPatternNode) {
            $urlPatterns[] = $urlPatternNode->__toString();
        }

        // return the array with the URL patterns
        return $urlPatterns;
    }

    /**
     * Returns the HTTP methods as an associative array.
     *
     * The HTTP methods will be converted to upper case when using this method.
     *
     * @return array The array with the HTTP methods
     */
    public function getHttpMethodsAsArray()
    {

        // initialize the array for the HTTP methods
        $httpMethods = array();

        // prepare the HTTP methods
        /** @var \AppserverIo\Appserver\Core\Api\Node\HttpMethodNode $httpMethodNode */
        foreach ($this->getHttpMethods() as $httpMethodNode) {
            $httpMethods[] = strtoupper($httpMethodNode->__toString());
        }

        // return the array with the HTTP methods
        return $httpMethods;
    }

    /**
     * Returns the HTTP method omissions as an associative array.
     *
     * The HTTP methods will be converted to upper case when using this method.
     *
     * @return array The array with the HTTP method omissions
     */
    public function getHttpMethodOmissionsAsArray()
    {

        // initialize the array for the HTTP method omissions
        $httpMethodOmissions = array();

        // prepare the HTTP method omissions
        /** @var \AppserverIo\Appserver\Core\Api\Node\HttpMethodOmissionNode $httpMethodOmissionNode */
        foreach ($this->getHttpMethodOmissions() as $httpMethodOmissionNode) {
            $httpMethodOmissions[] = strtoupper($httpMethodOmissionNode->__toString());
        }

        // return the array with the HTTP method omissions
        return $httpMethodOmissions;
    }
}
