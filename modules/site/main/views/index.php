<? defined('ROOT_DIR') or header('HTTP/1.1 404 Not Found') or header('Status: 404 Not Found') or die; ?>
<div class="page">
	<h1 class="page__title"><?= l('Кабинет'); ?></h1>
	<div class="page__body">
		<div class="info-blocks">
			<div class="info-blocks__item">
				<div class="info-blocks__label"><?= l('Заработано Ethereum'); ?></div>
				<img src="/modules/site/main/assets/info-block1.png" class="info-blocks__icon">
				<div class="info-blocks__footer">
					<div class="info-blocks__value"><?= round($profit_eth, 2) ?> ETH</div>
				</div>
			</div>
			<div class="info-blocks__item">
				<div class="info-blocks__label"><?= l('Заработано долларов'); ?></div>
				<img src="/modules/site/main/assets/info-block2.png" class="info-blocks__icon">
				<div class="info-blocks__footer">
					<div class="info-blocks__value"><?= round($profit_usd, 2) ?> USD</div>
				</div>
			</div>
			<div class="info-blocks__item">
				<div class="info-blocks__label"><?= l('Партнеры'); ?><br/>&nbsp;</div>
				<img src="/modules/site/main/assets/info-block3.png" class="info-blocks__icon">
				<div class="info-blocks__footer">
					<div class="info-blocks__value"><?= $referrals ?></div>
					<div class="social">
						<? $share = module('site/main')->getShare(); ?>
						<a href="https://t.me/share/url?text=<?= urlencode($share['msg']) ?>&url=<?= urlencode($share['url']) ?>" target="_blank"><i class="fab fa-fw fa-telegram-plane"></i></a>
						<a href="https://twitter.com/share?text=<?= urlencode($share['msg']) ?>&url=<?= urlencode($share['url']) ?>" target="_blank"><i class="fab fa-fw fa-twitter"></i></a>
						<a href="https://vk.com/share.php?title=<?= urlencode($share['msg']) ?>&url=<?= urlencode($share['url']) ?>" target="_blank"><i class="fab fa-fw fa-vk"></i></a>
						<a href="https://www.facebook.com/sharer.php?t=<?= urlencode($share['msg']) ?>&u=<?= urlencode($share['url']) ?>" target="_blank"><i class="fab fa-fw fa-facebook"></i></a>
					</div>
				</div>
			</div>
			<div class="info-blocks__item">
				<div class="info-blocks__label"><?= l('Текущий уровень'); ?></div>
				<img src="/modules/site/main/assets/info-block4.png" class="info-blocks__icon">
				<div class="info-blocks__footer">
					<div class="info-blocks__value"><?= ($t = @max(array_keys($my_levels))) ?: 0; ?> <?= l('Уровень'); ?></div>
					<? if($t < 10) : ?>
						<div class="info-blocks__more">
							<!--<button class="btn btn_transparent small" onclick="buyLevel(<?= $t + 1; ?>, <?= $levels[$t]['price'] ?>)"><?= l('Поднять'); ?></button>
							<button class="btn btn_transparent small" onclick="buyLevel(1, <?= $levels[0]['price'] ?>)"><?= l('Продлить'); ?></button>-->
						</div>
					<? endif; ?>
				</div>
			</div>
		</div>
		<div class="info-charts">
			<div class="block">
				<div class="block__title"><?= l('Рост структуры'); ?></div>
				<div class="block__body">
					<canvas data-charts='{"type":"line","data":{"datasets":[{"label":"Referrals","data":<?= json_encode(array_values($referrals_by_date)) ?>,"backgroundColor":"#2196F3"}],"labels":<?= json_encode(array_keys($referrals_by_date)) ?>},"options":{}}'></canvas>
				</div>
			</div>
			<div class="block">
				<div class="block__title"><?= l('Заработок ETH по уровням'); ?></div>
				<div class="block__body">
					<canvas height="150" data-charts='{"type":"pie","data":{"datasets":[{"data":<?= json_encode(array_map(function($v) { return round($v, 2); }, array_values($profit_by_levels))) ?>,"backgroundColor":["#2196F3","#004172","#4caf50","#e53935","#fb8c00"]}],"labels":<?= json_encode(array_map(function($v) { return 'Level '.$v; }, array_keys($profit_by_levels))) ?>},"options":{}}'></canvas>
				</div>
			</div>
		</div>
		<ul class="levels">
			<? foreach($levels as $v) : ?>
				<li<?= ($a = $my_levels[$v['id']] > time()) ? ' class="active"' : '' ?>>
					<div class="levels__name"><?= $v['id'] ?> <?= l('Уровень'); ?></div>
					<? if($a) : ?>
						<i class="fa fa-check-circle levels__check"></i>
						<div class="levels__date"><b><?= l('Активен'); ?>:</b> <?= floor(($my_levels[$v['id']] - time()) / 86400) ?> <?= l('дн.'); ?></div>
					<? else : ?>
						<div class="levels__date"><?= l('Неактивно'); ?></div>
					<? endif; ?>
					<div class="levels__price"><?= $v['price'] ?> ETH</div>
					<!--<? if($a) : ?>
						<button class="levels__btn btn btn_primary btn_transparent small" onclick="buyLevel(<?= $v['id'] ?>, <?= $v['price'] ?>)"><?= l('Продлить'); ?></button>
					<? elseif($v['id'] == 1 && $my_levels[$v['id']] == 0) : ?>
						<button class="levels__btn btn btn_primary small" disabled><?= l('Pending..'); ?></button>
					<? else : ?>
						<button class="levels__btn btn btn_primary small" onclick="buyLevel(<?= $v['id'] ?>, <?= $v['price'] ?>)"><?= l('Купить'); ?></button>
					<? endif; ?>-->
				</li>
			<? endforeach; ?>
		</ul>
	</div>
</div>