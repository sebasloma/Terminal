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
	require_once ('config.php');
	require_once('../functions/connection/connect.php');
	

	if($_POST["CONFIRMAR"] == 'S'){
		@session_start;
		$sql = "UPDATE ZMOV_SAL_LINEAS
				SET FLAG_ESTADO = 'C'
					WHERE NUMERO = '".$_POST["ALBARAN"]."'
					AND PRODUCTO = '".$_POST["PRODUCTO"]."'
					AND RELACION = '".$_POST["LINEA"]."'
					AND ALMACEN = '".$_SESSION["ALMACEN"]."'
					AND ENTIDAD = '".$_SESSION["ENTIDAD"]."';";
					
		$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result ->execute();
		exit();
	}
	
	if($_POST["ACTUALIZARLINEA"] == 'S'){
		$sql = "UPDATE ZMOV_SAL_LINEAS
				SET TOTAL_UD_CONFIRMADA = '".$_POST["UNIDADES"]."'
					WHERE NUMERO = '".$_POST["ALBARAN"]."'
					AND PRODUCTO = '".$_POST["PRODUCTO"]."'
					AND RELACION = '".$_POST["LINEA"]."'
					AND ALMACEN = '".$_SESSION["ALMACEN"]."'
					AND ENTIDAD = '".$_SESSION["ENTIDAD"]."';";
					
		$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result ->execute();
		exit();
	}
	
	if($_POST["CONFIRMAR"] == 'D'){
		@session_start;
		$sql = "UPDATE ZMOV_SAL_LINEAS
				SET FLAG_ESTADO = 'R'
					WHERE NUMERO = '".$_POST["ALBARAN"]."'
					AND PRODUCTO = '".$_POST["PRODUCTO"]."'
					AND RELACION = '".$_POST["LINEA"]."'
					AND ALMACEN = '".$_SESSION["ALMACEN"]."'
					AND ENTIDAD = '".$_SESSION["ENTIDAD"]."';";
					
		$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result ->execute();
		exit();
	}

	if($_GET){
		if($_GET["CLIENTE"]){
			$sql = "SELECT NUMERO, NOMBRE_CLIENTE,
					(SELECT TB3.DESCRIPCION FROM ZADM_MOVIMIENTOS TB3 WHERE TB1.MOVIMIENTO=TB3.MOVIMIENTO) AS MOVIMIENTO,
					(SELECT COUNT(TB2.RELACION) FROM ZMOV_SAL_LINEAS TB2 WHERE TB1.NUMERO=TB2.NUMERO GROUP BY NUMERO) AS LINEAS
					FROM ZMOV_SAL_CABECERA TB1
					WHERE  FLAG_ESTADO='R' AND DESTINO = '".$_GET["CLIENTE"]."';";
			//print $sql;
			//exit;
			$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
			$result ->execute();
			$CantidadRegistros = (int) $result->rowCount();
			//Si no hay registros mostramos al usuario un aviso y nos vamos Confirmacion de servicio de nuevo
			if ($CantidadRegistros == 0){
?>
			<script>
				alertify.alert(IdiomaRecogerTexto('text_aviso_cliente_no_albaran'));
				setTimeout(function() {ConseguirUbicacion('/php/ConfirmarServicio.php');}, 1000);
			</script>
<?php			
			}else{
				print '<table id="product-search">';
				foreach ($conn->query($sql) as $row) {
					print '<tr Onclick="BuscarLineas(\''.$row["NUMERO"].'\');">';
						print '<td>';
							print '<b>'.$row["NUMERO"].'</b><br>';
							print $row["MOVIMIENTO"].' - ' . $row["NOMBRE_CLIENTE"] . '<br>';
							print text_label_total_lineas . ' : ' . $row["LINEAS"];
						print '</td>';
					print '</tr>';
				}
				print '</table>';
			}
		}
		if($_GET["ALBARAN"]){
			$sql = "SELECT PRODUCTO, DESCRIPCION_PRODUCTO, TOTAL_UD_VENTA_SERVIDA AS UNIDADES_PEDIDAS,
					TOTAL_UD_CONFIRMADA AS UNIDADES_CONFIRMADAS,
					FLAG_ESTADO AS ESTADO, RELACION AS RELACION
					FROM ZMOV_SAL_LINEAS
					WHERE NUMERO = '".$_GET["ALBARAN"]."';";

			$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
			$result ->execute();
			$CantidadRegistros = (int) $result->rowCount();
			//Si no hay registros mostramos al usuario un aviso y nos vamos Confirmacion de servicio de nuevo
			if ($CantidadRegistros == 0){
?>
			<script>
				alertify.alert(IdiomaRecogerTexto('text_aviso_cliente_no_albaran'));
				setTimeout(function() {ConseguirUbicacion('/php/ConfirmarServicio.php');}, 2000);
			</script>
<?php			
			}else{
				print '<table id="product-search">';
				foreach ($conn->query($sql) as $row) {
					if($row["ESTADO"] == 'R'){
						$Clase= 'lineamostrada';
						$Onclick = ''; 
						
					}else{
						$Clase= 'lineaescondida';
						$Onclick = 'DesconfirmarLinea();';
					}
					print '<tr Onclick="SeleccionarLinea(this);'.$Onclick.'" class="'.$Clase.'">';
						print '<td>';
							print '<b><a name="producto">'.$row["PRODUCTO"].'</a></b><br>';
							print $row["DESCRIPCION_PRODUCTO"];
							print '<input type="text" class="unidades-pedida" name="cantidad-pedida" value="' . intval($row["UNIDADES_PEDIDAS"]) .'" readonly>';
							print '<input type="text" class="unidades-confirm" name="cantidad-confirmada" value="' . intval($row["UNIDADES_CONFIRMADAS"]) .'" readonly>';
							print '<input type="hidden" class="albaran" name="albaran" value="' . $_GET["ALBARAN"] .'" readonly>';
							print '<input type="hidden" class="producto" name="producto" value="' . $row["PRODUCTO"] .'" readonly>';
							print '<input type="hidden" class="relacion" name="relacion" value="' . $row["RELACION"] .'" readonly>';
						print '</td>';
					print '</tr>';
				}
				print '</table>';
			}
?>
			<section id="buttonstools">
				<a style="margin-left:10px;cursor:pointer;" Onclick="Editarlinea();" ><?php //print text_label_confirmar; ?>
					<img src="/img/edit-pq.png" alt="<?php print text_label_confirmar; ?>" >
				</a>
				<a style="margin-left:10px;cursor:pointer;" Onclick="AcumularAlTotal();" ><?php //print text_label_confirmar; ?>
					<img src="/img/sumar-pq.png" alt="<?php print text_label_acumular; ?>" >
				</a>
				<a style="margin-left:10px;cursor:pointer;" Onclick="ConfirmarLinea();" ><?php //print text_label_confirmar; ?>
					<img src="/img/confirm.png" alt="<?php print text_label_confirmar_linea; ?>" >
				</a>
				<a style="margin-left:10px;cursor:pointer;" Onclick="MostrarOcultarLineas();" ><?php //print text_label_confirmar; ?>
					<img src="/img/ver.png" alt="<?php print text_label_mostrar_ocultar; ?>" >
				</a>
			</section>
			<script>
				ComprobarFinalTabla();
			</script>
<?php		
		
		}
		
	}else{
?>
		<input type="text" name="cliente" placeholder="<?php print text_placeholder_cliente; ?>" id="txt_input" class="cliente holo" Onkeyup="EnviarPeticion('/php/SearchClientFilter.php', 'code', '.label-desc','N');">
		<i Onclick="CargarPopup('/php/SearchClient.php');" class="fa fa-search fa-2x"></i>

		<input type="text" name="description" class="label-desc" readonly>


		<section id="buttonstools">
			<button id="buttonconfirm" class="aceptar" onclick="BuscarAlbaranes($('.code').val());" ><?php print text_label_aceptar; ?></button>
			<!--<button id="buttonconfirm" class="limpiar" onclick="$(code).val('');$('.label-desc').val('');" ><?php print text_label_limpiar; ?></button>-->
			<button id="buttonconfirm" class="leer-codigo" OnClick="CargarPopup('/php/SearchAlbaran.php');$('.ubicacion-codigo').focus();">
				<!-- <?php //print text_label_leer_codigo; ?> -->
				<i class="fa fa-barcode fa-2x"></i>
			</button>
		</section>

		<div id="modal3" class="modalmask">
		</div>
<?php
	}
?>
	<button id="botonapp" class="back" onclick="Redirigir('/');"><?php print text_label_volver; ?></button><br>
	<script>
		$('.app-title').html("<?php print text_app_name_confirmar_servicio_mini ;?>")
		$('#txt_input').focus();
	</script>
	