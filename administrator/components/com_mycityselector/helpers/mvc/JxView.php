<?php
/**
 * @author Konstantin Kutsevalov
 * @email <mail@art-prog.ru>
 */

namespace adamasantares\jxmvc;

if (!class_exists('\adamasantares\html\Tag')) {
    require_once __DIR__ . '/../html/Tag.php';
}

use \JHtml;
use \JText;
use \adamasantares\html\Tag;

if (!function_exists('tg')) { function tg($properties, $content = null) { return \adamasantares\html\tg($properties, $content); } }


/**
 * Class JxView
 * @package adamasantares\jxmvc
 *
 * @param object document
 */
class JxView
{

    /**
     * @var JFactory::getDocument() $document Returns document or its methods values
     * $this->document will return JFactory::getDocument()
     * For example: $this->document->getTitle();
     */

    /**
     * @var JxController
     */
    public $controller;

    /**
     * Flag for checking of ordering JS injected
     * @var bool
     */
    private $sortingJs = false;
    private $sortingOrderingJs = false;


    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    /**
     * Returns component name
     */
    public function getComponentName()
    {
        return $this->controller->getComponentName();
    }

    /**
     * Returns component name
     */
    public function getControllerName()
    {
        return isset($_REQUEST['controller']) ? $_REQUEST['controller'] : 'default';
    }

    /**
     * Creates and returns URL by parameters
     * @param $action
     * @param $controller
     * @param $component
     * @return string
     */
    public function url($action, $params=[], $controller=null, $component=null)
    {
        $component = empty($component) ? $this->getComponentName() : $component;
        $controller = empty($controller) ? $this->getControllerName() : $controller;
        $url = 'index.php?option=' . urlencode($component) . '&controller=' . urlencode($controller)
            . '&task=' . urlencode($action);
        if (!empty($params)) {
            foreach ($params as $param => $value) {
                $url .= '&' . urlencode($param) . '=' . urlencode($value);
            }
        }
        return $url;
    }

    /**
     * Returns message from User's state
     */
    public function getMessage()
    {
        return $this->controller->getMessage();
    }

    /**
     * Returns link for sorting table by column header
     * @param string $title Title of this column
     * @param string $name Name of this column
     * @param string $listOrder Name of current column for sorting
     * @param string $listDirection direction (asc/desc)
     * @param string $saveOrderingUrl Url for save new ordering
     * @return
     */
    public function sortingColumn($title = '', $name, $listOrder, $listDirection, $saveOrderingUrl = null)
    {
        JHtml::_('bootstrap.tooltip');
        if (!$this->sortingJs) {
            $this->document->addScriptDeclaration('var sortingAction = function(name, direction) {
                    jQuery("input#order_by").val(name);
                    jQuery("input#order_direction").val(direction);
                    jQuery("input#order_direction").closest("form").submit();
                }');
            $this->sortingJs = true;
        }
        $metaTitle = JHtml::tooltipText(JText::_($title), JText::_('JGLOBAL_CLICK_TO_SORT_THIS_COLUMN'), 0, 0);
        $direction = 'asc';
        if ($name == 'ordering') {
            // this is sorting by ordering column
            $icon = 'icon-menu-2';
            if ($listOrder == $name) {
                $icon = ($listDirection == 'asc') ? 'icon-arrow-up-3' : 'icon-arrow-down-3';
                $direction = ($listDirection == 'asc') ? 'desc' : 'asc';
                // activate table ordering
                if (!$this->sortingOrderingJs && !empty($saveOrderingUrl)) {
                    JHtml::_('jquery.ui', array('core', 'sortable'));
                    $this->document->addScriptDeclaration('jQuery(function ($) {
                        var $table = $("#order_by").closest("form").find("table tbody");
                        $("tr", $table).addClass("dndlist-sortable");
		                $table.parents("table").css("position", "relative");
                        $table.sortable({items: "> tr", handle: ".sortable-handler", axis: "y", cursor: "move",
                            placeholder: "dnd-list-highlight dndlist-place-holder",
                            sort: function(event, ui) {
                                var w = $table.find("tr").width();
                                $(".ui-sortable .sortable-handler").width(w);
                            },
                            update: function(event, ui) {
                                var data = {};
                                $(".order-key").each(function(){
                                    var name = $(this).attr("name"), num = $(this).val();
                                    data[name] = num;
                                });
                                $.ajax({url: "' . $saveOrderingUrl . '", dataType: "json", data: data})
                            },
                            helper:function (e, $ui) {
                                $ui.css("left", "0px"); //hard set left position to fix y-axis drag problem on Safari
                                $ui.children().each(function () {
                                    $(this).width($(this).width());
                                });
                                $ui.children("td").addClass("dndlist-dragged-row");
                                return $ui;
                            },
                            stop: function(e, $ui) {
                                $("td", $(this)).removeClass("dndlist-dragged-row");
                                $($ui.item).css("opacity", "0.4").animate({opacity: 1}, 300, function (){
                                    $($ui.item).css("opacity", "");
                                });
                            }
                        });
                    });');
                    $this->document->addStyleDeclaration('.ui-sortable .sortable-handler { cursor: move; }');
                    $this->document->addStyleSheet(\JURI::root() . '/media/jui/css/sortablelist.css');
                    $this->sortingOrderingJs = true;
                }
            }
            $caption = tg('span.'.$icon);
        } else {
            // sorting by other columns
            $icon = 'hide';
            if ($listOrder == $name) {
                $icon = ($listDirection == 'asc') ? 'icon-arrow-up-3' : 'icon-arrow-down-3';
                $direction = ($listDirection == 'asc') ? 'desc' : 'asc';
            }
            $caption = tg('span.'.$icon) . ' ' . JText::_($title);
        }
        return tg(['a.hasTooltip', 'title' => $metaTitle, 'onclick' => "return sortingAction('{$name}','{$direction}');", 'href' => '#'],
            $caption
        );
    }


    /**
     * Returns row sorting element (html)
     * @param $listOrder
     * @param $itemId
     * @param $orderNum
     * @return string
     */
    public function orderingRow($listOrder, $itemId, $orderNum) {
        $iconClass = ($listOrder == 'ordering') ? '.sortable-handler' : '.sortable-handler.hasTooltip.inactive';
        $title = ($listOrder == 'ordering') ? '' : JHtml::tooltipText('JORDERINGDISABLED');
        $orderNum = empty($orderNum) ? '0' : $orderNum;
        return tg(['span'.$iconClass, 'title' => $title], tg('span.icon-menu'))
            . tg('input!hidden.order-key$order['.$itemId.']', $orderNum);
    }


    /**
     * Renders view
     * @param string $viewFile
     * @param array $variables
     */
    public function render($viewFile, $variables = [])
    {
        // define variables
        if (is_array($variables) && !empty($variables)) {
            foreach ($variables as $var => $value) {
                ${$var} = $value;
            }
        }
        // render
        include($viewFile);
    }


    /**
     * returns tag for task
     * @return string
     */
    public function formTask()
    {
        return tg('input!hidden$task', '');
    }

    /**
     * returns tag with option parameter from request
     * @return string
     */
    public function formOption()
    {
        $option = isset($_REQUEST['option']) ? $_REQUEST['option'] : $this->getComponentName();
        return tg('input!hidden$option', $option);
    }

    /**
     * returns tag with option parameter from request
     * @return string
     */
    public function formControllerName()
    {
        $controller = isset($_REQUEST['controller']) ? $_REQUEST['controller'] : 'default';
        return tg('input!hidden$controller', $controller);
    }

    /**
     * returns hidden input "boxchecked"
     * @return string
     */
    public function formBoxChecked()
    {
        return tg('input!hidden$boxchecked', '0');
    }

    public function formToken()
    {
        return \JHtml::_('form.token');
    }

    /**
     * Returns inputs for ordering table
     * @return string
     */
    public function formFilterSorting($currentBy = 'id', $currentDirection = 'asc')
    {
        return tg('input!hidden$order_by#order_by', $currentBy) . "\n"
            . tg('input!hidden$order_direction#order_direction', $currentDirection);
    }


    /**
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        $doc = \JFactory::getDocument();
        switch ($name) {
            case 'document':
                return $doc;
            case 'title':
                return $doc->getTitle();
            case 'formOptions':
                return $this->formControllerName() . "\n"
                    . $this->formOption() . "\n"
                    . $this->formTask() . "\n"
                    . $this->formBoxChecked() . "\n"
                    . $this->formToken() . "\n";
                break;
        }
        return null;
    }

}