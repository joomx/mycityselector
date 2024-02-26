<?php
/**
 * MyCitySelector
 * @author Konstantin Kutsevalov
 * @version 2.0.0
 */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');

use joomx\mcs\plugin\helpers\McsData;

Joomla\CMS\Factory::getDocument()
    ->addStyleSheet(Joomla\CMS\Uri\Uri::root() . 'components/com_mycityselector/views/default/tmpl/style.css');

$showCountry = $this->menuItem->get('show_country_name');
$showProvince = $this->menuItem->get('show_province_name');
$baseDomain = McsData::get('basedomain');
$onSubdomains = McsData::get('seo_mode');
$returnUrl = Joomla\CMS\Uri\Uri::getInstance()->toString();
?>
<div id="mcs-cities-list">
    <ul class="mcs-items">
        <?php
        foreach ($this->countries as $country) {
            if ($showCountry) {
                echo "<h3>{$country['name']}</h3>";
            }
            ?><li>
                <ul>
                <?php
                foreach ($this->provinces as $province) {
                    if ($country['id'] == $province['country_id']) {
                        echo '<li>';
                        if ($showProvince) {
                            echo "<h5>{$province['name']}</h5>";
                        }
                        echo '<ul>';

                        foreach ($this->cities as $city) {
                            if ($city['province_id'] == $province['id']) {

                                // TODO взять формирование URL из шаблона modules/mod_mycityselector/tmpl/__city.php
                                switch (McsData::get('seo_mode'))
                                {
                                    case 1:
                                        if (McsData::get('default_city') == $city['subdomain'])
                                        { // if this city is city of base domain
                                            $url = preg_replace('#^(http|https)(://)([^\/]*)(.*)$#i', '$1$2' . $baseDomain . '$4', $returnUrl);
                                        }
                                        else
                                        {
                                            $url = preg_replace('#^(http|https)(://)([^\/]*)(.*)$#i', '$1$2' . $city['subdomain'] . '.' . $baseDomain . '$4', $returnUrl);
                                        }
                                        break;
                                    case 2:
                                        $myUri['city'] = $city['subdomain'];
                                        $url = JRoute::_('index.php?' . JUri::buildQuery($myUri));
                                        break;
                                    case 2:case 3:
                                        $myUri['mcsC'] = $city['subdomain'];
                                        $url = JRoute::_('index.php?' . JUri::buildQuery($myUri));
                                        break;
                                    default:
                                        $url = '#';//$this->path['path'];
                                }
                                //$url = '#';

                                echo "<li><a href=\"{$url}\" title=\"\" class=\"\">{$city['name']}</a></li>";
                            }
                        }
                        echo '</ul></li>';
                    }
                }
                ?></ul>
            </li><?php
        }
        ?>
    </ul>
</div>


