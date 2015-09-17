<?php
/**
 * Sitemap Jen
 * @author Konstantin@Kutsevalov.name
 * @package    sitemapjen
 */
defined('_JEXEC') or die('Restricted access');
 
?>
<div id="j-sidebar-container" class="span2">
    <?= $this->sidebar ?>
</div>
<div id="j-main-container" class="span10">
    <form action="index.php" class="jenForm" method="post" name="adminForm" id="adminForm">
    <div class="col100">
        <table class="adminlist jenOptions">
        <tbody>
            <tr><td width="200" valign="top"><b>Список исключаемых url</b><br/><br/>
            (Исключение адресов из генерируемого sitemap происходит по принципу директивы Disallow в файлах robots.txt):</td><td><textarea style="width:99%;height:300px" cols="110" rows="25" name="ignore_list" class="inputbox"><?= $this->options['ignore_list'] ?></textarea></td></tr>
            <tr><td width="200"><b>Исключать адреса вида<br/><code>"?option=com_"</code></b>:</td><td><input class="inputbox" type="checkbox" name="ignore_option_com" id="ajax" value="Y" <?=($this->options['ignore_option_com']=='Y')?('checked="checked"'):('') ?> ></td></tr>
            <tr><td width="200"><b>Исключать адреса вида<br/><code>"?query=value&..."</code></b>:</td><td><input class="inputbox" type="checkbox" name="only_4pu" id="ajax" value="Y" <?=($this->options['only_4pu']=='Y')?('checked="checked"'):('') ?> ></td></tr>
            <tr><td width="200"><b>Исключать адреса вида<br/><code>rel="nofollow"</code></b>:</td><td><input class="inputbox" type="checkbox" name="ignore_nofollow" id="ajax" value="Y" <?=($this->options['ignore_nofollow']=='Y')?('checked="checked"'):('') ?> ></td></tr>
            <tr><td width="200"><b>Количество потоков</b>:</td><td><input class="inputbox" type="textbox" size="5" name="threads" value="<?= isset($this->options['threads']) ? $this->options['threads'] : '3' ?>" /></td></tr>
        </tbody>
        </table>
    </div>
    <div class="clr"></div>
    <?= $this->token ?>
    <input type="hidden" name="option" value="com_sitemapjen" />
    <input type="hidden" name="task" value="save_options" />
    </form>
</div>
