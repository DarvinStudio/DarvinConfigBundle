<?php

namespace Darvin\ConfigBundle;

use Darvin\ConfigBundle\DependencyInjection\Compiler\ConfigurationPoolPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Configuration bundle
 */
class DarvinConfigBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ConfigurationPoolPass(), PassConfig::TYPE_BEFORE_REMOVING);
    }
}
