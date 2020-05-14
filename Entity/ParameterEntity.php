<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ConfigBundle\Entity;

use Darvin\ConfigBundle\Parameter\Parameter;
use Doctrine\ORM\Mapping as ORM;

/**
 * Configuration parameter entity
 *
 * @ORM\Entity(repositoryClass="Darvin\ConfigBundle\Repository\ParameterRepository")
 * @ORM\Table(name="config")
 */
class ParameterEntity
{
    /**
     * @var string
     *
     * @ORM\Column(length=36, unique=true)
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $configuration;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $type;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $value;

    /**
     * @return \Darvin\ConfigBundle\Parameter\Parameter
     */
    public function toParameter(): Parameter
    {
        return new Parameter($this->configuration, $this->name, $this->type, $this->value);
    }

    /**
     * @param \Darvin\ConfigBundle\Parameter\Parameter $parameter Parameter
     */
    public function updateFromParameter(Parameter $parameter): void
    {
        $this->configuration = $parameter->getConfiguration();
        $this->name = $parameter->getName();
        $this->type = $parameter->getType();
        $this->value = $parameter->getValue();
    }

    /**
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getConfiguration(): ?string
    {
        return $this->configuration;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }
}
