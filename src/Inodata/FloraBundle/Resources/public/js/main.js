/**
 * Created by heriberto on 9/02/16.
 */
window.Main = {

    initGeneralProperties: function(){
        $.datepicker.setDefaults($.datepicker.regional['es']);
        $('.datepicker').datepicker({ dateFormat: "yy-mm-dd" });
        $('input#filter_createdAt_value, input#filter_created_at_value').datepicker({dateFormat: "yy-mm-dd"})

    }

};

$(document).ready(function () {
    Main.initGeneralProperties();
});

