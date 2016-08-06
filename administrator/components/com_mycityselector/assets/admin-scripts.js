/**
 * MyCitySelector
 * @author Konstantin Kutsevalov
 * @version 2.0.0
 */

jQuery(function($) {

    // for fields page
    if ($(".fields-page").length > 0) {

        function initSelect2(parent)
        {
            parent = parent || $(document);
            $(".select2.fields-value", parent).select2({
                ajax: {
                    url: "index.php?option=com_mycityselector&controller=fields&task=autocomplete",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { q: params.term };
                    },
                    cache: true
                },
                escapeMarkup: function (markup) {
                    return markup;
                },
                templateResult: function (item) {
                    if (item.loading) {
                        return "Поиск...";
                    }
                    return '<div class="mcs-select2-result">' + item.name
                        + ' (' + item.country_name + ', ' + item.province_name + ')</div>';
                },
                templateSelection: function(item) {
                    return item.name ? item.name : item.text;
                },
                minimumInputLength: 2
            });
        }

        function deleteContentFieldHandler() {
            $.ajax({
                "url": "index.php",
                "type": "get",
                "dataType": "json",
                "data": {
                    "option": "com_mycityselector",
                    "controller": "fields",
                    "task": "DeleteFieldValue",
                    "id": $(this).attr('id'),
                    "_$btn": $(this)
                }
            }).done(function (json) {
                if (json && json.status == "200") {
                    this._$btn.closest(".field-value").remove();
                } else {
                    alert("Произошла ошибка :( не смог удалить поле.")
                }
            }).fail(function (xhr, err) {
                alert("Произошла ошибка :(\n" + err);
            });
        }

        $('#addform').on("click", function () {
            $.get("index.php?option=com_mycityselector&controller=fields&task=getform&format=raw", function (data) {
                var $form = $(data);
                $form.insertBefore("#addform");
                initSelect2($form);
                $('.delete-field-value', $form).on("click", deleteContentFieldHandler);
            })
        });

        $('.delete-field-value').on("click", deleteContentFieldHandler);

        initSelect2();
    }

    // for editor popup window
    if ($("#fast-search-content").length > 0) {
        var $queryString = $("#query_string"),
            fsXHR = null;
        $queryString.on("keyup", function () {
            var query = $queryString.val(),
                lastQuery = $queryString.data("last_value") | "";
            if (query != lastQuery || query == "") {
                $queryString.data("last_value", query); // remember last search value
                if (fsXHR) fsXHR.abort();
                $.ajax({
                    "url": "index.php",
                    "type": "get",
                    "dataType": "json",
                    "data": {
                        "option": "com_mycityselector",
                        "controller": "fields",
                        "task": "Popup",
                        "query": query
                    }
                }).done(function (json) {
                    if (json && json.status == "200") {
                        $("#items-list-table tbody").replaceWith(json.html);
                    } else {
                        alert("Произошла ошибка :(")
                    }
                }).fail(function (xhr, err) {
                    alert("Произошла ошибка :(\n" + err);
                });
            }
        });
    }

});