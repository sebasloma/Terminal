<?php
	ob_start ();
	error_reporting(0);
	@session_start();
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

	require '../functions/functions.php';
	require '../languages/'.$_SESSION["IDIOMA"];
	require_once ('config.php');
	require_once('../functions/connection/connect.php');
	
	if($_POST["UbicacionFinal"]){
	//Mostramos la ubicación que hemos escogido
		print  '<a id ="titulo">' . text_label_consult_ubicacion . '</a>' . MostrarUbicacion($_POST["UbicacionFinal"]);
?>
	<script>
		$('.app-title').html('<?php print text_aviso_ubicacion_escogida; ?>');
	</script>
<?php		
	}elseif($_GET["RecuperarSegmentos"]){
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
			print '<div id="ubicacion" OnClick="SeleccionarUbicacion(\''.$row["UBICACION"].'\', '.$PosicionSiguiente.', \'/php/DesgloseUbicaciones.php\');">'.$row["UBICACION"].'</div>';
		}
		print '</div>';
	}
	$conn = null;
	flush();
	ob_end_flush();
?>

	<section id="buttonstools">
		<button id="buttonconfirm" class="aceptar"><?php print text_label_aceptar; ?></button>
		<button id="buttonconfirm" class="leer-codigo" OnClick="location.href='#modal3';$('.ubicacion-codigo').focus();">
			<!-- <?php //print text_label_leer_codigo; ?> -->
			<i class="fa fa-barcode fa-2x"></i>
		</button>
		<button id="buttonconfirm" class="volver-ubicacion" Onclick="LimpiarConsultaUbicaciones(); ConseguirUbicacion();"><?php print text_label_comenzar_nuevo; ?></button>
	</section>

	<button id="botonapp" class="back" onclick="CargarAplicacion('/php/ConsultaStock.php', 'contenido', 'N','')"><?php print text_label_volver; ?></button><br>

	<div id="modal3" class="modalmask">
		<div class="modalbox resize">
			<a href="#close" title="Close" class="close">X</a>
			<div class="modal-content">
				<input type="text" id="txt_input" class="ubicacion-codigo" placeholder="<?php print text_placeholder_esperando_ubicacion?>" onchange="ConsultaStockUbicacion($(this).val(),'S');">
			</div>
		</div>
	</div>