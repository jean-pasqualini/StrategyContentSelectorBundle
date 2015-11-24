<?php
/**
 * Created by PhpStorm.
 * User: Freelance
 * Date: 22/06/2015
 * Time: 14:36
 */

namespace Digitas\Bundle\StrategyContentSelectorBundle\Manager;

use Symfony\Component\DependencyInjection\ContainerInterface;

class StrategyIdSelectorManager {

    protected $strategys = array();

    /** @var $container ContainerInterface */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getIdByStrategy($strategy, $oneresult = false, array $idsAvailable, array $options = array(), array $context = array())
    {
        $ids = call_user_func($this->getMethodByStrategy($strategy), $idsAvailable, $options, $context);

        if(!is_array($ids))
        {
            $ids = array($ids);
        }

        $ids = array_unique($ids);

        return ($oneresult) ? current($ids) : $ids;
    }

    public function addStrategy($strategy, $callback)
    {
        $this->strategys[$strategy] = $callback;
    }

    public function configureDefaultStrategy()
    {
        $this->addStrategy("auto", array($this, "getContentByStrategyAuto"));

        $this->addStrategy("all", array($this, "getContentByStategyAll"));
    }

    /**
     * Retourne la méthode associé à une strategy pour le traitement de cette strategy
     *
     * @param $strategy
     * @return mixed
     * @throws \Exception
     */
    protected function getMethodByStrategy($strategy)
    {
        if(!isset($this->strategys[$strategy]))
        {
            throw new \Exception("strategy $strategy has no method mapping");
        }

        $callback = $this->strategys[$strategy];

        if(is_string($callback[0]) && $this->container->has($callback[0])) $callback[0] = $this->container->get($callback[0]);

        return $callback;
    }

    /**
     * Retourne le contenu par la strategy auto
     *
     * @param $column
     * @param array $options
     * @param ContextStep $contextStep
     * @return mixed
     */
    protected function getContentByStrategyAuto(array $idsAvailables, array $options, array $context)
    {
        return end($idsAvailables);
    }



    protected function getContentByStategyAll(array $idsAvailables, array $options, array $context)
    {
        return $idsAvailables;
    }


}