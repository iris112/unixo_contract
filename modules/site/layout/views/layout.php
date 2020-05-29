<? defined('ROOT_DIR') or header('HTTP/1.1 404 Not Found') or header('Status: 404 Not Found') or die; ?>
<!DOCTYPE html>
<html lang="<?= $this->lang->symbol; ?>">
	<head>
		<meta charset="UTF-8">
		<script>'fetch' in window && 'CSS' in window && CSS.supports('color', 'var(--)') ? '' : location.href = '/badbrowser/'</script>
		<title><?= $title ?: l('ETHRUN - Personal Account'); ?></title>
		<meta name="description" content="<?= l('ETHRUN - Personal Account'); ?>"/>
		<meta name="keywords" content="<?= l('ETHRUN, smart-contracts, eth, ethereum'); ?>"/>
		<meta property="og:title" content="<?= l('ETHRUN - Personal Account'); ?>">
		<meta property="og:description" content="<?= l('ETHRUN - Personal Account'); ?>">
		<meta property="og:url" content="<?= $this->origin ?>">
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
		<link rel="stylesheet" href="/modules/site/layout/assets/common.css?<?= filemtime(__DIR__.'/../assets/common.css') ?>">
		<? foreach((array)$css as $v) echo '<link rel="stylesheet" href="'.$v.'">'; ?>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=0"/>
		<meta name="csrf" content="<?= $this->csrf ?>"/>
		<meta name="eth-contract" content="<?= module('site/service')->contract; ?>"/>
		<meta name="eth-user" content="<?= $this->auth->user['address'] ?>"/>
	</head>
	<body>
		<div class="layout">
			<div class="aside">
				<a class="aside__logo" href="https://ethrun.io/"><img src="/modules/site/layout/assets/logo.svg?3"></a>
				<div class="aside__nav">
					<label><?= l('Меню аккаунта'); ?></label>
					<a href="/"<?= $this->path == '/' ? ' class="active"' : '' ?>><i class="fa fa-fw fa-user-circle"></i> <span><?= l('Панель управления'); ?></span></a>
					<a href="/partners/"<?= $this->path == '/partners/' ? ' class="active"' : '' ?>><i class="fa fa-fw fa-network-wired"></i> <span><?= l('Партнеры'); ?></span></a>
					<a href="/uplines/"<?= $this->path == '/uplines/' ? ' class="active"' : '' ?>><i class="fa fa-fw fa-users"></i> <span><?= l('Аплайны'); ?></span></a>
					<a href="/lost/"<?= $this->path == '/lost/' ? ' class="active"' : '' ?>><i class="fa fa-fw fa-search-dollar"></i> <span><?= l('Утерянные переводы'); ?></span></a>
					<a href="/auth/logout/"><i class="fa fa-fw fa-sign-out-alt"></i> <span><?= l('Выход'); ?></span></a>
				</div>
				<div class="aside__copyright">© <?= date('Y'); ?> Ethrun.io</div>
			</div>
			<div class="main">
				<div class="header">
					<? if($this->auth->user['id']) : ?>
						<div class="header__block @m">
							<div class="header__label"><?= l('Ваш ID в системе'); ?></div>
							<div class="header__value primary"><i class="far fa-id-card"></i> <?= $this->auth->user['id'] ?></div>
						</div>
					<? endif; ?>
					<div class="header__block header__block_grow">
						<div class="header__label"><?= l('Ваш Ethereum кошелек'); ?></div>
						<div class="header__value primary"><i class="fab fa-ethereum"></i> <span class="addr"><?= $this->auth->user['address'] ?></span></div>
					</div>
					<div class="header__block">
						<div class="header__label"><?= l('Язык системы'); ?></div>
						<div class="header__value">
							<div class="lang">
								<span><img src="https://api.smartcontract.ru/cdn/flags/<?= $this->lang->symbol; ?>.svg" width="20"> <?= $this->lang->name; ?></span>
								<div>
									<? foreach($this->lang->languages as $k => $v) : ?>
										<a href="?lang=<?= $k ?>"><span><img src="https://api.smartcontract.ru/cdn/flags/<?= $k ?>.svg" width="20"> <?= $v ?></span></a>
									<? endforeach; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="content">
					<?= $body; ?>
				</div>
				<div class="footer">
					<div>
						<?= l('Адрес смарт-контракта:'); ?><br/>
						<a href="https://etherscan.io/address/<?= module('site/service')->contract; ?>" target="_blank" class="addr"><?= module('site/service')->contract; ?></a>&nbsp;&nbsp;
						<a href="#" onclick="copyText('<?= module('site/service')->contract; ?>'); return false;"><i class="fa fa-copy"></i></a>
					</div>
				</div>
			</div>
		</div>
        <div id="Notice"></div>
		<script src="https://cdn.jsdelivr.net/npm/vue@2.6.10/dist/vue.min.js"></script>
		<script src="/modules/site/layout/assets/common.js?<?= filemtime(__DIR__.'/../assets/common.js') ?>"></script>
		<script src="/modules/site/layout/assets/notice.js?<?= filemtime(__DIR__.'/../assets/notice.js') ?>"></script>
		<? foreach((array)$js as $v) echo '<script src="'.$v.'"></script>'; ?>
	</body>
</html>