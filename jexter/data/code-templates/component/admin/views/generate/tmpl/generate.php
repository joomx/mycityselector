<?php
defined('_JEXEC') or die('Restricted access');

if( $this->inWork == 1 ){
	// сущестует текущая задача с активным статусом
	$startDisabled = 'disabled="disabled"';
	$stopDisabled = '';
	$mod0 = '';
	$mod1 = $this->mode==1 ? 'checked="checked"' : '';
	$mod2 = $this->mode==2 ? 'checked="checked"' : '';
}else{
	// задачи нет
	$startDisabled = '';
	$stopDisabled = 'disabled="disabled"';
	$mod0 = 'checked="checked"';
	$mod1 = '';
	$mod2 = '';
	if( $this->noLinks ){
		// нет ссылок в базе
		$mod1 = 'disabled="disabled"';
		$mod2 = 'disabled="disabled"';
	}
}

?>
<div id="j-sidebar-container" class="span2">
    <?= $this->sidebar ?>
</div>
<div id="j-main-container" class="span10">
    <div class="jenForm">
        <div class="col100">
        <fieldset class="adminform">
            <div class="jenToolbar">
                <div>
                    <input type="text" name="jhref" value="<?= $this->url ?>" style="width:600px;" <?= $startDisabled ?> />
                    <button class="jen-start" <?= $startDisabled ?>>Запуск</button>
                    <button class="jen-stop" <?= $stopDisabled ?>>Остановить</button>
                    <div class="jen-clear-float"></div>
                </div>
                <div class="jen-clear-float"></div>
                <div class="jen-scan-modes">
                    <div class="nofloat"><input type="radio" id="mode0" name="mode" value="0" <?= $startDisabled ?> <?= $mod0 ?> /> <label for="mode0">Начать новое сканирование</label></div>
                    <div class="nofloat"><input type="radio" id="mode1" name="mode" value="1" <?= $startDisabled ?> <?= $mod1 ?> /> <label for="mode1">Продолжить сканирование</label></div>
                    <div class="nofloat"><input type="radio" id="mode2" name="mode" value="2" <?= $startDisabled ?> <?= $mod2 ?> /> <label for="mode2">Генерация sitemap</label></div>
                    <div class="jen-clear-float"></div>
                </div>
                <div class="info"></div>
            </div>
            Логи<br/>
            <div class="jenLog">
                <?= $this->log ?>
            </div>
            <input type="hidden" id="jen_in_work" name="in_work" value="<?= $this->inWork ?>" />
        </fieldset>
        </div>
        <div class="clr"></div>
    </div>
</div>