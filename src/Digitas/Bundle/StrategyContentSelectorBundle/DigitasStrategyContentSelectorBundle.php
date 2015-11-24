<?php

namespace Digitas\Bundle\StrategyContentSelectorBundle;

use Digitas\Bundle\StrategyContentSelectorBundle\DependencyInjection\Compiler\StrategySelectorCompiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DigitasStrategyContentSelectorBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new StrategySelectorCompiler());
    }
}
