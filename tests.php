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
            $this->setA->pop($k);
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
