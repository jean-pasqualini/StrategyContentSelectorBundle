<?php
/**
 * Created by PhpStorm.
 * User: Freelance
 * Date: 24/11/2015
 * Time: 12:49
 */

namespace Digitas\Bundle\StrategyContentSelectorBundle\StrategySelector;

use Digitas\Bundle\StrategyContentSelectorBundle\Tools\ArrayPathNormalizer;
use Symfony\Component\Validator\ValidatorInterface;

use Symfony\Component\Validator\Constraints as Assert;

class SymfonyValidationStrategySelector {

    /**
     * The namespace to load constraints from by default.
     */
    const DEFAULT_NAMESPACE = '\\Symfony\\Component\\Validator\\Constraints\\';
    /**
     * @var array
     */
    protected $namespaces = array();

    protected $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public static function getStrategyMethods()
    {
        return array(
            'symfony_validation' => 'getBySymfonyValidation'
        );
    }

    /**
     * Creates a new constraint instance for the given constraint name.
     *
     * @param string $name    The constraint name. Either a constraint relative
     *                        to the default constraint namespace, or a fully
     *                        qualified class name. Alternatively, the constraint
     *                        may be preceded by a namespace alias and a colon.
     *                        The namespace alias must have been defined using
     *                        {@link addNamespaceAlias()}.
     * @param mixed  $options The constraint options
     *
     * @return Constraint
     *
     * @throws MappingException If the namespace prefix is undefined
     */
    protected function newConstraint($name, $options = null)
    {
        if(strpos($name, '-') !== false)
        {
            $name = substr($name, strpos($name, '-') + 1);
        }

        if (strpos($name, '\\') !== false && class_exists($name)) {
            $className = (string) $name;
        } elseif (strpos($name, ':') !== false) {
            list($prefix, $className) = explode(':', $name, 2);
            if (!isset($this->namespaces[$prefix])) {
                throw new MappingException(sprintf('Undefined namespace prefix "%s"', $prefix));
            }
            $className = $this->namespaces[$prefix].$className;
        } else {
            $className = self::DEFAULT_NAMESPACE.$name;
        }
        return new $className($options);
    }

    protected function newContraintContainerByArray($array, $optionsStrategy)
    {
        $collectionConstraint = array();

        foreach($array as $key => $value)
        {
            if(is_array($value))
            {
                $constraints = array($this->newContraintContainerByArray($value, $optionsStrategy));
            }
            else
            {
                $constraints = $this->newContraintCollectionByArrayObject($value);
            }

            $collectionConstraint[$key] = $constraints;
        }

        return new Assert\Collection(array(
            "fields" => $collectionConstraint,
            "allowExtraFields" => $optionsStrategy["allowExtraFields"],
            "allowMissingFields" => $optionsStrategy["allowMissingFields"],
        ));
    }

    protected function newContraintCollectionByArrayObject(\ArrayObject $arrayObject)
    {
        $constraintsDefinition = $arrayObject->getArrayCopy();
        $constraints = array();

        foreach($constraintsDefinition as $class => $options)
        {
            $constraints[] = $this->newConstraint($class, $options);
        }

        return $constraints;
    }

    /**
     * Parses a collection of YAML nodes.
     *
     * @param array $nodes The YAML nodes
     *
     * @return array An array of values or Constraint instances
     */
    protected function parseNodes(array $nodes, $optionsStrategy)
    {
        return $this->newContraintContainerByArray($nodes, $optionsStrategy);
    }

    protected function isValid($constraints, $value, $optionsStrategy)
    {

        if(is_array($constraints))
        {
            $constraints = $this->parseNodes($constraints, $optionsStrategy);
        }
        elseif($constraints instanceof \ArrayObject)
        {
            $constraints = $this->newContraintCollectionByArrayObject($constraints);
        }
        else
        {
            throw new \Exception("invalid type constraints");
        }

        return $this->validator->validateValue($value, $constraints)->count() < 1;
    }

    protected function applyPathArrayToStrandardArray($pathArray)
    {
        return ArrayPathNormalizer::normalize($pathArray);
    }

    public function applyConstraintCollection($constraints)
    {
        foreach($constraints as $key => $value)
        {
            $constraints[$key] = new \ArrayObject($value);
        }

        return $constraints;
    }

    public function getBySymfonyValidation(array $idsAvailables, array $options, array $context)
    {
        $options = array_merge(array(
            "unique_result" => false,
            "allowExtraFields" => true,
            "allowMissingFields" => false
        ), $options);

        $idsValides = array();

        foreach($options["aiguilleurs"] as $aiguilleur)
        {
            $aiguilleur["constraints"] = $this->applyConstraintCollection($aiguilleur["constraints"]);

            $aiguilleur["constraints"] = $this->applyPathArrayToStrandardArray($aiguilleur["constraints"]);

            if($this->isValid($aiguilleur["constraints"], $context["data"], $options))
            {
                $idValide = $aiguilleur["value"];

                $idsValides[] = $idValide;

                if(!empty($options["unique_result"])) return $idValide;
            }
        }

        return (!empty($idsValides)) ? $idsValides : null;
    }
}