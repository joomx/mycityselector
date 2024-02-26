<?php
// шаблон для страницы установшика

extract($data);
?>

<link href="<?= $css ?>" rel="stylesheet" />

<div id="mcs-installer-result">
    <h3>
        My City Selector
        <?= $route == 'update' ? ' обновление ' . $version : $version ?>
    </h3>
    <h4 class="red">Это переходная версия, пилим v4.0.0 для Joomla 4/5</h4>
    <div class="flex-container">
        <div class="flex-item">
            <h4>Проверка установки...</h4>

            <?php if (isset($base_domain)) { ?>
            <div class="section-title">&gt; Параметры</div>
            <div class="section-container">
                <p>Базовый домен: <?= $base_domain ?></p>
            </div>
            <?php } ?>

            <div class="section-title">&gt; Компонент</div>
            <div class="section-container">
                <?php foreach($components as $comp) { ?>
                    <p>
                        <span class="icon icon-cube"></span>
                        <?= $comp['name'] ?> &nbsp;
                        <?php if ($comp['result']) { ?>
                            <em class="green"><span class="icon icon-checkmark-2"></span></em>
                        <?php } else { ?>
                            <em class="red"><span class="icon icon-not-ok"></span></em>
                        <?php } ?>
                    </p>
                <?php } ?>
            </div>

            <div class="section-title">&gt; Таблицы</div>
            <div class="section-container">
                <?php foreach($tables as $tb) { ?>
                    <p>
                        <span class="icon icon-database"></span>
                        <?= $tb['name'] ?> &nbsp;
                        <?php if ($tb['result']) { ?>
                            <em class="green"><span class="icon icon-checkmark-2"></span></em>
                        <?php } else { ?>
                            <em class="red"><span class="icon icon-not-ok"></span></em>
                        <?php } ?>
                    </p>
                <?php } ?>
            </div>

            <div class="section-title">&gt; Плагины</div>
            <div class="section-container">
                <?php foreach($plugins as $plg) { ?>
                    <p>
                        <span class="icon icon-puzzle"></span>
                        <?= $plg['name'] ?> &nbsp;
                        <?php if ($plg['result']) { ?>
                            <em class="green"><span class="icon icon-checkmark-2"></span></em>
                        <?php } else { ?>
                            <em class="red"><span class="icon icon-not-ok"></span></em>
                        <?php } ?>
                    </p>
                <?php } ?>
            </div>

            <div class="section-title">&gt; Модули</div>
            <div class="section-container">
                <?php foreach($modules as $mod) { ?>
                    <p>
                        <span class="icon icon-grid-view-2"></span>
                        <?= $mod['name'] ?> &nbsp;
                        <?php if ($mod['result']) { ?>
                            <em class="green"><span class="icon icon-checkmark-2"></span></em>
                        <?php } else { ?>
                            <em class="red"><span class="icon icon-not-ok"></span></em>
                        <?php } ?>
                    </p>
                <?php } ?>
            </div>

            <?php if ($isInstallationOK !== null) { ?>
            <div class="section-result">
                <?php if ($isInstallationOK && empty($errors)) { ?>
                    <p class="green">Установка успешно завершена.</p>
                <?php } else { ?>
                    <p>В процессе установки возникли <em class="red">ошибки</em> :(<br>
                        Придётся <a href="<?= $github ?>" target="_blank" style="text-decoration:underline">жаловаться</a> разработчикам...
                    </p>
                    <?php if (!empty($errors)) {
                        ?>
                        <div class="mcs-error-logs">
                            <b>Error:</b>
                            <?= implode("<br>", $errors) ?>
                            <b>Full log:</b>
                            <?= implode("<br>", $logs) ?>
                        </div>
                        <?php
                    }
                } ?>
            </div>
            <?php } ?>
        </div>
        <div class="flex-item">
            <h4>Информация</h4>
            <p class="info">
                Подробную документацию мы можете найти на официальной странице расширения
                <a href="https://github.com/joomx/mycityselector">https://github.com/joomx/mycityselector</a>.
            </p>
            <p class="info">
                Кроме того, для управления платными версиями, Вы можете зарегистрировать аккаунт на сайте
                <a href="https://joomx.site/register">https://joomx.site</a>.
            </p>
            <p class="info">
                Связь с разработчиками можно держать через страницу
                <a href="https://github.com/joomx/mycityselector/issues">https://github.com/joomx/mycityselector/issues</a>.
            </p>
        </div>
    </div>
</div>
