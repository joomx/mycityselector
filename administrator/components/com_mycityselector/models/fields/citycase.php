<?php

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('text');

class JFormFieldCityCase extends JFormField
{

	protected $type = "CityCase";
	protected $caseId = 0;

	protected $case = null; //\morphos\Cases::GENETIVE;
    protected $db;

    public function __construct(Form $form = null)
    {
        $this->db = Joomla\CMS\Factory::getDbo();

        parent::__construct($form);
    }

	public function getInput()
	{
        $html = '';

	    $langs = $this->getLangs();

	    $city_id = JFactory::getApplication()->getUserStateFromRequest('com_mycityselector.edit.city.id', 'id', 0, 'int');

		if (!empty($city_id))
		{
		    foreach ($langs as $lang) {
                $query = $this->db->getQuery(true);
                $query->select('value')->from('#__mycityselector_city_cases');
                $query->where('city_id=' . $this->db->q($city_id));
                $query->where('case_id=' . $this->caseId);
                $query->where('lang_id=' . $lang['id']);
                $result = $this->db->setQuery($query)->loadResult();

                if ($result)
                {
                    $html .= "<label for=\"case_{$this->caseId}_{$lang['id']}\">{$lang['locale']}: </label><input id=\"case_{$this->caseId}_{$lang['id']}\" type='text' class=\"form-control\" name=\"jform[city_case_{$this->caseId}][{$lang['id']}]\" placeholder='Name' value=\"{$result}\"><br><br>";
                }
                else
                {
                    $query = $this->db->getQuery(true);
                    $query->select('name')
                          ->from('#__mycityselector_city_names')
                          ->where('`city_id`=' . $this->db->q($city_id))
                          ->where("`lang_id` = {$lang['id']}");

                    $result = $this->db->setQuery($query)->loadResult();

//                    if($lang['locale'] == 'ru-RU')
//                    {
                        $cityName = \morphos\Russian\GeographicalNamesInflection::getCase($result, $this->case);
//                    }
//                    else
//                    {
//                        $cityName = $result;
//                    }
                    $html .= "<label for=\"case_{$this->caseId}_{$lang['id']}\">{$lang['locale']}: </label><input id=\"case_{$this->caseId}_{$lang['id']}\" type='text' name=\"jform[city_case_{$this->caseId}][{$lang['id']}]\" placeholder='Name' value=\"{$cityName}\"><br><br>";
                }
            }
		}
		return $html;
	}

    protected function getLangs()
    {
        $query = $this->db->getQuery(true);
        $query->select('id, locale')
            ->from('#__mycityselector_langs')
            ->order('`default` DESC, locale ASC');

        $result = $this->db->setQuery($query)->loadAssocList();

        return $result;
    }
}