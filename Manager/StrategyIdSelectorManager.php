<?php
/**
 * Created by PhpStorm.
 * User: Freelance
 * Date: 22/06/2015
 * Time: 14:36
 */

namespace Digitas\Bundle\StrategyContentSelectorBundle\Manager;

use Digitas\Bundle\StrategyContentSelectorBundle\Model\ContextStep;

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

    /**
     * Retourne le contenu par la stratégy parcours_matching
     *
     * @param $column
     * @param array $options
     * @param ContextStep $contextStep
     * @return mixed
     * @throws \Exception
     */
    protected function getContentByStrategyParcoursMatching(array $idsAvailables, array $options, array $context)
    {
        foreach($options["patterns"] as $pattern => $idContent)
        {
            if(preg_match($pattern, $context["contextStep"]->getSteps()))
            {
                return $idContent;
            }
        }

        throw new \Exception("aucun pattern ne correspond");
    }

    /**
     * Retourne le contenu par la stratégy nature
     *
     * @param $column
     * @param array $options
     * @param ContextStep $contextStep
     * @return array|mixed
     */
    protected function getContentByStrategyNature(array $idsAvailables, array $options, array $context)
    {
        return isset($options["natures"][$context["contextStep"]->getNature()]) ? $options["natures"][$context["contextStep"]->getNature()] : array();
    }

    protected function getContentByStategyAll(array $idsAvailables, array $options, array $context)
    {
        return $idsAvailables;
    }

    protected function getContentByStrategyParcoursFilter(array $idsAvailables, array $options, array $context)
    {
        /**
         * @var $contextStep ContextStep
         */
        $contextStep = $context["contextStep"];

        $stepsMapped = $contextStep->getStepsMapped();

        $ids = array();

        foreach($options["ids"] as $potentialId => $idCondition)
        {
            foreach($idCondition["filters"] as $filter)
            {
                $nature = null;

                if(isset($filter["nature"]))
                {
                    $nature = $filter["nature"];
                    unset($filter["nature"]);
                }

                $difference = array_diff_assoc($filter, $stepsMapped);

                // S'il n'y as pas de filtres non conforme au context actuel alors on utilise ces ids
                if(empty($difference) && ($nature === null || $nature == $contextStep->getNature()))
                {
                    $ids[] = $potentialId;
                }
            }
        }

        return $ids;
    }
}