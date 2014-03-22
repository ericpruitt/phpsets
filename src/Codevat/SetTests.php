<?php
namespace Codevat;

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

    public static function assertEqual($a, $b, $strict = false)
    {
        if (($strict and !($a === $b)) or !($a == $b)) {
            throw new \Exception('Values not equal!');
        }
    }

    public static function assertFalse($falsehood)
    {
        if ($falsehood) {
            throw new \Exception('Value not false!');
        }
    }

    public static function assertTrue($truth)
    {
        if (!$truth) {
            throw new \Exception('Value not true!');
        }
    }

    protected function setUp()
    {
        $this->arrayA = $arrayA = range(1, 250);
        $this->setA = new Set($arrayA);
        $this->iterA = new \ArrayIterator($arrayA);

        $this->arrayB = $arrayB = range(125, 375);
        $this->setB = new Set($arrayB);
        $this->iterB = new \ArrayIterator($arrayB);

        $this->arrayC = $arrayC = range(251, 500);
        $this->setC = new Set($arrayC);
        $this->iterC = new \ArrayIterator($arrayC);

        $this->arrayD = $arrayD = range(1, 125);
        $this->setD = new Set($arrayD);
        $this->iterD = new \ArrayIterator($arrayD);

        $this->arrayE = $arrayE = range(1, 500);
        $this->setE = new Set($arrayE);
        $this->iterE = new \ArrayIterator($arrayE);
    }

    public function runTests()
    {
        foreach (get_class_methods($this) as $method) {
            if (stripos($method, 'test_') === 0) {
                $this->setUp();
                print("Running $method...");
                flush();
                try {
                    $this->$method();
                    print(" OK\n");
                } catch (\Exception $exc) {
                    print("\n$exc\n");
                }
            }
        }
    }

    public function test_toArray()
    {
        return $this->setA->toArray() == $this->arrayA;
    }

    public function test_equals()
    {
        self::assertTrue($this->setA->equals($this->arrayA));
        self::assertTrue($this->setA->equals($this->iterA));
        self::assertFalse($this->setA->equals($this->arrayB));
        self::assertFalse($this->setA->equals($this->setB));
        self::assertFalse($this->setA->equals($this->iterB));
    }

    public function test_add()
    {
        $this->setA->add(251);
        self::assertTrue($this->setA->equals(range(1, 251)));
    }

    public function test_remove()
    {
        $this->setA->remove(250);
        self::assertTrue($this->setA->equals(range(1, 249)));
    }

    public function test_update_disjoint_array()
    {
        $this->setA->update($this->arrayC);
        self::assertTrue($this->setA->equals(range(1, 500)));
    }

    public function test_update_disjoint_iterable()
    {
        $this->setA->update($this->iterC);
        self::assertTrue($this->setA->equals(range(1, 500)));
    }

    public function test_update_disjoint_set()
    {
        $this->setA->update($this->setC);
        self::assertTrue($this->setA->equals(range(1, 500)));
    }

    public function test_update_intersecting_array()
    {
        $this->setA->update($this->arrayB);
        self::assertTrue($this->setA->equals(range(1, 375)));
    }

    public function test_update_intersecting_set()
    {
        $this->setA->update($this->setB);
        self::assertTrue($this->setA->equals(range(1, 375)));
    }

    public function test_update_intersecting_iterable()
    {
        $this->setA->update($this->iterB);
        self::assertTrue($this->setA->equals(range(1, 375)));
    }

    public function test_union()
    {
        $union = $this->setA->union($this->setA, $this->arrayB, $this->iterC);
        self::assertEqual($union->toArray(), range(1, 500));
        self::assertTrue($union->equals($this->setE));
    }

    public function test_differenceUpdate_array_subset()
    {
        $this->setE->differenceUpdate($this->arrayC);
        self::assertTrue($this->setE->equals($this->arrayA));
    }

    public function test_differenceUpdate_set_subset()
    {
        $this->setE->differenceUpdate($this->setC);
        self::assertTrue($this->setE->equals($this->setA));
    }

    public function test_differenceUpdate_iterable_subset()
    {
        $this->setE->differenceUpdate($this->iterC);
        self::assertTrue($this->setE->equals($this->iterA));
    }

    public function test_differenceUpdate_array_superset()
    {
        $this->setA->differenceUpdate($this->arrayE);
        self::assertEqual(count($this->setA), 0);
    }

    public function test_differenceUpdate_set_superset()
    {
        $this->setA->differenceUpdate($this->setE);
        self::assertEqual(count($this->setA), 0);
    }

    public function test_differenceUpdate_iterable_superset()
    {
        $this->setA->differenceUpdate($this->iterE);
        self::assertEqual(count($this->setA), 0);
    }

    public function test_differenceUpdate_array_disjoint()
    {
        $this->setA->differenceUpdate($this->arrayC);
        self::assertEqual(count($this->setA), 250);
    }

    public function test_differenceUpdate_set_disjoint()
    {
        $this->setA->differenceUpdate($this->setC);
        self::assertEqual(count($this->setA), 250);
    }

    public function test_differenceUpdate_iterable_disjoint()
    {
        $this->setA->differenceUpdate($this->iterC);
        self::assertEqual(count($this->setA), 250);
    }

    public function test_symmetricDifference()
    {
        $sd = $this->setA->symmetricDifference($this->setB);
        foreach (range(1, 124) as $k) {
            self::assertTrue($sd->contains($k));
        }
        foreach (range(125, 250) as $k) {
            self::assertFalse($sd->contains($k));
        }
        foreach (range(251, 375) as $k) {
            self::assertTrue($sd->contains($k));
        }
    }

    public function test_symmetricDifferenceUpdate()
    {
        $this->setA->symmetricDifferenceUpdate($this->setB);
        foreach (range(1, 124) as $k) {
            self::assertTrue($this->setA->contains($k));
        }
        foreach (range(125, 250) as $k) {
            self::assertFalse($this->setA->contains($k));
        }
        foreach (range(251, 375) as $k) {
            self::assertTrue($this->setA->contains($k));
        }
    }

    public function test_intersectionUpdate_subset_array()
    {
        $this->setA->intersectionUpdate($this->arrayD);
        self::assertTrue($this->setA->equals($this->arrayD));
    }

    public function test_intersectionUpdate_subset_set()
    {
        $this->setA->intersectionUpdate($this->setD);
        self::assertTrue($this->setA->equals($this->setD));
    }

    public function test_intersectionUpdate_subset_iterable()
    {
        $this->setA->intersectionUpdate($this->iterD);
        self::assertTrue($this->setA->equals($this->iterD));
    }

    public function test_intersectionUpdate_disjoint_array()
    {
        $this->setA->intersectionUpdate($this->arrayC);
        self::assertEqual(count($this->setA), 0);
    }

    public function test_intersectionUpdate_disjoint_set()
    {
        $this->setA->intersectionUpdate($this->setC);
        self::assertEqual(count($this->setA), 0);
    }

    public function test_intersectionUpdate_disjoint_iterable()
    {
        $this->setA->intersectionUpdate($this->iterC);
        self::assertEqual(count($this->setA), 0);
    }

    public function test_intersection()
    {
        self::assertEqual(count($this->setA->intersection($this->iterC)), 0);
    }

    public function test_isSuperset_array()
    {
        self::assertTrue($this->setA->isSuperset($this->arrayA));
        self::assertFalse($this->setA->isSuperset($this->arrayB));
        self::assertFalse($this->setA->isSuperset($this->arrayC));
        self::assertTrue($this->setA->isSuperset($this->arrayD));
        self::assertFalse($this->setA->isSuperset($this->arrayE));
    }

    public function test_isSuperset_set()
    {
        self::assertTrue($this->setA->isSuperset($this->setA));
        self::assertFalse($this->setA->isSuperset($this->setB));
        self::assertFalse($this->setA->isSuperset($this->setC));
        self::assertTrue($this->setA->isSuperset($this->setD));
        self::assertFalse($this->setA->isSuperset($this->setE));
    }

    public function test_isSuperset_iterable()
    {
        self::assertTrue($this->setA->isSuperset($this->iterA));
        self::assertFalse($this->setA->isSuperset($this->iterB));
        self::assertFalse($this->setA->isSuperset($this->iterC));
        self::assertTrue($this->setA->isSuperset($this->iterD));
        self::assertFalse($this->setA->isSuperset($this->iterE));
    }

    public function test_isSubset_array()
    {
        self::assertTrue($this->setA->isSubset($this->arrayA));
        self::assertFalse($this->setA->isSubset($this->arrayB));
        self::assertFalse($this->setA->isSubset($this->arrayC));
        self::assertFalse($this->setA->isSubset($this->arrayD));
        self::assertTrue($this->setA->isSubset($this->arrayE));
    }

    public function test_isSubset_set()
    {
        self::assertTrue($this->setA->isSubset($this->setA));
        self::assertFalse($this->setA->isSubset($this->setB));
        self::assertFalse($this->setA->isSubset($this->setC));
        self::assertFalse($this->setA->isSubset($this->setD));
        self::assertTrue($this->setA->isSubset($this->setE));
    }

    public function test_isSubset_iterable()
    {
        self::assertTrue($this->setA->isSubset($this->iterA));
        self::assertFalse($this->setA->isSubset($this->iterB));
        self::assertFalse($this->setA->isSubset($this->iterC));
        self::assertFalse($this->setA->isSubset($this->iterD));
        self::assertTrue($this->setA->isSubset($this->iterE));
    }

    public function test_isDisjoint_array()
    {
        self::assertTrue($this->setA->isDisjoint($this->arrayC));
        self::assertFalse($this->setA->isDisjoint($this->arrayB));
    }

    public function test_isDisjoint_set()
    {
        self::assertTrue($this->setA->isDisjoint($this->setC));
        self::assertFalse($this->setA->isDisjoint($this->setB));
    }

    public function test_isDisjoint_iterable()
    {
        self::assertTrue($this->setA->isDisjoint($this->iterC));
        self::assertFalse($this->setA->isDisjoint($this->iterB));
    }

    public function test_pop()
    {
        foreach (range(1, 250) as $k) {
            $this->setA->pop();
        }
        self::assertEqual(count($this->setA), 0);
    }

    public function test_clear()
    {
        $this->setA->clear();
        foreach (range(1, 250) as $k) {
            self::assertFalse($this->setA->contains($k));
        }
    }

    public function test_contains()
    {
        foreach (range(1, 500) as $k) {
            if ($k < 251) {
                self::assertTrue($this->setA->contains($k));
            } else {
                self::assertFalse($this->setA->contains($k));
            }
        }
    }
}
