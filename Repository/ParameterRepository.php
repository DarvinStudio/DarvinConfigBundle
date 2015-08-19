<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ConfigBundle\Repository;

use Darvin\ConfigBundle\Entity\ParameterEntity;
use Doctrine\ORM\EntityRepository;

/**
 * Configuration parameter entity repository
 *
 * @method \Darvin\ConfigBundle\Entity\ParameterEntity[] findAll()
 */
class ParameterRepository extends EntityRepository implements ParameterRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        return array_map(function (ParameterEntity $parameterEntity) {
            return $parameterEntity->toParameter();
        }, $this->findAll());
    }

    /**
     * @param \Darvin\ConfigBundle\Parameter\Parameter[] $parameters Configuration parameters
     */
    public function save(array $parameters)
    {
        $parameterEntities = array();

        foreach ($this->findAll() as $parameterEntity) {
            $configurationName = $parameterEntity->getConfigurationName();

            if (!isset($parameterEntities[$configurationName])) {
                $parameterEntities[$configurationName] = array();
            }

            $parameterEntities[$configurationName][$parameterEntity->getName()] = $parameterEntity;
        }
        foreach ($parameters as $parameter) {
            $configurationName = $parameter->getConfigurationName();
            $parameterEntity = isset($parameterEntities[$configurationName][$parameter->getName()])
                ? $parameterEntities[$configurationName][$parameter->getName()]
                : new ParameterEntity();
            $parameterEntity->updateFromParameter($parameter);

            $this->_em->persist($parameterEntity);
        }

        $this->_em->flush();
    }
}
