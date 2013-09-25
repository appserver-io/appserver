<?php

/**
 * TechDivision\ApplicationServer\Vhost
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer;

/**
 * A basic vhost class containing domain name, base directory and aliases.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class Vhost
{

    /**
     * Path to the container's VHost configuration.
     *
     * @var string
     */
    const XPATH_CONTAINER_VHOSTS = '/container/host/vhosts/vhost';

    /**
     * Path to the container's VHost alias configuration.
     *
     * @var string
     */
    const XPATH_CONTAINER_ALIAS = '/vhost/aliases/alias';

    /**
     * The vhost's domain name.
     *
     * @var string
     */
    protected $name;

    /**
     * The vhost base directory relative to webapps directory.
     *
     * @var string
     */
    protected $appBase;

    /**
     * Array containing the vhost aliases.
     *
     * @var array
     */
    protected $aliases = array();

    /**
     * Initializes the vhost with the necessary information.
     *
     * @param string $name
     *            The vhost's domain name
     * @param string $appBase
     *            The vhost's base directory
     * @param array $aliases
     *            The aliases
     */
    public function __construct($name, $appBase, $aliases)
    {
        $this->name = $name;
        $this->appBase = $appBase;
        $this->aliases = $aliases;
    }

    /**
     * Returns the vhost's domain name.
     *
     * @return string The vhost's domain name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the vhost's base directory.
     *
     * @return string The vhost's base directory
     */
    public function getAppBase()
    {
        return $this->appBase;
    }

    /**
     * Returns the vhost's aliases.
     *
     * @return array The aliases
     */
    public function getAliases()
    {
        return $this->aliases;
    }
}