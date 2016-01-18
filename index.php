<?php

spl_autoload_register();
$app = new \core\Application(__DIR__.'/config/config.php');
$app->run();
