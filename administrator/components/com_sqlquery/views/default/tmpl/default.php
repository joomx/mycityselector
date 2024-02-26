<?php
/**
 *
 */
defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

$document = Factory::getDocument();
$db = Factory::getDbo();
?>
<script src="<?= JUri::root() ?>/administrator/components/com_sqlquery/script.js"></script>

<style>
    .sql-query-form form {
        width: 100%;
        margin-top: 20px;
    }
    .sql-query-form textarea {
        width: 100%;
        min-height: 150px;
        max-height: 400px;
    }
    .sql-query-form .warning {
        display: inline-block;
        padding: 5px 10px;
        border: none;
        background-color: #CC7832;
        color: #fff;
        border-radius: 4px;
    }
    .sql-result {
        margin-bottom: 20px;
    }
    .tables-title {
        padding: 0 0 0 10px;
    }
    .tables-list {
        padding: 10px;
        overflow: auto;
        max-height: 412px;
    }
</style>

<div id="j-sidebar-container" class="j-sidebar-container j-sidebar-visible">
    <?php
    $_prefix = $db->getPrefix();
    $sidebarContent = '<div class="sidebar-nav"><div class="tables-title"><strong>Tables</strong></div> <div class="tables-list">';
    foreach ($this->tables as $_name) {
        $_label = str_replace($_prefix, '#_', $_name);
        $sidebarContent .= '<p><a href="#" class="show-table" data-table="' . $_name . '">' . $_label . '</a></p>';
        // \JHtmlSidebar::addEntry($name, '#insert_table');
    }
    $sidebarContent .= '</div>';
    $sidebar = \JHtmlSidebar::render();
    echo preg_replace('/\<div\s+class="sidebar-nav"\>/is', $sidebarContent, $sidebar);
    ?>
</div>

<div id="j-main-container" class="span10">
    <div class="sql-query-form">
        <h4><?= Text::_('COM_SQLQUERY') ?></h4>
        <div class="warning"><strong><?= Text::_('COM_SQLQUERY_WARNING') ?></strong></div><br>
        <form id="sqlquery-form" action="" method="post">
            <label>
                <?= Text::_('COM_SQLQUERY_SQL_LABEL') ?>:<br>
                <textarea name="sql_query" placeholder="SELECT `username` FROM `#__users`;"><?= $this->sql_query ?></textarea>
            </label>
            <div class="note"><?= Text::_('COM_SQLQUERY_SQL_NOTE') ?></div>
            <button type="submit"><?= Text::_('COM_SQLQUERY_SQL_EXECUTE') ?></button>
            <input type="hidden" name="task" value=""/>
            <?= \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
        </form>
    </div>
    <?php
    foreach ($this->sql_queries as $j => $_query) {
        if (!empty($this->sql_results[$j])) {
            ?>
            <div class="sql-result">
                <hr>
                <div class="query-string">
                    <strong><?= Text::_('COM_SQLQUERY_QUERY_TITLE') ?>:</strong><br>
                    <pre><?= $_query ?></pre>
                </div>
                <div class="query-result">
                    <strong><?= Text::_('COM_SQLQUERY_RESULT_TITLE') ?>:</strong><br>
                    <?php
                    if (is_array($this->sql_results[$j])) {
                        if (empty($this->sql_results[$j])) {
                            echo '<div>There are no results</div>';
                        } else {
                            ?>
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <?php
                                    $row = current($this->sql_results[$j]);
                                    foreach ($row as $column => $value) {
                                        echo '<th width="1%">' . $column . '</th>';
                                    }
                                    ?>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    reset($this->sql_results[$j]);
                                    foreach ($this->sql_results[$j] as $row) { ?>
                                        <tr>
                                            <?php
                                            foreach ($row as $column => $value) {
                                                echo '<td>' . $value . '</td>';
                                            }
                                            ?>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <?php
                        }
                    } else {
                        echo $this->sql_results[$j];
                    }
                    ?>
                </div>
            </div>
            <?php
        }
    }
    ?>
</div>
