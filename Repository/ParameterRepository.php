<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ConfigBundle\Repository;

use Darvin\ConfigBundle\Entity\ParameterEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * Configuration parameter entity repository
 *
 * @method \Darvin\ConfigBundle\Entity\ParameterEntity[] findAll()
 */
class ParameterRepository extends ServiceEntityRepository implements ParameterRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getAllParameters(): iterable
    {
        foreach ($this->findAll() as $entity) {
            yield $entity->toParameter();
        }
    }

    /**
     * @param \Darvin\ConfigBundle\Parameter\Parameter[] $parameters Configuration parameters
     */
    public function save(array $parameters): void
    {
        $entities = [];

        foreach ($this->findAll() as $entity) {
            $configurationName = $entity->getConfigurationName();

            if (!isset($entities[$configurationName])) {
                $entities[$configurationName] = [];
            }

            $entities[$configurationName][$entity->getName()] = $entity;
        }
        foreach ($parameters as $parameter) {
            $configurationName = $parameter->getConfigurationName();

            $entity = isset($entities[$configurationName][$parameter->getName()])
                ? $entities[$configurationName][$parameter->getName()]
                : new ParameterEntity();

            $entity->updateFromParameter($parameter);

            $this->_em->persist($entity);
        }

        $this->_em->flush();
    }
}
