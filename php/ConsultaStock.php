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
?>
<button id="botonapp" class="clear" onclick="ConseguirUbicacion('/php/DesgloseUbicaciones.php', 'N')"><?php print text_app_name_consulta_stock_ubicacion;?></button><br>
<button id="botonapp" class="clear" onclick="CargarAplicacion('/php/ConsultaStockProducto.php', 'contenido', 'N', '')"><?php print text_app_name_consulta_stock_producto;?></button><br>
<button id="botonapp" class="clear" onclick="CargarAplicacion('/php/ConsultaStockEtiqueta.php', 'contenido', 'N', '')"><?php print text_app_name_consulta_stock_etiqueta;?></button><br>
<button id="botonapp" class="back" onclick="CargarInicio();"><?php print text_label_volver; ?></button><br>

<script>
	$('.app-title').html("<?php print text_app_name_consulta_stock ;?>")
</script>