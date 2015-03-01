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
	error_reporting(0);
	@session_start();
	require '../functions/functions.php';
	require '../languages/'.$_SESSION["IDIOMA"];
	require('config.php');
	require('../functions/connection/connect.php');

	if($_GET["RecuperarSegmentos"]){
	//Devolvemos la cantidad de segmentos que tiene el almacen
		$sql = 'SELECT count (*) AS SEGMENTOS FROM ZADM_ALM_ESTRUCTURA;';
		$CantidadSegmentos = 3;
		foreach ($conn->query($sql) as $row) {
			$CantidadSegmentos = $row["SEGMENTOS"];
		}
		print $CantidadSegmentos;
		exit();
	}else{
	//Mostramos en el titulo el nombre del segmento
		$sql = "SELECT DESCRIPCION AS NOMBRE FROM ZADM_ALM_ESTRUCTURA WHERE SEGMENTO=".$_POST["UbicacionActual"].";";
		$NombreSegmento = '';
		foreach ($conn->query($sql) as $row) {
			$NombreSegmento = $row["NOMBRE"];
		}
?>
	<script>
		$('.app-title').html('<?php print $NombreSegmento; ?>');
	</script>
<?php		
		$PosicionActual = $_POST["LongitudAnterior"];
		$PosicionSiguiente = (int) ConseguirLongitudUbicacion($_POST["UbicacionActual"]);
		
		//Maximo de ubicaciones por fila
		$UbicacionFila = 6;
		$ContadorUbicaciones = 1;
		$sql = "SELECT SUBSTRING(ESTADISTICO,".$PosicionActual.",".$PosicionSiguiente.") AS UBICACION
					FROM ZALM_UBICACIONES WHERE ALMACEN='".$_SESSION["ALMACEN"]."' 
						AND ESTADISTICO LIKE '".$_POST["UbicacionConsultar"]."%'
						AND ENTIDAD='01' 
						AND SUBESTRUCTURA='01'
					GROUP BY  SUBSTRING(ESTADISTICO,".$PosicionActual.",".$PosicionSiguiente.");";
		
		print '<div id="contenido-ubicaciones">';
		foreach ($conn->query($sql) as $row) {
			print '<div id="ubicacion" OnClick="SeleccionarUbicacion(\''.$row["UBICACION"].'\', '.$PosicionSiguiente.', \'/php/Inventario.php\');">'.$row["UBICACION"].'</div>';
		}
		print '</div>';
?>
	<section id="buttonstools">
		<button id="buttonconfirm" class="aceptar"><?php print text_label_aceptar; ?></button>
		<button id="buttonconfirm" class="leer-codigo" OnClick="location.href='#modal3';$('.ubicacion-codigo').focus();">
			<!-- <?php //print text_label_leer_codigo; ?> -->
			<i class="fa fa-barcode fa-2x"></i>
		</button>
		<button id="buttonconfirm" class="volver-ubicacion" Onclick="LimpiarConsultaUbicaciones(); ConseguirUbicacion();"><?php print text_label_comenzar_nuevo; ?></button>
		<button id="botonapp" class="back" onclick="CargarInicio();"><?php print text_label_volver; ?></button>
	</section>
	<div id="modal3" class="modalmask">
		<div class="modalbox resize">
			<a href="#close" title="Close" class="close">X</a>
			<div class="modal-content">
				<input type="text" id="txt_input" class="ubicacion-codigo" placeholder="<?php print text_placeholder_esperando_ubicacion?>" onchange="EnviarUbicacion($(this).val(),'/php/Inventario.php');">
			</div>
		</div>
	</div>
<?php
	}
	
	if($_POST["UbicacionFinal"]){
?>
	<script>
		$('.app-title').html("<?php print text_app_name_inventario ;?>")
	</script>
<?php
		//Miramos si la ubicacion fue inventariada, si es que SI preguntamos i borramos la ubicacion o no.
		$sqlVerificarUbicacion = "SELECT ESTADISTICO_ALMACEN  FROM ZALM_INVENTARIO WHERE ESTADISTICO_ALMACEN  = '".$_POST["UbicacionFinal"]."';";
		$result = $conn->prepare($sqlVerificarUbicacion);
		$result ->execute();
		if ($result->fetchColumn() > 0) {
?>			
		<script>
			alertify.confirm(IdiomaRecogerTexto('text_inventario_existe_borrar'), function (e) {
				// str is the input text
				if (e) {
					//si el usuario acepta borrarlo enviamos la orden a la funcion y seguimos con el programa
					var parametros = {
						"BORRAR_UBICACION_INVENTARIO" : '<?php print $_POST["UbicacionFinal"]; ?>'
					};
					$.ajax({
						async: false,
						data:  parametros,
						url:   host + '/php/InventarioInsertarLinea.php',
						type:  'GET',
						success:  function () {
							alertify.alert(IdiomaRecogerTexto('text_inventario_borrado'));
						}
					});
				} else {
					//si el usuario se niega a borrar el inventario realizado volvemos a la pantalla de elección de ubicación
					alertify.alert(IdiomaRecogerTexto('text_inventario_no_borrado'));
					setTimeout(function() {ConseguirUbicacion('/php/Inventario.php');}, 2000);
				}
			});
		</script>

<?php		
		}
		
	//Generamos el numero de inventario	
		$IdInventario = date('dmYHis').'-'.$_SESSION["ID_EMPRESA"];
?>
	<script>
		$('#contenido-ubicaciones').css('margin','0');
		$('#buttonconfirm').css('display','none');
		$('.leer-codigo').css('display','none');
	</script>
	
	<article id="form">
		<a id="title-estadistico"><?php print $_POST["UbicacionFinal"]; ?></a>
		<form>
			<table id="">
				<thead>
					<tr>
						<th><?php print text_label_sscc_et_interna; ?></th>
						<th><?php print  text_label_cantidad; ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><input type="number" name="producto" size="19" id="txt_input" class="producto no-margin" onkeypress="return tabular(event,this)" autofocus></td>
						<td><input type="number" name="cantidad" size="4" id="txt_input" class="cantidad no-margin"/></td>
						<td>
							<a style="margin-left:10px;cursor:pointer;" ><?php //print text_label_confirmar; ?>
								<img src="/img/confirmar-pq.png" alt="<?php print text_label_confirmar; ?>" onclick="EnviarInventario('<?php print $IdInventario; ?>');">
							</a>
							<input type="hidden" class="IdInventario" value="<?php print $IdInventario; ?>">
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	</article>
	
	<button id="botonapp" onclick="ConseguirUbicacion('/php/Inventario.php','S');"><?php print text_label_otra_ubicacion; ?></button><br>
	<button id="botonapp" class="back" onclick="FinalizarInventario('<?php print $_POST["UbicacionFinal"]; ?>','<?php print $IdInventario; ?>');"><?php print text_label_finalizar; ?></button><br>
	<!--<button id="botonapp" class="back" onclick="CargarInicio();"><?php print text_label_volver; ?></button><br>-->
<?php
	}
	$conn = null;
	flush();
	ob_end_flush();
?>

<!--<button id="botonapp" class="back" onclick="CargarAplicacion('/php/ConsultaStock.php', 'contenido', 'N','')"><?php print text_label_volver; ?></button><br>-->