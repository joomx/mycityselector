<?php
defined ('_JEXEC') or die ('restricted access');

use joomx\mcs\plugin\helpers\McsData;

$user = JFactory::getUser();
$input 	= JFactory::getApplication()->input;
$view 	= $input->get('view', NULL, 'STRING');
$option = $input->get('option', NULL, 'STRING');
$layout = $input->get('layout', NULL, 'STRING');
$component = $input->get('component', NULL, 'STRING');
$isMe = ($option == 'com_mycityselector');

if ($user->authorise('core.manage', 'com_mycityselector')) {
	?>
    <nav class="main-nav-container">
        <ul class="nav flex-column main-nav metismenu">
            <li class="item parent item-level-1">
                <a class="has-arrow" href="#" aria-label="MCS" aria-expanded="false">
                    <span class="icon-puzzle-piece icon-fw" aria-hidden="true"></span>
                    <span class="sidebar-item-title"><?= JText::_('MOD_MYCITYSELECTORADMINMENU_TITLE'); ?></span>
                </a>
                <span class="menu-dashboard">
        <!--            <a href="/administrator/index.php?option=com_cpanel&amp;view=cpanel&amp;dashboard=content">-->
        <!--                <span class="icon-th-large" title="Content Dashboard" aria-hidden="true"></span>-->
        <!--                <span class="visually-hidden">Content Dashboard</span>-->
        <!--            </a>-->
                </span>
                <ul id="collapse1" class="collapse-level-1 <?= $isMe ? 'mm-show' : '' ?> mm-collapse">

                    <li class="item item-level-2">
                        <a class="no-dropdown <?= ($isMe && ($view == '' || $view == 'countries') ) ? 'mm-active' : '' ?>"
                           href="<?= JRoute::_('index.php?option=com_mycityselector&task=default&view=countries') ?>"
                           aria-label="<?= JText::_('MOD_MYCITYSELECTORADMINMENU_COUNTRIES') ?>">
                            <span class="sidebar-item-title"><?= JText::_('MOD_MYCITYSELECTORADMINMENU_COUNTRIES') ?></span>
                        </a>
                        <span class="menu-quicktask">
                            <a href="<?= JRoute::_('index.php?option=com_mycityselector&task=country.add') ?>">
                                <span class="icon-plus" title="Add New" aria-hidden="true"></span>
                                <span class="visually-hidden">Add New</span>
                            </a>
                        </span>
                    </li>

                    <li class="item item-level-2">
                        <a class="no-dropdown <?= ($isMe && $view == 'provinces') ? 'mm-active' : '' ?>"
                           href="<?= JRoute::_('index.php?option=com_mycityselector&task=default&view=provinces'); ?>"
                           aria-label="<?= JText::_('MOD_MYCITYSELECTORADMINMENU_PROVINCES') ?>">
                            <span class="sidebar-item-title"><?= JText::_('MOD_MYCITYSELECTORADMINMENU_PROVINCES') ?></span>
                        </a>
                        <span class="menu-quicktask">
                            <a href="<?= JRoute::_('index.php?option=com_mycityselector&view=province&layout=edit'); ?>">
                                <span class="icon-plus" title="Add New" aria-hidden="true"></span>
                                <span class="visually-hidden">Add New</span>
                            </a>
                        </span>
                    </li>

                    <li class="item item-level-2">
                        <a class="no-dropdown <?= ($isMe && $view == 'cities') ? 'mm-active' : '' ?>"
                           href="<?= JRoute::_('index.php?option=com_mycityselector&task=default&view=cities') ?>"
                           aria-label="<?= JText::_('MOD_MYCITYSELECTORADMINMENU_CITIES') ?>">
                            <span class="sidebar-item-title"><?= JText::_('MOD_MYCITYSELECTORADMINMENU_CITIES') ?></span>
                        </a>
                        <span class="menu-quicktask">
                            <a href="<?= JRoute::_('index.php?option=com_mycityselector&view=city&layout=edit'); ?>">
                                <span class="icon-plus" title="Add New" aria-hidden="true"></span>
                                <span class="visually-hidden">Add New</span>
                            </a>
                        </span>
                    </li>

                    <li class="item item-level-2">
                        <a class="no-dropdown <?= ($isMe && $view == 'fields') ? 'mm-active' : '' ?>"
                           href="<?= JRoute::_('index.php?option=com_mycityselector&task=default&view=fields') ?>"
                           aria-label="<?= JText::_('MOD_MYCITYSELECTORADMINMENU_FIELDS') ?>">
                            <span class="sidebar-item-title"><?= JText::_('MOD_MYCITYSELECTORADMINMENU_FIELDS') ?></span>
                        </a>
                        <span class="menu-quicktask">
                            <a href="<?= JRoute::_('index.php?option=com_mycityselector&view=field&layout=edit'); ?>">
                                <span class="icon-plus" title="Add New" aria-hidden="true"></span>
                                <span class="visually-hidden">Add New</span>
                            </a>
                        </span>
                    </li>

                    <li class="item item-level-2">
                        <a class="no-dropdown <?= ($option == 'com_mycityselector' && $view == 'component' && $component == 'com_mycityselector') ? 'mm-active' : '' ?>"
                           href="<?= JRoute::_('index.php?option=com_config&view=component&component=com_mycityselector') ?>"
                           aria-label="<?= JText::_('MOD_MYCITYSELECTORADMINMENU_OPTIONS') ?>">
                            <span class="sidebar-item-title"><?= JText::_('MOD_MYCITYSELECTORADMINMENU_OPTIONS') ?></span>
                        </a>
                    </li>

                </ul>
            </li>
        </ul>
    </nav>
	<?php
}
