<?php defined('_JEXEC') or die('Restricted access');

$count = count( $this->links );

?>
<div id="j-sidebar-container" class="span2">
    <?= $this->sidebar ?>
</div>
<div id="j-main-container" class="span10">
    <form action="index.php" method="post" name="adminForm" class="jenForm" id="adminForm">
        <div class="pagination"><?= $this->pagination ?></div>
        <hr/>
        <table class="table">
        <thead>
            <tr>
                <th width="20">
                    <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
                </th>
                <th nowrap="nowrap">Адрес</th>
                <th nowrap="nowrap" style="width:120px;">Дата последнего изменения</th>
                <th nowrap="nowrap" style="width:120px;">Частота обновлений</th>
                <th nowrap="nowrap" style="width:80px;">Приоритет</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if( $count == 0 ){
            ?><tr><td colspan="5" align="center"><b>Нет данных для отображения</b></td></tr><?php
        }else{
            foreach( $this->links as $i => $row ){
                ?><tr class="row<?= ($i%2>0) ? '1' : '0' ?>">
                    <td align="center">
                        <input type="checkbox" id="cb<?= $i ?>" name="cid[]" value="<?= $row['id'] ?>" onclick="Joomla.isChecked(this.checked);" />
                    </td>
                    <td align="left">
                        <a href="<?= $row['loc'] ?>" target="_blank" title=""><?= $row['loc'] ?></a>
                    </td>
                    <td align="center"><?= $row['lastmod'] ?></td>
                    <td align="center"><?= $row['changefreq'] ?></td>
                    <td align="center"><?= $row['priority'] ?></td>
                </tr><?php
            }
        }
        ?>
        </tbody>
        </table>
        <hr/>
        <div class="pagination"><?= $this->pagination ?></div>

        <div class="clr"></div>
        <input type="hidden" name="boxchecked" value="0">
        <input type="hidden" name="option" value="com_sitemapjen" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="">
        <?= $this->token ?>
    </form>
</div>
