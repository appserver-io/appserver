<?php
/**
 * TechDivision\ApplicationServer\Api\Node\FileHandlerNode
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Johann Zelger <jz@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Api\Node;

/**
 * DTO to transfer file handler information.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Johann Zelger <jz@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class FileHandlerNode extends AbstractNode
{

    /**
     * The file handler name
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The file extension
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $extension;

    /**
     * Returns the file handler's name
     *
     * @return string The file handler's name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return's the file extension
     *
     * @return string The file extension
     */
    public function getExtension()
    {
        return $this->extension;
    }
}
