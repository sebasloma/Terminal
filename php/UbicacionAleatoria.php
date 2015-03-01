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
	
	@session_start();
	require '../functions/functions.php';
	require '../languages/'.$_SESSION["IDIOMA"];
	require_once ('config.php');
	require_once('../functions/connection/connect.php');
	
	//Numero de ubicaciones que vamos a comprobar esto debe ponerlo por defecto el usuario.
	$NumeroUbicacionesTotal = DevolverParametro('CANTIDAD_CONTROL_UBICACIONES');
	$sql = "SELECT ESTADISTICO FROM ZALM_UBICACIONES
				WHERE ESTADISTICO IN (SELECT ESTADISTICO_ALMACEN
				FROM ZALM_ALMACEN_OCUPADO)
				AND ALMACEN='".$_SESSION["ALMACEN"]."'
				AND ESTADISTICO NOT IN(SELECT ESTADISTICO_ALMACEN  
					FROM ZALM_CONTROL_INVENTARIO 
					WHERE ALMACEN = '".$_SESSION["ALMACEN"]."' 
					AND USUARIO = '".$_SESSION["ID_EMPRESA"]."'
					AND ENTIDAD = '".$_SESSION["ENTIDAD"]."');";

	$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
	$result ->execute();
	
	if ($result->fetchColumn() > 0) {
		foreach ($conn->query($sql) as $row) {
			$UbicacionesOcupadas[] = $row["ESTADISTICO"];
		}
	}	
	
	$i = 0;
	$EnviarUbicaciones = array();
	//Si el número de ubicaciones a contar es mayor que el número de ubicaciones del almacén escogemos 
	if (count($UbicacionesOcupadas) > $NumeroUbicacionesTotal){
		while( $i <= $NumeroUbicacionesTotal ){
			$UbicacionAleatoria = array_rand($UbicacionesOcupadas, 1);
			if (!in_array($UbicacionesOcupadas[$UbicacionAleatoria], $EnviarUbicaciones)){
				$EnviarUbicaciones[] = $UbicacionesOcupadas[$UbicacionAleatoria];
				$i++;
			}
		}
		sort($EnviarUbicaciones);
		echo json_encode($EnviarUbicaciones);  // lo convierte a fichero json
	}elseif(count($UbicacionesOcupadas) == $NumeroUbicacionesTotal){
		sort($UbicacionesOcupadas);
		echo json_encode($UbicacionesOcupadas);  // lo convierte a fichero json	
	}elseif(count($UbicacionesOcupadas) < $NumeroUbicacionesTotal){
		$UbicacionesFaltantes = $NumeroUbicacionesTotal - count($UbicacionesOcupadas);
		$i = 1;
		if(count($UbicacionesOcupadas)== 0){
			$sql = "SELECT ESTADISTICO FROM ZALM_UBICACIONES
					WHERE ESTADISTICO IN (SELECT ESTADISTICO_ALMACEN
					FROM ZALM_ALMACEN_OCUPADO)
					AND ALMACEN='".$_SESSION["ALMACEN"]."';";
		}else{
			$sql = "SELECT ESTADISTICO FROM ZALM_UBICACIONES
							WHERE ESTADISTICO IN (SELECT ESTADISTICO_ALMACEN
							FROM ZALM_ALMACEN_OCUPADO)
							AND ALMACEN='".$_SESSION["ALMACEN"]."'
							AND ESTADISTICO NOT IN(".implode(',',$UbicacionesOcupadas).");";
		}
		$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result ->execute();
		foreach ($conn->query($sql) as $row) {
			if ($i <= $UbicacionesFaltantes){
				$UbicacionesOcupadas[] = $row["ESTADISTICO"];
				$i++;
			}
		}
		//print $UbicacionesOcupadas;
		sort($UbicacionesOcupadas);
		echo json_encode($UbicacionesOcupadas);  // lo convierte a fichero json
	}
?>