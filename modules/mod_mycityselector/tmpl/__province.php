<?php
defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');
/**
 * Country item template
 *
 * @var $this MyCitySelectorModule
 * @var $country string
 * @var $province string
 * @var $provinces string
 *          ['субдомен' => ['name' => 'название']]
 */
?>

<div
    class="provinces <?= $cities_list_type != 2 || isset($provinces[$province]) ? 'active' : 'hidden' ?> provinces-<?= $country ?>"><?php
    foreach ($provinces as $provinceKey => $data) {
        ?>
        <div class="province">
            <a class="<?= isset($data['list'][$this->variables['city']]) ? ' active' : '' ?>"
               href="#" data-group="<?= $provinceKey ?>"><?= $data['name'] ?></a>
        </div>
        <?php
    }
    ?>
</div>

