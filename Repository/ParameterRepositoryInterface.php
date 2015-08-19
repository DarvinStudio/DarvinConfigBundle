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
