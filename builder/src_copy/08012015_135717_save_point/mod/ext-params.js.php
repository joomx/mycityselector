<?php
// Расширенные параметры для модуля
// (внедряется в страницу настроек модуля посредством плагина)
if (isset($_GET['vpb8t9s23hx09g80hj56i345hiasdtf6q2'])) {
    define('_JEXEC', 1);
}
defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');


// подключаем файлы Joomla
define('JPATH_BASE', realpath(dirname(__FILE__) . '/../..'));
require_once(JPATH_BASE . '/includes/defines.php');
require_once(JPATH_BASE . '/includes/framework.php');
JFactory::getApplication('site')->initialise();
$DB = JFactory::getDbo();

mb_internal_encoding('UTF-8');

$doc = JFactory::getDocument();
$myUrl = JURI::base() . 'modules/mod_mycityselector/';

//$doc->addScript($myUrl . 'tablednd.js', 'text/javascript'); //TODO здесь это не должно быть

// ==============================================================================


//TODO список шаблонов удалить, теперь это делает сама система

// составляем список доступных шаблонов
$tepmlatesOptions = '<option value="default">Default</option>';
// определяем текущий шаблон
$current = 'default';
$cities_list = array('Москва', 'Санкт-Петербург');
$DB->setQuery("SELECT `params` FROM `#__modules` WHERE `module`='mod_mycityselector'");
$res = $DB->loadResult();
$params = new JRegistry();
if (!empty($res)) {
    $params->loadString($res);
    $current = $params->get('template');
    $cities_list = explode("\n", $params->get('cities_list'));
}
// составляем список существующих шаблонов
$myDir = realpath(dirname(__FILE__)) . '/templates/';
$tpls = glob($myDir . '*', GLOB_ONLYDIR);
sort($tpls);
foreach ($tpls as $tpl) {
    $tpl = pathinfo($tpl);
    $tplName = $tpl['filename'];
    if (is_file($myDir . $tplName . '/' . $tplName . '.tpl.php') && $tplName != 'default') {
        $optSel = ($tplName == $current) ? ' selected="selected"' : '';
        $tplTitle = str_replace(array('_', '-'), array(' ', ' '), $tplName);
        $tplTitle = mb_strtoupper(mb_substr($tplTitle, 0, 1)) . mb_substr($tplTitle, 1);
        $tepmlatesOptions .= '<option value="' . $tplName . '"' . $optSel . '>' . $tplTitle . '</option>';
    }
}

// формируем список городов в виде таблицы (здесь не должно быть переносов строк в тексте, иначе js не будет работать)
$cities = '<tr style="border-bottom: 1px solid gray"><th>Город</th><th title="Здесь можно указать какой\n'
    . 'поддомен или страница\nсоответствует указанному городу.\n'
    . 'Поддомен должен записывать без\nосновного домена.'
    . '">Subdomain/Page (?)</th>'
    . '<th><a href="javascipt:void(0)" class="add">'
    . '<img style="float:none;margin:0;position:relative;top:-2px;" src="/administrator/templates/hathor/images/menu/icon-16-new.png" alt=""/> Добавить</a>'
    . '</th>'
    . '</tr>';
foreach ($cities_list as $city) {
    $city = explode('=', $city);
    if (trim($city[0]) == '') {
        continue;
    }
    if (!isset($city[1])) {
        $city[1] = '';
    }
    $cities .= '<tr style="border-bottom: 1px solid gray"><td><input type="text" name="city[]" class="city" value="' . htmlspecialchars(trim($city[0])) . '" /></td>'
        . '<td><input type="text" name="sub[]" class="sub" value="' . htmlspecialchars(trim($city[1])) . '" /></td>'
        . '<td><a href="javascipt:void(0)" class="remove">'
        . '<img style="float:none;margin:0;position:relative;top:-2px;" src="/administrator/templates/hathor/images/menu/icon-16-delete.png" alt=""/> Удалить</a></td>'
        . '</tr>';
}

// ==============================================================================

// javascript (тег "script" нужен для синтаксической подсветки js в IDE)
ob_start(); ?><script>


//TODO убрать Table DND или найти замену

if (window.addEvent) {
    window.addEvent('domready', function () {
        myCitySelectorParams();
    });
} else {
// каким по очереди будет загружен этот скрипт заранее не известно, поэтому ждем, пока не подгрузится mootools
    window.mcsint = setInterval(function () {
            if (window.addEvent) {
                clearInterval(window.mcsint);
                window.addEvent('domready', function () {
                    myCitySelectorParams();
                });
            }
        },
        50
    );
}

function myCitySelectorParams() {
// Шаблон окна
    $$("#jform_params_template")[0].setProperty("type", "hidden");
// создаем выпадающий список
    ( new Element('select', {
            "class": "mcs_select_template",
            "html": '<?php echo $tepmlatesOptions; ?>',
            "events": {
                "change": function () {
                    var tpl = this.getSelected().get("value");
                    $$("#jform_params_template")[0].set("value", tpl);
                }
            }
        }
    ) ).inject($$("#jform_params_template")[0], 'after');

// список городов
    $$("#jform_params_cities_list")[0].setStyle("display", "none");
    var table = new Element('table', {
        "class": "mcs_table_cities_list",
        "id": "table-1",
        "html": '<?= $cities ?>',
        "style": "width: 560px; border-top: 1px solid gray; border-collapse: collapse; margin-bottom: 12px;"
    });
    table.inject($$("#jform_params_cities_list")[0], 'before');
    $$(".mcs_table_cities_list .remove").addEvent('click', function () {
        var tr = this.getParent("tr");
        var city = tr.getElement(".city").get("value");
        if (city.trim() == "") {
            tr.destroy();
        } else {
            if (confirm("Удалить " + city + "?")) {
                tr.destroy();
            }
        }
        return false;
    });
    $$(".mcs_table_cities_list tr .add").addEvent("click", function () { // добавление новой строки
        var tr = new Element("tr", { // создаем
            "style": "border-bottom: 1px solid gray",
            "html": '<td> <input type="text" name="city[]" class="city"/> </td>\
                <td><input type="text" name="sub[]" class="sub"/></td>\
                <td> <a href="javascipt:void(0)" class="remove">\
                <img style="float: none; margin: 0; position: relative; top: -2px;"\
                    src="/administrator/templates/hathor/images/menu/icon-16-delete.png"\
                    alt=""/>Удалить</a>\
                </td>'
        });
        tr.getElement(".remove").addEvent('click', function () { // прикручиваем событие удаления
            var tr = this.getParent("tr");
            var city = tr.getElement(".city").get("value");
            if (city.trim() == "") {
                tr.destroy();
            } else {
                if (confirm("Удалить " + city + "?")) {
                    tr.destroy();
                }
            }
            return false;
        });
        tr.inject($$(".mcs_table_cities_list")[0], "bottom");
        return false;
    });

    // привязываем событие к форме, чтобы подготовить список городов к сохранению
    $$("form#module-form")[0].addEvent('submit', function () {
        var lines = [];
        $$(".mcs_table_cities_list tr").each(function (el) {
            var city = el.getElement(".city");
            if (!city) {
                return;
            }
            city = city.get("value");
            var sub = el.getElement(".sub").get("value");
            if (sub.trim() != "") {
                lines.push(city + "=" + sub);
            } else {
                lines.push(city);
            }
        });
        $$("#jform_params_cities_list")[0].set("value", lines.join("\n"));
        return true;
    });
};


// updated by ruslan2735 (Skype)
/*-----------------------------------------------------------------------------------------------------------------------------*/
if( window.jQuery ){

    jQuery(window).load(function () {
        jQuery("#table-1").tableDnD();
    });

    jQuery.tableDnD = {
        /** Keep hold of the current table being dragged */
        currentTable: null,
        /** Keep hold of the current drag object if any */
        dragObject: null,
        /** The current mouse offset */
        mouseOffset: null,
        /** Remember the old value of Y so that we don't do too much processing */
        oldY: 0,
        /** Actually build the structure */
        build: function (options) {
            // Make sure options exists
            options = options || {};
            // Set up the defaults if any
            this.each(function () {
                // Remember the options
                this.tableDnDConfig = {
                    onDragStyle: options.onDragStyle,
                    onDropStyle: options.onDropStyle,
                    // Add in the default class for whileDragging
                    onDragClass: options.onDragClass ? options.onDragClass : "tDnD_whileDrag",
                    onDrop: options.onDrop,
                    onDragStart: options.onDragStart,
                    scrollAmount: options.scrollAmount ? options.scrollAmount : 5
                };
                // Now make the rows draggable
                jQuery.tableDnD.makeDraggable(this);
            });
            // Now we need to capture the mouse up and mouse move event
            // We can use bind so that we don't interfere with other event handlers
            jQuery(document)
                .bind('mousemove', jQuery.tableDnD.mousemove)
                .bind('mouseup', jQuery.tableDnD.mouseup);
            // Don't break the chain
            return this;
        },
        /** This function makes all the rows on the table draggable apart from those marked as "NoDrag" */
        makeDraggable: function (table) {
            // Now initialise the rows
            var rows = table.rows; //getElementsByTagName("tr")
            var config = table.tableDnDConfig;
            for (var i = 0; i
                < rows.length; i++) {
                // To make non-draggable rows, add the nodrag class (eg for Category and Header rows)
                // inspired by John Tarr and Famic
                var nodrag = $(rows[i]).hasClass("nodrag");
                if (!nodrag) { //There is no NoDnD attribute on rows I want to drag
                    jQuery(rows[i]).mousedown(function (ev) {
                        if (ev.target.tagName == "TD") {
                            jQuery.tableDnD.dragObject = this;
                            jQuery.tableDnD.currentTable = table;
                            jQuery.tableDnD.mouseOffset = jQuery.tableDnD.getMouseOffset(this, ev);
                            if (config.onDragStart) {
                                // Call the onDrop method if there is one
                                config.onDragStart(table, this);
                            }
                            return false;
                        }
                    }).css("cursor", "move"); // Store the tableDnD object
                }
            }
        },
        /** Get the mouse coordinates from the event (allowing for browser differences) */
        mouseCoords: function (ev) {
            if (ev.pageX || ev.pageY) {
                return {x: ev.pageX, y: ev.pageY};
            }
            return {
                x: ev.clientX + document.body.scrollLeft - document.body.clientLeft,
                y: ev.clientY + document.body.scrollTop - document.body.clientTop
            };
        },
        /** Given a target element and a mouse event, get the mouse offset from that element.
         To do this we need the element's position and the mouse position */
        getMouseOffset: function (target, ev) {
            ev = ev || window.event;
            var docPos = this.getPosition(target);
            var mousePos = this.mouseCoords(ev);
            return {x: mousePos.x - docPos.x, y: mousePos.y - docPos.y};
        },
        /** Get the position of an element by going up the DOM tree and adding up all the offsets */
        getPosition: function (e) {
            var left = 0;
            var top = 0;
            /** Safari fix -- thanks to Luis Chato for this! */
            if (e.offsetHeight == 0) {
                /** Safari 2 doesn't correctly grab the offsetTop of a table row
                 this is detailed here:
                 http://jacob.peargrove.com/blog/2006/technical/table-row-offsettop-bug-in-safari/
                 the solution is likewise noted there, grab the offset of a table cell in the row - the firstChild.
                 note that firefox will return a text node as a first child, so designing a more thorough
                 solution may need to take that into account, for now this seems to work in firefox, safari, ie */
                e = e.firstChild; // a table cell
            }
            while (e.offsetParent) {
                left += e.offsetLeft;
                top += e.offsetTop;
                e = e.offsetParent;
            }
            left += e.offsetLeft;
            top += e.offsetTop;
            return {x: left, y: top};
        },
        mousemove: function (ev) {
            if (jQuery.tableDnD.dragObject == null) {
                return;
            }
            var dragObj = jQuery(jQuery.tableDnD.dragObject);
            var config = jQuery.tableDnD.currentTable.tableDnDConfig;
            var mousePos = jQuery.tableDnD.mouseCoords(ev);
            var y = mousePos.y - jQuery.tableDnD.mouseOffset.y;
            //auto scroll the window
            var yOffset = window.pageYOffset;
            if (document.all) {
                // Windows version
                //yOffset=document.body.scrollTop;
                if (typeof document.compatMode != 'undefined' &&
                    document.compatMode != 'BackCompat') {
                    yOffset = document.documentElement.scrollTop;
                }
                else if (typeof document.body != 'undefined') {
                    yOffset = document.body.scrollTop;
                }
            }
            if (mousePos.y - yOffset < config.scrollAmount) {
                window.scrollBy(0, -config.scrollAmount);
            } else {
                var windowHeight = window.innerHeight ? window.innerHeight
                    : document.documentElement.clientHeight ? document.documentElement.clientHeight : document.body.clientHeight;
                if (windowHeight - (mousePos.y - yOffset) < config.scrollAmount) {
                    window.scrollBy(0, config.scrollAmount);
                }
            }
            if (y != jQuery.tableDnD.oldY) {
                // work out if we're going up or down...
                var movingDown = y > jQuery.tableDnD.oldY;
                // update the old value
                jQuery.tableDnD.oldY = y;
                // update the style to show we're dragging
                if (config.onDragClass) {
                    dragObj.addClass(config.onDragClass);
                } else {
                    dragObj.css(config.onDragStyle);
                }
                // If we're over a row then move the dragged row to there so that the user sees the
                // effect dynamically
                var currentRow = jQuery.tableDnD.findDropTargetRow(dragObj, y);
                if (currentRow) {
                    // TODO worry about what happens when there are multiple TBODIES
                    if (movingDown && jQuery.tableDnD.dragObject != currentRow) {
                        jQuery.tableDnD.dragObject.parentNode.insertBefore(jQuery.tableDnD.dragObject, currentRow.nextSibling);
                    } else if (!movingDown && jQuery.tableDnD.dragObject != currentRow) {
                        jQuery.tableDnD.dragObject.parentNode.insertBefore(jQuery.tableDnD.dragObject, currentRow);
                    }
                }
            }
            return false;
        },
        /** We're only worried about the y position really, because we can only move rows up and down */
        findDropTargetRow: function (draggedRow, y) {
            var rows = jQuery.tableDnD.currentTable.rows;
            for (var i = 0; i
                < rows.length; i++) {
                var row = rows[i];
                var rowY = this.getPosition(row).y;
                var rowHeight = parseInt(row.offsetHeight) / 2;
                if (row.offsetHeight == 0) {
                    rowY = this.getPosition(row.firstChild).y;
                    rowHeight = parseInt(row.firstChild.offsetHeight) / 2;
                }
                // Because we always have to insert before, we need to offset the height a bit
                if ((y > rowY - rowHeight) && (y < (rowY + rowHeight))) {
                    // that's the row we're over
                    // If it's the same as the current row, ignore it
                    if (row == draggedRow) {
                        return null;
                    }
                    var config = jQuery.tableDnD.currentTable.tableDnDConfig;
                    if (config.onAllowDrop) {
                        if (config.onAllowDrop(draggedRow, row)) {
                            return row;
                        } else {
                            return null;
                        }
                    } else {
                        // If a row has nodrop class, then don't allow dropping (inspired by John Tarr and Famic)
                        var nodrop = $(row).hasClass("nodrop");
                        if (!nodrop) {
                            return row;
                        } else {
                            return null;
                        }
                    }
                    return row;
                }
            }
            return null;
        },
        mouseup: function (e) {
            if (jQuery.tableDnD.currentTable && jQuery.tableDnD.dragObject) {
                var droppedRow = jQuery.tableDnD.dragObject;
                var config = jQuery.tableDnD.currentTable.tableDnDConfig;
                // If we have a dragObject, then we need to release it,
                // The row will already have been moved to the right place so we just reset stuff
                if (config.onDragClass) {
                    jQuery(droppedRow).removeClass(config.onDragClass);
                } else {
                    jQuery(droppedRow).css(config.onDropStyle);
                }
                jQuery.tableDnD.dragObject = null;
                if (config.onDrop) {
                    // Call the onDrop method if there is one
                    config.onDrop(jQuery.tableDnD.currentTable, droppedRow);
                }
                jQuery.tableDnD.currentTable = null; // let go of the table too
            }
        },
        serialize: function () {
            if (jQuery.tableDnD.currentTable) {
                var result = "";
                var tableId = jQuery.tableDnD.currentTable.id;
                var rows = jQuery.tableDnD.currentTable.rows;
                for (var i = 0; i
                    < rows.length; i++) {
                    if (result.length > 0) result += "&";
                    result += tableId + '[]=' + rows[i].id;
                }
                return result;
            } else {
                return "Error: No Table id set, you need to set an id on your table and every row";
            }
        }
    }
    jQuery.fn.extend(
        {
            tableDnD: jQuery.tableDnD.build
        }
    );

}

</script><?= str_replace(array('<script>', '</script>'), array('', ''), ob_get_clean()) ?>