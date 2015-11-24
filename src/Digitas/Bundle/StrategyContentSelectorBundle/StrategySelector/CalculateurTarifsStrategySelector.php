<?php
/**
 * Created by PhpStorm.
 * User: Freelance
 * Date: 24/11/2015
 * Time: 11:48
 */
namespace Digitas\Bundle\StrategyContentSelectorBundle\StrategySelector;

use Digitas\Bundle\LaPosteCalculateurTarifsBundle\Model\ContextStep;

class CalculateurTarifsStrategySelector {

    public static function getStrategyMethods()
    {
        return array(
            "parcours_matching" => "getContentByStrategyParcoursMatching",
            "nature_matching" => "getContentByStrategyNature",
            "parcours_filter" => "getContentByStrategyParcoursFilter"
        );
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