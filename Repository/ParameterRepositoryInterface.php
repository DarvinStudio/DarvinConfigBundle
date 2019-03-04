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

/**
 * Configuration parameter repository
 */
interface ParameterRepositoryInterface
{
    /**
     * @param string $configuration Configuration name
     *
     * @return \Darvin\ConfigBundle\Parameter\Parameter[]|iterable
     */
    public function getConfigurationParameters(string $configuration): iterable;

    /**
     * @param string                                     $configuration Configuration name
     * @param \Darvin\ConfigBundle\Parameter\Parameter[] $parameters    Configuration parameters
     */
    public function saveConfigurationParameters(string $configuration, array $parameters): void;
}
