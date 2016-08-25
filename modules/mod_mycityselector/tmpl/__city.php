<?php
defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');
/**
 * City item template
 *
 * @var $this MyCitySelectorModule
 * @var $cities array
 *          ['субдомен' => 'название']
 * @var $province string
 * @var $city string
 */
?>
<div class="cities<?= isset($cities[$city]) ? ' active' : ' hidden' ?> group-<?= $province ?>">
    <?php
    foreach ($cities as $cityKey => $data) {
        ?>
        <div class="city">
            <a class="link<?= ($city == $cityKey) ? ' active' : '' ?>"
               id="city-<?= $cityKey ?>" data-city="<?= $data ?>"
               href="<?= preg_replace('#^(http|https)(://)([^\/]*)(.*)$#', '$1$2' . $cityKey . '.' . $this->variables['baseDomain'] . '$4', $this->variables['returnUrl']); ?>"
               title=""><?= $data ?></a>
        </div>
        <?php
    }
    ?>
    <div class="mcs-clear"></div>
</div>