<?php
namespace Onedrop\CampaignMonitor\Domain\Dto;

use Neos\Flow\Persistence\QueryInterface;
use Neos\Flow\Persistence\QueryResultInterface;

/**
 * A generic Query that can be used to produce proper QueryResults from computed results (via callbacks)
 * This is useful to provide simple pagination for example
 */
class CallbackQuery implements QueryInterface
{

    /**
     * @var \Closure
     */
    protected $resultCallback;

    /**
     * @var \Closure
     */
    protected $countCallback;

    /**
     * @var array in the format array('foo' => QueryInterface::ORDER_ASCENDING, 'bar' => QueryInterface::ORDER_DESCENDING)
     */
    protected $orderings;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var int
     */
    protected $offset;

    /**
     * @param \Closure $resultCallback
     * @param \Closure $countCallback
     */
    public function __construct(\Closure $resultCallback, \Closure $countCallback = null)
    {
        $this->resultCallback = $resultCallback;
        $this->countCallback = $countCallback;
    }

    /**
     * Returns the type this query cares for.
     *
     * @return string
     */
    public function getType()
    {
        return null;
    }

    /**
     * Executes the query and returns the result.
     *
     * @param  bool                 $cacheResult If the result cache should be used
     * @return QueryResultInterface The query result
     */
    public function execute($cacheResult = false)
    {
        return new CallbackQueryResult($this);
    }

    /**
     * @return array
     */
    public function getResult()
    {
        return call_user_func_array($this->resultCallback, [$this]);
    }

    /**
     * Returns the query result count.
     *
     * @return int The query result count
     */
    public function count()
    {
        if ($this->countCallback !== null) {
            return call_user_func_array($this->countCallback, [$this]);
        }
        return count($this->getResult());
    }

    /**
     * Sets the property names to order the result by. Expected like this:
     * array(
     *  'foo' => QueryInterface::ORDER_ASCENDING,
     *  'bar' => QueryInterface::ORDER_DESCENDING
     * )
     *
     * @param  array          $orderings The property names to order by
     * @return QueryInterface
     */
    public function setOrderings(array $orderings)
    {
        $this->orderings = $orderings;
    }

    /**
     * Gets the property names to order the result by, like this:
     * array(
     *  'foo' => QueryInterface::ORDER_ASCENDING,
     *  'bar' => QueryInterface::ORDER_DESCENDING
     * )
     *
     * @return array
     */
    public function getOrderings()
    {
        return $this->orderings;
    }

    /**
     * Sets the maximum size of the result set to limit. Returns $this to allow
     * for chaining (fluid interface).
     *
     * @param  int            $limit
     * @return QueryInterface
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * Returns the maximum size of the result set to limit.
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Sets the start offset of the result set to offset. Returns $this to
     * allow for chaining (fluid interface).
     *
     * @param  int            $offset
     * @return QueryInterface
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * Returns the start offset of the result set.
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param  object         $constraint Some constraint, depending on the backend
     * @return QueryInterface
     */
    public function matching($constraint)
    {
        throw new \BadMethodCallException('This method is not implemented in this query implementation.');
    }

    /**
     * @return mixed the constraint, or null if none
     */
    public function getConstraint()
    {
        throw new \BadMethodCallException('This method is not implemented in this query implementation.');
    }

    /**
     * @param  mixed  $constraint1 The first of multiple constraints or an array of constraints.
     * @return object
     */
    public function logicalAnd($constraint1)
    {
        throw new \BadMethodCallException('This method is not implemented in this query implementation.');
    }

    /**
     * @param  mixed  $constraint1 The first of multiple constraints or an array of constraints.
     * @return object
     */
    public function logicalOr($constraint1)
    {
        throw new \BadMethodCallException('This method is not implemented in this query implementation.');
    }

    /**
     * @param  object $constraint Constraint to negate
     * @return object
     */
    public function logicalNot($constraint)
    {
        throw new \BadMethodCallException('This method is not implemented in this query implementation.');
    }

    /**
     * @param  string $propertyName  The name of the property to compare against
     * @param  mixed  $operand       The value to compare with
     * @param  bool   $caseSensitive Whether the equality test should be done case-sensitive for strings
     * @return object
     */
    public function equals($propertyName, $operand, $caseSensitive = true)
    {
        throw new \BadMethodCallException('This method is not implemented in this query implementation.');
    }

    /**
     * @param  string                                                 $propertyName  The name of the property to compare against
     * @param  string                                                 $operand       The value to compare with
     * @param  bool                                                   $caseSensitive Whether the matching should be done case-sensitive
     * @throws \Neos\Flow\Persistence\Exception\InvalidQueryException if used on a non-string property
     * @return object
     */
    public function like($propertyName, $operand, $caseSensitive = true)
    {
        throw new \BadMethodCallException('This method is not implemented in this query implementation.');
    }

    /**
     * @param  string                                                 $propertyName The name of the multivalued property to compare against
     * @param  mixed                                                  $operand      The value to compare with
     * @throws \Neos\Flow\Persistence\Exception\InvalidQueryException if used on a single-valued property
     * @return object
     */
    public function contains($propertyName, $operand)
    {
        throw new \BadMethodCallException('This method is not implemented in this query implementation.');
    }

    /**
     * @param  string                                                 $propertyName The name of the multivalued property to compare against
     * @throws \Neos\Flow\Persistence\Exception\InvalidQueryException if used on a single-valued property
     * @return bool
     */
    public function isEmpty($propertyName)
    {
        throw new \BadMethodCallException('This method is not implemented in this query implementation.');
    }

    /**
     * @param  string                                                 $propertyName The name of the property to compare against
     * @param  mixed                                                  $operand      The value to compare with, multivalued
     * @throws \Neos\Flow\Persistence\Exception\InvalidQueryException if used on a multi-valued property
     * @return object
     */
    public function in($propertyName, $operand)
    {
        throw new \BadMethodCallException('This method is not implemented in this query implementation.');
    }

    /**
     * @param  string                                                 $propertyName The name of the property to compare against
     * @param  mixed                                                  $operand      The value to compare with
     * @throws \Neos\Flow\Persistence\Exception\InvalidQueryException if used on a multi-valued property or with a non-literal/non-DateTime operand
     * @return object
     */
    public function lessThan($propertyName, $operand)
    {
        throw new \BadMethodCallException('This method is not implemented in this query implementation.');
    }

    /**
     * @param  string                                                 $propertyName The name of the property to compare against
     * @param  mixed                                                  $operand      The value to compare with
     * @throws \Neos\Flow\Persistence\Exception\InvalidQueryException if used on a multi-valued property or with a non-literal/non-DateTime operand
     * @return object
     */
    public function lessThanOrEqual($propertyName, $operand)
    {
        throw new \BadMethodCallException('This method is not implemented in this query implementation.');
    }

    /**
     * @param  string                                                 $propertyName The name of the property to compare against
     * @param  mixed                                                  $operand      The value to compare with
     * @throws \Neos\Flow\Persistence\Exception\InvalidQueryException if used on a multi-valued property or with a non-literal/non-DateTime operand
     * @return object
     */
    public function greaterThan($propertyName, $operand)
    {
        throw new \BadMethodCallException('This method is not implemented in this query implementation.');
    }

    /**
     * @param  string                                                 $propertyName The name of the property to compare against
     * @param  mixed                                                  $operand      The value to compare with
     * @throws \Neos\Flow\Persistence\Exception\InvalidQueryException if used on a multi-valued property or with a non-literal/non-DateTime operand
     * @return object
     */
    public function greaterThanOrEqual($propertyName, $operand)
    {
        throw new \BadMethodCallException('This method is not implemented in this query implementation.');
    }

    /**
     * @param  bool           $distinct
     * @return QueryInterface
     */
    public function setDistinct($distinct = true)
    {
        throw new \BadMethodCallException('This method is not implemented in this query implementation.');
    }

    /**
     * @return bool
     */
    public function isDistinct()
    {
        throw new \BadMethodCallException('This method is not implemented in this query implementation.');
    }
}
