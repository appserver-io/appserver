<?php
/**
 * AppserverIo\Appserver\Core\Api\DtoNormalizer
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api;

use TechDivision\Configuration\Interfaces\ConfigurationInterface;
use AppserverIo\Appserver\Core\Api\NormalizerInterface;

/**
 * Normalizes configuration nodes to DTO instances.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class DtoNormalizer extends AbstractNormalizer
{

    /**
     * (non-PHPdoc)
     *
     * @param \TechDivision\Configuration\Interfaces\ConfigurationInterface $configuration The configuration node to normalize
     *
     * @return \stdClass The normalized configuration node
     * @see \AppserverIo\Appserver\Core\Api\NormalizerInterface::normalize()
     */
    public function normalize(ConfigurationInterface $configuration)
    {
        $nodeType = $this->getService()->getNodeType();
        return $this->newInstance($nodeType, array(
            $configuration
        ));
    }
}
