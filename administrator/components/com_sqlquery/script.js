
jQuery(function($) {

    $('#sidebar .tables-list a.show-table').on('click', function(e) {
        e.preventDefault();
        var table = $(this).data('table');
        $('textarea[name="sql_query"]').text("SELECT * FROM `" + table + "` LIMIT 0,20;");
        $('#sqlquery-form').submit();
        $(window).scrollTop(100);
    });

});