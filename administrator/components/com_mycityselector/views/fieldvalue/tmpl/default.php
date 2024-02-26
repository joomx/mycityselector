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
JHtml::_('formbehavior.chosen', 'select');

$app      = JFactory::getApplication();
$field_id = $app->getUserStateFromRequest('field_id', 'field_id');
$this->form->setValue('field_id', null, $field_id);
$default = $this->item->default;
if ($default)
{
	$this->form->removeField('cities');
	$this->form->removeField('countries');
	$this->form->removeField('provinces');
}

$fieldSet = $this->form->getFieldset('edit');
$id       = $app->getUserStateFromRequest('com_mycityselector.edit.fieldvalue.id', 'id');


JFactory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "fieldvalue.cancel" || document.formvalidator.isValid(document.getElementById("adminForm")))
		{
			Joomla.submitform(task, document.getElementById("adminForm"));

			// @deprecated 4.0  The following js is not needed since 3.7.0.
//			if (task !== "fieldvalue.apply")
//			{
//				window.parent.jQuery("#fieldValueModal' . $id . '").modal("hide");
//			}
		}
	};
	function _ADD_ALL_CITIES() {
	    jQuery("#jform_cities option:not(:disabled)").attr("selected", "selected");
	    jQuery("#jform_cities").trigger("liszt:updated");
	    return false;
	}
	function _ADD_ALL_PROVINCES() {
	    jQuery("#jform_provinces option:not(:disabled)").attr("selected", "selected");
	    jQuery("#jform_provinces").trigger("liszt:updated");
	}
	function _ADD_ALL_COUNTRIES() {
	    jQuery("#jform_countries option:not(:disabled)").attr("selected", "selected");
	    jQuery("#jform_countries").trigger("liszt:updated");
	}
');
?>

<div class="span12">
    <h3><?= JText::_('COM_MYCITYSELECTOR_CONTENT') ?></h3>


    <form action="<?php echo JRoute::_('index.php?option=com_mycityselector&view=fieldvalue&tmpl=component&id=' . (int) $this->item->id); ?>"
          method="post" name="fieldValue-form" id="adminForm"
          class="form-horizontal">
		<?php foreach ($fieldSet as $key => $field)
		{
			?>
            <div class="control-group">
                <div class="control-label">
					<?= $field->label; ?>
                </div>
                <div class="controls">
					<?= $field->input; ?>
					<?php if ($field->fieldname == 'cities') : ?>
                        <a href="#!" onclick="return _ADD_ALL_CITIES()"
                           role="button"
                           class="btn btn-primary"
                           title="<?= JText::_('COM_MYCITYSELECTOR_ADD_ALL_CITIES'); ?>"><?= JText::_('COM_MYCITYSELECTOR_ADD_ALL_CITIES'); ?>
                        </a>
					<?php endif; ?>
                    <?php if ($field->fieldname == 'provinces') : ?>
                        <a href="#!" onclick="return _ADD_ALL_PROVINCES()"
                           role="button"
                           class="btn btn-primary"
                           title="<?= JText::_('COM_MYCITYSELECTOR_ADD_ALL_PROVINCES'); ?>"><?= JText::_('COM_MYCITYSELECTOR_ADD_ALL_PROVINCES'); ?>
                        </a>
                    <?php endif; ?>
                    <?php if ($field->fieldname == 'countries') : ?>
                        <a href="#!" onclick="return _ADD_ALL_COUNTRIES()"
                           role="button"
                           class="btn btn-primary"
                           title="<?= JText::_('COM_MYCITYSELECTOR_ADD_ALL_COUNTRIES'); ?>"><?= JText::_('COM_MYCITYSELECTOR_ADD_ALL_COUNTRIES'); ?>
                        </a>
                    <?php endif; ?>
                </div>

            </div>
			<?php
		} ?>
		<?php if ($default) : ?>
            <input type="hidden" name="jform[cities]" value="0"/>
            <input type="hidden" name="jform[provinces]" value="0"/>
            <input type="hidden" name="jform[countries]" value="0"/>
		<?php endif; ?>
        <input type="hidden" name="task" value=""/>
		<?php echo JHtml::_('form.token'); ?>
    </form>
</div>