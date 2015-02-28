<?php
    if (isset($_SERVER['HTTP_ORIGIN'])) {  
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");  
        header('Access-Control-Allow-Credentials: true');  
        header('Access-Control-Max-Age: 86400');   
    }  
      
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {  
      
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))  
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");  
      
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))  
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");  
    }
	require ('../languages/es_ES.tmpl');
?>
	<script src="js/jquery-1.11.1.js"></script>
	<script src="js/login.js"></script>
        
	<article>
		<form action="" method="POST" id="form_login" autocomplete="off">
			<input type="text" name="user" placeholder="<?php print text_usuario_placeholder; ?>" id="txt_input" class="name_login" required autofocus/><br>
			<input type="password" name="password" placeholder="<?php print text_password_placeholder; ?>" id="txt_input" class="pass_login" required/><br>
			<input type="submit" id="txt_login" class="login-button" value="<?php print text_iniciar_sesion_button; ?>">
		</form>
	</article>
		
	<script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.1.min.js"><\/script>')</script>
	<script src="js/plugins.js"></script>