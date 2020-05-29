<?
	defined('ROOT_DIR') or header('HTTP/1.1 404 Not Found') or header('Status: 404 Not Found') or die;

	$Db = module('db');

	$this->languages = $Db->rows("SELECT `symbol`,`name` FROM languages", [], PDO::FETCH_KEY_PAIR);
	$this->translations = [];
	$this->symbol = 'ru';
	$this->name = 'Русский';

	// Иницилизация
	$this->main = function($App, $next) use(&$Db) {
		if(!function_exists('l')) {
			function l($key) {
				static $fn;

				if(!$fn) {
					$fn = module('lang')->get;
				}

				return call_user_func_array($fn, func_get_args());
			}
		}

		if(($t = $App->get['lang']) && $this->languages[$t]) $App->cookie('lang', $t);

		$this->symbol = in_array($t = $App->cookie('lang'), array_keys($this->languages)) ? $t : (in_array($t = $_SERVER['GEOIP_COUNTRY_CODE'], array_keys($this->languages)) ? $t : $this->symbol);
		$this->name = $this->languages[$this->symbol];
		$this->translations = $Db->rows("SELECT `key`,`val` FROM translations WHERE `lang` = ?", [$this->symbol], PDO::FETCH_KEY_PAIR);

		module_extend('app', [
			'lang' => $this,
			'user_lang' => $this->symbol
		]);

		$next();
	};

	// Получить преобразованую строку
	$this->get = function($key, $data = null) {
		$key = trim($key);
		$val = isset($this->translations[$key]) ? $this->translations[$key] : $key;

		if(func_num_args() > 1 && !is_array($data)) {
			$data = func_get_args();
			array_shift($data);
		}
		
		if(count((array)$data)) {
			return preg_replace_callback('/{([a-z0-9_]+)}/i', function($m) use(&$data) {
				return isset($data[$m[1]]) ? $data[$m[1]] : $m[0];
			}, $val);
		}
		else return $val;
	};

	// Получить все доступные фразы для перевода
	$this->calcAndGetAllKeys = function() {
		$data = [];

		$sc = function($d) use(&$sc, &$data) {
			foreach(scandir($d) as $v) {
				if($v != '.' && $v != '..' && !is_link($d.$v)) {
					if(is_dir($d.$v)) {
						$sc($d.$v.'/');
					}
					else if(preg_match('/\.php$/', $d.$v)) {
						preg_match_all('/[^a-z_]l\(\'([^\']+)\'/', @file_get_contents($d.$v), $m);
						foreach((array)$m[1] as $v) {
							$data[trim($v)] = '';
						}
					}
				}
			}
		};

		$sc(ROOT_DIR.'modules/');

		return array_keys($data);
	};

	// Перевести все фразы для языка
	$this->translateAllKeysForLang = function($lang) use(&$Db) {
		foreach($this->calcAndGetAllKeys() as $key) {
			if(!$Db->val("SELECT `key` FROM translations WHERE `lang` = ? AND `key` = ?", [$lang, $key])) {
				$val = json_decode(file_get_contents('https://translate.yandex.net/api/v1.5/tr.json/translate?key=trnsl.1.1.20181121T084033Z.21318b8664e3bb05.580ccf7ed56dd9df655160d12116106645df5a22&lang='.urlencode($lang).'&text='.urlencode($key)), true)['text'][0];

				$Db->insert('translations', [
					'lang' => $lang,
					'key' => $key,
					'val' => $val
				]);
			}
		}
	};

	// Перевести все фразы на все языки
	$this->translateAllKeysForAllLangs = function() use(&$Db) {
		foreach($this->calcAndGetAllKeys() as $key) {
			foreach($this->languages as $lang => $v) {
				if($lang != 'ru' && !$Db->val("SELECT `key` FROM translations WHERE `lang` = ? AND `key` = ?", [$lang, $key])) {
					$val = json_decode(file_get_contents('https://translate.yandex.net/api/v1.5/tr.json/translate?key=trnsl.1.1.20181121T084033Z.21318b8664e3bb05.580ccf7ed56dd9df655160d12116106645df5a22&lang='.urlencode($lang).'&text='.urlencode($key)), true)['text'][0];

					$Db->insert('translations', [
						'lang' => $lang,
						'key' => $key,
						'val' => $val
					]);
				}
			}
		}
	};