<?php
/**
 * @author Konstantin Kutsevalov (Adamas Antares)
 * @email <mail@art-prog.ru>
 */

namespace adamasantares\jxforms;

// TODO form generator ...
/*
 * JxForm::render($component, $task, $models, $fields);
 *
 * echo JxForm::render('com_hello', 'update', [$modelBase, $relatedModel], [
 *      'id' => ['type' => 'hidden'],
 *      'name',  // autoconfig by Model (at first it search field in base model and if no found, then search in other models)
 *      'description' => [
 *          // custom config
 *          'label' => 'Published',
 *          'type' => 'editor',
 *          'width' => "600",
 *          'height' => "400"
 *      ],
 *      'status' => [
 *          'type' => 'radio',
 *          'value' => '0',
 *          'label' => 'Published',
 *          'options' => ['0' => 'No', '1' => 'Yes'],
 *      ],
 *      'question' => [
 *          'type' => 'chosen',
 *          'value' => '1',
 *          'label' => 'How are you?',
 *          'options' => ['0' => 'Not very well...', '1' => "I'm well", '2' => "I'm fine", '2' => "All right!"],
 *      ]
 * ]);
 *
 *
 */


// TODO redo
class JxForm {

    private $component = 'com_xxx';

    /**
     * @var \JModelList
     */
    public $model = null;

    /**
     * Array of fields list for form
     * @var array
     */
    private $fields = [];


    /**
     * Array of related inputs classes
     * @var array
     */
    public $inputs = [
        'text' => '\adamasantares\jxforms\TextInput',
        'editor' => '\adamasantares\jxforms\TextEditor',
        // todo ...
    ];

    /**
     * Relations types of inputs by types of MySQL model's fields
     * @var array
     */
    public $types = [
        'varchar' => 'text',
        'text' => 'editor',
    ];


    /**
     * Create form object
     * @param String $component Name on component ("com_componentname")
     * @param \JModelList $model
     * @param array $fields <p>
     *  [
     *      {field_name} => [ {parameters} ],
     *      'id' => ['type' => 'hidden', 'required' => true],
     *      'name' => ['type' => 'text', 'required' => true, 'maxLength' => '100'],
     *      'content' => ['type' => 'editor', 'required' => false, 'maxLength' => '5000'],
     *      'status' => ['type' => 'chosen', 'sidebar' => true, 'value' => '0', 'options' => [
     *              '0' => 'unPublished', '1' => 'Published'
     *          ]
     *      ],
     *  ]
     * in HTML it will generated with names like "ModelName[field_name]"
     * </p>
     */
    public function __construct($component, $fields = [], $model = null)
    {
        $this->fields = $fields;
        $this->component = preg_replace('/[^a-zA-Z_]/i', '', $component);
        if (get_parent_class($model) == 'JModelList') {
            // the passed model can be used for creating a from without fields list
            $this->model = $model;
            if (empty($this->fields)) {
                // trying to get fields list from model
                if (method_exists($model, 'getFields')) {
                    $this->fields = $model->getFields();
                } else {
                    // trying to get table name and load fields by query
                    if (method_exists($model, 'getTableName')) {
                        $table = $model->getTableName();
                        $this->fields = $this->getTableFields($table);
                    }
                }
            }
            // TODO getting validations rules of model
            if (method_exists($model, 'getValidateRules')) {

            }
        }
    }


    /**
     * Load table's columns
     * @param $table
     * @return array
     */
    private function getTableFields($table)
    {
        $fields = [];
        $db = \JFactory::getDbo();
        $columns = $db->getTableColumns($table, false);
        foreach ($columns as $field => $details) {
            $type = explode('(', $details->Type);
            $max = isset($type[1]) ? intval(trim($type[1], ')')) : 0;
            $this->fields[$field] = [
                'name' => $field,
                'primary' => ($details->Key == 'PRI' ? true : false),
                'type' => $type[0],
                'maxLength' => $max,
                'required' => ($details->Null == 'NO' ? true : false),
                'default' => $details->Default,
                'comment' => $details->Comment
            ];
        }
        return $fields;
    }


    /**
     * Renders the HTML code of form
     * @param array $fields
     * @return string Form HTML
     */
    public function render($fields = [])
    {
        if (empty($fields)) {
            $fields = $this->fields;
        }
        $html = '<form action="index.php?option=' . $this->component . '" method="post" name="adminForm" id="adminForm">';
        // setup the fields for this form
        if (is_array($fields) && !empty($fields)) {
            // from argument
            $this->_fieldsObjects = [];
            foreach ($fields as $field => $options) {

                // if field is primary key then make it hidden


            }
        }
        $html .= '<input type="hidden" name="task" value="" />';
        $html .= '<input type="hidden" name="option" value="' . $this->component . '" />';
        $html .= \JHtml::_('form.token');
        $html .= '</form>';
        return $html;
    }

} 