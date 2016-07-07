<?php

defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');

// Требуются переменные $country (субдомен страны) и массив $provinces = ['субдомен' => ['name' => 'название']]
?>

<div
    class="provinces <?= $this->variables['cities_list_type'] != 2 || isset($provinces[$this->variables['province']]) ? 'active' : 'hidden' ?> provinces-<?= $country ?>"><?php
    foreach ($provinces as $province => $data) {
        ?>
        <div class="province">
            <a class="<?= isset($data['list'][$this->variables['city']]) ? ' active' : '' ?>"
               href="#" data-group="<?= $province ?>"><?= $data['name'] ?></a>
        </div>
        <?php
    }
    ?>
</div>

