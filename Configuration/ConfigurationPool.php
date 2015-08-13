<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 13.08.15
 * Time: 15:55
 */

namespace Darvin\ConfigBundle\Configuration;

/**
 * Configuration pool
 */
class ConfigurationPool
{
    /**
     * @var \Darvin\ConfigBundle\Configuration\ConfigurationInterface[]
     */
    private $configurations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->configurations[] = array();
    }

    /**
     * @param \Darvin\ConfigBundle\Configuration\ConfigurationInterface $configuration Configuration
     */
    public function add(ConfigurationInterface $configuration)
    {
        $this->configurations[] = $configuration;
    }

    /**
     * @return \Darvin\ConfigBundle\Configuration\ConfigurationInterface[]
     */
    public function getAll()
    {
        return $this->configurations;
    }
}
