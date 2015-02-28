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
?>
<div class="modalbox resize">
	<a href="#close" title="Close" class="close">X</a>
	<div class="modal-content">
<?php
	require '../functions/functions.php';
	require '../languages/'.$_SESSION["IDIOMA"];
	require_once ('config.php');
	require_once('../functions/connection/connect.php');
	
	if($_GET["SSCC"]){
		$filtro_sscc_et_interna = "AND TB2.SSCC = ".$_GET["SSCC"];
	}elseif($_GET["ET_INTERNA"]){
		$filtro_sscc_et_interna = "AND TB2.ETIQUETA_INTERNA = ".$_GET["ET_INTERNA"];
	}

	$sql = "SELECT TB1.PRODUCTO AS PRODUCTO, TB1.DESCRIPCION AS DESCRIPCION,
			TB2.LOTE AS LOTE, TB2.CANTIDAD_UNIDAD_VENTA AS CANTIDAD,
			TB2.ESTADISTICO_ALMACEN AS UBICACION, TB2.SSCC AS SSCC,
			TB2.ETIQUETA_INTERNA AS E_INTERNA, TB3.FECHA_CADUCIDAD AS F_CADUCIDAD,
			TB4.DESCRIPCION AS ES_ETIQUETA, TB4.FLAG_PERMITE_SALIDA AS PER_SALIDA,
			TB1.UNIDAD_MEDIDA AS UD_MEDIDA
			FROM ZADM_PRODUCTOS TB1
			INNER JOIN ZALM_ALMACEN_OCUPADO TB2 ON TB1.PRODUCTO = TB2.PRODUCTO
			AND ALMACEN='".$_SESSION["ALMACEN"]."'
			INNER JOIN ZALM_ETIQUETAS TB3 ON TB3.ETIQUETA_INTERNA = TB2.ETIQUETA_INTERNA
			AND TB3.PRODUCTO = TB2.PRODUCTO
			INNER JOIN ZADM_ESTADOS_ETIQUETAS TB4 ON TB3.FLAG_ESTADO = TB4.FLAG_ESTADO
			AND TB1.PRODUCTO='".$_GET["PRODUCTO"]."' 
			AND TB2.ESTADISTICO_ALMACEN='".$_GET["UBICACION"]."'
			".$filtro_sscc_et_interna."
			ORDER BY TB2.ESTADISTICO_ALMACEN;";	
			
	$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
	$result ->execute();
	if ($result->fetchColumn() <= 0) {
		print 'No hay productos para mostrar.';
	}else{
		//Declaramos las variables
			$CantidadTotal = 0;
			$IncompletosTotal = 0;
			$CompletosTotal = 0;
		print '<table id="stock-product">';
		foreach ($conn->query($sql) as $row) {
			print '<tr>';
				print '<td>'. text_label_descripcion .'</td>';
				print '<td>'.$row["DESCRIPCION"].'</td>';
			print '</tr>';
			print '<tr>';
				print '<td>'. text_label_codigo .'</td>';
				print '<td>'.$row["PRODUCTO"].'</td>';
			print '</tr>';
			if (($row["UD_MEDIDA"] == '') || ($row["UD_MEDIDA"] == null)){
				$row["UD_MEDIDA"] = 'UN';
			}
			print '<tr>';
				print '<td>'. text_label_etiqueta_cantidad.'</td>';
				print '<td>'.ImprimirNumero($row["CANTIDAD"]). " " .$row["UD_MEDIDA"].'</td>';
			print '</tr>';
			print '<tr>';
				print '<td>'. text_label_etiqueta_interna .'</td>';
				print '<td>'.$row["E_INTERNA"].'</td>';
			print '</tr>';
			print '<tr>';
				print '<td>'. text_label_fecha_caducidad .'</td>';
				print '<td>'.ImprimirFecha($row["F_CADUCIDAD"]).'</td>';
			print '</tr>';				
			print '<tr>';
				print '<td>'. text_label_estado_etiqueta .'</td>';
				print '<td>'.$row["ES_ETIQUETA"].'</td>';
			print '</tr>';
			print '<tr>';
				print '<td>'. text_label_sscc .'</td>';
				print '<td>'.$row["SSCC"].'</td>';
			print '</tr>';
			print '<tr class="border-bottom">';
				print '<td>'. text_label_lote.'</td>';
				print '<td>'.$row["LOTE"].'</td>';
			print '</tr>';
			print '<tr><td colspan="2"><hr class="separador"></tr></tr>';
		}
		print '</table>';
	}
?>
	</div>
</div>
<script>
	$("#modal3").height($(document).height());
</script>