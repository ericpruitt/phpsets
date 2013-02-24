<?php
require('set.php');

class GenericIterator implements IteratorAggregate
{
    function __construct($arr)
    {
        $this->arr = $arr;
    }

    function getIterator()
    {
        return new ArrayIterator($this->arr);
    }
}

class SetTests
{
    private $arrayA;
    private $setA;
    private $iterA;
    private $arrayB;
    private $setB;
    private $iterB;
    private $arrayC;
    private $setC;
    private $iterC;
    private $arrayD;
    private $setD;
    private $iterD;
    private $arrayE;
    private $setE;
    private $iterE;

    function setUp()
    {
        $this->arrayA = $arrayA = range(1, 250);
        $this->setA = new Set($arrayA);
        $this->iterA = new GenericIterator($arrayA);

        $this->arrayB = $arrayB = range(125, 375);
        $this->setB = new Set($arrayB);
        $this->iterB = new GenericIterator($arrayB);

        $this->arrayC = $arrayC = range(251, 500);
        $this->setC = new Set($arrayC);
        $this->iterC = new GenericIterator($arrayC);

        $this->arrayD = $arrayD = range(1, 125);
        $this->setD = new Set($arrayD);
        $this->iterD = new GenericIterator($arrayD);

        $this->arrayE = $arrayE = range(1, 500);
        $this->setE = new Set($arrayE);
        $this->iterE = new GenericIterator($arrayE);
    }

    function runTests()
    {
        foreach (get_class_methods($this) as $method) {
            if (stripos($method, 'test_') === 0) {
                $this->setUp();
                print("Running $method...");
                flush();
                try {
                    $this->$method();
                    print(" OK\n");
                } catch (Exception $exc) {
                    print("\n$exc\n");
                }
            }
        }
    }

    function test_toArray()
    {
        return $this->setA->toArray() == $this->arrayA;
    }

    function test_equals()
    {
        assertTrue($this->setA->equals($this->arrayA));
        assertTrue($this->setA->equals($this->iterA));
        assertFalse($this->setA->equals($this->arrayB));
        assertFalse($this->setA->equals($this->setB));
        assertFalse($this->setA->equals($this->iterB));
    }

    function test_add()
    {
        $this->setA->add(251);
        assertTrue($this->setA->equals(range(1, 251)));
    }

    function test_remove()
    {
        $this->setA->remove(250);
        assertTrue($this->setA->equals(range(1, 249)));
    }

    function test_update_disjoint_array()
    {
        $this->setA->update($this->arrayC);
        assertTrue($this->setA->equals(range(1, 500)));
    }

    function test_update_disjoint_iterable()
    {
        $this->setA->update($this->iterC);
        assertTrue($this->setA->equals(range(1, 500)));
    }

    function test_update_disjoint_set()
    {
        $this->setA->update($this->setC);
        assertTrue($this->setA->equals(range(1, 500)));
    }

    function test_update_intersecting_array()
    {
        $this->setA->update($this->arrayB);
        assertTrue($this->setA->equals(range(1, 375)));
    }

    function test_update_intersecting_set()
    {
        $this->setA->update($this->setB);
        assertTrue($this->setA->equals(range(1, 375)));
    }

    function test_update_intersecting_iterable()
    {
        $this->setA->update($this->iterB);
        assertTrue($this->setA->equals(range(1, 375)));
    }

    function test_union()
    {
        $union = $this->setA->union($this->setA, $this->arrayB, $this->iterC);
        assertEqual($union->toArray(), range(1, 500));
        assertTrue($union->equals($this->setE));
    }

    function test_differenceUpdate_array_subset()
    {
        $this->setE->differenceUpdate($this->arrayC);
        assertTrue($this->setE->equals($this->arrayA));
    }

    function test_differenceUpdate_set_subset()
    {
        $this->setE->differenceUpdate($this->setC);
        assertTrue($this->setE->equals($this->setA));
    }

    function test_differenceUpdate_iterable_subset()
    {
        $this->setE->differenceUpdate($this->iterC);
        assertTrue($this->setE->equals($this->iterA));
    }

    function test_differenceUpdate_array_superset()
    {
        $this->setA->differenceUpdate($this->arrayE);
        assertEqual(count($this->setA), 0);
    }

    function test_differenceUpdate_set_superset()
    {
        $this->setA->differenceUpdate($this->setE);
        assertEqual(count($this->setA), 0);
    }

    function test_differenceUpdate_iterable_superset()
    {
        $this->setA->differenceUpdate($this->iterE);
        assertEqual(count($this->setA), 0);
    }

    function test_differenceUpdate_array_disjoint()
    {
        $this->setA->differenceUpdate($this->arrayC);
        assertEqual(count($this->setA), 250);
    }

    function test_differenceUpdate_set_disjoint()
    {
        $this->setA->differenceUpdate($this->setC);
        assertEqual(count($this->setA), 250);
    }

    function test_differenceUpdate_iterable_disjoint()
    {
        $this->setA->differenceUpdate($this->iterC);
        assertEqual(count($this->setA), 250);
    }

    function test_symmetricDifference()
    {
        $sd = $this->setA->symmetricDifference($this->setB);
        foreach (range(1, 124) as $k) {
            assertTrue($sd->contains($k));
        }
        foreach (range(125, 250) as $k) {
            assertFalse($sd->contains($k));
        }
        foreach (range(251, 375) as $k) {
            assertTrue($sd->contains($k));
        }
    }

    function test_symmetricDifferenceUpdate()
    {
        $this->setA->symmetricDifferenceUpdate($this->setB);
        foreach (range(1, 124) as $k) {
            assertTrue($this->setA->contains($k));
        }
        foreach (range(125, 250) as $k) {
            assertFalse($this->setA->contains($k));
        }
        foreach (range(251, 375) as $k) {
            assertTrue($this->setA->contains($k));
        }
    }

    function test_intersectionUpdate_subset_array()
    {
        $this->setA->intersectionUpdate($this->arrayD);
        assertTrue($this->setA->equals($this->arrayD));
    }

    function test_intersectionUpdate_subset_set()
    {
        $this->setA->intersectionUpdate($this->setD);
        assertTrue($this->setA->equals($this->setD));
    }

    function test_intersectionUpdate_subset_iterable()
    {
        $this->setA->intersectionUpdate($this->iterD);
        assertTrue($this->setA->equals($this->iterD));
    }

    function test_intersectionUpdate_disjoint_array()
    {
        $this->setA->intersectionUpdate($this->arrayC);
        assertEqual(count($this->setA), 0);
    }

    function test_intersectionUpdate_disjoint_set()
    {
        $this->setA->intersectionUpdate($this->setC);
        assertEqual(count($this->setA), 0);
    }

    function test_intersectionUpdate_disjoint_iterable()
    {
        $this->setA->intersectionUpdate($this->iterC);
        assertEqual(count($this->setA), 0);
    }

    function test_intersection()
    {
        assertEqual(count($this->setA->intersection($this->iterC)), 0);
    }

    function test_isSuperset_array()
    {
        assertTrue($this->setA->isSuperset($this->arrayA));
        assertFalse($this->setA->isSuperset($this->arrayB));
        assertFalse($this->setA->isSuperset($this->arrayC));
        assertTrue($this->setA->isSuperset($this->arrayD));
        assertFalse($this->setA->isSuperset($this->arrayE));
    }

    function test_isSuperset_set()
    {
        assertTrue($this->setA->isSuperset($this->setA));
        assertFalse($this->setA->isSuperset($this->setB));
        assertFalse($this->setA->isSuperset($this->setC));
        assertTrue($this->setA->isSuperset($this->setD));
        assertFalse($this->setA->isSuperset($this->setE));
    }

    function test_isSuperset_iterable()
    {
        assertTrue($this->setA->isSuperset($this->iterA));
        assertFalse($this->setA->isSuperset($this->iterB));
        assertFalse($this->setA->isSuperset($this->iterC));
        assertTrue($this->setA->isSuperset($this->iterD));
        assertFalse($this->setA->isSuperset($this->iterE));
    }

    function test_isSubset_array()
    {
        assertTrue($this->setA->isSubset($this->arrayA));
        assertFalse($this->setA->isSubset($this->arrayB));
        assertFalse($this->setA->isSubset($this->arrayC));
        assertFalse($this->setA->isSubset($this->arrayD));
        assertTrue($this->setA->isSubset($this->arrayE));
    }

    function test_isSubset_set()
    {
        assertTrue($this->setA->isSubset($this->setA));
        assertFalse($this->setA->isSubset($this->setB));
        assertFalse($this->setA->isSubset($this->setC));
        assertFalse($this->setA->isSubset($this->setD));
        assertTrue($this->setA->isSubset($this->setE));
    }

    function test_isSubset_iterable()
    {
        assertTrue($this->setA->isSubset($this->iterA));
        assertFalse($this->setA->isSubset($this->iterB));
        assertFalse($this->setA->isSubset($this->iterC));
        assertFalse($this->setA->isSubset($this->iterD));
        assertTrue($this->setA->isSubset($this->iterE));
    }

    function test_isDisjoint_array()
    {
        assertTrue($this->setA->isDisjoint($this->arrayC));
        assertFalse($this->setA->isDisjoint($this->arrayB));
    }

    function test_isDisjoint_set()
    {
        assertTrue($this->setA->isDisjoint($this->setC));
        assertFalse($this->setA->isDisjoint($this->setB));
    }

    function test_isDisjoint_iterable()
    {
        assertTrue($this->setA->isDisjoint($this->iterC));
        assertFalse($this->setA->isDisjoint($this->iterB));
    }

    function test_pop()
    {
        foreach (range(1, 250) as $k) {
            $this->setA->pop();
        }
        assertEqual(count($this->setA), 0);
    }

    function test_clear()
    {
        $this->setA->clear();
        foreach (range(1, 250) as $k) {
            assertFalse($this->setA->contains($k));
        }
    }

    function test_contains()
    {
        foreach (range(1, 500) as $k) {
            if ($k < 251) {
                assertTrue($this->setA->contains($k));
            } else {
                assertFalse($this->setA->contains($k));
            }
        }
    }
}

function assertEqual($a, $b, $strict = false)
{
    if (($strict and !($a === $b)) or !($a == $b)) {
        throw new Exception('Values not equal!');
    }
}

function assertFalse($falsehood)
{
    if ($falsehood) {
        throw new Exception('Value not false!');
    }
}

function assertTrue($truth)
{
    if (!$truth) {
        throw new Exception('Value not true!');
    }
}

error_reporting(E_ALL);
$suite = new SetTests();
$suite->runTests();
