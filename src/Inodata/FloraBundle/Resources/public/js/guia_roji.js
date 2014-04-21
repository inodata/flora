Ajax = {
    request: function(_type, _url, _data, _callback, _event){
        $.ajax({
                type: _type,
                url: _url,
                data: _data
            })
            .done(function(response){
                _callback(response, _event);
            }).fail(function(response){
                alert("Failed");
            }).always(function(){
                //alert("executed");
            });
    }
};

GuiaRoji = {
    init: function(){
        var page=1, nPages=0, nChar=0;
        $(document).on('click', '#search-updates', function(event){
            var url = Routing.generate("inodata_flora_guiaroji_search");
            var data = {letter:getLetter(nChar), page:page};
            
            showInserting(true);
            //Request to get the result for letter and page selected
            Ajax.request('POST', url, data, parseAndSaveResult, event);
        });
        
        parseAndSaveResult = function(result, event){
            var list = $(result).find("table.head-results tbody > tr");
            var data_tosave = [];
            
            $(list).each(function(){
                var name = $(this).find('td.first-col').text();
                var city = $(this).find('td.mid-col').text();
                var cp = $(this).find('td.last-col').text();
                
                if(name !=='--'){
                    var map = parseAndGetX($(this).attr("onclick"));
                    var coordinate = parseAndGetY($(this).attr("onclick"));

                    var colony = {
                        name: name,
                        city: city,
                        cp: cp,
                        map: map,
                        coordinate: coordinate
                    };
                    data_tosave.push(colony);
                }
            });
            
            var url = Routing.generate("inodata_flora_guiaroji_save");
            Ajax.request("POST", url, {colonies:data_tosave}, savedSuccess, event);
            
            if(page === 1){
                nPages = $(result).find("a.linkPagina").last().text();
                
                if(!nPages){
                    nPages = 1;
                }
            }
        };
        
        savedSuccess = function(result, event){ 
            //alert("Result for "+getLetter(nChar)+"-"+page+" inserted");
            if(continueInserting()){
                $(".inserting > span").text(getLetter(nChar)+"-"+page);
                $("#search-updates").click();
            }else{
                showInserting(false);
                alert ("Inserting finshed");
            }
        };
        
        continueInserting = function(){
            if(page<nPages){
                page++;
            }else{
                if(getLetter(nChar)!=='Z'){
                    page=1; nChar++;
                }else{
                    return false;
                }
            }
            
            return true;
        };
        
        parseAndGetX = function(text){
            var results = text.split("&");
            
            var map = results[results.length-2];
            map = map.split("=");
            map = map[1];
            
            return map;
        };
        
        parseAndGetY = function(text){
            var results = text.split("&");
            
            var coordinate = results[results.length-1];
            coordinate = coordinate.substring(0, coordinate.length-1);
            coordinate = coordinate.split("=");
            coordinate = coordinate[1];
            
            return coordinate;
        };
        
        getLetter = function(n){
            letters =  [
                'A','B','C','D','E','F','G','H','I','J','K','L','M','N',
                'O','P','Q','R','S','T','U','V','W','X','Y','Z'
            ];
            
            return letters[n];
        };
        
        showInserting = function(show){
            if(show === true){
                $(".inserting").addClass("on");
            }else{
                $(".inserting").removeClass("on");
            }
        };
    }
};

$(document).ready(function(){
    GuiaRoji.init();
});