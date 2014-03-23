phpsets
=======

This is an implementation of mathematical sets in PHP. The class was built to
function in a manner similar to Python's "set" object; the method names are
identical in most cases with underscore-based names being substituted for
camel-case ones, and like the Python implementation of sets, all of the methods
that accept sets also accept arrays or any other object that implements the
Iterator interface.

Although written for Python, many of the concepts discussed in my tutorial
["Using Sets In Python"](http://codevat.com/sets.html) also apply to this
class.

Documentation
-------------

Each of the methods of the `Set` class has been documented using
inline-documentation in the phpDocumentor2 format.

Examples
--------

**NOTE:** All examples take place in a scope with `use \Codevat\Set;` declared.


Basic element addition and deletion:

    php> $nouns = new Set(array("axe", "horse", "duck"));
    php> $nouns->add("dog");
    php> echo $nouns;
    {"axe", "horse", "duck", "dog"}
    php> $nouns->remove("axe");
    php> echo $nouns;
    {"horse", "duck", "dog"}
    php> $nouns->update(array("car", "grass"));
    php> echo $nouns;
    {"horse", "duck", "dog", "car", "grass"}
    php> var_dump($nouns->contains("pooch"));
    bool(false)
    php> var_dump($nouns->contains("duck"));
    bool(true)

Intersections, differences and unions:

    php> $group_one = new Set(array(10, 20, 30, 40, 50));
    php> $group_two = new Set(array(40, 50, 60, 70, 80));
    php> echo $group_one->intersection($group_two);
    {40, 50}
    php> echo $group_one->difference($group_two);
    {10, 20, 30}
    php> echo $group_one->union($group_two);
    {10, 20, 30, 40, 50, 60, 70, 80}

The various update methods modify sets in-place:

    php> $pets = new Set(array("alligator", "anole", "cat", "spiders"));
    php> $reptiles = new Set(array("alligator", "turtle", "anole"));
    php> $pets->differenceUpdate($reptiles);
    php> echo $pets;
    {"alligator", "cat", "spiders"}

Some miscellaneous operations:

    php> $birds = new Set(array("hawk", "pigeon", "owl", "swallow"));
    php> $raptors = new Set(array("falcon", "hawk", "owl", "hawk", "eagle"));
    php> var_dump($raptors->isSubset($birds));
    bool(true)
    php> $mammals = new Set(array("dog", "cat", "beaver", "horse"));
    php> var_dump($mammals->isSuperset($birds));
    bool(false)
    php> var_dump($mammals->isDisjoint($birds));
    bool(true)
