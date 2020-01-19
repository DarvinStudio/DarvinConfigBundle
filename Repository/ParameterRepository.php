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
use Doctrine\ORM\QueryBuilder;

/**
 * Configuration parameter entity repository
 */
class ParameterRepository extends ServiceEntityRepository implements ParameterRepositoryInterface
{
    /**
     * @var \Darvin\ConfigBundle\Entity\ParameterEntity[]|null
     */
    private $entities = null;

    /**
     * {@inheritDoc}
     */
    public function getConfigurationParameters(string $configuration): iterable
    {
        foreach ($this->getByConfiguration($configuration) as $entity) {
            yield $entity->toParameter();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function saveConfigurationParameters(string $configuration, array $parameters): void
    {
        $entities = $this->getByConfiguration($configuration);

        foreach ($parameters as $parameter) {
            if (isset($entities[$parameter->getName()])) {
                $entity = $entities[$parameter->getName()];

                unset($entities[$parameter->getName()]);
            } else {
                $entity = new ParameterEntity();
            }

            $entity->updateFromParameter($parameter);

            $this->_em->persist($entity);
        }
        foreach ($entities as $entity) {
            $this->_em->remove($entity);
        }

        $this->_em->flush();
    }

    /**
     * @param string $configuration Configuration name
     *
     * @return \Darvin\ConfigBundle\Entity\ParameterEntity[]
     */
    private function getByConfiguration(string $configuration): array
    {
        if (null === $this->entities) {
            $entities = [];

            /** @var \Darvin\ConfigBundle\Entity\ParameterEntity $entity */
            foreach ($this->createDefaultBuilder()->getQuery()->enableResultCache()->getResult() as $entity) {
                if (!isset($entities[$entity->getConfiguration()])) {
                    $entities[$entity->getConfiguration()] = [];
                }

                $entities[$entity->getConfiguration()][$entity->getName()] = $entity;
            }

            $this->entities = $entities;
        }

        return $this->entities[$configuration] ?? [];
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function createDefaultBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('o');
    }
}
