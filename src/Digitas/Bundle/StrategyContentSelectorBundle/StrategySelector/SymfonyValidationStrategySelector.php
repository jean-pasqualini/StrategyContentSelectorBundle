<?php
/**
 * Created by PhpStorm.
 * User: Freelance
 * Date: 24/11/2015
 * Time: 12:49
 */

namespace Digitas\Bundle\StrategyContentSelectorBundle\StrategySelector;

use Digitas\Bundle\FormCourseBundle\Tools\ArrayPathNormalizer;
use Digitas\Bundle\FormErrorMappingBundle\Form\Field\Accessor\FormFieldAccessor;
use Symfony\Component\Validator\Validation;
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

    protected function newContraintContainerByArray($array)
    {
        $collectionConstraint = array();

        foreach($array as $key => $value)
        {
            if(is_array($value))
            {
                $constraints = array($this->newContraintContainerByArray($value));
            }
            else
            {
                $constraintsDefinition = $value->getArrayCopy();
                $constraints = array();

                foreach($constraintsDefinition as $class => $options)
                {
                    $constraints[] = $this->newConstraint($class, $options);
                }
            }

            $collectionConstraint[$key] = $constraints;
        }

        return new Assert\Collection($collectionConstraint);
    }

    /**
     * Parses a collection of YAML nodes.
     *
     * @param array $nodes The YAML nodes
     *
     * @return array An array of values or Constraint instances
     */
    protected function parseNodes(array $nodes)
    {
        return $this->newContraintContainerByArray($nodes);
    }

    protected function isValid($constraints, $value)
    {
        $constraints = $this->parseNodes($constraints);

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
        foreach($options["aiguilleurs"] as $aiguilleur)
        {
            $aiguilleur["constraints"] = $this->applyConstraintCollection($aiguilleur["constraints"]);

            $aiguilleur["constraints"] = $this->applyPathArrayToStrandardArray($aiguilleur["constraints"]);

            if($this->isValid($aiguilleur["constraints"], $context["data"]))
            {
                return $aiguilleur["value"];
            }
        }

        return null;
    }
}