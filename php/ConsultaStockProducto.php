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
	
	//Miramos si tenemos que mostrar la petición de Lote
	$MostrarLote = DevolverParametro('MOSTRAR_LOTE');
?>
<input type="text" name="producto" placeholder="<?php print text_aviso_buscar_producto; ?>" id="txt_input" class="code" Onkeyup="EnviarPeticion('/php/SearchProductFilter.php', 'code', '.label-desc', 'N');">
<i Onclick="CargarPopup('/php/SearchProduct.php');" class="fa fa-search fa-2x"></i>

<input type="text" name="description" class="label-desc" readonly>

<?php 
	//Mostramos el campo lote si el parametro MOSTRAR_LOTE está activo.
	if($MostrarLote=='S'){
?>
		<input type="text" name="lote" id="txt_input" Onkeyup="EnviarPeticion('/php/SearchProductFilter.php', 'code', '.label-desc', 'N');" class="label-lote" placeholder=" <?php print text_label_lote; ?>">
<?php
	}
?>

<section id="buttonstools">
	<button id="buttonconfirm" class="aceptar" onclick="ConsultaStock('code', '#contenido', '<?php print $MostrarLote;?>');" ><?php print text_label_aceptar; ?></button>
	<button id="buttonconfirm" class="limpiar" onclick="$('.code').val('');$('.label-desc').val('');" ><?php print text_label_limpiar; ?></button>
</section>

<button id="botonapp" class="back" onclick="CargarAplicacion('/php/ConsultaStock.php', 'contenido', 'N','')"><?php print text_label_volver; ?></button><br>

<div id="modal3" class="modalmask">
</div>

<script>
	$('.app-title').html("<?php print text_app_name_consulta_stock_producto; ?>");
	$('.code').focus();
</script>