<?php
	error_reporting(0);
	@session_start();
	require '../functions/functions.php';
	require '../languages/'.$_SESSION["IDIOMA"];
	require_once ('config.php');
	require_once('../functions/connection/connect.php');

	
	if(isset($_POST["CONSULTA"])){
	
		$sql = "SELECT * FROM ZADM_PRODUCTOS WHERE PRODUCTO = '".$_POST["CONSULTA"]."';";
		$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result ->execute();
		if ($result->fetchColumn() <= 0) {
			print 'El producto no existe.';
		}else{
			foreach ($conn->query($sql) as $row) {
				print $row["DESCRIPCION"];
			}
		}
	}elseif(isset($_POST["BUSCAR"])){

		$FILTRO = "WHERE TB1.PRODUCTO LIKE '%".$_POST["BUSCAR"] ."%' 
					OR TB1.DESCRIPCION LIKE '%".$_POST["BUSCAR"]."%'
					OR TB1.EAN_UNIDAD_VENTA LIKE '%".$_POST["BUSCAR"]."%'
					OR TB1.EAN_UNIDAD_TRANSPORTE LIKE '%".$_POST["BUSCAR"]."%'";
					
		$sql = "SELECT * FROM ZADM_PRODUCTOS TB1 ".$FILTRO." ;";

		$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result ->execute();
		$CantidadRegistros = (int) $result->rowCount();
		
		if ($CantidadRegistros <= 0) {
			print text_aviso_no_producto;
		}elseif($CantidadRegistros > 50){
			print text_aviso_muchos_producto;
		}else{
			print '<table id="product-search">';
			foreach ($conn->query($sql) as $row) {
				print '<tr Onclick="RegistroEncontrado(\''.$row["PRODUCTO"].'\',\''.$row["DESCRIPCION"].'\', \'code\');">';
					print '<td>';
						print '<b>'.$row["PRODUCTO"].'</b><br>';
						print $row["DESCRIPCION"].' - ' . $row["CODIGO_PROPIO"];
					print '</td>';
				print '</tr>';
			}
			print '</table>';
		}
	}
?>