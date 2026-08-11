<?php

$baseDir = dirname(__DIR__);
require "$baseDir/vendor/autoload.php";

$app = require "$baseDir/bootstrap/app.php";
$app->run();
