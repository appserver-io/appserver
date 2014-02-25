<?php
/**
 * TechDivision\ApplicationServer\Api\Node\DatasourceNode
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

namespace TechDivision\ApplicationServer\Api\Node;

/**
 * DTO to transfer a datasource.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class DatasourceNode extends AbstractNode
{

    /**
     * The unique datasource name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The database connection information.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\DatabaseNode
     * @AS\Mapping(nodeName="database", nodeType="TechDivision\ApplicationServer\Api\Node\DatabaseNode")
     */
    protected $database;

    /**
     * Returns the unique datasource name.
     *
     * @return string|null The unique datasource name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the database connection information.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\DatabaseNode The database connection information
     */
    public function getDatabase()
    {
        return $this->database;
    }
}
