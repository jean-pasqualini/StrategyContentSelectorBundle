<?php
/**
 * Created by PhpStorm.
 * User: Freelance
 * Date: 22/06/2015
 * Time: 14:36
 */

namespace Digitas\Bundle\StrategyContentSelectorBundle\Manager;

class StrategyIdSelectorManager {

    public function getIdByStrategy($strategy, $oneresult = false, array $idsAvailable, array $options = array(), array $context = array())
    {
        $ids = $this->{$this->getMethodByStrategy($strategy)}($idsAvailable, $options, $context);

        if(!is_array($ids))
        {
            $ids = array($ids);
        }

        $ids = array_unique($ids);

        return ($oneresult) ? current($ids) : $ids;
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
        $methodByStragery = array(
            "auto" => "getContentByStrategyAuto",
            "all" => "getContentByStategyAll",
            "parcours_matching" => "getContentByStrategyParcoursMatching",
            "nature_matching" => "getContentByStrategyNature",
            "parcours_filter" => "getContentByStrategyParcoursFilter"
        );

        if(!isset($methodByStragery[$strategy]))
        {
            throw new \Exception("strategy $strategy has no method mapping");
        }

        return $methodByStragery[$strategy];
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