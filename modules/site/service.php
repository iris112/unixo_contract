<?
	defined('ROOT_DIR') or header('HTTP/1.1 404 Not Found') or header('Status: 404 Not Found') or die;

	$Db = module('db');
	$Infura = module('infura');

	$this->contract = '';
	$this->contract_deploy_block = 0;

	// Парсить лог
	$this->parseLog = function($log) use(&$Db, &$Infura) {
		$time = time();
		$data = $Infura->parseData($log['data']);

    	// event Register(address indexed addr, address indexed upline, uint256 id);
		if($log['topics'][0] == '0xcc0bec1447060c88cdc5a739cf29cfa26c453574dd3f5b9e4dcc317d6401cb1c') {
			$Db->insert('sk_events', [
				'tx' => $log['transactionHash'],
				'index' => hexdec(substr($log['logIndex'], 2)),
				'type' => 'Register',
				'user' => '0x'.substr($log['topics'][1], 26),
				'ref' => '0x'.substr($log['topics'][2], 26),
				'level' => 1,
				'time' => $time
			]);

			$Db->insert('users', [
				'id' => hexdec($data[0]),
				'pid' => $Db->val("SELECT `id` FROM `users` WHERE `address` = ?", ['0x'.substr($log['topics'][2], 26)]),
				'address' => '0x'.substr($log['topics'][1], 26),
			]);
		}
	    // event UpLevel(address indexed addr, uint8 level, uint40 expires);
		else if($log['topics'][0] == '0x47e75f266c9bc08e80aafb7c86af9dd1dfcaef630293a3960eb6710720adbb1d') {
			$Db->insert('sk_events', [
				'tx' => $log['transactionHash'],
				'index' => hexdec(substr($log['logIndex'], 2)),
				'type' => 'UpLevel',
				'user' => '0x'.substr($log['topics'][1], 26),
				'level' => hexdec($data[0]) + 1,
				'time' => $time
			]);
		}
	    // event Profit(address indexed addr, address indexed referral, uint256 value);
		else if($log['topics'][0] == '0x927ca72beeafa042127c9b97483d6b6f5ada2790237a7b3310232cab8888ac27') {
			$Db->insert('sk_events', [
				'tx' => $log['transactionHash'],
				'index' => hexdec(substr($log['logIndex'], 2)),
				'type' => 'Profit',
				'user' => '0x'.substr($log['topics'][1], 26),
				'ref' => '0x'.substr($log['topics'][2], 26),
				'value' => hexdec($data[0]) / 1e18,
				'time' => $time
			]);
		}
	    // event Lost(address indexed addr, address indexed referral, uint256 value);
		else if($log['topics'][0] == '0xb38c5ffc8559f83ecf23d55719cd798bf510f15e45ad81bdc0b0026fa9d7311f') {
			$Db->insert('sk_events', [
				'tx' => $log['transactionHash'],
				'index' => hexdec(substr($log['logIndex'], 2)),
				'type' => 'Lost',
				'user' => '0x'.substr($log['topics'][1], 26),
				'ref' => '0x'.substr($log['topics'][2], 26),
				'value' => hexdec($data[0]) / 1e18,
				'time' => $time
			]);
		}
	};

	// Синхронизироваться с смартконтрактом
	$this->syncWithContract = function($App, $data = null) use(&$Db, &$Infura) {
		if($App->params['tx']) {
			try {
				$tx = $Infura->api('eth_getTransactionReceipt', [$App->params['tx']]);

				foreach((array)$tx['logs'] as $log) $this->parseLog($log);

				$App->json(['status' => !!hexdec($tx['status'])]);
			}
			catch(Exception $e) {
				$App->json(['status' => false]);
			}
		}
		else {
			$block = (int)$Db->storage('syncWithContract.lastBlock') ?: $this->contract_deploy_block;
			
			try {
				foreach($Infura->getLogs($this->contract, '0x'.dechex($block - 10), [], '0x'.dechex($block + 100000)) as $log) {
					if(($b = hexdec(substr($log['blockNumber'], 2))) > $block) {
						$block = $b;
					}

					$this->parseLog($log);
				}

				$Db->storage('syncWithContract.lastBlock', $block);
			}
			catch(Exception $e) {
				$Db->storage('syncWithContract.error', $e->getMessage(), time());
			}
		}
	};