<?
	defined('ROOT_DIR') or header('HTTP/1.1 404 Not Found') or header('Status: 404 Not Found') or die;

	// Сгенерировать пагинацию
	$this->create = function($count, $page, $limit, $search = [], $def_limit = 20) {
		$page = max(0, (int)$page);
		$limit = max(1, min(500, (int)$limit ?: $def_limit));
		$max_page = max(0, floor($count / $limit) - ($count % $limit == 0 ? 1 : 0));

		if($page > $max_page) $page = $max_page;
		if($limit != $def_limit) $search['limit'] = $limit;

		if($count > $limit) {
			ob_start();

			?>
				<ul class="pagination">
					<? if($page > 0) : ?><li><a href="?<?= $search ? http_build_query($search).'&' : '' ?>p=<?= $page - 1; ?>"><?= l('Назад'); ?></a></li><? endif; ?>
					<li<?= 0 == $page ? ' class="active"' : '' ?>><a href="?<?= $search ? http_build_query($search).'&' : '' ?>p=0">1</a></li>
					<? if($page > 3) : ?><li class="pagination__separ"></li><? endif; ?>
					<? for($i = -2; $i < 3; $i++) : ?>
						<? if(($t = $page + $i) && $t > 0 && $t < $max_page) : ?>
							<li<?= $t == $page ? ' class="active"' : '' ?>><a href="?<?= $search ? http_build_query($search).'&' : '' ?>p=<?= $t; ?>"><?= $t + 1; ?></a></li>
						<? endif; ?>
					<? endfor; ?>
					<? if($page < $max_page - 3) : ?><li class="pagination__separ"></li><? endif; ?>
					<li<?= $max_page == $page ? ' class="active"' : '' ?>><a href="?<?= $search ? http_build_query($search).'&' : '' ?>p=<?= $max_page ?>"><?= $max_page + 1 ?></a></li>
					<? if($page < $max_page) : ?><li><a href="?<?= $search ? http_build_query($search).'&' : '' ?>p=<?= $page + 1; ?>"><?= l('Вперед'); ?></a></li><? endif; ?>
				</ul>
			<?

			$html = ob_get_clean();
		}

		return [
			'offset' => $page * $limit,
			'limit' => $limit,
			'page' => $page,
			'max_page' => $max_page,
			'search' => $search,
			'html' => $html
		];
	};