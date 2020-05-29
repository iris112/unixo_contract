<?
	define('ROOT_DIR', __DIR__.'/');
	define('DEBUG', preg_match('/\.(loc)$/', $_SERVER['SERVER_NAME']) ? true : false);

	require_once(ROOT_DIR.'modules/module.php');
	
	module('app')->main();