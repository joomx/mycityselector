/**
 * MyCitySelector
 * @author Konstantin Kutsevalov
 * @version 1.0.0
 */

jQuery(function($){
    $('#addform').click(function(){
        $.get('index.php?option=com_mycityselector&controller=fields&task=getform&format=raw',function(data){
            $(data).insertBefore('#addform');
            $(".chzn-done").chosen();
            tinymce.init({
                selector: 'textarea'
            });
        })
    })
    // todo put your code here

});