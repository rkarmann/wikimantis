
$(document).ready(function(){

	$("#searchEmail").keyup(function(){
		$.ajax({
		type: "POST",
		url: "plugins/wikiCylande/pages/process.php",
		data:'user_mail='+$(this).val(),
		beforeSend: function(){
			$("#searchEmail").css("background","#FFF url(plugins/wikiCylande/files/LoaderIcon.gif) no-repeat 165px");
		},
		success: function(data){
			$("#resultEmail").show();
			$("#resultEmail").html(data);
			$("#searchEmail").css("background","#FFF");
		}
		});
	});

});
//To select country name
function selectMail(val) {
$("#searchEmail").val(val);
$("#resultEmail").hide();
}
