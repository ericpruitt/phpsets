phpsets
=======

This is an implementation of mathematical sets in PHP. The class was built to
function in a manner similar to Python's "set" object; the method names are
identical in most cases with underscore-based names being substituted for
camel-case ones, and like the Python implementation of sets, all of the methods
that accept sets also accept arrays or any other object that implements the
Iterator interface.

Examples
--------

Basic element addition:

    php> $set = new Set(Array(1, 2, 3, 4, 5));
    php> $set->add(10);
    php> echo $set;
    {1, 2, 3, 4, 5, 10}
    php> $set->remove(5);
    php> echo $set;
    {1, 2, 3, 4, 10}
    php> $set->update(Array(15, 16, 17));
    php> echo $set;
    {1, 2, 3, 4, 10, 15, 16, 17}

Intersections, differences and unions:

    php> $group_one = new Set(Array(10, 20, 30, 40, 50));
    php> $group_two = new Set(Array(40, 50, 60, 70, 80));
    php> echo $group_one->intersection($group_two);
    {40, 50}
    php> echo $group_one->difference($group_two);
    {10, 20, 30}
    php> echo $group_one->union($group_two);
    {10, 20, 30, 40, 50, 60, 70, 80}

Full Method List
----------------

Below is a list of all of the methods in the code along with examples. Note
that anywhere an array is used, a Set can be used in its place.

### Set::add ###

Add a member to the set.

    php> $set = new Set(Array(1, 2, 3, 4, 5));
    php> $set->add(10);
    php> echo $set;
    {1, 2, 3, 4, 5, 10}

### Set::remove ###

Remove a member from the set.

    php> $set = new Set(Array(1, 2, 3, 4, 5));
    php> $set->remove(3);
    php> echo $set;
    {1, 2, 4, 5}

### Set::update ###

    php> $set = new Set(Array(1, 2, 3));
    php> $set->update(Array(7, 8, 9));
    php> echo $set;
    {1, 2, 3, 7, 8, 9}

