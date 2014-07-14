<?php
/**
 * TechDivision\ApplicationServer\Api\DtoNormalizer
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Api;

use TechDivision\Configuration\Interfaces\ConfigurationInterface;
use TechDivision\ApplicationServer\Api\NormalizerInterface;

/**
 * Normalizes configuration nodes to DTO instances.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
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
     * @see \TechDivision\ApplicationServer\Api\NormalizerInterface::normalize()
     */
    public function normalize(ConfigurationInterface $configuration)
    {
        $nodeType = $this->getService()->getNodeType();
        return $this->newInstance($nodeType, array(
            $configuration
        ));
    }
}
