AjaxWidget = {
    autocomplete: function(){
        $(".ajax-autocomplete").select2({
            minimumInputLength: 2,
            allowClear: true,
            ajax: {
                 url: Routing.generate ("inodata_flora_ajax_autocomplete"),
                 dataType: 'json',
                 data: function(term, page){
                     return {text: term, entity: $(this).attr('entity'), column: $(this).attr('column')};
                 },
                 results: function(data, page){
                     return { results: data };
                 }
            },
            initSelection: function(element, callback){
                callback({ id: $(element).val(), text: $(element).val()});
            }
        });
        
        $(document).live('keyup', '.select2-search > input[type="text"]', function(event){
            var dropdown = $('div.ajax-autocomplete.select2-dropdown-open');
            if(event.which == 13 && dropdown.length == 1){
                if($('.select2-results li:first').attr('class')=="select2-no-results"){
                   // alert($(this).val());
                    //$(dropdown).next().attr('value', "hola mundo");
                }
            }
        });
		
    },
    entity: function(){
        
    }
};


$(document).ready(function(){
    AjaxWidget.autocomplete();
});

