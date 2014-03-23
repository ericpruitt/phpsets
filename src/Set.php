<?php
/**
 * Python's "set" builtin ported to PHP
 *
 * @author  Eric Pruitt <eric.pruitt@gmail.com>
 * @license BSD 2-Clause
 * @link    http://codevat.com/
 */
namespace Codevat;

/**
 * This is an implemention of mathematical sets in based on Python's builtin
 * `set` object. All methods that accept another Set or Sets will also work
 * with arrays or any other object that implements the Iterator interface.
 */
class Set implements \Countable, \IteratorAggregate
{
    /**
     * Associative array with the set members stored as keys.
     *
     * @access protected
     * @var array
     */
    protected $map = array();

    /**
     * Instantiate new `Set`.
     *
     * @param $members Elements the new set should contain. When not specified,
     * the newly created set will be empty.
     */
    public function __construct($members = null)
    {
        if ($members) {
            foreach ($members as $member) {
                $this->map[$member] = true;
            }
        }
    }

    /**
     * Return string representation of the set.
     *
     * @return string Representation of the set.
     */
    public function __toString()
    {
        $converted = '';
        $keys = array_keys($this->map);
        foreach ($keys as $member) {
            if (strlen($converted)) {
                $converted .= ', ';
            }
            if (is_numeric($member) or is_bool($member)) {
                $converted .= $member;
            } else {
                $converted .= '"' . addslashes($member) . '"';
            }
        }
        return '{' . $converted . '}';
    }

    /**
     * Return the number of elements in the set.
     *
     * @param integer Number of elements in the set.
     */
    public function count()
    {
        return count($this->map);
    }

    /**
     * Return an iterator for the set.
     *
     * @return \ArrayIterator An iterator that iterates over all the elements
     * in the set.
     */
    public function getIterator()
    {
        return new \ArrayIterator(array_keys($this->map));
    }

    /**
     * Return an array containing the members of the set.
     *
     * @param bool $sort Indicates if array should be sorted. Defaults to true.
     *
     * @return array Array containing the members of the set.
     */
    public function toArray($sort = true)
    {
        $members = array_keys($this->map);
        if ($sort) {
            asort($members);
        }
        return $members;
    }

    /**
     * Return value indicating if two sets contain the same members.
     *
     * @param mixed $other Set to compare to.
     *
     * @return bool Value indicating the equality of the sets.
     */
    public function equals($other)
    {
        $other = is_a($other, 'Set') ? $other : new self($other);
        return $this->map == $other->map;
    }

    /**
     * Add a member to the set.
     *
     * @param mixed $member New set member.
     */
    public function add($member)
    {
        $this->map[$member] = true;
    }

    /**
     * Remove a member from the set.
     *
     * @param mixed $member Set member to be removed.
     */
    public function remove($member)
    {
        unset($this->map[$member]);
    }

    /**
     * Add members from another set or sets to this set.
     *
     * @param mixed $other ,... One or more iterables whose elements should be
     * added to the set.
     */
    public function update($other)
    {
        foreach (func_get_args() as $other) {
            foreach ($other as $member) {
                $this->map[$member] = true;
            }
        }
    }

    /**
     * Return union of instance set and one or more other sets.
     *
     * @param mixed $other ,... One or more iterables whose elements should be
     * included in the returned set.
     *
     * @return Set Result set operaton.
     */
    public function union($other)
    {
        $_ = clone $this;
        call_user_func_array(array($_, 'update'), func_get_args());
        return $_;
    }

    /**
     * Remove elements in another set or sets from this set.
     *
     * @param mixed $other ,... One or more iterables whose elements should be
     * removed from the set.
     */
    public function differenceUpdate($other)
    {
        foreach (func_get_args() as $other) {
            if (is_a($other, 'Set')) {
                $this->map = array_diff_assoc($this->map, $other->map);
            } else {
                foreach ($other as $member) {
                    unset($this->map[$member]);
                }
            }
        }
    }

    /**
     * Return set with members from another set or sets removed.
     *
     * @param mixed $other ,... One or more iterables whose elements should not
     * be present in the returned set.
     *
     * @return Set Result of set operation.
     */
    public function difference($other)
    {
        $_ = clone $this;
        call_user_func_array(array($_, 'differenceUpdate'), func_get_args());
        return $_;
    }

    /**
     * Return symmetric differnce of this set with another, that is, all
     * elements that are present in either one set or the other, but not both.
     *
     * @param mixed $other Another set or iterable.
     *
     * @return Set Result of set operation.
     */
    public function symmetricDifference($other)
    {
        $other = is_a($other, 'Set') ? $other : new self($other);
        return $other->difference($this)->union($this->difference($other));
    }

    /**
     * Remove all elements present in both sets from this set.
     *
     * @param mixed $other Another set or iterable.
     */
    public function symmetricDifferenceUpdate($other)
    {
        $this->map = $this->symmetricDifference($other)->map;
    }

    /**
     * Remove all elements not shared with other sets from this set.
     *
     * @param mixed $other ,... One or more sets or iterables.
     */
    public function intersectionUpdate($other)
    {
        foreach (func_get_args() as $other) {
            if (is_a($other, 'Set')) {
                $this->map = array_intersect_assoc($this->map, $other->map);
            } else {
                $newmap = array();
                foreach ($other as $member) {
                    if (isset($this->map[$member])) {
                        $newmap[$member] = true;
                    }
                }
                $this->map = $newmap;
            }
        }
    }

    /**
     * Return all elements in common with another set or sets.
     *
     * @param mixed $other ,... One or more iterables to be intersected.
     *
     * @return Set Result of set operation.
     */
    public function intersection($other)
    {
        $_ = clone $this;
        call_user_func_array(array($_, 'intersectionUpdate'), func_get_args());
        return $_;
    }

    /**
     * Return value indicating whether this set contains all the elements of
     * another set.
     *
     * @param mixed $other Set or iterable tested for inclusion.
     *
     * @return bool Value indicating whether or not this set is a superset
     * of the given collection.
     */
    public function isSuperset($other)
    {
        $other = is_a($other, 'Set') ? $other : new self($other);
        if (count($other) > count($this)) {
            return false;
        }

        return !count($other->difference($this));
    }

    /**
     * Return value indicating whether all of this set's members are also
     * members of another set.
     *
     * @param mixed $other Set or iterable tested for inclusion.
     *
     * @return bool Value indicating whether or not this set is a subset of
     * the given collection.
     */
    public function isSubset($other)
    {
        if ((is_array($other) or is_a($other, 'Set')) and
          count($other) < count($this)) {
            return false;
        }

        return !count($this->difference($other));
    }

    /**
     * Return value indicating if that set has no values in common with another
     * set.
     *
     * @param mixed $other
     *
     * @return bool Value indicating if this set has no values in common with
     * the other set.
     */
    public function isDisjoint($other)
    {
        return !count($this->intersection($other));
    }

    /**
     * Remove and return an arbitrary item from this set.
     *
     * @throws \OutOfBoundsException if the set is empty.
     *
     * @return mixed Returns the popped value.
     */
    public function pop()
    {
        foreach ($this->map as $key => $value) {
            unset($this->map[$key]);
            return $key;
        }

        throw new \OutOfBoundsException("Cannot pop from an empty set");
    }

    /**
     * Remove all elements from this set.
     */
    public function clear()
    {
        $this->map = array();
    }

    /**
     * Return value indicating whether the parameter is a member of this set.
     *
     * @param mixed $value Value to test for membership.
     *
     * @return bool Value indicating whether or not the given value is a member
     * of this set.
     */
    public function contains($value)
    {
        return isset($this->map[$value]);
    }
}
