<? defined('ROOT_DIR') or header('HTTP/1.1 404 Not Found') or header('Status: 404 Not Found') or die; ?>
<div class="page">
	<h1 class="page__title"><?= l('Аплайны'); ?></h1>
	<div class="page__body">
		<div class="block">
			<? if(count($users)) : ?>
				<table class="table table_fullw">
					<tr>
						<th><?= l('Линия'); ?></th>
						<th><?= l('ID'); ?></th>
						<th><?= l('Кошелек'); ?></th>
						<th><?= l('Уровень'); ?></th>
					</tr>
					<? foreach($users as $v) : ?>
						<tr>
							<td><?= $v['deep'] ?></td>
							<td><?= $v['id'] ?></td>
							<td><a href="https://etherscan.io/address/<?= $v['address'] ?>" target="_blank" class="addr"><?= $v['address'] ?></a></td>
							<td><?= $v['level'] ?: 1 ?></td>
						</tr>
					<? endforeach; ?>
				</table>
			<? else : ?>
				<div class="cap"><?= l('Аплайны пока нет'); ?></div>
			<? endif; ?>
		</div>
	</div>
</div>