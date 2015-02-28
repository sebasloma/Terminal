<?php
	error_reporting(0);
	@session_start();
	require '../functions/functions.php';
	require '../languages/'.$_SESSION["IDIOMA"];
	require_once ('config.php');
	require_once('../functions/connection/connect.php');
	
	if(isset($_POST["BUSCAR"])){
		$FILTRO = "WHERE TB1.MOVIMIENTO LIKE '%".$_POST["BUSCAR"] ."%' 
					OR TB1.DESCRIPCION LIKE '%".$_POST["BUSCAR"]."%'";
					
		$sql = "SELECT * FROM ZADM_MOVIMIENTOS TB1 ".$FILTRO." ;";

		$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result ->execute();
		$CantidadRegistros = (int) $result->rowCount();
		
		if ($CantidadRegistros <= 0) {
			print text_aviso_no_movimientos;
		}elseif($CantidadRegistros > 50){
			print text_aviso_muchos_movimientos;
		}else{
			print '<table id="product-search">';
			foreach ($conn->query($sql) as $row) {
				print '<tr Onclick="RegistroEncontrado(\''.$row["MOVIMIENTO"].'\',\''.$row["DESCRIPCION"].'\', \'movimiento\');">';
					print '<td>';
						print '<b>'.$row["CLIENTE"].'</b><br>';
						print $row["MOVIMIENTO"].' - ' . $row["DESCRIPCION"];
					print '</td>';
				print '</tr>';
			}
			print '</table>';
		}
	}
?>