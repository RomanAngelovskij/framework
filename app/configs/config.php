<?php
$Config = [
	'main' => [
		'defaultController' => 'site',
	],
	'db' => [
		'dsn' => 'mysql:host=localhost;dbname=tests',
		'username' => 'tests',
		'password' => 'TestTasks',
		'charset' => 'utf8',
	],
	'routes' => [
		'rules' => [
			'test' => 'site/index',
		],
	],
];