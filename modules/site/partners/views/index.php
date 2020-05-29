<? defined('ROOT_DIR') or header('HTTP/1.1 404 Not Found') or header('Status: 404 Not Found') or die; ?>
<div class="page" id="Partner">
	<h1 class="page__title"><?= l('Партнеры'); ?></h1>
	<div class="page__body">
		<!--
		<div class="partner">
			<div class="block">
				<div class="block__title"><?= l('Ваша партнерская ссылка'); ?></div>
				<div class="block__body">
					<? $share = module('site/main')->getShare(); ?>
					<div class="partner__link">
						<input class="inp" readonly id="rlink" value="<?= $share['url'] ?>">
						<button class="btn btn_primary" onclick="copyText(document.getElementById('rlink').value)"><?= l('Копировать'); ?></button>
					</div>
					<div class="social">
						<a href="https://t.me/share/url?text=<?= ($share['msg']) ?>&url=<?= urlencode($share['url']) ?>" target="_blank"><i class="fab fa-fw fa-telegram-plane"></i></a>
						<a href="https://twitter.com/share?text=<?= urlencode($share['msg']) ?>&url=<?= urlencode($share['url']) ?>" target="_blank"><i class="fab fa-fw fa-twitter"></i></a>
						<a href="https://vk.com/share.php?title=<?= urlencode($share['msg']) ?>&url=<?= urlencode($share['url']) ?>" target="_blank"><i class="fab fa-fw fa-vk"></i></a>
						<a href="https://www.facebook.com/sharer.php?t=<?= urlencode($share['msg']) ?>&u=<?= urlencode($share['url']) ?>" target="_blank"><i class="fab fa-fw fa-facebook"></i></a>
					</div>
				</div>
			</div>
			<div class="block" v-cloak>
				<div class="block__title"><?= l('Данные о партнере'); ?></div>
				<div class="block__body">
					<div class="partner__link">
						<input class="inp" v-model="addr" placeholder="<?= l('ID или адрес кошелька партнера') ?>">
						<button class="btn btn_primary" @click="getUserInfo"><?= l('Показать'); ?></button>
					</div>
					<div class="partner__info" v-if="user.id > 0"><?= l('ID') ?>: <b>{{ user.id }}</b> &nbsp;&nbsp;&nbsp; <?= l('Уровень') ?>: <b>{{ parseInt(user.level) || 1 }}</b> &nbsp;&nbsp;&nbsp; <?= l('Адрес') ?>: {{ user.address }} <a :href="'https://etherscan.io/address/' + user.address" target="_blank"><i class="fa fa-external-link-alt"></i></a></div>
				</div>
			</div>
		</div>
		-->
		<? if($this->auth->user['id'] > 0) : ?>
			<div class="block block_mt30">
				<div class="block__title"><?= l('Ваша структура'); ?></div>
				<div class="block__body">
					<a href="#" onclick="toggleTree(); return false;"><?= l('Развернуть\свернуть всё'); ?></a>
					<div id="tree" data-tree='<?= json_encode($tree); ?>'></div>
				</div>
			</div>
		<? endif; ?>
		<form method="GET" class="search search_mt30">
			<input placeholder="<?= l('Поиск'); ?>" name="q" value="<?= htmlspecialchars($pag['search']['q']); ?>">
			<button type="submit"><i class="fa fa-search"></i></button>
		</form>
		<div class="block block_mt10">
			<div class="block__title"><?= l('Полученные переводы'); ?></div>
			<? if(count($repayments)) : ?>
				<table class="table table_fullw">
					<tr>
						<th><a href="?<?= http_build_query(array_merge($pag['search'], ['s' => 'time', 'r' => $pag['search']['s'] == 'time' && !$pag['search']['r'] ? '1' : '0'])) ?>" class="sort<?= $pag['search']['s'] == 'time' ? ($pag['search']['r'] ? ' active reverse' : ' active') : '' ?>"><?= l('Дата'); ?></a></th>
						<th><?= l('От кого'); ?></th>
						<th><?= l('ID'); ?></th>
						<th><a href="?<?= http_build_query(array_merge($pag['search'], ['s' => 'amount', 'r' => $pag['search']['s'] == 'amount' && !$pag['search']['r'] ? '1' : '0'])) ?>" class="sort<?= $pag['search']['s'] == 'amount' ? ($pag['search']['r'] ? ' active reverse' : ' active') : '' ?>"><?= l('Сумма ETH'); ?></a></th>
						<th class="@m"><?= l('Сумма USD'); ?></th>
					</tr>
					<? foreach($repayments as $v) : ?>
						<tr>
							<td><?= date('d.m H:i', $v['time']) ?></td>
							<td><a href="https://etherscan.io/address/<?= $v['address'] ?>" target="_blank" class="addr"><?= $v['address'] ?></a></td>
							<td><?= $v['id'] ?></td>
							<td><?= round($v['amount'], 2) ?></td>
							<td class="@m"><?= round($v['amount'] * $rates['eth'], 2) ?></td>
						</tr>
					<? endforeach; ?>
				</table>
				<? if($pag['html']) : ?><div class="block__body"><?= $pag['html']; ?></div><? endif; ?>
			<? else : ?>
				<div class="cap"><?= l('Переводов пока нет'); ?></div>
			<? endif; ?>
		</div>
	</div>
</div>