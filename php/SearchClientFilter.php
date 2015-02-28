<?php
	error_reporting(0);
	@session_start();
	require '../functions/functions.php';
	require '../languages/'.$_SESSION["IDIOMA"];
	require_once ('config.php');
	require_once('../functions/connection/connect.php');

	if(isset($_POST["CONSULTA"])){
		$sql = "SELECT * FROM ZADM_CLIENTES WHERE CLIENTE = '".$_POST["CONSULTA"]."';";
		$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result ->execute();
		if ($result->fetchColumn() <= 0) {
			print 0;
		}else{
			foreach ($conn->query($sql) as $row) {
				print $row["NOMBRE"];
			}
		}
	}elseif(isset($_POST["BUSCAR"])){
		$FILTRO = "WHERE TB1.CLIENTE LIKE '%".$_POST["BUSCAR"] ."%' 
					OR TB1.NOMBRE LIKE '%".$_POST["BUSCAR"]."%'";
					
		$sql = "SELECT * FROM ZADM_CLIENTES TB1 ".$FILTRO." ;";

		$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result ->execute();
		$CantidadRegistros = (int) $result->rowCount();
		
		if ($CantidadRegistros <= 0) {
			print text_aviso_no_clientes;
		}elseif($CantidadRegistros > 50){
			print text_aviso_muchos_clientes;
		}else{
			print '<table id="product-search">';
			foreach ($conn->query($sql) as $row) {
				print '<tr Onclick="RegistroEncontrado(\''.$row["CLIENTE"].'\',\''.$row["NOMBRE"].'\',\'cliente\');">';
					print '<td>';
						print '<b>'.$row["CLIENTE"].'</b><br>';
						print $row["CLIENTE"].' - ' . $row["NOMBRE"];
					print '</td>';
				print '</tr>';
			}
			print '</table>';
		}
	}
?>