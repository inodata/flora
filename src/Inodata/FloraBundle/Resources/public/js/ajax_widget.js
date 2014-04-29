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
        
        $(document).on('keyup', '.select2-search > input[type="text"]', function(event){
            var dropdown = $('div.ajax-autocomplete.select2-dropdown-open');
            
            if($(dropdown).find('.no-guia-roji').length == 1){
                $(dropdown).find('.no-guia-roji').remove();
            }
            
            if(event.which == 13 && dropdown.length == 1){
                if($('.select2-results li:first').attr('class')=="select2-no-results"){
                    $(dropdown).next().attr('value', $(this).val());
                    $(dropdown).next().select2('val', $(this).val());
                    $(dropdown).next().select2('close');
                    $(dropdown).append('<span class="no-guia-roji">No existe en la guia roji</span>');
                }
            }
        });
        
         $(document).on('change', '.ajax-autocomplete', function(){
             if($(this).val()){
                 var url = Routing.generate("inodata_flora_guiaroji_find_by_id", {id: $(this).val()});
                 $.get(url, function(object){
                     $('.shipping_city').val(object.city);
                     $('.shipping_postal_code').val(object.postal_code);
                     $('.shipping_neighborhood').val(object.neighborhood);
                     
                 }, 'json');
             }else{
                 $('.shipping_city').val("");
                 $('.shipping_postal_code').val("");
             }
        });
		
    },
    entity: function(){
        
    }
};


$(document).ready(function(){
    AjaxWidget.autocomplete();
});

