<? defined('ROOT_DIR') or header('HTTP/1.1 404 Not Found') or header('Status: 404 Not Found') or die; ?>
<!DOCTYPE html>
<html lang="<?= $this->lang->symbol; ?>">
	<head>
		<meta charset="UTF-8">
		<script>'fetch' in window && 'CSS' in window && CSS.supports('color', 'var(--)') ? '' : location.href = '/badbrowser/'</script>
		<title><?= $title ?: l('UNIXO - Personal Account'); ?></title>
		<meta name="description" content="<?= l('UNIXO - Personal Account'); ?>"/>
		<meta name="keywords" content="<?= l('UNIXO, smart-contracts, eth, ethereum'); ?>"/>
		<meta property="og:title" content="<?= l('UNIXO - Personal Account'); ?>">
		<meta property="og:description" content="<?= l('UNIXO - Personal Account'); ?>">
		<meta property="og:url" content="<?= $this->origin ?>">
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
		<link rel="stylesheet" href="/modules/site/layout/assets/common.css?<?= filemtime(__DIR__.'/../assets/common.css') ?>">
		<? foreach((array)$css as $v) echo '<link rel="stylesheet" href="'.$v.'">'; ?>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=0"/>
		<meta name="csrf" content="<?= $this->csrf ?>"/>
		<meta name="eth-contract" content="<?= module('site/service')->contract; ?>"/>
	</head>
	<body>
		<div class="layout">
			<?= $body; ?>
		</div>
        <div id="Notice"></div>
		<script src="https://cdn.jsdelivr.net/npm/vue@2.6.10/dist/vue.min.js"></script>
		<script src="/modules/site/layout/assets/common.js?<?= filemtime(__DIR__.'/../assets/common.js') ?>"></script>
		<script src="/modules/site/layout/assets/notice.js?<?= filemtime(__DIR__.'/../assets/notice.js') ?>"></script>
		<? foreach((array)$js as $v) echo '<script src="'.$v.'"></script>'; ?>
	</body>
</html>