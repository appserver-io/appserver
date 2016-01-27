<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\MetadataConfigurationNode
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

use AppserverIo\Description\Api\Node\AbstractNode;

/**
 * DTO to transfer an entity manager's metadata configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class MetadataConfigurationNode extends AbstractNode
{

    /**
     * Constant for the parameter 'isDevMode'.
     *
     * @var string
     */
    const PARAM_IS_DEV_MODE = 'isDevMode';

    /**
     * Constant for the parameter 'proxyDir'.
     *
     * @var string
     */
    const PARAM_PROXY_DIR = 'proxyDir';

    /**
     * Constant for the parameter 'useSimpleAnnotationReader'.
     *
     * @var string
     */
    const PARAM_USE_SIMPLE_ANNOTATION_READER = 'useSimpleAnnotationReader';

    /**
     * A directories node trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\DirectoriesNodeTrait
     */
    use DirectoriesNodeTrait;

    /**
     * A params node trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ParamsNodeTrait
     */
    use ParamsNodeTrait;

    /**
     * The metadata configuration type, either one of 'annotation', 'xml' or 'yaml'.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * Returns metadata configuration type, either one of 'annotation', 'xml' or 'yaml'.
     *
     * @return string The metadata configuration type
     */
    public function getType()
    {
        return $this->type;
    }
}
