<?php
/**
 * Шаблон модуля MyCitySelector
 *
 * В данном шаблоне существует объект $this, который указывает
 * на экземпляр класса 'MyCitySelectorModule' из файла 'modules/mod_mycityselector/mod_mycityselector.php'.
 * Поэтому здесь можно использовать вызов любых его методов, кроме '__construct()'.
 */
defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');


// загружаем jquery
$this->addJQuery();

// подлючаем файлы стилей и скриптов ($myUrl - это URL до директории, в которой находится текущий шаблон)
$this->addScript($myUrl . 'default.js');
$this->addStyle($myUrl . 'default.css');

// drop-down меню
?><div id="mycityselector" class="mycityselector<?= $this->get('moduleclass_sfx') ?>">
	<?= $this->get('text_before') ?>
	<a class="city" href="javascript:void(0)" title="Выбрать другой город"><?= $currentCity ?></a>
	<?= $this->get('text_after') ?>
	<div class="mycityselector-question" style="display:none;">Не ваш город?&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" class="close-q">x</a></div>
</div><?php

// Диалог выбора города.
// Основной контейнер обязательно должен иметь класс "mycityselector-dialog", остальная html разметка на ваш вкус.
?><div class="mycityselector-dialog" style="display:none;">
	<a class="close" href="javascript:void(0)" title=""></a>
	<div class="title">Пожалуйста, выберите ваш город</div>
	<div class="inner"><?php
        // Для справки:
        // $citiesList - это массив вида: [
        //   '__all__' => [   # все города одним списком
        //      'Москва' => ['subdomain' => 'moscow', 'path' => ''],
        //      'Санкт-Петербург' => ['subdomain' => 'spb', 'path' => ''],
        //      'Черемушки' => ['subdomain' => '', 'path' => '/other/cities']
        //   ],
        //   'Московская область' => [   # если были заданны группы городов, то для каждой группы также свой список
        //      'Москва' => ['subdomain' => 'moscow', 'path' => ''],
        //   ],
        //   ...
        // ]

        // если города раздлены по группам, выводим их в отдельный блок
        if (count($citiesList) > 1) {
            ?><div class="groups"><?php
                foreach ($citiesList as $group => $cities) {
                    if ($group != '__all__') continue;
                    ?>
                    <div class="group">
                        <a class="<?= isset($cities[$currentCity]) ? ' active' : ' hidden' ?>"
                           href="#" data-group="<?= $this->translit($group) ?>"><?= $group ?></a>
                    </div>
                    <?php
                }
            ?></div><?php
        }

        // города
        foreach ($citiesList as $group => $cities) {
            if (count($citiesList) > 1 && $group == '__all__') { continue; } // если есть группы, то пропускаем полный список
            ?><div class="cities<?= isset($cities[$currentCity]) ? ' active' : ' hidden' ?>
                group-<?= $this->translit($group) ?>"><?php
            foreach ($cities as $city => $data) {
                ?>
                <div class="city">
                    <a class="link<?= ($currentCity==$city) ? ' active' : '' ?>"
                        id="city-<?= $this->translit($city) ?>"
                        href="<?= $data['url'] ?>" title=""><?= $city ?></a>
                </div>
                <?php
            }
            ?></div><?php
        }

	?></div>
</div>
