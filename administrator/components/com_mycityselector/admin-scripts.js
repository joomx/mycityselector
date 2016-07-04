/**
 * MyCitySelector
 * @author Konstantin Kutsevalov
 * @version 1.0.0
 */

jQuery(function($){
    function deleteHandler(button) {
        var id = $(button).attr('id');
        $.get('index.php?option=com_mycityselector&controller=fields&task=DeleteFieldValue&id='+id, function(){
            $(button).parent().parent().parent().remove();
        })
    }
    $('#addform').click(function(){
        $.get('index.php?option=com_mycityselector&controller=fields&task=getform&format=raw',function(data){
            $(data).insertBefore('#addform');
            $(".chzn-done").chosen(choosen_opt);
            tinymce.init({
                selector: 'textarea'
            });
            $('.delete-field-value').click(function() {
                deleteHandler(this);
            });
        })
    });
    $('.delete-field-value').click(function() {
        deleteHandler(this);
    });
    // todo put your code here

});