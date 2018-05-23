<?php

require_once "SubscribeHrTest.php";

echo "[".date("Y-m-d H:i:s")."] - Running Subscribe HR Dev Test".PHP_EOL;

$devTest = new SubscribeHrTest($argv[1]);

$devTest->runTest();
