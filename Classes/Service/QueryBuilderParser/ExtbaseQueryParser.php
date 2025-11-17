<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Service\QueryBuilderParser;

use \stdClass;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Query;

/**
 * Class ExtbaseQueryParser
 *
 * Changed to add TYPO3 compatibility
 *
 * @package Aix\Dataviewer\Service\QueryBuilderParser
 * @author José DA COSTA
 * @copyright https://github.com/josedacosta/jQueryQueryBuilderBundle
 */
class ExtbaseQueryParser
{

    use ExtbaseQueryFunctions;

    protected $fields;

    /**
     * Extbase Query Parser Main Parsing Method
     * @param mixed $filters
     * @param Query $query
     * @return Query
     */
    public function jQueryToExtbase($filters, Query &$query, array $fields = null): Query
    {
        $this->fields = $fields;

        if(!$filters instanceof stdClass) {
            $filters = $this->decodeJSON($filters);
        }

        if (!isset($filters->rules) || !is_array($filters->rules)) {
            return $query;
        }
        if (count($filters->rules) < 1) {
            return $query;
        }

        $constraints = $this->createConstraints($filters->rules, $query, $filters->condition);

        if(is_null($constraints)) {
            return $query;
        }

        return $query->matching($constraints);
    }

    /**
     * Called by parse, checks initial constraints and processes nested items
     * @param array $rules
     * @param Query $query
     * @param string $queryCondition
     * @return ConstraintInterface
     */
    protected function createConstraints(array $rules, Query $query, $queryCondition = 'AND'): ConstraintInterface
    {
        switch ($queryCondition) {
            case 'OR':
                return $query->logicalOr(...$this->loopThroughRules($rules, $query));
            case 'AND':
            default:
                return $query->logicalAnd(...$this->loopThroughRules($rules, $query));
        }
    }

    /**
     * Called by parse, loops through all the rules to find out if nested or not.
     * @param array $rules
     * @param Query $query
     * @return ConstraintInterface|array
     */
    protected function loopThroughRules(array $rules, Query $query)
    {
        $constraints = [];
        foreach ($rules as $rule) {
            if ($this->isNested($rule) && $rule->condition) {
                $constraints[] = $this->createNestedConstraints($query, $rule, $rule->condition);
            } else {
                $constraints[] = $this->makeConstraint($query, $rule);
            }
        }

        return $constraints;
    }

    /**
     * @param Query $query
     * @param stdClass $rule
     * @return ConstraintInterface
     */
    protected function makeConstraint(Query $query, stdClass $rule): ConstraintInterface
    {
        // Check for pipe
        if(!is_array($rule->value)) {
            if(strpos($rule->value,'|') !== false) {
                $values = explode('|', $rule->value);
                if(count($values) == 2) {
                    $rule->value = end($values);
                    $rule->field = $rule->field.'.'.reset($values);
                }
            }
        }

        try {
            $value = $this->getValueForQueryFromRule($rule);
        } catch (\Exception $e) {
            throw $e;
        }

        return $this->convertRuleToConstraint($query, $rule, $value);
    }

    /**
     * Ensure that the value is correct for the rule, try and set it if it's not.
     * @param stdClass $rule
     * @throws \Exception
     * @return mixed
     */
    protected function getValueForQueryFromRule(stdClass $rule)
    {
        // assurez-vous que la plupart des champs communs de QueryBuilder ont été ajoutés
        $value = $this->getRuleValue($rule);

        // le "field" doit exister dans notre liste de "fields" (fournie à l'entrée)
        $this->ensureFieldIsAllowed($this->fields, $rule->field);

        // si l'opérateur SQL est défini pour ne pas avoir une valeur, assurez-vous que nous définissons la valeur à null
        if ($this->operators[$rule->operator]['accept_values'] === false) {
            return $this->operatorValueWhenNotAcceptingOne($rule);
        }

        // Convertissez l'opérateur (LIKE / NOT LIKE / GREATER THAN) qui nous est fourni par QueryBuilder
        // sur un que nous pouvons utiliser à l'intérieur de la requête SQL
        $sqlOperator = $this->operator_sql[$rule->operator];
        $operator = $sqlOperator['operator'];

        // vérifie que la valeur est un tableau uniquement si elle doit être
        return $this->getCorrectValue($operator, $rule, $value);
    }

    /**
     * Check if a given rule is correct.
     * Just before making a query for a rule, we want to make sure that the field, operator and value are set
     * @param stdClass $rule
     * @return bool true if values are correct.
     */
    protected function checkRuleCorrect(stdClass $rule): bool
    {
        // vérifie la présence des valeurs indispensables
        if (!isset($rule->field, $rule->operator, $rule->value)) {
            return false;
        }
        // vérifie l'existance de l'opérateur
        if (!isset($this->operators[$rule->operator])) {
            return false;
        }
        return true;
    }

    /**
     * Give back the correct value when we don't accept one.
     * @param stdClass $rule
     * @return null|string
     */
    protected function operatorValueWhenNotAcceptingOne(stdClass $rule): ?string
    {
        if ($rule->operator == 'is_empty' || $rule->operator == 'is_not_empty') {
            return '';
        }
        return null;
    }

    /**
     * Ensure that the value for a field is correct.
     * Append/Prepend values for SQL statements, etc.
     * @param $operator
     * @param stdClass $rule
     * @param $value
     * @return mixed
     */
    protected function getCorrectValue($operator, stdClass $rule, $value)
    {
        $field = $rule->field;
        $sqlOperator = $this->operator_sql[$rule->operator];
        $requireArray = $this->operatorRequiresArray($operator);
        $value = $this->enforceArrayOrString($requireArray, $value, $field);

        switch ($rule->operator) {
            case 'less':
            case 'less_or_equal':
            case 'greater':
            case 'greater_or_equal':
                $value = (int)$value;
                break;
            default:
                break;
        }

        return $this->appendOperatorIfRequired($requireArray, $value, $sqlOperator);
    }

    /**
     * Déterminer si une règle particulière est en réalité un groupe d'autres règles.
     * @param $rule
     * @return bool
     */
    protected function isNested($rule): bool
    {
        if (isset($rule->rules) && is_array($rule->rules) && count($rule->rules) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Convert an incomming rule from jQuery QueryBuilder to the Doctrine Querybuilder
     * (This used to be part of makeQuery, where the name made sense, but I pulled it
     * out to reduce some duplicated code inside JoinSupportingQueryBuilder)
     * @param Query $query
     * @param stdClass $rule
     * @param mixed $value the value that needs to be queried in the database.
     * @param string $queryCondition and/or...
     * @return ConstraintInterface
     */
    protected function convertRuleToConstraint(Query $query, stdClass $rule, $value): ConstraintInterface
    {
        $sqlOperator = $this->operator_sql[$rule->operator];
        $operator = $sqlOperator['operator'];

        if ($this->operatorRequiresArray($operator)) {
            if(!is_array($value)) {
                $value = GeneralUtility::trimExplode(',', $value);
            }
            return $this->makeQueryWhenArray($query, $rule, $sqlOperator, $value);
        } else if ($this->operatorIsNull($operator)) {
            return $this->makeQueryWhenNull($query, $rule, $sqlOperator);
        }

        $field = str_replace('FIELD:','', $rule->field);

        switch ($rule->operator) {
            case 'not_equal':
            case 'is_not_empty':
                return $query->logicalNot($query->equals($field, $value));
            case 'begins_with':
            case 'contains':
            case 'ends_with':
                return $query->like($field, $value);
            case 'not_begins_with':
            case 'not_contains':
            case 'not_ends_with':
                return $query->logicalNot($query->like($field, $value));
            case 'less':
                return $query->lessThan($field, $value);
            case 'less_or_equal':
                return $query->lessThanOrEqual($field, $value);
            case 'greater':
                return $query->greaterThan($field, $value);
            case 'greater_or_equal':
                return $query->greaterThanOrEqual($field, $value);
            case 'equal':
            default:
                return $query->equals($field, $value);
        }
    }

    /**
     * Create nested queries
     *
     * When a rule is actually a group of rules, we want to build a nested query with the specified condition (AND/OR)
     *
     * @param Query $query
     * @param stdClass $rule
     * @param string|null $condition
     * @return ConstraintInterface
     */
    protected function createNestedConstraints(Query $query, stdClass $rule, $condition = null): ConstraintInterface
    {
        if ($condition === null) {
            $condition = $rule->condition;
        }

        $condition = $this->validateCondition($condition);
        $constraints = $this->loopThroughRules($rule->rules, $query);

        switch ($condition) {
            case 'or':
                return $query->logicalOr(...$constraints);
            case 'and':
            default:
                return $query->logicalAnd(...$constraints);
        }
    }
}
