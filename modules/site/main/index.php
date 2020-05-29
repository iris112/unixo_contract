<?
	defined('ROOT_DIR') or header('HTTP/1.1 404 Not Found') or header('Status: 404 Not Found') or die;

	$App = module('app');
	$Db = module('db');
	$Fetch = module('fetch');

	// Получить рейтинги
	$this->getRates = function() use(&$Db, &$Fetch) {
		if(!($rates = $Db->storage('site/main:rates'))) {
			$Db->storage('sc.gateway.api:rates', ($rates = json_decode($Fetch->query('https://api.smartcontract.ru/gateway/rates_usd.json')['body'], true)['data']), time() + 600);
		}

		return $rates;
	};

	// Получить текст и ссылку для "подедиться""
	$this->getShare = function() use(&$App, &$Db) {
		static $a, $b;

		if(!$a) {
			$a = (float)$Db->val("SELECT SUM(`value`) FROM `sk_events` WHERE `user` = ? AND `type` = 'Profit'", [$App->auth->user['address']]);
			$b = $a * $this->getRates()['eth'];
		}

		return [
			'msg' => l('Я уже заработал {0} ETH (${1}) за 5 дней. Присоединяйся!', round($a, 2), round($b)),
			'url' => $App->origin.'a/1/'//$App->origin.'a/'.$App->auth->user['id'].'/'
		];
	};

	// Вывод главной
	$this->index = function($App, $next) use(&$Db) {
		// Генерация график рефералов
		$referrals_by_date = [];
		$fn = function($addr) use(&$Db, &$fn, &$referrals_by_date) {
			static $check;
			if($check[$addr]) return;
			$check[$addr] = true;

			foreach($Db->rows("SELECT `time`,`user` FROM `sk_events` WHERE `ref` = ? AND `type` = 'Register'", [$addr]) as $v) {
				$referrals_by_date[date('d.m.Y', $v['time'])]++;
				$fn($v['user']);
			}
		};
		$fn($App->auth->user['address']);
		uksort($referrals_by_date, function($a, $b) { return strtotime($a) > strtotime($b); });

		// Получить инфу об остатках левелов
		$my_levels = ['1' => $Db->val("SELECT `time` FROM `sk_events` WHERE `user` = ? AND `type` = 'Register'", [$App->auth->user['address']]) + 86400 * 30];
		foreach($Db->rows("SELECT level,`time` FROM `sk_events` WHERE `user` = ? AND `type` = 'UpLevel'", [$App->auth->user['address']], PDO::FETCH_NUM) as $v) {
			if($my_levels[$v[0]]) $my_levels[$v[0]] += 86400 * 30;
			else $my_levels[$v[0]] = $v[1] + 86400 * 30;
		}

		// Вывод данных
		$App->layout->render(__DIR__.'/views/index', [
			'referrals' => array_sum($referrals_by_date),
			'levels' => $Db->rows("SELECT id,price FROM `levels`"),
			'my_levels' => $my_levels,
			'profit_eth' => ($t = $Db->val("SELECT `value` FROM `sk_events` WHERE `user` = ? AND `type` = 'Profit'", [$App->auth->user['address']])),
			'profit_usd' => $t * $this->getRates()['eth'],
			'profit_by_levels' => $Db->rows("SELECT `level` + 1, SUM(`value`) 'profit' FROM `sk_events` WHERE `user` = ? AND `type` = 'Profit' GROUP BY `level`", [$App->auth->user['address']], PDO::FETCH_KEY_PAIR),
			'referrals_by_date' => $referrals_by_date,
		], [
			'js' => [
				'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js',
				'/modules/site/main/assets/common.js?'.filemtime(__DIR__.'/assets/common.js')
			],
			'css' => ['/modules/site/main/assets/common.css?'.filemtime(__DIR__.'/assets/common.css')]
		]);
	};

	// Вывод инфы
	$this->statInfo = function($App, $next) use(&$Db) {
		$App->header('Access-Control-Allow-Origin', '*');
		$rates = $this->getRates();

		$App->json([
			'txs' => [
				'all' => count($Db->rows("SELECT '' FROM `sk_events` GROUP BY `tx`")),
				'week' => count($Db->rows("SELECT '' FROM `sk_events` WHERE `time` > UNIX_TIMESTAMP() - 86400 * 7 GROUP BY `tx`")),
				'day' => count($Db->rows("SELECT '' FROM `sk_events` WHERE `time` > UNIX_TIMESTAMP() - 86400 GROUP BY `tx`")),
				'last' => $Db->rows("SELECT `time`, `user` 'address', IF(type = 'Register', 1, `level`) 'level', `value` 'amount' FROM `sk_events` WHERE `type` IN ('Register', 'UpLevel') ORDER BY `time` DESC LIMIT 10")
			],
			'users' => [
				'all' => $Db->val("SELECT COUNT(*) FROM `users`"),
				'by_levels' =>
					[1 => $Db->val("SELECT COUNT(*) FROM sk_events WHERE sk_events.type = 'Register'")]
					+ $Db->rows("SELECT id,(SELECT COUNT(*) FROM sk_events WHERE sk_events.level = levels.id AND sk_events.type = 'UpLevel') FROM `levels`", [], PDO::FETCH_KEY_PAIR)
			],
			'rates' => [
				'eth' => $rates['eth'],
				'btc' => $rates['btc'],
			],
			'fees' => [
				'all' => round($eth = $Db->val("SELECT SUM(`value`) FROM `sk_events` WHERE `type` = 'Profit'"), 2),
				'day' => round($eth = $Db->val("SELECT SUM(`value`) FROM `sk_events` WHERE `type` = 'Profit' AND `time` > UNIX_TIMESTAMP() - 86400"), 2),
			],
			'contract' => module('site/service')->contract,
			'days_left' => ceil((time() - $Db->val("SELECT MIN(`time`) FROM `sk_events`")) / 86400)
		]);
	};