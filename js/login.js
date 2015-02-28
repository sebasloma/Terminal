//Declaramos la variables Globales
	var host = 'http://test.esferasoftware.es';
$(document).ready(function(){
	$('.login-button').click(function(){
		var username = $(".name_login").val();
		var password = $(".pass_login").val();
		var namebutton = $(".login-button").val();
		var dataString = 'user='+username+'&password='+password;
		if($.trim(username).length>0 && $.trim(password).length>0){
			$.ajax({
				type: "POST",
				url: host + "/functions/tmpl-login.php",
				data: dataString,
				cache: false,
				beforeSend: function(){ $(".login-button").val('...');},
				success: function(data){
					if(data=='ok'){
						$(".login-button").val('OK');
						window.location=host;
					}else{
						//Shake animation effect.
						alertify.alert(data);
						$(".login-button").val(namebutton);
					}
				}
			});
		}
		return false;
	});
});