<?
	defined('ROOT_DIR') or header('HTTP/1.1 404 Not Found') or header('Status: 404 Not Found') or die;

	$Db = module('db');

	// Поиск свободного реферала
	$this->findFreeReferrer = function($id) use(&$Db) {
		if(count($list = $Db->rows("SELECT `id` FROM `users` WHERE `pid` = ? LIMIT 3", [$id], PDO::FETCH_COLUMN)) < 3) {
			return $id;
		}

        for($i = 0; $i < count($list); $i++) {
        	if(count($refs = $Db->rows("SELECT `id` FROM `users` WHERE `pid` = ? LIMIT 3", [$list[$i]], PDO::FETCH_COLUMN)) < 3) {
        		return $list[$i];
        	}

            $list[$n = ($i + 1) * 3] = $refs[0];
            $list[$n + 1] = $refs[1];
            $list[$n + 2] = $refs[2];
		}
	};

	// Вывод главной
	$this->index = function($App, $next) use(&$Db) {
		if(preg_match('/^0x[a-f0-9]{40}$/i', $address = $App->post['address'] ?: $App->get['regis'])) {
			if($Db->val("SELECT COUNT(*) FROM `sk_events` WHERE `user` = ?", [$address])) {
				$App->cookie('eth_address', $address);
				$App->redirect(preg_match('~^(/[^/]|\?)~', $r = $App->get['r']) ? $r : '/');
			}
			else if(!$App->get['regis']) $App->redirect('/auth/?regis='.$address);
			else $regis = true;
		}

		$App->layout->render(__DIR__.'/views/index', [
			'address' => $address,
			'regis' => $regis,
			'upline' => ($t = 0),//($t = $App->cookie('upline')),
			'upline_address' => preg_match('/^[0-9]+$/', $t) ? $Db->val("SELECT `address` FROM `users` WHERE `id` = ?", [$t]) : $t
		], [
			'view' => 'empty',
			'js' => ['/modules/site/auth/assets/common.js?'.filemtime(__DIR__.'/assets/common.js')],
			'css' => ['/modules/site/auth/assets/common.css?'.filemtime(__DIR__.'/assets/common.css')]
		]);
	};

	// Поиск свободного реферала
	$this->freeReferrer = function($App, $next) use(&$Db) {
		$App->json([
			'success' => true,
			'id' => $this->findFreeReferrer(abs((int)$App->params['id'] ?: 1)) ?: 1
		]);
	};

	// Переход по реф. ссылке
	$this->rlink = function($App, $next) use(&$Db) {
		if(preg_match('/^(0x[a-f0-9]{40}|[0-9]+)$/i', $t = $App->params['id'])) {
			$App->cookie('upline', $t);
		}
		
		$App->redirect('/auth/');
	};

	// Выход
	$this->logout = function($App, $next) use(&$Db) {
		$App->cookie('eth_address', null);
		$App->redirect('/auth/');
	};