<?php
error_reporting(E_ALL);
require('src/Set.php');
require('src/SetTests.php');

$suite = new \Codevat\SetTests();
$suite->runTests();
