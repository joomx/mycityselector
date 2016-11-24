<?php
defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');
/**
 * City item template
 *
 * @var $this MyCitySelectorModule
 * @var $cities array ['субдомен' => 'название']
 * @var $province string
 * @var $baseDomain string
 * @var $returnUrl string
 * @var $city string
 * @var $defaultCityCode string
 */
?>
<div class="cities<?= isset($cities[$city]) ? ' active' : ' hidden' ?> group-<?= $province ?>">
    <?php
    $onSubdomains = McsData::get('subdomain_cities');
    foreach ($cities as $cityKey => $data) {
        $url = $returnUrl;
        if ($onSubdomains == '1') {
            if ($defaultCityCode == $cityKey) { // if this city is city of base domain
                $url = preg_replace('#^(http|https)(://)([^\/]*)(.*)$#i', '$1$2' . $baseDomain . '$4', $returnUrl);
            } else {
                $url = preg_replace('#^(http|https)(://)([^\/]*)(.*)$#i', '$1$2' . $cityKey . '.' . $baseDomain . '$4', $returnUrl);
            }
        }
        ?>
        <div class="city">
            <a class="link<?= ($city == $cityKey) ? ' active' : '' ?>"
               id="city-<?= $cityKey ?>" data-city="<?= $data ?>" data-code="<?= $cityKey ?>"
               href="<?= $url ?>"
               title=""><?= $data ?></a>
        </div>
        <?php
    }
    ?>
    <div class="mcs-clear"></div>
</div>