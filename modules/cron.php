<?
	defined('ROOT_DIR') or header('HTTP/1.1 404 Not Found') or header('Status: 404 Not Found') or die;

	$this->time_limit = 0;
	$this->log_file = null;

	$tasks = [];

	// Обработчик запросов
	$this->index = function($App, $next) use(&$tasks) {
		$time = explode(' ', date('i H d m N'));

		if(strlen($index = $App->get['i']) > 0) {
			if(($task = $tasks[$index]) && $this->avafplay($time, explode(' ', $task[0]))) { // && ($App->user_ip == $_SERVER['SERVER_ADDR'] || DEBUG)) {
				ignore_user_abort(true);
				set_time_limit($this->time_limit);

				ob_start();

				if(is_string($task[1]) && ($act = explode(':', $task[1])) && module_exists($act[0]) && ($md = module($act[0]))) {
					if(is_callable($t = $md->{$act[1]})) $t($App, $task[2]);
				}
				else if(is_callable($task[1])) $task[1]($App, $task[2]);

				if($this->log_file) file_put_contents($this->log_file, PHP_EOL.'date='.date('c').";\ttask=".$index.";\targs=".json_encode($task[2]).PHP_EOL.ob_get_clean().PHP_EOL, FILE_APPEND);
				else ob_get_clean();
			}
		}
		else {
			foreach($tasks as $index => $task) {
				if($this->avafplay($time, explode(' ', $task[0]))) {
					@file_get_contents($App->origin.'cron/?i='.$index, false, stream_context_create(['http' => ['timeout' => 1]]));
				}
			}
		}
	};

	// Добавить задачу в список
	$this->task = function($timeout, $action = null, $args = null) use(&$tasks) {
		if(is_array($timeout)) foreach($timeout as $v) $this->task($v[0], $v[1], $v[2]);
		else $tasks[] = [$timeout, $action, $args];
		return $this;
	};

	// Проверить правило можно ли его запустить (* 2 1,15 */2 1-5)
	$this->avafplay = function($time, $rule) {
		foreach((array)$time as $k => $v) {
			if(strlen($v2 = $rule[$k]) > 0 && $v2 != '*') {
				if(
					(is_numeric($v2) && $v != $v2)
					|| (preg_match('/^\*\/(\d+)$/', $v2, $m) && ($v % $m[1] !== 0))
					|| (preg_match('/^(\d+)\-(\d+)$/', $v2, $m) && ($v < $m[1] || $v > $m[2]))
					|| (preg_match('/^\d+(,\d+)+$/', $v2, $m) && !in_array($v, explode(',', $v2)))
				) return false;
			}
		}

		return true;
	};