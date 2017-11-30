<?php

require_once '../../vendor/autoload.php';

define('APP_ROOT', getcwd());
define('APP_ASSETS', 'public/');
define('APP_IMG', APP_ASSETS . 'img/');
define('APP_CSS', APP_ASSETS . 'css/');
define('APP_JS', APP_ASSETS . 'js/');

$app = new Vanksen\Kimple\App();
$app->render();