/**
 * Created by u92 on 03.12.2015.
 */
$(document).ready(function(){
/*
    if ((window.location.pathname = '?r=site/fizlic') || (window.location.pathname = '/index.php?r=site/fizlic')) {
        alert(window.location.href);
    }*/

    /* Если выбран 3-ий формат, то показываем дополнительные блоки */
    $("#tutdf30r").hide();
    $(":radio").click(function() {

        var tdf_val = $(this).val();

            if(tdf_val == "1") {
                $("#tutdf30r").show();
            } else {
                $("#tutdf30r").hide();
            }
    });

    // Если статус ЮР лица не 0, то разблокироваем поле "Дата статуса"
    $("#input_CompanyStatusDate").attr("disabled","disabled");

    $("#urlic-companystatus").change(function() {
       if ($(this).val() != "0") {
           $("#input_CompanyStatusDate").attr("disabled",false);
            $("#input_CompanyStatusDate").val("");
       } else {
           $("#input_CompanyStatusDate").attr("disabled","disabled");
       }
    });

/*    $("#urlic-kredscet").onmouseout(function() {
        var data = this.val().length;

        alert(data);
    });*/

});