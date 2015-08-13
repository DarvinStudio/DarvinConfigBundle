<?php

namespace Darvin\ConfigBundle;

use Darvin\ConfigBundle\Configuration\Compiler\ConfigurationPoolPass;
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

        $container->addCompilerPass(new ConfigurationPoolPass());
    }
}
