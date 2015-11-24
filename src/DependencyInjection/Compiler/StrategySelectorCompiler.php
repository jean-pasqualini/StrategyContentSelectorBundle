<?php
/**
 * Created by PhpStorm.
 * User: Freelance
 * Date: 24/11/2015
 * Time: 12:04
 */

namespace Digitas\Bundle\StrategyContentSelectorBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class StrategySelectorCompiler implements CompilerPassInterface {
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        exit("a");

        if(!$container->hasDefinition("digitas_strategy_content_selector_bundle.id_strategy_selector_manager"))
        {
            return;
        }

        $definition = $container->getDefinition("digitas_strategy_content_selector_bundle.id_strategy_selector_manager");

        foreach($container->findTaggedServiceIds("strategy_selector.extension_subscriber") as $id => $attributes)
        {
            $class = $container->getDefinition($id)->getClass();

            $strategyMethods = call_user_func_array($class, array("getStategyMethods"));

            exit(print_r($strategyMethods, true));
        }
    }
}