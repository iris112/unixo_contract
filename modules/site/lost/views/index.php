<? defined('ROOT_DIR') or header('HTTP/1.1 404 Not Found') or header('Status: 404 Not Found') or die; ?>
<div class="page">
	<h1 class="page__title"><?= l('Утерянные переводы'); ?></h1>
	<div class="page__body">
		<form method="GET" class="search">
			<input placeholder="<?= l('Поиск'); ?>" name="q" value="<?= htmlspecialchars($pag['search']['q']); ?>">
			<button type="submit"><i class="fa fa-search"></i></button>
		</form>
		<div class="block block_mt10">
			<? if(count($losts)) : ?>
				<table class="table table_fullw">
					<tr>
						<th><a href="?<?= http_build_query(array_merge($pag['search'], ['s' => 'time', 'r' => $pag['search']['s'] == 'time' && !$pag['search']['r'] ? '1' : '0'])) ?>" class="sort<?= $pag['search']['s'] == 'time' ? ($pag['search']['r'] ? ' active reverse' : ' active') : '' ?>"><?= l('Дата'); ?></a></th>
						<th><?= l('Кошелек'); ?></th>
						<th class="@m"><?= l('ID'); ?></th>
						<th><a href="?<?= http_build_query(array_merge($pag['search'], ['s' => 'amount', 'r' => $pag['search']['s'] == 'amount' && !$pag['search']['r'] ? '1' : '0'])) ?>" class="sort<?= $pag['search']['s'] == 'amount' ? ($pag['search']['r'] ? ' active reverse' : ' active') : '' ?>"><?= l('Сумма ETH'); ?></a></th>
						<th class="@m"><?= l('Уровень'); ?></th>
					</tr>
					<? foreach($losts as $v) : ?>
						<tr>
							<td><?= date('d.m H:i', $v['time']) ?></td>
							<td><a href="https://etherscan.io/address/<?= $v['address'] ?>" target="_blank" class="addr"><?= $v['address'] ?></a></td>
							<td class="@m"><?= $v['id'] ?></td>
							<td><?= round($v['amount'], 2) ?></td>
							<td class="@m"><?= $v['level'] ?></td>
						</tr>
					<? endforeach; ?>
				</table>
				<? if($pag['html']) : ?><div class="block__body"><?= $pag['html']; ?></div><? endif; ?>
			<? else : ?>
				<div class="cap"><?= l('Утерянные переводы пока нет'); ?></div>
			<? endif; ?>
		</div>
	</div>
</div>