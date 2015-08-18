<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 13.08.15
 * Time: 16:25
 */

namespace Darvin\ConfigBundle\Entity;

use Darvin\ConfigBundle\Parameter\Parameter;
use Doctrine\ORM\Mapping as ORM;

/**
 * Configuration parameter entity
 *
 * @ORM\Entity(repositoryClass="Darvin\ConfigBundle\Repository\ParameterRepository")
 * @ORM\Table(name="configuration")
 */
class ParameterEntity
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $value;

    /**
     * @param \Darvin\ConfigBundle\Parameter\Parameter $parameter Parameter
     */
    public function updateFromParameter(Parameter $parameter)
    {
        $this->configurationName = $parameter->getConfigurationName();
        $this->name = $parameter->getName();
        $this->type = $parameter->getType();
        $this->value = $parameter->getValue();
    }

    /**
     * @return \Darvin\ConfigBundle\Parameter\Parameter
     */
    public function toParameter()
    {
        return new Parameter($this->configurationName, $this->name, $this->type, $this->value);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getConfigurationName()
    {
        return $this->configurationName;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
