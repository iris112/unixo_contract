<?
	defined('ROOT_DIR') or header('HTTP/1.1 404 Not Found') or header('Status: 404 Not Found') or die;

	$App = module('app');
	$Db = module('db');

	$this->user = [];

	// Точка входа
	$this->main = function($App, $next) use(&$Db) {
		if(($address = $App->cookie('eth_address')) && preg_match('/^0x[a-f0-9]{40}$/i', $address)) {
			$this->user = [
				'id' => $Db->val("SELECT `id` FROM `users` WHERE `address` = ?", [$address]),
				'address' => $address,
				'level' => $Db->val("SELECT MAX(`level`) FROM `sk_events` WHERE `user` = ? AND `time` > UNIX_TIMESTAMP() - (86400 * 30) AND `type` = 'UpLevel'", [$address])
			];
		}

		module_extend('app', [
			'auth' => $this
		]);

		$next();
	};