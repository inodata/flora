GuiaRoji = {
    init: function(){
        $(document).on('click', '#search-updates', function(event){
            var url_guia_roji = 'http://guiaroji.com.mx/listado_colonia.php?letra=A&ciudad=3';
            
            $.ajax({
                type: 'GET',
                url: url_guia_roji
            })
            .done(function(response){
                alert (response);
            }).fail(function(response){
                alert($(response).text());
            }).always(function(){
                alert("executed");
            });
        });
        
        getLetters = function(){
            return [
                'A','B','C','D','E','F','G','H','I','J','K','L','M','N',
                'O','P','Q','R','S','T','U','VW','X','Y','Z'
            ];
        };
    }
};

$(document).ready(function(){
    GuiaRoji.init();
});