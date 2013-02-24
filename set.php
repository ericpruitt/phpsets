<?php
/**
 * This is an implemention of mathematical sets in PHP based on Python's
 * implementation of sets. All methods that accept another Set or
 * PrimitivesSet will also work with arrays or any object that implements the
 * Iterator interface.
 *
 * @author Eric Pruitt <eric.pruitt@gmail.com>
 * @package set
 */

class Set implements Countable, IteratorAggregate
{
    /**
     * Array containing the members of the set.
     * @access public
     * @var array
     */
    public $members = array();

    public function __construct($members = null)
    {
        if (is_a($members, 'Set')) {
            $this->members = $members->members;
        } else if (is_array($members)) {
            $this->members = array_unique($members);
        } else if ($members) {
            foreach ($members as $member) {
                $this->add($member);
            }
        }
    }

    public function __isset($key)
    {
        return $this->contains($key);
    }

    public function __toString()
    {
        $converted = '';
        asort($this->members);
        foreach ($this->members as $member) {
            if ($converted) {
                $converted .= ', ';
            }
            if(is_int($member) or is_float($member)) {
                $converted .= $member;
            } else {
                $converted .= '"' . addslashes($member) . '"';
            }
        }
        return '{' . $converted . '}';
    }

    public function count()
    {
        return count($this->members);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->members);
    }

    /**
     * Add a member to the set.
     * @param mixed $member
     */
    public function add($member)
    {
        if (!in_array($member, $this->members)) {
            $this->members[] = $member;
        }
    }

    /**
     * Remove a member from the set.
     * @param mixed $member
     */
    public function remove($member)
    {
        if (($index = array_search($member, $this->members)) !== false) {
            unset($this->members[$index]);
        }
    }

    /**
     * Add members from another set or sets to this set.
     * @param mixed $other,...
     */
    public function update($other)
    {
        if (func_num_args() > 1) {
            foreach (func_get_args() as $other) {
                $this->update($other);
            }
        } else {
            $other = is_a($other, 'Set') ? $other->members : $other;
            if (is_array($other)) {
                $this->members = array_merge($this->members, $other);
            } else {
                foreach ($other as $member) {
                    $this->members[] = $member;
                }
            }

            $this->members = array_unique($this->members);
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
        if (func_num_args() > 1) {
            foreach (func_get_args() as $other) {
                $this->differenceUpdate($other);
            }
        } else {
            $other = is_a($other, 'Set') ? $other->members : $other;
            if (is_array($other)) {
                $this->members = array_diff($this->members, $other);
            } else {
                foreach ($other as $member) {
                    $this->remove($member);
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
        $this->members = $this->symmetricDifference($other)->members;
    }

    /**
     * Remove all elements not shared with other sets from this set.
     * @param mixed $other,...
     */
    public function intersectionUpdate($other)
    {
        if (func_num_args() > 1) {
            foreach (func_get_args() as $other) {
                $this->intersectionUpdate($other);
            }
        } else {
            if (is_array($other)) {
                $members = $other;
            } else if (is_a($other, 'Set')) {
                $members = $other->members;
            } else {
                $other = new self($other);
                $members = $other->members;
            }

            $this->members = array_intersect($this->members, $members);
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
        return count($other->symmetricDifference($this)) == 0;
    }

    /**
     * Return value indicating whether this all of this sets members are also
     * members of another set.
     * @param mixed $other
     * @return boolean
     */
    public function isSubset($other)
    {
        return !$this->isSuperset($other);
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
        return array_pop($this->members);
    }

    /**
     * Remove all elements from this set.
     */
    public function clear()
    {
        $this->members = array();
    }

    /**
     * Return value indicating whether the parameter is a member of this set.
     * @param mixed $value
     * @return boolean
     */
    public function contains($value)
    {
        return in_array($value, $this->members);
    }
}

class PrimitivesSet extends Set
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

    public function __get($name)
    {
        if ($name != 'members') {
            $trace = debug_backtrace();
            trigger_error("Undefined property via __get(): $name" .
                " in $trace[0][file] on line $trace[0][line]", E_USER_NOTICE);
            return null;
        }

        return array_keys($this->map);
    }

    //! @copydoc Set::add()
    public function add($member)
    {
        $this->map[$member] = true;
    }

    //! @copydoc Set::remove()
    public function remove($member)
    {
        unset($this->map[$member]);
    }

    //! @copydoc Set::update()
    public function update($other)
    {
        foreach (func_get_args() as $other) {
            foreach ($other as $member) {
                $this->map[$member] = true;
            }
        }
    }

    //! @copydoc Set::differenceUpdate()
    public function differenceUpdate($other)
    {
        foreach (func_get_args() as $other) {
            if (is_a($other, 'PrimitivesSet')) {
                $this->map = array_diff_assoc($this->map, $other->map);
            } else {
                foreach ($other as $member) {
                    unset($this->map[$member]);
                }
            }
        }
    }

    //! @copydoc Set::symmetricDifferenceUpdate()
    public function symmetricDifferenceUpdate($other)
    {
        $this->map = $this->symmetricDifference($other)->map;
    }

    //! @copydoc Set::intersectionUpdate()
    public function intersectionUpdate($other)
    {
        foreach (func_get_args() as $other) {
            if (is_a($other, 'PrimitivesSet')) {
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

    //! @copydoc Set::pop()
    public function pop()
    {
        foreach ($this->map as $key => $value) {
            unset($this->map[$key]);
            return $key;
        }
        return null;
    }

    //! @copydoc Set::clear()
    public function clear()
    {
        $this->map = array();
    }

    //! @copydoc Set::contains()
    public function contains($value)
    {
        return isset($this->map, $value);
    }
}
