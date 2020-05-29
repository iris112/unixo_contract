<?
	defined('ROOT_DIR') or header('HTTP/1.1 404 Not Found') or header('Status: 404 Not Found') or die;

	$Db = module('db');

	// Вывод главной
	$this->index = function($App, $next) use(&$Db) {
		$users = [];

		$fn = function($addr, $deep) use(&$Db, &$users, &$fn) {
			if($deep < 6 && ($ref = $Db->row("SELECT `ref` addr,(SELECT id FROM users WHERE address = t1.ref) id, (SELECT MAX(`level`) FROM sk_events t2 WHERE t2.user = t1.ref AND t2.time > UNIX_TIMESTAMP() - (86400 * 30) AND t2.type = 'buyLevelEvent') level FROM `sk_events` t1 WHERE `user` = ? AND `type` = 'regLevelEvent'", [$addr]))) {
				$users[] = ['address' => $ref['addr'], 'deep' => $deep, 'level' => $ref['level'], 'id' => $ref['id']];
				$fn($ref['addr'], $deep + 1);
			}
		};

		$fn($App->auth->user['address'], 1);

		$App->layout->render(__DIR__.'/views/index', [
			'users' => $users
		]);
	};