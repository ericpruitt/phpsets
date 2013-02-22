<?php
/**
 * This is an implemention of mathematical sets in PHP based on Python's
 * implementation of sets. All methods that accept another Set will also work
 * with arrays or any object that implements the Iterator interface.
 *
 * @author Eric Pruitt <eric.pruitt@gmail.com>
 * @version 2013-02-20
 * @package set
 */

class Set implements Countable, IteratorAggregate
{
    /**
     * Array containing the members of the set.
     * @access public
     * @var array
     */
    public $members = Array();

    public function __construct($members = null)
    {
        if (is_a($members, 'Set')) {
            $this->members = $members->members;
        } else if (is_array($members)) {
            $this->members = array_unique($members);
        } else if ($members) {
            $this->members = Array();
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
        asort($this->members);
        return '{' . implode(', ', $this->members) . '}';
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
     * @return boolean Value indicating whether member was a new set member.
     */
    public function add($member)
    {
        if (!in_array($member, $this->members)) {
            $this->members[] = $member;
            return true;
        }
        return false;
    }

    /**
     * Remove a member from the set.
     * @param mixed $member
     * @return boolean Value indicating whether member existed.
     */
    public function remove($member)
    {
        if (($index = array_search($member, $this->members, true)) !== false) {
            unset($this->members[$index]);
            return true;
        }
        return false;
    }

    /**
     * Add members to set.
     * @param mixed $other
     */
    public function update($other)
    {
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

    /*
     * Return union of instance set and another set.
     * @param mixed $other
     * @return Set
     */
    public function union($other)
    {
        $clone = clone $this;
        $clone->update($other);
        return $clone;
    }

    /**
     * Remove elements in another set from this set.
     * @param mixed $other
     */
    public function differenceUpdate($other)
    {
        $other = is_a($other, 'Set') ? $other->members : $other;
        if (is_array($other)) {
            $this->members = array_diff($this->members, $other);
        } else {
            foreach ($other as $member) {
                $this->remove($member);
            }
        }
    }

    /**
     * Return set with members from another set removed.
     * @param mixed $other
     * @return Set
     */
    public function difference($other)
    {
        $clone = clone $this;
        $clone->differenceUpdate($other);
        return $clone;
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
        $a = $other->difference($this);
        $b = $this->difference($other);
        return $a->union($b);
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
     * Return all elements in common with another set.
     * @param mixed $other
     * @return Set
     */
    public function intersection($other)
    {
        $other = is_a($other, 'Set') ? $other : new self($other);
        return new self(array_intersect($this->members, $other->members));
    }

    /**
     * Remove all elements not shared with another set from this set.
     * @param mixed $other
     */
    public function intersectionUpdate($other)
    {
        $this->members = $this->intersection($other)->members;
    }

    /**
     * Return value indicating whether this set contains all the elements of
     * another set.
     * @param mixed $other
     * @return boolean
     */
    public function isSuperset($other)
    {
        $other = is_a($other, 'Set') ? $other : new self($other);
        $merge = array_unique(array_merge($this->members, $other->members));
        return count($merge) == count($this);
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
        return empty($this->intersection($other)->members);
    }

    /**
     * Remove and return an arbitrary item from this set.
     * @return mixed
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
        $this->members = Array();
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
