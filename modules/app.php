<?
	defined('ROOT_DIR') or header('HTTP/1.1 404 Not Found') or header('Status: 404 Not Found') or die;

	$this->time = $_SERVER['REQUEST_TIME_FLOAT'] ?: microtime(true);
	$this->host = $_SERVER['SERVER_NAME'];
	$this->proto = $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || $_SERVER['HTTPS'] == 'on' || stripos($_SERVER['HTTP_CF_VISITOR'], '"scheme":"https"') !== false ? 'https' : 'http';
	$this->method = $_SERVER['REQUEST_METHOD'];
	$this->origin = $this->proto.'://'.$this->host.'/';
	$this->path = parse_url($_SERVER['REQUEST_URI'])['path'];
	$this->url = rtrim($this->origin, '/').$this->path;
	$this->get = $_GET;
	$this->post = $_POST;
	$this->data = (array)json_decode(file_get_contents('php://input'), true);
	$this->params = [];
	$this->is_ajax = strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ? true : false;
	$this->user_ip = $_SERVER['REMOTE_ADDR'] && $_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR'] ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_FORWARDED_FOR'];
	$this->user_agent = $_SERVER['HTTP_USER_AGENT'];
	$this->user_lang = strtolower($_SERVER['GEOIP_COUNTRY_CODE'] ?: (preg_match('/^([a-z]{2}),/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $m) ? $m[1] : ''));

	$routes = [];

	// Отлов ошибок
	set_exception_handler(function($e) {
		$this->error(500, null, $e->getMessage());
	});

	// Точка входа (роутинг)
	$this->main = function() use(&$routes) {
		$index = -1;
		$next = function() use(&$next, &$routes, &$index) {
			if($routes[++$index][0]) {
				if(
					(!$routes[$index][2] || $routes[$index][2] == $this->method)
					&& preg_match('/^'.preg_replace(['/\\\:([a-z0-9_]+)/i', '/\\\\\*/'], ['(?<\1>[^\/]+)', '(.*?)'], preg_quote($routes[$index][0], '/')).'$/', $this->path, $params)
				) {
					$this->params = $params;

					if(is_string($routes[$index][1]) && ($act = explode(':', $routes[$index][1])) && module_exists($act[0]) && ($md = module($act[0]))) {
						if(is_callable($t = $md->{$act[1]})) $t($this, $next);
						else $next();
					}
					else if(is_callable($routes[$index][1])) $routes[$index][1]($this, $next);
					else $next();
				}
				else $next();
			}
			else $this->error();
		};
		$next();
	};

	// Добавить запись в роутер
	$this->route = function($url, $action = '', $method = '') use(&$routes) {
		if(is_array($url)) foreach($url as $v) $this->route($v[0], $v[1], $v[2]);
		else $routes[] = [$url, $action, $method];
		return $this;
	};

	// Отрисовать шаблон
	$this->render = function($name, $data = [], $get = false) {
		if(file_exists($file = $name) || file_exists($file = $name.'.php')) {
			if($get) ob_start();
			$fn = function($__file, $data) { extract($data); include($__file); };
			$fn($file, (array)$data);
			if($get) return ob_get_clean();
		}
		else throw new Exception("View '".$name."' not found");
	};

	// Вывести JSON
	$this->json = function($data, $get = false) {
		if(!$get) {
			header('Content-Type: application/json; charset=utf-8');
			exit(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | (DEBUG ? JSON_PRETTY_PRINT : 0)));
		}
		else return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | (DEBUG ? JSON_PRETTY_PRINT : 0));
	};

	// Выкинуть ошибку
	$this->error = function($code = 404, $title = '', $message = '') {
		$codes = [
			400 => ['400 Bad Request', 'Invalid request', 'Service cannot process your request, try changing the parameters'],
			401 => ['401 Unauthorized', 'You must log in', 'To perform this action, you must log in'],
			404 => ['404 Not Found', 'Page not found', "Unfortunately, such a page does not exist.\nProbably, it was deleted, or it never was here"],
			418 => ['418 I’m a teapot', 'Bad browser', "To use all the features of the site, download and install one of these browsers:\n\n<style>a{display:inline-block;padding:5px 15px;border-radius:3px;color:#fff;background:rgba(0,0,0,0.4);text-decoration:none;margin:3px 5px;}</style><a href=https://www.google.com/chrome/>Google Chrome</a><a href=https://www.mozilla.org/>Mozilla Firefox</a>\n<a href=https://www.microsoft.com/EN-US/windows/microsoft-edge>Microsoft Edge</a><a href=https://www.opera.com/>Opera</a>\n<a href=https://browser.yandex.com/>Yandex browser</a>"],
			500 => ['500 Internal Server Error', 'Server Error', 'Sorry, an error occurred while executing the request, try to repeat the request'],
			503 => ['503 Service Unavailable', 'Service Unavailable', 'Unfortunately, the service is currently unavailable'],
		];

		if(!isset($codes[$code])) $code = 404;

		header('HTTP/1.1 '.$codes[$code][0]);
		header('Status: '.$codes[$code][0]);

		exit('<!DOCTYPE html><html lang="ru"><head><meta charset="utf-8"/><link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400"/></head><body style="height:100%;color:#343f4b;font:15px/1.5 \'Open Sans\',Roboto,sans-serif;margin:0;padding:0;"><svg style="position:fixed;left:0;top:0;width:100%;height:100%;" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 640 640" width="100%" height="100%"><path d="M-40.39 -16.05L341.54 -202.48L593.94 314.59L212.01 501.02L-40.39 -16.05Z" fill="#dfe9ee"></path><path d="M-63.58 562.19L659.29 1.91L968.82 401.26L245.94 961.54L-63.58 562.19Z" fill="#2a88a8"></path><path d="M-5.29 58.64L594.49 667.25L364.22 894.18L-235.55 285.57L-5.29 58.64Z" fill="#f5f7f8"></path><path d="M-133.43 308.87L939.54 581.12L822.87 1040.97L-250.11 768.72L-133.43 308.87Z" fill="#a4b9c4"></path></svg><div style="position:relative;padding:100px 0 0;margin:0 auto;max-width:960px;text-align:center;"><div style="font-size:128px;">:(</div><h1 style="font-weight:normal;">'.(is_string($title) && trim($title) ? $title : $codes[$code][1].' ('.$code.' error)').'</h1><p>'.(str_replace("\n", '<br/>', is_string($message) && trim($message) ? $message : $codes[$code][2])).'</p></div></body></html>');
	};
	
	// Получить\выставить куку
	$this->cookie = function($key, $val = null, $params = []) {
		if(is_string($key)) {
			if(func_num_args() > 1) return setcookie($key, $_COOKIE[$key] = $val, $params['expire'] ?: strtotime('+30 days'), $params['path'] ?: '/', $params['domain'] ?: $this->domain, $params['secure'] ?: false, $params['httponly'] ?: false);
			else if(isset($_COOKIE[$key])) return $_COOKIE[$key];
		}
		return null;
	};
	
	// Получить\выставить заголовок
	$this->header = function($key, $val = null) {
		static $headers;

		if(!$headers) $headers = apache_request_headers();

		if(is_string($key)) {
			if(func_num_args() > 1) return header($key.': '.$val);
			else if(isset($headers[$key])) return $headers[$key];
		}
		return null;
	};
	
	// Редирект
	$this->redirect = function($url) {
		$this->header('Location', $url) or die;
	};