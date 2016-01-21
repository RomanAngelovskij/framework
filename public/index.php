<?php
error_reporting(-1);
ini_set('display_errors', 1);

define('DS', '/');
define('ROOT_DIR', __DIR__ . '/../');
define('CORE_FOLDER', 'core');
define('APP_FOLDER', 'app');

function var_dump_pre($data){
	echo '<pre>';
	var_dump($data);
	echo '</pre>';
}

function showError($msg){
	exit('<div style="border: 1px solid #ff0000;">' . $msg . '</div>');
}

$configFile = rtrim(ROOT_DIR, DS) . DS . rtrim(APP_FOLDER, DS) . DS . 'configs' . DS . 'config.php';

$applicationFile = rtrim(ROOT_DIR, DS) . DS . rtrim(CORE_FOLDER, DS) . DS . 'application.php';

$autoloadFile = rtrim(ROOT_DIR, DS) . DS . rtrim(CORE_FOLDER, DS) . DS . 'autoload.php';

if (!file_exists($configFile)){
	exit('Config not found');
}

include $configFile;

if (!file_exists($applicationFile)){
	exit('application.php not found');
}

include $applicationFile;

if (!file_exists($autoloadFile)){
	exit('autoload.php not found');
}

include $autoloadFile;

try{
	FM\Application::getInstance()->run($Config);
} catch (Exception $e) {
	showError($e->getMessage());
}
