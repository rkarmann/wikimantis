
$(document).ready(function(){

    $("#product").change(function(){
        var productId = $(this).val();

        $.ajax({
            url: 'plugins/wikiCylande/pages/process.php',
            type: 'post',
            data: {productId:productId},
            dataType: 'json',
            success:function(responsePd){

                var len = responsePd.length;

                $("#module").empty();
                for( var i = 0; i<len; i++){
                    var id = responsePd[i]['id'];
                    var name = responsePd[i]['name'];

                    $("#module").append("<option value='"+id+"'>"+name+"</option>");

                }
            }
        });
    });

    $("#module").change(function(){
        var moduleId = $(this).val();

        $.ajax({
            url: 'plugins/wikiCylande/pages/process.php',
            type: 'post',
            data: {moduleId:moduleId},
            dataType: 'json',
            success:function(responseMd){

                var len = responseMd.length;

                $("#type").empty();
                for( var i = 0; i<len; i++){
                    var id = responseMd[i]['id'];
                    var name = responseMd[i]['name'];

                    $("#type").append("<option value='"+id+"'>"+name+"</option>");

                }
            }
        });
    });

});
