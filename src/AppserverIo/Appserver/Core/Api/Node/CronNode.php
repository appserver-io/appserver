<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\CronNode
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
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * DTO to transfer CRON information.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class CronNode extends AbstractNode implements CronNodeInterface
{

    /**
     * A jobs trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\JobsNodeTrait
     */
    use JobsNodeTrait;

    /**
     * This method merges the passed CRON node with this one
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\CronNodeInterface $cronNode The node to merge
     *
     * @return void
     */
    public function merge(CronNode $cronNode)
    {
        $this->setJobs(array_merge($this->getJobs(), $cronNode->getJobs()));
    }
}
