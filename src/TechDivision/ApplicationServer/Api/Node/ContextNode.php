<?php
/**
 * TechDivision\ApplicationServer\Api\Node\ContextNode
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @author     Johann Zelger <jz@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Api\Node;

/**
 * DTO to transfer server information.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @author     Johann Zelger <jz@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class ContextNode extends AbstractNode
{
    // We use several traits which give us the possibility to have collections of the child nodes mentioned in the
    // corresponding trait name
    use ClassLoadersNodeTrait;
    use ManagersNodeTrait;

    /**
     * The context name,
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The context type.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * Initializes the context configuration with the passed values.
     *
     * @param string $name The context name
     * @param string $type The context class name
     */
    public function __construct($name = '', $type = '')
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * Returns the context name.
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the context type.
     *
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * This method merges the installation steps of the passed provisioning node into the steps of
     * this instance. If a installation node with the same type already exists, the one of this
     * instance will be overwritten.
     *
     * @param \TechDivision\ApplicationServer\Api\Node\ContextNode $contextNode The node with the installation steps we want to merge
     *
     * @return void
     */
    public function merge(ContextNode $contextNode)
    {

        // load the managers defined of this context
        $localManagers = $this->getDirectories();

        // merge them with the passed ones
        foreach ($contextNode->getManagers() as $managerToMerge) {
            $isMerged = false;
            foreach ($localManagers as $key => $manager) {
                if ($manager->getName() === $managerToMerge->getName()) {
                    $localManagers[$key] = $managerToMerge;
                    $isMerged = true;
                }
            }
            if ($isMerged === false) {
                $localManagers[$managerToMerge->getUuid()] = $managerToMerge;
            }
        }

        // set the managers back to the context
        $this->setManagers($localManagers);

        // load the class loaders of this context
        $localClassLoaders = $this->getClassLoaders();

        // merge them with the passed ones
        foreach ($contextNode->getClassLoaders() as $classLoaderToMerge) {
            $isMerged = false;
            foreach ($localDirectories as $key => $classLoader) {
                if ($classLoader->getName() === $classLoaderToMerge->getName()) {
                    $localClassLoaders[$key] = $classLoaderToMerge;
                    $isMerged = true;
                }
            }
            if ($isMerged === false) {
                $localClassLoaders[$classLoaderToMerge->getUuid()] = $classLoaderToMerge;
            }
        }

        // set the class loaders back to the context
        $this->setClassLoaders($localClassLoaders);
    }
}
