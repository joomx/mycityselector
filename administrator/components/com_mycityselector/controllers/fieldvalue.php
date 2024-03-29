<?php
/**
 * MyCitySelector
 * @author  Konstantin Kutsevalov
 * @version 2.0.0
 */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');

use Joomla\Utilities\ArrayHelper;

class MycityselectorControllerFieldvalue extends Joomla\CMS\MVC\Controller\FormController
{

	public function __construct(array $config = [])
	{
		parent::__construct($config);
		$this->registerTask('delete', 'delete');
	}


	public function getModel($name = '', $prefix = '', $config = [])
	{
		return parent::getModel('Fieldvalue', $prefix, $config); // TODO: Change the autogenerated stub
	}


	public function delete()
	{
		// Check for request forgeries
        Joomla\CMS\Session\Session::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = $this->input->get('cid', [], 'array');

		if (!is_array($cid) || count($cid) < 1)
		{
            Joomla\CMS\Log\Log::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), Joomla\CMS\Log\Log::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			$cid = ArrayHelper::toInteger($cid);

			// Remove the items.
			if ($model->delete($cid))
			{
				$this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
			}
			else
			{
				$this->setMessage($model->getError(), 'error');
			}
		}

		$this->setRedirect(Joomla\CMS\Router\Route::_('index.php?option=' . $this->option . '&view=field&layout=edit&id=' . $this->input->getInt('id', 0), false));
	}


}