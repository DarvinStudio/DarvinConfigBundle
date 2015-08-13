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
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}
