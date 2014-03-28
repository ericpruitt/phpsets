<?php
error_reporting(E_ALL);
require('src/Set.php');
require('src/Tests/SetTests.php');

$suite = new \Codevat\Tests\SetTests();
$suite->runTests();
