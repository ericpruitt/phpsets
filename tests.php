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
        assertTrue(!$this->setA->equals($this->arrayB));
        assertTrue(!$this->setA->equals($this->setB));
        assertTrue(!$this->setA->equals($this->iterB));
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
}

function assertEqual($a, $b, $strict = false)
{
    if (($strict and !($a === $b)) or !($a == $b)) {
        throw new Exception('Values not equal!');
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
