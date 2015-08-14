<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 13.08.15
 * Time: 16:29
 */

namespace Darvin\ConfigBundle\Repository;

/**
 * Configuration parameter repository
 */
interface ParameterRepositoryInterface
{
    /**
     * @return \Darvin\ConfigBundle\Parameter\Parameter[]
     */
    public function getAll();

    /**
     * @param \Darvin\ConfigBundle\Parameter\Parameter[] $parameters Configuration parameters
     */
    public function save(array $parameters);
}
