<?
	defined('ROOT_DIR') or header('HTTP/1.1 404 Not Found') or header('Status: 404 Not Found') or die;

	$Db = module('db');

	// Вывод главной
	$this->index = function($App, $next) use(&$Db) {
		if($_SERVER['PHP_AUTH_USER'] != 'admin' || $_SERVER['PHP_AUTH_PW'] != '64eWxU7UmHEHBH') {
			header('WWW-Authenticate: Basic') or header('HTTP/1.0 401 Unauthorized') or die;
		}

		if(preg_match('/^[a-z]{2}$/', $App->get['lang'])) {
			$lang = $App->get['lang'];
			$translations_keys = $Db->rows("SELECT `key` FROM `translations` GROUP BY `key`", [], PDO::FETCH_COLUMN);

			if(!$Db->val("SELECT `symbol` FROM `languages` WHERE `symbol` = ?", [$lang])) {
				$Db->insert('languages', ['symbol' => $lang]);
			}

			if($App->get['find_keys']) {
				$sc = function($d) use(&$sc, &$lang, &$Db) {
					foreach(scandir($d) as $v) {
						if($v != '.' && $v != '..' && !is_link($d.$v)) {
							if(is_dir($d.$v)) $sc($d.$v.'/');
							else if(preg_match('/\.php$/', $d.$v)) {
								preg_match_all('/[^a-z_]l\(\'([^\']+)\'/', @file_get_contents($d.$v), $m);
								foreach((array)$m[1] as $v) {
									if($v = trim($v)) {
										$Db->query("INSERT INTO `translations` (`lang`,`key`) VALUES (?,?) ON DUPLICATE KEY UPDATE `key` = VALUES(`key`)", [$lang, $v]);
									}
								}
							}
						}
					}
				};

				$sc(ROOT_DIR.'modules/');
				header('Location: ?lang='.$lang) or die;
			}

			if(trim($App->get['add_key'])) {
				$Db->query("INSERT INTO `translations` (`lang`,`key`) VALUES (?,?)", [$lang, trim($App->get['add_key'])]);
				header('Location: ?lang='.$lang) or die;
			}

			if($App->method == 'POST') {
				foreach($translations_keys as $key) {
					if(isset($App->post[$hk = substr(md5($key), 0, 8)])) {
						$Db->query("INSERT INTO `translations` (`lang`,`key`,`val`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE `val` = VALUES(`val`)", [$lang, $key, trim($App->post[$hk])]);
					}
				}
			}

			$translations_vals = $Db->rows("SELECT `key`,`val` FROM `translations` WHERE `lang` = ?", [$lang], PDO::FETCH_KEY_PAIR);
		}

		$langs = $Db->rows("SELECT `symbol` FROM `languages`", [], PDO::FETCH_COLUMN);

		$App->render(__DIR__.'/views/index', [
			'langs' => $langs,
			'lang' => $lang,
			'translations_keys' => $translations_keys,
			'translations_vals' => $translations_vals
		]);
	};