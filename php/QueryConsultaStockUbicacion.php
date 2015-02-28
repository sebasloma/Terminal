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
	
	if($_GET["INCOMPRE"] = 'S'){
		$FILTRO = "AND TB2.ESTADISTICO_ALMACEN LIKE '".$_GET["BUSCAR"]."%'";
	}else{
		$FILTRO = "AND TB2.ESTADISTICO_ALMACEN = '".$_GET["BUSCAR"]."'";
	}
	
	$sql = "SELECT TB1.PRODUCTO AS PRODUCTO, TB1.DESCRIPCION AS DESCRIPCION,
			SUM(TB2.CANTIDAD_UNIDAD_VENTA) AS CANTIDAD,
			TB2.ESTADISTICO_ALMACEN AS UBICACION, TB1.UNIDADES_VENTA_PALET AS UD_PALET
			FROM ZADM_PRODUCTOS TB1
			INNER JOIN ZALM_ALMACEN_OCUPADO TB2 ON TB1.PRODUCTO = TB2.PRODUCTO
			AND ALMACEN='".$_SESSION["ALMACEN"]."'
			INNER JOIN ZALM_ETIQUETAS TB3 ON TB3.ETIQUETA_INTERNA = TB2.ETIQUETA_INTERNA
			AND TB3.PRODUCTO = TB2.PRODUCTO
			INNER JOIN ZADM_ESTADOS_ETIQUETAS TB4 ON TB3.FLAG_ESTADO = TB4.FLAG_ESTADO
			".$FILTRO."
			GROUP BY TB2.ESTADISTICO_ALMACEN, TB1.DESCRIPCION, TB1.PRODUCTO, TB1.UNIDADES_VENTA_PALET
			ORDER BY TB2.ESTADISTICO_ALMACEN;";

	$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
	$result ->execute();
	
//Declaramos las variables
	$CantidadTotal = 0;
	$IncompletosTotal = 0;
	$CompletosTotal = 0;
	$UnidadesCompletas = 0;
	$UnidadesIncompletas = 0;
	$CantidadTotalInc = 0;
	$CantidadTotalCom = 0;
			
			
	if ($result->fetchColumn() <= 0) {
		print '<script>alertify.alert("El producto no se encuentra en la ubicación");</script>';
		print '<table id="stock-product">';
			print '<tr>';
				print '<td colspan="3" class="rect-negro">';
					print text_label_total;
				print '</td>';
			print '</tr>';
			print '<tr>';
				print '<td class="tipo-palet">';
					print text_label_completo.'<br>';
					print '<div style="text-align:left;width: 45%;float: left;">'.ImprimirNumero($CompletosTotal).'</div>';
					print '<div style="text-align:right;width: 45%;float: left;">'.ImprimirNumero($CantidadTotalCom).'</div>';
				print '</td>';
				print '<td class="tipo-palet">';
					print text_label_incompleto.'<br>';
					print '<div style="text-align:left;width: 45%;float: left;">'.ImprimirNumero($IncompletosTotal).'</div>';
					print '<div style="text-align:right;width: 45%;float: left;">'.ImprimirNumero($CantidadTotalInc).'</div>';
				print '</td>';
				print '<td></td>';
			print '</tr>';
			print '<tr>';
				print '<td>'. text_label_total .' <b>'.ImprimirNumero($CantidadTotal).' - '.ImprimirImporte($row["PRECIO"]).' </b></td>';
			print '</tr>';
		print '</table>';
	}else{
			
		print '<table id="stock-product">';
		foreach ($conn->query($sql) as $row) {
			print "<script>	$('.app-title').html('<a style=\'font-size:13px !important;\'>".$row["DESCRIPCION"]."'); </script>";
			
			if($row["UD_PALET"] == $row["CANTIDAD"]){
				$Completas = 1;
				$Incompletas = 0;
				$UdCompletas = ImprimirNumero($row["CANTIDAD"]);
				$UdIncompletas = 0;
				$CantidadTotalCom = $CantidadTotalCom + $row["CANTIDAD"];
				$CompletosTotal++;
			}else{
				$Incompletas = 1;
				$Completas = 0;
				$UdCompletas = 0;
				$UdIncompletas = ImprimirNumero($row["CANTIDAD"]);
				$CantidadTotalInc = $CantidadTotalInc + $row["CANTIDAD"];
				$IncompletosTotal++;
			}

			print '<tr onclick="CargarPopup(\'/php/QueryEtiquetas.php?UBICACION='.$row["UBICACION"].'&PRODUCTO='.$row["PRODUCTO"].'\')">';
				print '<td colspan="3" class="rect-negro">';
					print text_label_ubicacion . ' ' . $row["UBICACION"];
				print '</td>';
			print '</tr>';
			print '<tr onclick="CargarPopup(\'/php/QueryEtiquetas.php?UBICACION='.$row["UBICACION"].'&PRODUCTO='.$row["PRODUCTO"].'\')">';
				print '<td class="tipo-palet">';
					print text_label_completo.'<br>';
					print '<div style="text-align:left;width: 45%;float: left;"> '.$Completas.' </div>';
					print '<div style="text-align:right;width: 45%;float: left;">'.$UdCompletas.'</div>';
				print '</td>';
				print '<td class="tipo-palet">';
					print text_label_incompleto.'<br>';
					print '<div style="text-align:left;width: 45%;float: left;"> '.$Incompletas.'</div>';
					print '<div style="text-align:right;width: 45%;float: left;">'.$UdIncompletas.'</div>';
				print '</td>';
				print '<td></td>';
			print '</tr>';
			print '<tr onclick="CargarPopup(\'/php/QueryEtiquetas.php?UBICACION='.$row["UBICACION"].'&PRODUCTO='.$row["PRODUCTO"].'\')">';
				print '<td colspan="3" class="total-stock">'.text_label_total.' <b>'.ImprimirNumero($row["CANTIDAD"]).' - '.ImprimirImporte($row["PRECIO"]).' </b></td>';
			print '</tr>';
			$CantidadTotal = $CantidadTotal + $row["CANTIDAD"];
		}
			print '<tr>';
				print '<td colspan="3" class="rect-negro">';
					print text_label_total;
				print '</td>';
			print '</tr>';
			print '<tr>';
				print '<td class="tipo-palet">';
					print text_label_completo.'<br>';
					print '<div style="text-align:left;width: 45%;float: left;">'.ImprimirNumero($CompletosTotal).'</div>';
					print '<div style="text-align:right;width: 45%;float: left;">'.ImprimirNumero($CantidadTotalCom).'</div>';
				print '</td>';
				print '<td class="tipo-palet">';
					print text_label_incompleto.'<br>';
					print '<div style="text-align:left;width: 45%;float: left;">'.ImprimirNumero($IncompletosTotal).'</div>';
					print '<div style="text-align:right;width: 45%;float: left;">'.ImprimirNumero($CantidadTotalInc).'</div>';
				print '</td>';
				print '<td></td>';
			print '</tr>';
			print '<tr>';
				print '<td>'. text_label_total .' <b>'.ImprimirNumero($CantidadTotal).' - '.ImprimirImporte($row["PRECIO"]).' </b></td>';
			print '</tr>';
		print '</table>';
	}
?>

<div id="modal3" class="modalmask">
</div>
<button id="botonapp" class="back" onclick="CargarAplicacion('<?php print $_GET["URL"]?>', 'contenido', 'N', '');"><?php print text_label_volver; ?></button><br>
<script>
	$('.app-title').html("<?php print text_label_ubicacion . ' : ' . $_GET["BUSCAR"]; ?>");
</script>