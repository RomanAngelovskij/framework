<?php
spl_autoload_register(function ($class) {

	$ClassParts = explode('\\', $class);
	$classPath = null;

	if (count($ClassParts) === 1){
		$classPath = rtrim(CORE_FOLDER, DS) . DS . $ClassParts[0] . '.php';
	}

	if ($ClassParts[0] === 'FM'){
		array_shift($ClassParts);
		$classPath = rtrim(CORE_FOLDER, DS) . DS . implode(DS , $ClassParts) . '.php';
	}

	if ($ClassParts[0] === 'app'){
		array_shift($ClassParts);
		$classPath = rtrim(APP_FOLDER, DS) . DS . implode(DS , $ClassParts) . '.php';
	}

	if (empty($classPath)){
		$classPath = implode(DS , $ClassParts) . '.php';
	}

	if (file_exists(rtrim(ROOT_DIR, DS) . DS . $classPath)){
		include rtrim(ROOT_DIR, DS) . DS . $classPath;
	} else {
		showError($class . ' not found in ' . $classPath);
		exit;
	}
});