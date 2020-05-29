<?
	defined('ROOT_DIR') or header('HTTP/1.1 404 Not Found') or header('Status: 404 Not Found') or die;

	$Db = module('db');
	$Pagination = module('pagination');

	// Вывод главной
	$this->index = function($App, $next) use(&$Db, &$Pagination) {
		$query = "";
		$params = [$App->auth->user['address']];
		$search = [];

		if($t = trim($App->get['q'])) { $query .= " AND `ref` LIKE ?"; $params[] = $t; $search['q'] = $t; }
		if($t = preg_match('/^[a-z0-9_]+$/', $App->get['s']) ? $App->get['s'] : 'time') $search['s'] = $t;
		if($App->get['r']) $search['r'] = 1;

		$App->layout->render(__DIR__.'/views/index', [
			'count' => ($count = $Db->val("SELECT COUNT(*) FROM `sk_events` WHERE `user` = ? AND `type` = 'lostMoneyForLevelEvent' $query", $params)),
			'pag' => ($pag = $Pagination->create($count, $App->get['p'], $App->get['l'], $search, 20)),
			'losts' => $Db->rows("SELECT `time`, `ref` 'address', `level`,  (SELECT id FROM users WHERE address = sk_events.ref) 'id', (SELECT price FROM levels WHERE id = sk_events.level) 'amount' FROM `sk_events` WHERE `user` = ? AND `type` = 'lostMoneyForLevelEvent' $query ORDER BY `".$search['s']."` ".($search['r'] ? 'ASC' : 'DESC')." LIMIT ".$pag['offset'].", ".$pag['limit'], $params)
		]);
	};