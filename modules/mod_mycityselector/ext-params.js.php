<?php
/**
 * Расширенные параметры для модуля
 * (внедряется в страницу настроек модуля посредством плагина)
 */
if (!isset($_GET['vpb8t9s23hx09g80hj56i345hiasdtf6q2'])) {
    exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');
}

header("Content-type: text/javascript");

// => изображения
$imgAdd = '/administrator/templates/hathor/images/menu/icon-16-new.png';
$imgDel = '/administrator/templates/hathor/images/menu/icon-16-delete.png';
$imgMove = '/modules/mod_mycityselector/tmpl/icon-16-move-3x.png';
$imgDefault = '/administrator/templates/hathor/images/menu/icon-16-default.png';
$imgArrow = '/administrator/templates/hathor/images/menu/icon-16-download.png';


// => шаблоны
// - таблица городов
ob_start();
?><div id="mcs_cities_list">
    <table id="mcs_cities_table" class="table">
        <tr class="table-header">
            <th><img src="<?= $imgDefault ?>" title="Город по умолчанию"></th>
            <th>Название</th>
            <th>Поддомен/Адрес</th>
            <th>
                <div class="add">
                    <div class="btn-group">
                        <a class="btn btn-small action" href="#" title="Добавить запись">
                            <img src="<?= $imgAdd ?>" alt=""><i class="icon-save-new"></i>
                        </a>
                        <a href="#" class="btn btn-small drop">
                            <img src="<?= $imgArrow ?>" alt=""><i class="icon-chevron-down"></i>
                        </a>
                        <div class="clearfix"></div>
                    </div>
                    <ul class="dropdown-menu">
                        <li><a href="#" class="add-city">Добавить запись</a></li>
                        <li><a href="#" class="add-group">Добавить группу</a></li>
                    </ul>
                </div>
            </th>
        </tr>
    </table>
</div><?php
$tableTpl = str_replace("\n", '', ob_get_clean());
// - запись группы
ob_start();
?><tr>
    <td class="name group" colspan="2"><input type="text" value="{group}"/></td>
    <td class="adr">&nbsp;</td>
    <td class="operations">
        <a href="#" class="remove btn btn-small" title="Удалить">
            <span class="icon-cancel"></span>
            <img src="<?= $imgDel ?>"/>
        </a>
    </td>
</tr><?php
$groupTpl = str_replace("\n", '', ob_get_clean());
// - запись города
ob_start();
?><tr>
    <td class="def"><input type="radio" name="sel_def" value="" title="По умолчанию" {sel}/></td>
    <td class="name city"><input type="text" value="{city}"/></td>
    <td class="adr {sub}"><input type="text" value="{adr}"/><span class="domain">.{baseDomain}</span></td>
    <td class="operations">
        <a href="#" class="remove btn btn-small" title="Удалить">
            <span class="icon-cancel"></span>
            <img src="<?= $imgDel ?>"/>
        </a>
        &nbsp;&nbsp;<img class="dnd" src="<?= $imgMove ?>" title="Переместить"/>
    </td>
</tr><?php
$cityTpl = str_replace("\n", '', ob_get_clean());


// javascript (тег "script" нужен для синтаксической подсветки js в IDE)
ob_start(); ?><script>
// ======================================== MCS ===============================================

jQuery(function($){

    var $table = null,
        joomlaVer = "2.5";
    if ($("body").hasClass("admin") && $("body").hasClass("com_modules")) {
        joomlaVer = '3.x';
    }

    // => парсим список городов
    var citiesList = $("#jform_params_cities_list").val().split("\n"), i = 0, cities = [];
    for (; i<citiesList.length; i++) {
        var city = $.trim(citiesList[i]),
            chars = city.split("");
        if (city.length > 0) {
            if (chars[0] == "[" && chars[chars.length-1] == "]") {
                // группа
                cities.push({"group": city.replace(/^\[(.+)\]$/, "$1")});
            } else {
                // город
                var parts = (city + "=").split("="), sub = '', url = '';
                if (parts[1].length > 0 && parts[1].substring(0, 1) == "/") {
                    url = parts[1];
                } else if (parts[1].length > 0 && parts[1].indexOf("/") == -1) {
                    sub = parts[1];
                }
                cities.push({"city": parts[0], "subdomain": sub, "url": url});
            }
        }
    }
    /* получаем миссив вида
    [ {"group":"Россия"},
     {"city":"Москва", "subdomain":"", "url":"" },
     {"city":"Омск", "subdomain":"", "url":"" } ] */

    // => функции
    var updateCitiesList = function(){
            var rows = [];
            $table.find("tr").each(function(i, tr){
                var $tr = $(tr),
                    $tdName = $tr.find(".name"),
                    name = $.trim($tdName.find("input").val()),
                    $tdAdr = $tr.find(".adr"),
                    adr = "";
                if (name.length > 0) {
                    if ($tdName.hasClass("group")) {
                        // группа
                        rows.push("[" + name + "]");
                    } else {
                        // город
                        adr = $.trim($tdAdr.find("input").val());
                        if (adr.length > 0) {
                            adr = "=" + adr;
                        }
                        rows.push(name + adr);
                    }
                }
            });
            rows = $.trim(rows.join("\n"));
            console.log(rows);
            $("#jform_params_cities_list").val(rows);
        },
        onCheckDefault = function(){
            if ($(this).prop("disabled")) {
                return false;
            }
            var city = $.trim($(this).closest("tr").find(".name input").val());
            if (city.length > 0) {
                $("#jform_params_default_city").val(city);
            }
        },
        onChangeName = function(){
            var $me = $(this),
                value = $.trim($me.val()),
                $ch = $me.closest("tr").find(".def input");
            if (value.length > 0) {
                $ch.prop("disabled", false).val($me.val()); // разблокируем блокируем radiobutton и обновим значение
            } else {
                $ch.prop("disabled", true).val("");
            }
            updateCitiesList();
        },
        onChangeAdr = function(){
            var $me = $(this),
                value = $.trim($me.val()),
                $adr = $me.closest("td.adr");
            if (value.length > 0) {
                if (value.indexOf("/") < 0) {
                    $adr.addClass("sub");
                } else {
                    $adr.removeClass("sub");
                }
            } else {
                $adr.removeClass("sub");
            }
            updateCitiesList();
        },
        onAddRecord = function(){
            $("#mcs_cities_table .table-header .add .dropdown-menu").css("display", "none");
            if ($table.find("tr").length < 31) {
                var $tr = $('<?= $cityTpl ?>'
                    .replace('{sel}', 'disabled="disabled"').replace('{adr}', '').replace('{sub}', '')
                    .replace('{baseDomain}', baseDomain).replace('{city}', '')
                );
                // биндим события на элементы
                $tr.find(".def input").on("click", onCheckDefault);
                $tr.find(".name input").on("keyup", onChangeName);
                $tr.find(".adr input").on("keyup", onChangeAdr);
                $tr.find(".operations .remove").on("click", onRemoveRecord);
                $table.append($tr);
                $table.tableDnDUpdate({dragHandle: ".operations .dnd", onDrop: updateCitiesList});
            } else {
                alert("Извините, максимум 30 строк.");
            }
            return false;
        },
        onAddGroup = function(){
            $("#mcs_cities_table .table-header .add .dropdown-menu").css("display", "none");
            if ($table.find("tr").length < 31) {
                var $tr = $('<?= $groupTpl ?>'.replace('{group}', ""));
                $tr.find(".operations .remove").on("click", onRemoveRecord);
                // проверяем существование других групп
                if ($table.find(".name.group").length == 0) {
                    $table.find(".table-header").after($tr);
                } else {
                    $table.append($tr);
                }
                $table.tableDnDUpdate({dragHandle: ".operations .dnd", onDrop: updateCitiesList});
            } else {
                alert("Извините, максимум 30 строк.");
            }
            return false;
        },
        onRemoveRecord = function(){
            $(this).closest("tr").remove();
            updateCitiesList();
            return false;
        };

    // => стилизуем и расширяем функциональность страницы настроек
    // - прячем поле с предупреждением
    $("#jform_params_plugin_check").closest(".control-group").css("display", "none");
    // - основной домен
    $("#jform_params_main_domain").before('<span class="b-info">http://</span>');
    $("#jform_params_main_domain").after('<span class="a-info">(без "www")</span>');
    // - геолокация (формируем список пунктов)
    var geolocation = [];
    $("#jform_params_baseip").removeClass("radio");
    $("#jform_params_baseip label").each(function(index, object){
        var $input = $("#" + $(object).attr("for"));
        geolocation.push({
            "label": object.innerHTML,
            "name": $input.attr("name"),
            "value": $input.val(),
            "selected": $input.prop("checked") ? 'selected="selected"' : ''
        });
    });
    // - формируем список городов
    $("#jform_params_cities_list").css("display", "none").after('<?= $tableTpl ?>');
    $table = $("#mcs_cities_table");
    // заполняем города
    var defaultCity = $("#jform_params_default_city").val(),
        baseDomain = $("#jform_params_main_domain").val();
    for (var i=0; i<cities.length; i++) {
        if (cities[i].city) {
            // Город
            var sel = (cities[i].city == defaultCity) ? ' checked="checked"' : ''; // селектор города по умолчанию
            // адрес
            var sub = '', adr = '';
            if (cities[i].subdomain.length > 0) {
                adr = cities[i].subdomain;
                sub = " sub";
            } else if (cities[i].url.length > 0) {
                adr = cities[i].url;
            }
            var cityName = cities[i].city.replace(/"/g,'');
            var $tr = $('<?= $cityTpl ?>'
                .replace('{sel}', sel).replace('{adr}', adr).replace('{sub}', sub)
                .replace('{baseDomain}', baseDomain).replace('{city}', cityName)
            );
        } else {
            // Группа
            var $tr = $('<?= $groupTpl ?>'.replace('{group}', cities[i].group.replace(/"/g,'')));
        }
        // добавляем строку
        $table.append($tr);
    }
    $table.tableDnD({dragHandle: ".operations .dnd", onDrop: updateCitiesList});

    // => биндим события на элементы
    $table.find(".def input").on("click", onCheckDefault);
    $table.find(".name input").on("keyup", onChangeName);
    $table.find(".adr input").on("keyup", onChangeAdr);
    $table.find(".operations .remove").on("click", onRemoveRecord);
    // - изменение основного домена
    $("#jform_params_main_domain").on("keyup", function(e){
        $("#mcs_cities_table .domain").html("." + $(this).val());
    });
    // - "добавить запись"
    $("#mcs_cities_table .table-header .add .action, #mcs_cities_table .table-header .add-city").on("click", onAddRecord);
    // - "добавить группу"
    $("#mcs_cities_table .table-header .add-group").on("click", onAddGroup);
    // - клик по стрелке у кнопки "добавить"
    $("#mcs_cities_table .table-header .add .drop").on("click", function(){
        $("#mcs_cities_table .table-header .add .dropdown-menu").css("display", "block");
        return false;
    });
    $(window).on("click", function(){
        $("#mcs_cities_table .table-header .add .dropdown-menu").css("display", "none");
    });

    // - геолокация (создаем выпадающий список)
    $("#jform_params_baseip").html('<select class="form-control" name="' + geolocation[0].name + '"></select>');
    for (var i=0; i<geolocation.length; i++) {
        $("#jform_params_baseip select")
            .append('<option value="' + geolocation[i].value + '" '
                + geolocation[i].selected + '>'
                + geolocation[i].label + '</option>');
    }

    /**/

    // - специфичная стилизация
    if (joomlaVer == '3.x') {
        // => для joomla 3.x
        // - прячем поле "город по умолчанию", он будет выбираться чекбоксами в списке городов
        $("#jform_params_default_city").closest(".control-group").css("display", "none");
        // - диалог on/off (стилизуем переключатель)
        if (!$("#jform_params_let_select").hasClass("btn-group-yesno")) {
            $("#jform_params_let_select").addClass("btn-group").addClass("btn-group-yesno");
            $("#jform_params_let_select label").addClass("btn");
            $('#jform_params_let_select label:not(.active)').click(function(){
                var $label = $(this);
                var $input = $('#' + $label.attr('for'));
                if (!$input.prop('checked')) {
                    $label.closest('.btn-group').find('label').removeClass('active btn-success btn-danger btn-primary');
                    if ($input.val() == 2) {
                        $label.addClass('active btn-danger');
                    } else {
                        $label.addClass('active btn-success');
                    }
                    $input.prop('checked', true);
                }
            });
            $('#jform_params_let_select input[checked=checked]').each(function(){
                if ($(this).val() == 2) {
                    $('label[for=' + $(this).attr('id') + ']').addClass('active btn-danger');
                } else {
                    $('label[for=' + $(this).attr('id') + ']').addClass('active btn-success');
                }
            });
        }
    } else if (joomlaVer == "2.5") {
        // => для joomla 2.5
        $("#jform_params_plugin_check-lbl").closest("li").css("display", "none");
        // - прячем поле "город по умолчанию", он будет выбираться чекбоксами в списке городов
        $("#jform_params_default_city").closest("li").css("display", "none");
    }








});

// =======================================================================================
</script><?= str_replace(array('<script>', '</script>'), array('', ''), ob_get_clean()) ?>