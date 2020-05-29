<?
	defined('ROOT_DIR') or header('HTTP/1.1 404 Not Found') or header('Status: 404 Not Found') or die;

	$Fetch = module('fetch');

	// Ключ АПИ
	$this->key = '';

	// Разбить даные на масив
	$this->parseData = function($data) {
		return explode(PHP_EOL, trim(chunk_split(substr($data, 2), 64)));
	};

	// Запрос к АПИ
	$this->api = function($method, $params = []) use(&$Fetch) {
		static $query_id;

		$req = $Fetch->query('https://mainnet.infura.io/v3/'.$this->key, [
			'method' => 'POST',
			'headers' => ['Content-Type' => 'application/json'],
			'body' => json_encode([
				'jsonrpc' => '2.0',
				'id' => ++$query_id,
				'method' => $method,
				'params' => (array)$params
			])
		]);

		$res = stripos($req['headers']['Content-Type'], 'text/plain') !== false ? json_decode($req['body'], true) : $req['body'];

		if(isset($res['result'])) return $res['result'];

		throw new Exception($res['error']['message'] ?: 'Infura: Bad request');
	};

	// Получить логи
	$this->getLogs = function($address, $fromBlock = 'earliest', $topics = [], $toBlock = 'latest') {
		return (array)$this->api('eth_getLogs', [['address' => $address, 'topics' => $topics, 'fromBlock' => $fromBlock, 'toBlock' => $toBlock]]);
	};

	// Запрос к адресу
	$this->call = function($to, $data, $from = '') {
		return (string)$this->api('eth_call', [['to' => $to, 'data' => $data, 'from' => $from], 'latest']);
	};