<?php
defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');
/**
 * Country item template
 *
 * @var $this MyCitySelectorModule
 * @var $countries array
 *         ['субдомен' => ['name' => 'название']]
 * @var $province string
 */
?>
<div class="countries"><?php
    foreach ($countries as $country => $data) {
        ?>
        <div class="country country-<?= $country ?> <?= isset($data['list'][$province]) ? 'active' : '' ?>">
            <a class="<?= isset($data['list'][$province]) ? 'active' : '' ?>" href="#" data-group="<?= $country ?>"><?= $data['name'] ?></a>
        </div>
        <?php
    }
    ?>
</div>