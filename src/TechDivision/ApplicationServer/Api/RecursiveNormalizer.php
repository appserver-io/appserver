<?php
/**
 * TechDivision\ApplicationServer\Api\RecursiveNormalizer
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

/**
 * Normalizes configuration nodes recursive to \stdClass instances.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class RecursiveNormalizer extends Normalizer
{

    /**
     * Normalizes the passed configuration node recursive and returns
     * a \stdClass representation of it.
     *
     * @param \TechDivision\Configuration\Interfaces\ConfigurationInterface $configuration The configuration node to normalize recursive
     *
     * @return \stdClass The normalized configuration node
     */
    public function normalize(ConfigurationInterface $configuration)
    {

        // normalize the configuration node without children
        $node = parent::normalize($configuration);

        // now we add recursive normalization
        foreach ($configuration->getChildren() as $child) {
            $node->{$configuration->getNodeName()}->children[] = $this->normalize($child);
        }

        // return the normalized node instance
        return $node;
    }
}
