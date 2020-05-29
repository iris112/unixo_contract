<?
	defined('ROOT_DIR') or header('HTTP/1.1 404 Not Found') or header('Status: 404 Not Found') or die;

	return [
		'db' => [
			'connect' => (DEBUG ? ['localhost', 'account.unixo.io', 'root', ''] : ['localhost', 'account.unixo.io', 'unixo', 'szekUqRtzBkvzJ6Z'])
		],
		'app' => [
			'route' => [
				[
					['/cron/', 'cron:index'],
					['/sync/', 'site/service:syncWithContract'],
					['/badbrowser/', function($App) { $App->error(418); }],
					['/autotranslate/', function($App) { module('lang')->translateAllKeysForAllLangs(); }],
					['/statInfo/', 'site/main:statInfo'],
					['/langs/', 'site/langs:index'],
					['*', 'lang:main'],
					['*', 'auth:main'],
					['*', 'site/layout:main'],
					['/', 'site/main:index'],
					['/auth/', 'site/auth:index'],
					['/auth/logout/', 'site/auth:logout'],
					['/auth/freeReferrer/:id/', 'site/auth:freeReferrer'],
					['/a/:id/', 'site/auth:rlink'],
					['/partners/', 'site/partners:index'],
					['/partners/:id/', 'site/partners:item'],
					['/partners/info/:id/', 'site/partners:getPartnerInfo'],
					['/uplines/', 'site/uplines:index'],
					['/lost/', 'site/lost:index'],
				]
			]
		],
		'cron' => [
			'task' => [
				[
					['* * * * *', 'site/service:syncWithContract']
				]
			]
		],
		'infura' => [
			'network' => 'mainnet',
			'key' => '4db4c898fc1d4346acc5c2d80967ee40'
		],
		'site/service' => [
			'contract_deploy_block' => 9966745,
			'contract' => '0x015496890621576deffaE01dB042106B23D76062'
		]
	];