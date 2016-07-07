<?php

// Требуются переменные $province (субдомен региона) и массив $cities = ['субдомен' => 'название']

defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');
?>

<div class="cities<?= isset($cities[$this->variables['city']]) ? ' active' : ' hidden' ?>
                group-<?= $province ?>"><?php
                foreach ($cities as $city => $data) {
                    ?>
                    <div class="city">
                        <a class="link<?= ($this->variables['city'] == $city) ? ' active' : '' ?>"
                           id="city-<?= $city ?>" data-city="<?= $data ?>"
                           href="<?= preg_replace('#^(http|https)(://)([^\/]*)(.*)$#', '$1$2' . $city . '.' . $this->variables['baseDomain'] . '$4', $this->variables['returnUrl']); ?>"
                           title=""><?= $data ?></a>
                    </div>
                    <?php
                }
                ?>
<div class="mcs-clear"></div>
</div>