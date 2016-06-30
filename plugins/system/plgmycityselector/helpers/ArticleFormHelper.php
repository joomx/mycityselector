<?php

// todo задумывался хелпер для добавления в форму редактирования статьи некого функционала из данного расширения
// ну там кнопка например для вставки кода из раздела "Данные" (или строка быстрого поиска по кодам)

class ArticleFormHelper {

    public static function addWidget()
    {
        $app = JFactory::getApplication();
        switch ($app->input->get('option')) {
            case 'com_content':
                if ($app->isAdmin() && JFactory::getApplication()->getName() == 'administrator') {
                    $string = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<form>
	<fields name="params" label="MyCitySelector">
	    <fieldset name="params" label="&#9733; MyCitySelector &#9733;">
			<field
				name="field1"
				type="text"
				label="Bla bla bla"
				/>
			<field
				name="field2"
				type="text"
				label="tututu"
			/>
        </fieldset>
	</fields>
</form>
XML;
                    $xml = simplexml_load_string($string);
                    $form->load($xml, true, false);
                }
                return true;
        }
        return true;
    }

} 