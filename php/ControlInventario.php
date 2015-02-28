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
?>
	<script>
		$('.app-title').html('<?php print text_app_name_control_inventario; ?>');
	</script>
	
<?php
	@session_start();
	require '../functions/functions.php';
	require '../languages/'.$_SESSION["IDIOMA"];
	require('config.php');
	require('../functions/connection/connect.php'); 

	//Miramos si el usuario ha realizado algún inventario antes del período estipulado por el administrador
	$TiempoMinimo = DevolverParametro('TIEMPO_MINIMO_CONTROL_INVENTARIO');
	$IdInventario = date('dmYHis').'-'.$_SESSION["ID_EMPRESA"];
	
	$sqlUltimoInventarioParcial = "SELECT MAX(FECHA_HORA) AS ULTIMO_INVENTARIO FROM ZALM_CONTROL_INVENTARIO WHERE USUARIO = '".$_SESSION["ID_EMPRESA"]."';";
	$result = $conn->prepare($sqlUltimoInventarioParcial, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
	$result ->execute();
	
	if ($result->fetchColumn() > 0) {
		foreach ($conn->query($sqlUltimoInventarioParcial) as $row) {
			$UltimoInventario = $row["ULTIMO_INVENTARIO"];
		}
	}else{
		$UltimoInventario = '1999-01-01 00:00:00';
	}

	$Segundos = strtotime('now') - strtotime($UltimoInventario);
	$DiferenciaMinutos = intval($Segundos/60);

	if (($DiferenciaMinutos > $TiempoMinimo) or ($TiempoMinimo == 0)){
?>
	<article id="form">
		<a id="title-estadistico"></a>
		<form>
			<table id="inventarioparcial">
				<thead>
					<tr>
						<th width="100%"><?php print text_label_producto; ?></th>
						<?php if (DevolverParametro('PEDIR_UNIDADES_INVENTARIO_PARCIAL') == 'S'){ ?>
						<th><?php print  text_label_cantidad; ?></th>
						<?php }else{ print '<th height="26px"></th>';} ?>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><input type="text" name="producto" size="19" id="txt_input" class="producto no-margin" onkeypress="return tabular(event,this)" autofocus></td>
						<?php if (DevolverParametro('PEDIR_UNIDADES_INVENTARIO_PARCIAL') == 'S'){ ?>
							<td><input type="text" name="cantidad" size="7" id="txt_input no-margin" class="cantidad"/></td>
						<?php }else{ ?>
							<td style="display:none;"><input type="hidden" name="cantidad"  value="0" size="7" id="txt_input no-margin" class="cantidad"/></td>
						<?php }?>
						<td><a style="margin-left:90px;cursor:pointer;" ><?php //print text_label_confirmar; ?><img src="/img/confirmar-pq.png" alt="<?php print text_label_confirmar; ?>" onclick="EnviarControlInventario('<?php print $IdInventario; ?>');"></a></td>
					</tr>
				</tbody>
			</table>
		</form>
	</article>
	<button id="botonapp" onclick="InventarioParcial('/php/UbicacionAleatoria.php', <?php print DevolverParametro('CANTIDAD_CONTROL_UBICACIONES'); ?>, '<?php print $IdInventario; ?>')"><?php print text_label_siguiente_ubicacion; ?></button><br>
	<button id="botonapp" class="back" onclick="FinalizarInventarioParcial();"><?php print text_label_finalizar; ?></button><br>
	<!--<button id="botonapp" class="back" onclick="CargarInicio();"><?php print text_label_volver; ?></button><br>-->
	
	<script>
		$('.app-title').html("<?php print text_app_name_control_inventario ;?>")		
		InventarioParcial('/php/UbicacionAleatoria.php', <?php print DevolverParametro('CANTIDAD_CONTROL_UBICACIONES'); ?>, '<?php print $IdInventario?>');
	</script>
	<?php
		}else{
	?>
			<script>
				alertify.alert(IdiomaRecogerTexto('text_aviso_error_tiempo_minimo_control_inventario') + '<b><?php print $TiempoMinimo;?></b>' + IdiomaRecogerTexto('text_label_minutos'));
				CargarInicio();
			</script>
	<?php
		}
	?>