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

	if($_POST["ACCION"] == 'INSERT_CABECERA'){
		//Insertamos la linea de la cabecera y la ponemos a estado 'C' de creado
		$sql = "INSERT INTO ZMOV_PEDIDOS_CABECERA VALUES ('".$_POST["PEDIDO"]."', '".$_POST["CLIENTE"]."', 
					'".$_POST["MOVIMIENTO"]."', '".$_POST["FECHA_PEDIDO"]."', '".$_POST["FECHA_PROGRAMADA"]."', 'C');";
		$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result ->execute();
		ActualizarNumeroPedido();
	}
	
	if($_GET["ACCION"] == 'INSERT_LINEA'){
		$sql = "SELECT * FROM ZMOV_PEDIDOS_LINEAS WHERE PRODUCTO = '".$_GET["PRODUCTO"]."' AND NUMERO = '".$_GET["NUMERO"]."';";
		$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result ->execute();
		if ($result->fetchColumn() > 0) {
			//El producto existe en el albaran hay que preguntar si quiere sumar a la cantidad o no
			print 2;
		}else{
		//Insertamos la linea de la cabecera y la ponemos a estado 'C' de creado
			$sql = "INSERT INTO ZMOV_PEDIDOS_LINEAS VALUES ( '".$_GET["NUMERO"]."', ".ImprimirNumero(RelacionPedido($_GET["NUMERO"])).", 'C', 
					'".$_GET["PRODUCTO"]."', '". ConseguirDescripcion($_GET["PRODUCTO"])."', 
					(SELECT PRECIO FROM ZADM_PRODUCTOS WHERE PRODUCTO='".$_GET["PRODUCTO"]."'), ".$_GET["CANTIDAD"].");";
			$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
			$result ->execute();
			//El producto NO existe en el albaran insertamos la linea
			print 1;
		}
		
	}
	
	if($_GET["ACCION"] == 'SUMAR_CANTIDAD_LINEA'){
		//Insertamos la linea de la cabecera y la ponemos a estado 'C' de creado
		$sql = "UPDATE ZMOV_PEDIDOS_LINEAS SET CANTIDAD_UNIDAD_VENTA = CANTIDAD_UNIDAD_VENTA + ".$_GET["CANTIDAD"]."
					WHERE NUMERO = '".$_GET["NUMERO"]."' AND PRODUCTO = '".$_GET["PRODUCTO"]."';";
		$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result ->execute();
	}
	
	if($_GET["ACCION"] == 'REFRESCAR_TABLA'){
		//Metemos el último registro introducido
		$sql="SELECT *
				FROM ZMOV_PEDIDOS_LINEAS 
				WHERE NUMERO = '" . $_GET["NUMERO"] . "'
				AND RELACION IN 
				(SELECT MAX(RELACION) FROM ZMOV_PEDIDOS_LINEAS WHERE NUMERO = '" . $_GET["NUMERO"] . "' );";
		$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result ->execute();
		if ($result->fetchColumn() > 0) {
			foreach ($conn->query($sql) as $row) {
				$subtotal = $row["PRECIO"] * ImprimirNumero($row["CANTIDAD_UNIDAD_VENTA"]);
				$insert = '<tr onclick="SeleccionarLinea(this);">';
				$insert.= '<td><a class="producto">'.$row["PRODUCTO"].' </a>';
				$insert.= '<br> '.RecortarDescripcion($row["DESCRIPCION_PRODUCTO"], 28);
				$insert.= '<br> Uds. <tab>'.ImprimirNumero($row["CANTIDAD_UNIDAD_VENTA"]);
				$insert.= ' x ' . ImprimirMoneda( $row["PRECIO"]);
				$insert.= '<input type="text" class="unidades-pedidas" value="'.ImprimirMoneda($subtotal).'" disabled></td>';
				$insert.= '</tr>';
?>	
				<script>
					$('table#lineas-pedido > tbody:last').append('<?php print $insert;?>');
				</script>
<?php
				print $insert;
				$insert = '';
			}
		}else{
			print 'error';
		}
	}
	
	if($_GET["COMPROBAR_PRODUCTO"]){
		$sql="SELECT * FROM ZADM_PRODUCTOS WHERE PRODUCTO='".$_GET["COMPROBAR_PRODUCTO"]."';";
		$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result ->execute();
		if ($result->fetchColumn() > 0) {
			print 1;
		}else{
			print 0;
		}
	}
	
	if($_GET["COMPROBAR_CANTIDAD"]){
		$unidades_stock = ((ConsegirStockProducto($_GET["COMPROBAR_CANTIDAD"]) - ConseguirCantidadPedidas($_GET["COMPROBAR_CANTIDAD"])) - ConseguirCantidadPendienteServir($_GET["COMPROBAR_CANTIDAD"]));
		print $unidades_stock;
	}

	if($_GET["ACTION"]== 'REFRESCAR_TOTAL'){
?>
		<script>
			$('#TotalPedido').html('<?php ConseguirImporteTotalPedido($_GET["NUMERO"]);?>');
		</script>
<?php				
	}
	
	if($_GET["ACCION"] == 'ELIMINAR_LINEA'){
		//Insertamos la linea de la cabecera y la ponemos a estado 'C' de creado
		$sql = "DELETE ZMOV_PEDIDOS_LINEAS	WHERE NUMERO = '".$_GET["NUMERO"]."' AND PRODUCTO = '".$_GET["PRODUCTO"]."';";
		$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result ->execute();
?>
		<script>
			alertify.success(IdiomaRecogerTexto("text_aviso_actualizada_eliminada_linea_pedido"));
		</script>
<?php		
	}
	
	gc_disable();
?>