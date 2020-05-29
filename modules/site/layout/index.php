<?
	defined('ROOT_DIR') or header('HTTP/1.1 404 Not Found') or header('Status: 404 Not Found') or die;

	$App = module('app');
	$Db = module('db');

	// Точка входа
	$this->main = function($App, $next) use(&$Db) {
		// Проверка авторизации
		if(!$App->auth->user['address'] && !preg_match('~^/(auth|a)/~i', $App->path)) {
			$App->redirect('/auth/'.($App->path != '/' ? '?r='.urldecode($App->path) : ''));
		}

		module_extend('app', [
			'layout' => $this
		]);

		$next();
	};
	
	// Рендер функция
	$this->render = function($name, $data = [], $params = []) use(&$App, &$Db) {
		if(!$App->is_ajax) {
			echo $this->minifyHTML($App->render(__DIR__.'/views/'.($params['view'] ?: 'layout'), array_merge([
				'body' => $App->render($name, $data, true)
			], $params), true));
		}
		else echo $this->minifyHTML($App->render($name, $data, true));
	};

	// Минифицировать HTML
	$this->minifyHTML = function($html) {
		return preg_replace(['/<\!\-\-([\s\S]*?)\-\->/', '/>\s+</'], ['', '><'], $html);
	};