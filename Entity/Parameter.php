<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 13.08.15
 * Time: 16:25
 */

namespace Darvin\ConfigBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Configuration parameter
 *
 * @ORM\Entity(repositoryClass="Darvin\ConfigBundle\Repository\ParameterRepository")
 * @ORM\Table(name="configuration")
 */
class Parameter
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $configurationName;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $value;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $configurationName configurationName
     *
     * @return Parameter
     */
    public function setConfigurationName($configurationName)
    {
        $this->configurationName = $configurationName;

        return $this;
    }

    /**
     * @return string
     */
    public function getConfigurationName()
    {
        return $this->configurationName;
    }

    /**
     * @param string $name name
     *
     * @return Parameter
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $type type
     *
     * @return Parameter
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $value value
     *
     * @return Parameter
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
