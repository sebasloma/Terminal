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
	@session_start();

	require '../functions/functions.php';
	require '../languages/'.$_SESSION["IDIOMA"];
	$AppAsiganadas = ConseguirApp($_SESSION["USERNAME"], $_SESSION["EMPRESA"]);	
?>
<?php if (in_array('NO_APP', $AppAsiganadas)) {  ?>
	<button id="botonapp" class="clear" ><?php print text_no_app;?></button><br>
<?php } ?>

<?php if (in_array(1, $AppAsiganadas)) {  ?>
	<button id="botonapp" class="clear" onclick="CargarAplicacion('/php/ControlInventario.php', 'contenido', 'N', '<?php print text_pregunta_inicio_control_inventario;?>')"><?php print text_app_name_control_inventario;?></button><br>
<?php } ?>

<?php if (in_array(2, $AppAsiganadas)) {  ?>
	<button id="botonapp" class="clear" onclick="ConseguirUbicacion('/php/Inventario.php', 'N')"><?php print text_app_name_inventario;?></button><br>
<?php } ?>

<?php if (in_array(3, $AppAsiganadas)) {  ?>
	<button id="botonapp" class="clear"onclick="CargarAplicacion('/php/Pedidos.php', 'contenido', 'N','')"><?php print text_app_name_pedidos;?></button><br>
<?php } ?>

<?php if (in_array(4, $AppAsiganadas)) {  ?>
	<button id="botonapp" class="clear" onclick="CargarAplicacion('/php/ConsultaStock.php', 'contenido', 'N','')"><?php print text_app_name_consulta_stock;?></button><br>
<?php } ?>

<?php if (in_array(5, $AppAsiganadas)) {  ?>
	<button id="botonapp" class="clear" onclick="CargarAplicacion('/php/ConfirmarServicio.php', 'contenido', 'N','')"><?php print text_app_name_confirmar_servicio;?></button><br>
<?php } ?>

<?php if (in_array(6, $AppAsiganadas)) {  ?>
	<button id="botonapp" class="clear" ><?php print text_app_name_autoventa;?></button><br>
<?php } ?>

<script>
	$('.app-title').html("<?php print text_app_principal_menu; ?>");
</script>