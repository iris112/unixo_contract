<? defined('ROOT_DIR') or header('HTTP/1.1 404 Not Found') or header('Status: 404 Not Found') or die; ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Translate</title>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.3/css/uikit.min.css" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.3/js/uikit.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.3/js/uikit-icons.min.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	</head>
	<body>
		<div class="uk-section">
		    <div class="uk-container uk-container-xsmall">
		    	<div style="position:fixed; top:0; padding:20px; background:#fff; width:750px; margin:0 -20px; border-bottom:1px solid #ddd;">
					<ul uk-tab>
						<? foreach($langs as $v) : ?>
							<li<?= $lang == $v ? ' class="uk-active"' : '' ?> onclick="location.href = '?lang=<?= $v ?>'"><a href="?lang=<?= $v ?>"><?= strtoupper($v) ?></a></li>
						<? endforeach; ?>
						<li onclick="var t = prompt('Specify a language code in ISO 639-1 format'); if(/^[a-z]{2}$/.test(t)) location.href = '?lang=' + t;"><a href="javascript:void(0)" uk-icon="icon:plus"></a></li>
					</ul>
					<input class="uk-input uk-form-large" placeholder="Search" oninput="var t = this.value.trim().toLowerCase(); document.querySelectorAll('.pair').forEach(v => { v.style.display = t.length && v.querySelector('label').innerText.toLowerCase().indexOf(t) < 0 ? 'none' : 'block'; })">
		    	</div>
				<? if($lang) : ?>
					<form method="POST" action="?lang=<?= $lang ?>" style="margin:100px 0 100px 0;">
						<input type="hidden" name="csrf" value="<?= $this->csrf ?>"/>
						<? foreach($translations_keys as $key) : ?>
							<div class="uk-margin pair">
								<label class="uk-form-label"><?= htmlspecialchars($key) ?></label>
								<div class="uk-form-controls">
									<textarea class="uk-input field" data-name="<?= substr(md5($key), 0, 8) ?>" onchange="$(this).attr('name', $(this).attr('data-name'))"><?= htmlspecialchars($translations_vals[$key]); ?></textarea>
								</div>
							</div>
						<? endforeach; ?>
						<div style="position:fixed; bottom:0; padding:20px; background:#fff; width:750px; margin:0 -20px; border-top:1px solid #ddd;">
							<button type="submit" class="uk-button uk-button-primary uk-margin">Save</button>
							<a href="javascript:void(0)" onclick="location.href = '?lang=<?= $lang; ?>&add_key=' + encodeURIComponent(prompt('Enter key:'));" class="uk-margin-left">Add key</a>
							<a href="#" onclick="$('.field').each((k, e) => { if($(e).val().length) return; $.getJSON('https://translate.yandex.net/api/v1.5/tr.json/translate?key=trnsl.1.1.20181121T084033Z.21318b8664e3bb05.580ccf7ed56dd9df655160d12116106645df5a22&lang=<?= $lang ?>&callback=?&text=' + $(e).parent().prev().text(), (r) => { $(e).val(r.text.length ? r.text[0] : '').attr('name', $(e).attr('data-name')); }); });" class="uk-margin-left">Translate all empty fields</a>
							<? if($lang) : ?>
								<a href="?find_keys=1&lang=<?= $lang; ?>" class="uk-margin-left">Find keys</a>
							<? endif; ?>
						</div>
					</form>
				<? endif; ?>
		    </div>
		</div>
	</body>
</html>