<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\HeadersNodeTrait
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

use AppserverIo\Description\Annotations as DI;

/**
 * Trait that serves nodes having a headers/header child.
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
trait HeadersNodeTrait
{

    /**
     * The file headers.
     *
     * @var array
     * @DI\Mapping(nodeName="headers/header", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\HeaderNode")
     */
    protected $headers = array();

    /**
     * Returns the file headers nodes.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Returns the headers as an associative array.
     *
     * @return array The array with the headers
     */
    public function getHeadersAsArray()
    {
        $headers = array();
        foreach ($this->getHeaders() as $headerNode) {
            $header = array(
                'type' => $headerNode->getType(),
                'name' => $headerNode->getName(),
                'value' => $headerNode->getValue(),
                'uri' => $headerNode->getUri(),
                'override' => $headerNode->getOverride(),
                'append' => $headerNode->getAppend()
            );
            $headers[$headerNode->getType()][] = $header;
        }
        return $headers;
    }
}
