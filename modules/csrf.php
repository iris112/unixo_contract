<?
	defined('ROOT_DIR') or header('HTTP/1.1 404 Not Found') or header('Status: 404 Not Found') or die;

	$this->key = 'dyTUstkZrBfDUKrzq0RimU7JjEFGgNhlcUKbW5lLKa';
	$this->reset_data_in_get = true;
	$this->csrf = '';

	// Точка входа
	$this->main = function($App, $next) {
		if(!preg_match('/^[a-f0-9]{32}$/i', $csrf = $App->cookie('csrf'))) {
			$App->cookie('csrf', $csrf = md5(mt_rand()), ['httponly' => true]);
		}

		$this->csrf = hash('sha256', $csrf.':'.$this->key);

		module_extend('app', [
			'csrf' => $this->csrf
		]);

		if($App->method != 'GET') {
			if(!($this->csrf == $App->header('X-CSRF-Token') || $this->csrf == $App->post['csrf'] || $this->csrf == $App->data['csrf'])) {
				$App->error(400);
			}
		}
		else if($this->reset_data_in_get) {
			module_extend('app', [
				'post' => [],
				'data' => []
			]);
		}

		$next();
	};