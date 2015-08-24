<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ConfigBundle\Security\Authorization;

use Darvin\ConfigBundle\Configuration\ConfigurationInterface;
use Darvin\UtilsBundle\Security\Authorization\AuthorizationCheckerProvider;

/**
 * Configuration authorization checker
 */
class ConfigurationAuthorizationChecker
{
    /**
     * @var \Darvin\UtilsBundle\Security\Authorization\AuthorizationCheckerProvider
     */
    private $authorizationCheckerProvider;

    /**
     * @param \Darvin\UtilsBundle\Security\Authorization\AuthorizationCheckerProvider $authorizationCheckerProvider Authorization checker provider
     */
    public function __construct(AuthorizationCheckerProvider $authorizationCheckerProvider)
    {
        $this->authorizationCheckerProvider = $authorizationCheckerProvider;
    }

    /**
     * @param \Darvin\ConfigBundle\Configuration\ConfigurationInterface $configuration Configuration to check
     *
     * @return bool
     */
    public function isAccessible(ConfigurationInterface $configuration)
    {
        $allowedRoles = $configuration->getAllowedRoles();

        if (empty($allowedRoles)) {
            return true;
        }

        $authorizationChecker = $this->authorizationCheckerProvider->getAuthorizationChecker();

        foreach ($allowedRoles as $allowedRole) {
            if ($authorizationChecker->isGranted($allowedRole)) {
                return true;
            }
        }

        return false;
    }
}
