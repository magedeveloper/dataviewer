<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Service\QueryBuilderParser;

use Exception;
use stdClass;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Exception\UnexpectedTypeException;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ComparisonInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Query;

/**
 * Trait ExtbaseQueryFunctions
 *
 * Changed to add TYPO3 compatibility
 *
 * @package Aix\Dataviewer\Service\ExtbaseQueryFunctions
 * @author José DA COSTA
 * @copyright https://github.com/josedacosta/jQueryQueryBuilderBundle
 */

trait ExtbaseQueryFunctions
{
    /**
     * @param stdClass $rule
     */
    abstract protected function checkRuleCorrect(stdClass $rule);

    protected $operators = [
        'equal' => ['accept_values' => true, 'apply_to' => ['string', 'number', 'datetime']],
        'not_equal' => ['accept_values' => true, 'apply_to' => ['string', 'number', 'datetime']],
        'in' => ['accept_values' => true, 'apply_to' => ['string', 'number', 'datetime']],
        'not_in' => ['accept_values' => true, 'apply_to' => ['string', 'number', 'datetime']],
        'less' => ['accept_values' => true, 'apply_to' => ['number', 'datetime']],
        'less_or_equal' => ['accept_values' => true, 'apply_to' => ['number', 'datetime']],
        'greater' => ['accept_values' => true, 'apply_to' => ['number', 'datetime']],
        'greater_or_equal' => ['accept_values' => true, 'apply_to' => ['number', 'datetime']],
        'between' => ['accept_values' => true, 'apply_to' => ['number', 'datetime']],
        'begins_with' => ['accept_values' => true, 'apply_to' => ['string']],
        'not_begins_with' => ['accept_values' => true, 'apply_to' => ['string']],
        'contains' => ['accept_values' => true, 'apply_to' => ['string']],
        'not_contains' => ['accept_values' => true, 'apply_to' => ['string']],
        'ends_with' => ['accept_values' => true, 'apply_to' => ['string']],
        'not_ends_with' => ['accept_values' => true, 'apply_to' => ['string']],
        'is_empty' => ['accept_values' => false, 'apply_to' => ['string']],
        'is_not_empty' => ['accept_values' => false, 'apply_to' => ['string']],
        'is_null' => ['accept_values' => false, 'apply_to' => ['string', 'number', 'datetime']],
        'is_not_null' => ['accept_values' => false, 'apply_to' => ['string', 'number', 'datetime']],
        'in_list_equal' => ['accept_values' => true, 'apply_to' => ['string']],
        'in_list_contains' => ['accept_values' => true, 'apply_to' => ['string']],
        'in_list_like' => ['accept_values' => true, 'apply_to' => ['string']],
    ];

    protected $operator_sql = [
        'equal' => ['operator' => '='],
        'not_equal' => ['operator' => '!='],
        'in' => ['operator' => 'IN'],
        'not_in' => ['operator' => 'NOT IN'],
        'less' => ['operator' => '<'],
        'less_or_equal' => ['operator' => '<='],
        'greater' => ['operator' => '>'],
        'greater_or_equal' => ['operator' => '>='],
        'between' => ['operator' => 'BETWEEN'],
        'begins_with' => ['operator' => 'LIKE', 'prepend' => '%'],
        'not_begins_with' => ['operator' => 'NOT LIKE', 'prepend' => '%'],
        'contains' => ['operator' => 'LIKE', 'append' => '%', 'prepend' => '%'],
        'not_contains' => ['operator' => 'NOT LIKE', 'append' => '%', 'prepend' => '%'],
        'ends_with' => ['operator' => 'LIKE', 'append' => '%'],
        'not_ends_with' => ['operator' => 'NOT LIKE', 'append' => '%'],
        'is_empty' => ['operator' => '='],
        'is_not_empty' => ['operator' => '!='],
        'is_null' => ['operator' => 'NULL'],
        'is_not_null' => ['operator' => 'NOT NULL'],
        'in_list_equal' => ['operator' => 'IN_LIST'],
        'in_list_contains' => ['operator' => 'IN_LIST'],
        'in_list_like' => ['operator' => 'IN_LIST'],
    ];

    protected $needs_array = [
        'IN', 'NOT IN', 'BETWEEN', 'IN_LIST',
    ];

    /**
     * Determine if an operator (LIKE/IN) requires an array.
     *
     * @param string $operator
     * @return bool
     */
    protected function operatorRequiresArray(string $operator): bool
    {
        return in_array($operator, $this->needs_array);
    }

    /**
     * Determine if an operator is NULL/NOT NULL
     *
     * @param string $operator
     * @return bool
     */
    protected function operatorIsNull(string $operator): bool
    {
        return $operator == 'NULL' || $operator == 'NOT NULL';
    }

    /**
     * Make sure that a condition is either 'or' or 'and'.
     *
     * @param string $condition
     * @return string
     * @throws Exception
     */
    protected function validateCondition(string $condition): string
    {
        $condition = trim(strtolower($condition));

        if ($condition !== 'and' && $condition !== 'or') {
            throw new Exception("Condition can only be one of: 'and', 'or'.");
        }

        return $condition;
    }

    /**
     * Enforce whether the value for a given field is the correct type
     *
     * @param bool $requireArray value must be an array
     * @param mixed $value the value we are checking against
     * @param string $field the field that we are enforcing
     * @return mixed value after enforcement
     * @throws Exception if value is not a correct type
     */
    protected function enforceArrayOrString(bool $requireArray, $value, string $field)
    {
        $this->checkFieldIsAnArray($requireArray, $value, $field);

        if (!$requireArray && is_array($value)) {
            return $this->convertArrayToFlatValue($field, $value);
        }

        return $value;
    }

    /**
     * Ensure that a given field is an array if required.
     *
     * @param bool $requireArray
     * @param mixed $value
     * @param string $field
     * @return null|array
     * @see enforceArrayOrString
     */
    protected function checkFieldIsAnArray(bool $requireArray, $value, string $field): ?array
    {
        if ($requireArray && !is_array($value)) {
            return GeneralUtility::trimExplode(',', $value);
        }

        return null;
    }

    /**
     * Convert an array with just one item to a string.
     * In some instances, and array may be given when we want a string.
     *
     * @see enforceArrayOrString
     * @param string $field
     * @param array $value
     * @return mixed
     * @throws Exception
     */
    protected function convertArrayToFlatValue(string $field, array $value)
    {
        if (count($value) !== 1) {
            throw new Exception("Field ($field) should not be an array, but it is.");
        }

        return $value[0];
    }

    /**
     * Append or prepend a string to the query if required.
     *
     * @param bool $requireArray value must be an array
     * @param mixed $value the value we are checking against
     * @param mixed $sqlOperator
     * @return mixed $value
     */
    protected function appendOperatorIfRequired(bool $requireArray, $value, $sqlOperator)
    {
        if (!$requireArray) {
            if (isset($sqlOperator['append'])) {
                $value = $sqlOperator['append'] . $value;
            }

            if (isset($sqlOperator['prepend'])) {
                $value = $value . $sqlOperator['prepend'];
            }
        }

        return $value;
    }

    /**
     * Decode the given JSON
     *
     * @param string incoming json
     * @throws Exception
     * @return array
     */
    private function decodeJSON($json)
    {
        $query = json_decode($json);
        if (json_last_error()) {
            throw new Exception('JSON parsing threw an error: ' . json_last_error_msg());
        }
        if (!is_object($query) && (!is_array($query))) {
            throw new Exception('The query is not valid JSON');
        }

        return $query;
    }

    /**
     * get a value for a given rule.
     * throws an exception if the rule is not correct.
     *
     * @param stdClass $rule
     * @return string|array
     */
    private function getRuleValue(stdClass $rule)
    {
        if (!$this->checkRuleCorrect($rule)) {
            return '';
        }
        return $rule->value;
    }

    /**
     * Check that a given field is in the allowed list if set.
     *
     * @param $fields
     * @param $field
     * @throws Exception
     */
    private function ensureFieldIsAllowed($fields, $field)
    {
        if (is_array($fields) && !in_array($field, $fields)) {
            throw new Exception("Field ({$field}) does not exist in fields list");
        }
    }

    /**
     * makeQuery, for arrays.
     * Some types of SQL Operators (ie, those that deal with lists/arrays) have specific requirements.
     * This function enforces those requirements.
     *
     * @param Query $query
     * @param stdClass $rule
     * @param array $sqlOperator
     * @param array $value
     * @return ConstraintInterface
     * @throws Exception
     */
    protected function makeQueryWhenArray(Query $query, stdClass $rule, array $sqlOperator, array $value): ConstraintInterface
    {
        if ($sqlOperator['operator'] == 'IN' || $sqlOperator['operator'] == 'NOT IN') {
            return $this->makeArrayQueryIn($query, $rule, $sqlOperator['operator'], $value);
        } elseif ($sqlOperator['operator'] == 'BETWEEN') {
            return $this->makeArrayQueryBetween($query, $rule, $value);
        } elseif ($sqlOperator['operator'] == 'IN_LIST') {
            return $this->makeArrayQueryInList($query, $rule, $value);
        }
        throw new Exception('makeQueryWhenArray could not return a value');
    }

    /**
     * Create a 'null' query when required.
     *
     * @param Query $query
     * @param stdClass $rule
     * @param array $sqlOperator
     * @return ComparisonInterface
     * @throws Exception
     */
    protected function makeQueryWhenNull(Query $query, stdClass $rule, array $sqlOperator): ConstraintInterface
    {
        if ($sqlOperator['operator'] == 'NULL') {
            return $query->equals($rule->field, null);
        } elseif ($sqlOperator['operator'] == 'NOT NULL') {
            return $query->logicalNot($query->equals($rule->field, null));
        }
        throw new Exception('makeQueryWhenNull was called on an SQL operator that is not null');
    }

    /**
     * makeArrayQueryIn, when the query is an IN or NOT IN...
     *
     * @param Query $query
     * @param stdClass $rule
     * @param string $operator
     * @param array $value
     * @return ConstraintInterface
     * @throws UnexpectedTypeException
     * @see makeQueryWhenArray
     */
    private function makeArrayQueryIn(Query $query, stdClass $rule, string $operator, array $value): ConstraintInterface
    {
        $field = str_replace('FIELD:','', $rule->field);
        if ($operator == 'NOT IN') {
            return $query->logicalNot($query->in($field, $value));
        }
        return $query->in($field, $value);
    }


    /**
     * makeArrayQueryBetween, when the query is an IN or NOT IN...
     *
     * @see makeQueryWhenArray
     * @param Query $query
     * @param stdClass $rule
     * @param array $value
     * @throws Exception when more then two items given for the between
     * @return ConstraintInterface
     */
    private function makeArrayQueryBetween(Query $query, stdClass $rule, array $value): ConstraintInterface
    {
        $field = str_replace('FIELD:','', $rule->field);
        if (count($value) !== 2) {
            throw new Exception("{$field} should be an array with only two items.");
        }
        return $query->between($field, reset($value), end($value));
    }

    /**
     * makeArrayQueryInList, when the query is an CONTAINS
     * @see makeQueryWhenArray
     * @param Query $query
     * @param stdClass $rule
     * @param array $value
     * @throws \Exception when more then two items given for the between
     * @return Query
     */
    private function makeArrayQueryInList(Query $query, stdClass $rule, array $value)
    {
        $field = str_replace('FIELD:','', $rule->field);
        if (count($value) < 1) {
            throw new \Exception("{$field} should be an array with at least one item.");
        }

        $constraints = [];
        foreach ($value as $_v) {
            switch($rule->operator) {
                case 'in_list_equal':
                    $constraints[] = $query->equals($field, $_v);
                    break;
                case 'in_list_like':
                    $constraints[] = $query->like($field, $_v);
                    break;
                case 'in_list_contains':
                default:
                    $constraints[] = $query->contains($field, $_v);
                    break;
            }
        }

        return $query->logicalOr(...$constraints);
    }
}
