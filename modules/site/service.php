<?
	defined('ROOT_DIR') or header('HTTP/1.1 404 Not Found') or header('Status: 404 Not Found') or die;

	$Db = module('db');
	$Infura = module('infura');

	$this->contract = '';

	// Синхронизироваться с смартконтрактом
	$this->syncWithContract = function($App, $data = null) use(&$Db, &$Infura) {
		$types = [
			'0x788c06d2405ae89dd3f0528d38be7691289474d72176408bc2c2406dc5e342f1' => 'regLevelEvent',
			'0x9ea70f0eb33d898c3336ecf2c0e3cf1c0195c13ad3fbcb34447777dbfd5ff2d0' => 'buyLevelEvent',
			//'' => 'prolongateLevelEvent',
			'0xce7dc747411ac40191c5335943fcc79d8c2d8c01ca5ae83d9fed160409fa6120' => 'getMoneyForLevelEvent',
			'0x7df0f6bac5c770af7783500bb7f1c0d073adb11316004ba6f9f6c704af1a1aea' => 'lostMoneyForLevelEvent',
		];

		$block = (int)$Db->storage('syncWithContract.lastBlock');
		
		try {
			foreach($Infura->getLogs($this->contract, '0x'.dechex($block - 10), [], '0x'.dechex($block + 100000)) as $v) {
				if(($b = hexdec(substr($v['blockNumber'], 2))) > $block) {
					$block = $b;
				}

				if($type = $types[$v['topics'][0]]) {
					$data1 = hexdec(substr($v['data'], 2, 64));
					$data2 = hexdec(substr($v['data'], 66, 64));

					$Db->insert('sk_events', [
						'tx' => $v['transactionHash'],
						'index' => hexdec(substr($v['logIndex'], 2)),
						'type' => $type,
						'user' => '0x'.($user = substr($v['topics'][1], 26)),
						'ref' => (($ref = substr($v['topics'][2], 26)) ? '0x'.$ref : ''),
						'level' => $type == 'regLevelEvent' ? 0 : $data1,
						'time' => $type == 'regLevelEvent' ? $data1 : $data2
					]);

					if($type == 'regLevelEvent' && !$Db->val("SELECT `id` FROM `users` WHERE `address` = ?", ['0x'.$user])) {
						if(($data = array_map('hexdec', $Infura->parseData($Infura->call($this->contract, '0xa87430ba000000000000000000000000'.$user, $this->contract)))) && $data[1] > 0) {
							$Db->insert('users', [
								'id' => $data[1],
								'pid' => $data[2],
								'address' => '0x'.$user
							]);
						}
					}
				}
			}

			$Db->storage('syncWithContract.lastBlock', $block);
		}
		catch(Exception $e) {
			$Db->storage('syncWithContract.error', $e->getMessage(), time());
		}

		foreach($Db->rows("SELECT `user` FROM `sk_events` WHERE `type` = 'regLevelEvent' AND `user` NOT IN(SELECT `address` FROM `users`) LIMIT 5", [], PDO::FETCH_COLUMN) as $addr) {
			if(($data = array_map('hexdec', $Infura->parseData($Infura->call($this->contract, '0xa87430ba000000000000000000000000'.substr($addr, 2), $this->contract)))) && $data[1] > 0) {
				$Db->insert('users', [
					'id' => $data[1],
					'pid' => $data[2],
					'address' => $addr
				]);
			}
		}
	};