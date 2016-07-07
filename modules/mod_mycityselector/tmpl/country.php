<?php

defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');

// Требуются переменные  массив $countries = ['субдомен' => ['name' => 'название']]
?>

<div class="countries"><?php
    foreach ($countries as $country => $data) {
        ?>
        <div class="country country-<?= $country ?>">
            <a class="<?= isset($data['list'][$this->variables['province']]) ? 'active' : '' ?>"
               href="#" data-group="<?= $country ?>"><?= $data['name'] ?></a>
        </div>
        <?php
    }
    ?>
</div>