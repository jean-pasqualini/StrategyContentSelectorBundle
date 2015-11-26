<?php
/**
 * Created by PhpStorm.
 * User: Freelance
 * Date: 25/11/2015
 * Time: 15:17
 */

namespace Digitas\Bundle\StrategyContentSelectorBundle\Tools;

/**
 * Class ArrayPathNormalizer
 * @package Digitas\Bundle\FormCourseBundle\Tools
 */
class ArrayPathNormalizer {

    /**
     * Normalize un tableau d'ont l'arborescende des clé est réprésenté sous forme de chemin ex (level1.level2.level3 : contenu)
     * @param $pathArray
     * @return array
     */
    public static function normalize($pathArray)
    {
        $standartArray = array();

        foreach($pathArray as $path => $value)
        {
            $standartArray = array_merge_recursive($standartArray, self::createArray($path, $value));
        }

        return $standartArray;
    }

    /**
     * @param $path
     * @param $data
     * @param array $output
     * @return array
     */
    public static function createArray($path, $data, $output = array())
    {
        $arborescence = explode(".", $path);

        if(count($arborescence) === 1)
        {
            $output[$arborescence[0]] = $data;

            return $output;
        }

        $output[$arborescence[0]] = self::createArray(implode(".", array_slice($arborescence, 1)), $data, $output);

        return $output;
    }
}