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
	
	//Si recibimos el parametro "CREAR" mostramos la pantalla de creación de pedidos
	if($_GET["CREAR"]== 'S'){
		print '<table width="100%">';
			print '<tr>';
				print '<td width="50%"><a class="label">' . text_label_numero_documento . ': </a></td>';
				print '<td width="50%"><input type="text" name="num-pedido" class="num-pedido holo" value="'.ImprimirNumero(ConseguirNumeroPedido()).'" readonly></td>';
			print '</tr>';
			print '<tr>';
				print '<td width="50%"><a class="label">' . text_label_fecha_hoy . ': </a></td>';
				print '<td width="50%"><input type="text" name="fecha-actual" class="fecha-actual holo" value="'. date('d/m/Y') .'" readonly disabled></td>';
			print '</tr>';
			print '<tr>';
				print '<td width="50%"><a class="label">' . text_label_fecha_programada . ': </a></td>';
				print '<td width="50%"><input type="text" id="fecha-prog" onclick="$(\'#fecha-prog\').pickadate();"  name="fecha-prog" class="fecha-prog holo" value="" placeholder="' . text_placeholder_fecha . '"></td>';
			print '</tr>';
			print '<tr>';
				print '<td width="50%"><a class="label">' . strtoupper(text_label_cliente) . ': </a></td>';
				print '<td width="50%"><input type="text" name="cliente" Onclick="CargarPopup(\'/php/SearchClient.php\');" class="cliente holo" value="" placeholder="' . text_placeholder_cliente . '"></td>';
			print '</tr>';
			print '<tr>';
			print '</tr>';
			print '<tr>';
				print '<td width="50%"><a class="label">' . strtoupper(text_label_movimiento) . ': </a></td>';
				print '<td width="50%"><input type="text" name="movimiento" Onclick="CargarPopup(\'/php/SearchMovimiento.php\');" class="movimiento holo" value="" placeholder="' . text_placeholder_movimiento . '"></td>';
			print '</tr>';
			print '<tr>';
			print '</tr>';
		print '</table>';
?>

	<section id="buttonstools">
		<button id="buttonconfirm" class="aceptar" onclick="CrearPedido();" ><?php print text_label_aceptar; ?></button>
		<button id="buttonconfirm" class="limpiar" onclick="$('.code').val('');$('.label-desc').val('');" ><?php print text_label_limpiar; ?></button>
	</section>
	<button id="botonapp" class="back" onclick="CargarAplicacion('/php/Pedidos.php', 'contenido', 'N','');"><?php print text_label_volver; ?></button><br>

<?php		
	}elseif($_GET["PEDIDO"]){
		print '<p id="title-insert"><span id="TotalPedido">'.ConseguirImporteTotalPedido($_GET["PEDIDO"]).'</span>';
		print '<i Onclick="CargarPopup(\'/php/SearchProduct.php\');" class="fa fa-search fa-2x"></i></p>';
?>
		<input type="text" style="position:fixed;top: 38px;margin: 0 -50px;color: white;overflow: hidden;z-index: 4 !important;" name="producto" class="code holo" value="" autofocus="autofocus" id="EntrarLiniaPedido" onkeypress="return CapturarEnter(event)">
		<input type="hidden" name="pedido" class="pedido" value="<?php print $_GET["PEDIDO"]?>">
		<div class="barra-lateral">
			<div id="contenedor-tools">
				<i class="fa fa-arrow-up fa-2x" onclick="SeleccionarLineaPedido('-')"></i><br>
				<i class="fa fa-arrow-down fa-2x" onclick="SeleccionarLineaPedido('+')"></i><br>
				<i class="fa fa-times fa-2x" onclick="EliminarLineaPedido()"></i>
			</div>
		</div>
<?php
		$sql="SELECT * FROM ZMOV_PEDIDOS_LINEAS WHERE NUMERO='".$_GET["PEDIDO"]."';";
							
				$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
				$result ->execute();
				print '<table id="lineas-pedido">';
					print '<tbody>';
					foreach ($conn->query($sql) as $row) {
						$subtotal = $row["PRECIO"] * ImprimirNumero($row["CANTIDAD_UNIDAD_VENTA"]);
						print '<tr onclick="SeleccionarLinea(this);">';
							print '<td><a class="producto">'.$row["PRODUCTO"].' </a>
									<br> '.RecortarDescripcion($row["DESCRIPCION_PRODUCTO"], 28).' 
									<br> Uds. <tab>'.ImprimirNumero($row["CANTIDAD_UNIDAD_VENTA"]).
									' x ' . ImprimirMoneda( $row["PRECIO"]).
									'<input type="text" class="unidades-pedidas" value="'.ImprimirMoneda($subtotal).'" disabled></td>';
						print '</tr>';
					}
					print '</tbody>';
				print '</table>';
?>		
		<script>
			$('.code').focus();
			var Arriba = $('#title-insert').offset().top - parseFloat($('#title-insert').css('marginTop').replace(/auto/, 0));
			$(document).ready(function () {
			  $(window).scroll(function (event) {
				var movimiento = $(this).scrollTop();
				if (movimiento >= Arriba) {
				  $('#title-insert').addClass('totop');
				  $('#EntrarLiniaPedido').addClass('totop');
				} else {
					$(EntrarLiniaPedido).removeClass('totop');
					$('#title-insert').removeClass('totop');
				}
			  });
			});
		</script>		
		<button id="botonapp" class="back" onclick="CargarAplicacion('/php/Pedidos.php', 'contenido', 'N','');"><?php print text_label_volver; ?></button><br>
<?php		
	}else{
?>
		<button id="buttonconfirm" class="aceptar" style="margin:10px 0" Onclick="AbrirPedido($('tr.select-registro > td.pedido').html(), $('tr.select-registro > td.cliente').attr('name'));"><?php print text_button_seleccionar?></button>
		<button id="buttonconfirm" class="aceptar" style="margin:10px 0" onclick="CargarAplicacion('/php/Pedidos.php?CREAR=S', 'contenido', 'N','')"><?php print text_button_crear_albaran?></button>

<?php	
		$sql="SELECT TB1.NUMERO AS NUMERO, TB1.FECHA_PEDIDO AS FECHA_PEDIDO, 
					TB2.NOMBRE AS CLIENTE, TB1.CLIENTE AS COD_CLIENTE
				FROM ZMOV_PEDIDOS_CABECERA TB1, 
					ZADM_CLIENTES TB2
				WHERE TB1.CLIENTE = TB2.CLIENTE
					AND TB1.FLAG_ESTADO IN('C','N')
					ORDER BY NUMERO;";
					
		$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result ->execute();
		if ($result->fetchColumn() <= 0) {
			print '<table id="pedidos" width="100%">';
				print '<tr>';
					print '<th>' . text_aviso_no_hay_pedido_mostrar . '</th>';
				print '</tr>';
			print '</table>';
		}else{
			print '<table id="pedidos" width:"100%">';
				print '<tr>';
					print '<th width="33.3%">' . text_label_doc_salida . '</th>';
					print '<th width="33.3%">' . text_label_cliente . '</th>';
					print '<th width="33.3%">' . text_label_fecha_pedido . '</th>';
				print '</tr>';
				foreach ($conn->query($sql) as $row) {
					print '<tr Onclick="SeleccionarLinea(this);">';
						print '<td class="pedido">'.$row["NUMERO"].'</td>';
						print '<td name="'.$row["COD_CLIENTE"].'" class="cliente">'.RecortarDescripcion($row["CLIENTE"], 40).'</td>';
						print '<td>'.ImprimirFecha($row["FECHA_PEDIDO"]).'</td>';
					print '</tr>';
				}
			print '</table>';
		}
?>
		<button id="botonapp" class="back" onclick="CargarInicio();"><?php print text_label_volver; ?></button><br>
<?php		
	}
?>
<div id="modal3" class="modalmask">
</div>

<script>
	$('.app-title').html("<?php print text_app_name_pedidos ;?>");
</script>