<?php
/**
 * This is an implemention of mathematical sets in PHP based on Python's
 * implementation of sets. All methods that accept another Set or
 * Set will also work with arrays or any object that implements the
 * Iterator interface.
 *
 * @author Eric Pruitt <eric.pruitt@gmail.com>
 * @package set
 */

class Set implements Countable, IteratorAggregate
{
    /**
     * Associative array with the set members stored as keys.
     * @access protected
     * @var array
     */
    protected $map = array();

    public function __construct($members = null)
    {
        if ($members) {
            foreach ($members as $member) {
                $this->map[$member] = true;
            }
        }
    }

    public function __isset($key)
    {
        return isset($this->map[$key]);
    }

    public function __toString()
    {
        $converted = '';
        $keys = array_keys($this->map);
        foreach ($keys as $member) {
            if (strlen($converted)) {
                $converted .= ', ';
            }
            if (is_numeric($member)) {
                $converted .= $member;
            } else {
                $converted .= '"' . addslashes($member) . '"';
            }
        }
        return '{' . $converted . '}';
    }

    public function count()
    {
        return count($this->map);
    }

    public function getIterator()
    {
        return new ArrayIterator(array_keys($this->map));
    }

    /**
     * Return an array containing the members of the set.
     * @param bool $sort Indicates if array should be sorted. Defaults to true.
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
     * Add a member to the set.
     * @param mixed $member
     */
    public function add($member)
    {
        $this->map[$member] = true;
    }

    /**
     * Remove a member from the set.
     * @param mixed $member
     */
    public function remove($member)
    {
        unset($this->map[$member]);
    }

    /**
     * Add members from another set or sets to this set.
     * @param mixed $other,...
     */
    public function update($other)
    {
        foreach (func_get_args() as $other) {
            foreach ($other as $member) {
                $this->map[$member] = true;
            }
        }
    }

    /*
     * Return union of instance set and one or more other sets.
     * @param mixed $other,...
     * @return Set
     */
    public function union($other)
    {
        $_ = clone $this;
        call_user_func_array(array($_, 'update'), func_get_args());
        return $_;
    }

    /**
     * Remove elements in another set or sets from this set.
     * @param mixed $other,...
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
     * @param mixed $other,...
     * @return Set
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
     * @param mixed $other
     * @return Set
     */
    public function symmetricDifference($other)
    {
        $other = is_a($other, 'Set') ? $other : new self($other);
        return $other->difference($this)->union($this->difference($other));
    }

    /**
     * Remove all elements in common with another set from this set.
     * @param mixed $other
     */
    public function symmetricDifferenceUpdate($other)
    {
        $this->map = $this->symmetricDifference($other)->map;
    }

    /**
     * Remove all elements not shared with other sets from this set.
     * @param mixed $other,...
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
     * @param mixed $other,...
     * @return Set
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
     * @param mixed $other
     * @return boolean
     */
    public function isSuperset($other)
    {
        if (count($this) < count($other)) {
            return false;
        }

        $other = is_a($other, 'Set') ? $other : new self($other);
        return !count($other->difference($this));
    }

    /**
     * Return value indicating whether this all of this sets members are also
     * members of another set.
     * @param mixed $other
     * @return boolean
     */
    public function isSubset($other)
    {
        if (count($other) < count($this)) {
            return false;
        }

        return !count($this->difference($other));
    }

    /**
     * Return value indicating if that set has no values in common with another
     * set.
     * @param mixed $other
     * @return boolean
     */
    public function isDisjoint($other)
    {
        return (bool) count($this->intersection($other));
    }

    /**
     * Remove and return an arbitrary item from this set.
     * @return mixed Returns the popped value or null of the set is empty.
     */
    public function pop()
    {
        foreach ($this->map as $key => $value) {
            unset($this->map[$key]);
            return $key;
        }
        return null;
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
     * @param mixed $value
     * @return boolean
     */
    public function contains($value)
    {
        return isset($this->map, $value);
    }
}
