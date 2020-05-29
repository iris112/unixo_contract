<?
	defined('ROOT_DIR') or header('HTTP/1.1 404 Not Found') or header('Status: 404 Not Found') or die;

	// Запрос
	$this->query = function($url, array $options = [], array $args = []) {
		curl_setopt_array($ch = curl_init(), [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER => true,
			CURLOPT_AUTOREFERER => true,
			CURLOPT_FOLLOWLOCATION => isset($options['redirect']) ? $options['redirect'] : true,
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_CUSTOMREQUEST => ($method = strtoupper(is_string($options['method']) ? $options['method'] : ($options['body'] ? 'POST' : 'GET'))),
			CURLOPT_POSTFIELDS => ($options['body'] ? (is_array($options['body']) ? ($method == 'PUT' ? json_encode($options['body']) : http_build_query($options['body'])) : $options['body']) : null),
			CURLOPT_SSL_VERIFYPEER => 1,
			CURLOPT_SSL_VERIFYHOST => 2,
			CURLOPT_FORBID_REUSE => 1,
			CURLOPT_HTTPHEADER => array_map(function($k, $v) { return $k.': '.$v; }, array_keys((array)$options['headers']), (array)$options['headers']),
			CURLOPT_COOKIE => http_build_query($cookie = (array)$options['cookie'], '', '; '),
			CURLOPT_USERAGENT => $options['user_agent'] ?: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36',
			CURLOPT_REFERER => $options['referrer']
		] + $args);

		$content = curl_exec($ch);
		$info = curl_getinfo($ch);

		$head = substr($content, 0, $info['header_size']);
		$body = substr($content, $info['header_size']);

		curl_close($ch);

	    foreach(explode(PHP_EOL, $head) as $v) {
	        if(preg_match('/Set\-Cookie: ([^\=]+)=([^;]+)/', $v, $m)) $cookie[$m[1]] = $m[2];
	        else if(preg_match('/(.+?): (.+)/', $v, $m)) $headers[$m[1]] = $m[2];
	    }

		return [
			'status' => (int)$info['http_code'],
			'ok' => $info['http_code'] >= 200 && $info['http_code'] < 300,
			'headers' => (array)$headers,
			'cookie' => $cookie,
			'body' => stripos($headers['Content-Type'], 'application/json') !== false ? json_decode($body, true) : $body
		];
	};