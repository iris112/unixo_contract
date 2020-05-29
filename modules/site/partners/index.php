<?
	defined('ROOT_DIR') or header('HTTP/1.1 404 Not Found') or header('Status: 404 Not Found') or die;

	$Db = module('db');
	$Pagination = module('pagination');
	
	// Вывод главной
	$this->index = function($App, $next) use(&$Db, &$Pagination) {
		// Генерация дерева
		$fn = function($item, $deep = 0) use(&$Db, &$fn) {
			$res = ['n' => 'ID: '.$item['id'], 'l' => $item['level'] ?: 1];

			if($deep < 8 && count($t = $Db->rows("SELECT `id`,`address`, (SELECT MAX(`level`) FROM `sk_events` WHERE `user` = users.address AND `time` > UNIX_TIMESTAMP() - (86400 * 30) AND `type` = 'UpLevel') 'level' FROM `users` WHERE `pid` = ? ORDER BY `id` DESC", [$item['id']]))) {
				foreach($t as $v) $res['children'][] = $fn($v, $deep + 1);
			}

			return $res;
		};

		$query = "";
		$params = [$App->auth->user['address']];
		$search = [];

		if($t = trim($App->get['q'])) { $query .= " AND `ref` LIKE ?"; $params[] = $t; $search['q'] = $t; }
		if($t = preg_match('/^[a-z0-9_]+$/', $App->get['s']) ? $App->get['s'] : 'time') $search['s'] = $t;
		if($App->get['r']) $search['r'] = 1;

		// Вывод данных
		$App->layout->render(__DIR__.'/views/index', [
			'count' => ($count = $Db->val("SELECT COUNT(*) FROM `sk_events` WHERE `user` = ? AND `type` = 'Profit' $query", $params)),
			'pag' => ($pag = $Pagination->create($count, $App->get['p'], $App->get['l'], $search, 20)),
			'repayments' => $Db->rows("SELECT `time`, `ref` 'address',  (SELECT id FROM users WHERE address = sk_events.ref) 'id', `value` 'amount' FROM `sk_events` WHERE `user` = ? AND `type` = 'Profit' $query ORDER BY `".$search['s']."` ".($search['r'] ? 'ASC' : 'DESC')." LIMIT ".$pag['offset'].", ".$pag['limit'], $params),
			'rates' => module('site/main')->getRates(),
			'tree' => $App->auth->user['id'] > 0 ? $fn(['id' => $App->auth->user['id'], 'address' => $App->auth->user['address'], 'level' => $App->auth->user['level']]) : []
		], [
			'css' => ['/modules/site/partners/assets/common.css?'.filemtime(__DIR__.'/assets/common.css')],
			'js' => ['https://d3js.org/d3.v4.min.js', '/modules/site/partners/assets/common.js?'.filemtime(__DIR__.'/assets/common.js')]
		]);
	};

	// Получить инфу по адресу или id
	$this->getPartnerInfo = function($App, $next) use(&$Db) {
		$App->json([
			'success' => true,
			'user' => $Db->row("SELECT `id`,CONCAT(`address`, ' ') 'address',(SELECT MAX(`level`) FROM `sk_events` WHERE `user` = `users`.`address` AND `time` > UNIX_TIMESTAMP() - (86400 * 30) AND `type` = 'UpLevel') 'level' FROM `users` WHERE `id` = ? OR `address` = ?", [$App->params['id'], $App->params['id']])
		]);
	};