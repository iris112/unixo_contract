<? defined('ROOT_DIR') or header('HTTP/1.1 404 Not Found') or header('Status: 404 Not Found') or die; ?>
<div class="auth">
	<div class="auth__wrap">
		<div class="lang auth_lang">
			<span><img src="https://api.smartcontract.ru/cdn/flags/<?= $this->lang->symbol; ?>.svg" width="20"> <?= $this->lang->name; ?></span>
			<div>
				<? foreach($this->lang->languages as $k => $v) : ?>
					<a href="?lang=<?= $k ?>"><span><img src="https://api.smartcontract.ru/cdn/flags/<?= $k ?>.svg" width="20"> <?= $v ?></span></a>
				<? endforeach; ?>
			</div>
		</div>
		<div class="auth__left">
			<br/>
			<? if($regis && $upline) : ?>
				<div class="auth__subtitle"><?= l('Пригласил:'); ?> <?= preg_match('/^[0-9]+$/', $upline) ? 'ID '.$upline : $upline; ?></div>
				<br/>
			<? endif; ?>
			<div class="auth__title"><?= $regis ? l('Регистрация') : l('Вход в систему'); ?></div>
			<div class="auth__subtitle"><?= $regis ? l('(покупка 1 уровня)') : l('Войдите автоматический если у вас установлен один из следующих кошельков:'); ?></div>
			<img src="/modules/site/auth/assets/auth-icons.png" class="auth__icons">
			<form action="" method="POST" onsubmit="return <?= $regis ? 'registr' : 'autoLogin'; ?>(this)">
				<input type="hidden" name="csrf" value="<?= $this->csrf ?>"/>
				<input type="hidden" name="address" value="<?= $address ?>"/>
				<div class="only-web3">
					<? if($regis) : ?>
						<? if($upline) : ?>
							<input type="hidden" name="upline" value="<?= $upline ?>"/>
						<? else: ?>
							<div class="auth__subtitle"><a href="#" onclick="this.parentNode.nextSibling.style.display = 'block'; this.parentNode.remove(); return false;"><?= l('Указать пригласителя вручную'); ?></a></div>
							<div style="display:none;">
								<div class="auth__subtitle"><?= l('Введите желаемый Ethereum адрес аплайна или оставьте его пустым что бы получить автоматически'); ?></div>
								<input placeholder="<?= l('ETH адрес или ID аплайна'); ?>" name="upline" value="" pattern="^(0x[a-f0-9A-F]{40}|[0-9]+)$" class="inp auth__inp">
							</div>
						<? endif; ?>
					<? endif; ?>
					<button class="btn btn_primary auth__btn" type="submit"><?= $regis ? l('Зарегистрироваться') : l('Войти автоматически'); ?></button>
				</div>
				<? if($regis) : ?>
					<div class="auth__desc only-noweb3">
						<p><?= l('К сожалению, мы не смогли установить соединение с Вашим кошельком для автоматической регистрации.'); ?></p>
						<p><?= l('Это можно выполнить вручную, создав транзакцию со следующими параметрами:'); ?></p>
						<p><?= l('Адрес Получателя перевода:'); ?> <b>0x206C97c79368a29631d26f16405217154e4cCD41</b></p>
						<p><?= l('Данные Data транзакции:'); ?> <b><?= $upline_address ?: '0x174b16cc1af3a9e9b0aa89e3d50598b1593c2084' ?></b></p>
						<p><?= l('Сумма перевода:'); ?> <b>0.1 ETH</b></p>
						<p><?= l('Лимит газа:'); ?> <b>400.000</b></p>
						</div>
				<? endif; ?>
			</form>
			<? if(!$regis) : ?>
				<br/>
				<form action="" method="POST">
					<input type="hidden" name="csrf" value="<?= $this->csrf ?>"/>
					<div class="auth__subtitle"><?= l('Или вы можете войти вручную, введите номер своего ETH кошелька'); ?></div>
					<input placeholder="<?= l('Введите ETH адрес'); ?>" name="address" required pattern="^0x[a-f0-9A-F]{40}$" class="inp auth__inp">
					<button type="submit" class="btn btn_success auth__btn"><?= l('Войти вручную'); ?></button>
				</form>
			<? else: ?>
				<div class="auth__links">
					<a href="/auth/"><i class="fa fa-arrow-left"></i> <?= l('Назад'); ?></a>
				</div>
			<? endif; ?>
		</div>
		<div class="auth__right">
			<a href="/" class="auth__logo"><img src="/modules/site/auth/assets/logo.svg?3"></a>
			<div class="auth__subtitle2"><?= l('Следите за нами в социальных сетях'); ?></div>
			<div class="auth__social">
				<a href="https://t.me/ethrun_group" target="_blank"><i class="fab fa-fw fa-telegram-plane"></i></a>
				<a href="https://discordapp.com/invite/3ECKbSG" target="_blank"><i class="fab fa-fw fa-discord"></i></a>
				<!--<a href="#" target="_blank"><i class="fab fa-fw fa-twitter"></i></a>
				<a href="#" target="_blank"><i class="fab fa-fw fa-instagram"></i></a>
				<a href="#" target="_blank"><i class="fab fa-fw fa-vk"></i></a>-->
			</div>
			<br/>
			<div class="auth__subtitle2"><?= l('На любой вопрос вы можете получить ответ в нашем чате:'); ?></div>
			<a href="https://t.me/ethrun_group" target="_blank" class="btn btn_transparent auth__btn"><i class="fab fa-telegram-plane"></i> <?= l('Чат в Telegram'); ?></a>
			<div class="auth__links">
				<a href="https://etherscan.io/address/<?= module('site/service')->contract; ?>" target="_blank"><?= l('Адрес смарт-контракта'); ?></a>
				<a href="#" target="_blank"><?= l('Правила'); ?></a>
				<a href="#" target="_blank"><?= l('Помощь'); ?></a>
			</div>
		</div>
	</div>
	<? if($regis && !$upline) : ?>
		<div class="auth__notify"><i class="fa fa-exclamation-circle"></i> <?= l('Вы пришли без партнерской ссылки, или она не сработала, поэтому Ваш аплайн неизвестен. Если Вы знаете id или Ethereum адрес Вашего аплайна, то впишите его в поле'); ?></div>
	<? endif; ?>
</div>