<?php
/**
 * MyCitySelector
 * @author   Konstantin Kutsevalov
 * @version  2.0.0
 *
 * @formatter:off
 *
 * @var $this    \adamasantares\jxmvc\JxView
 * @var $sidebar string
 * @var $data    array
 * @var $model   CountryModel
 */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');

JHtml::_('behavior.formvalidator');

JFactory::getDocument()->addScriptDeclaration("
	Joomla.submitbutton = function(task)
	{
		if (task == 'country.cancel' || document.formvalidator.isValid(document.getElementById('country-form')))
		{
			Joomla.submitform(task, document.getElementById('country-form'));
		}
	};
");

$fieldSet = $this->form->getFieldset('edit');
?>

<div class="span12">
    <h3><?= JText::_('COM_MYCITYSELECTOR_COUNTRY') ?></h3>


    <form action="<?php echo JRoute::_('index.php?option=com_mycityselector&layout=edit&id=' . (int) $this->item->id); ?>"
          method="post" name="country-form" id="country-form"
          class="form-horizontal">
		<?php foreach ($fieldSet as $field)
		{
			?>
            <div class="control-group">
                <div class="control-label">
					<?= $field->label; ?>
                </div>
                <div class="controls">
					<?= $field->input; ?>
                </div>
            </div>
			<?php
		} ?>
        <input type="hidden" name="task" value=""/>
		<?php echo JHtml::_('form.token'); ?>
    </form>
</div>

