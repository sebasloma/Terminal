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
	
	if($_GET["JSTXTLANG"]){
		$texto = constant($_GET["JSTXTLANG"]);
		echo $texto;
	}

	if($_GET["UBICACION_EXISTE"]){
		@session_start;
		include ('connection/connect.php');
		$sqlUbicacion = "SELECT * FROM ZALM_UBICACIONES WHERE ESTADISTICO='".$_GET["UBICACION_EXISTE"]."' AND ALMACEN='".$_SESSION["ALMACEN"]."';";
		$result_ubicacion = $conn->prepare($sqlUbicacion,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result_ubicacion ->execute();
		if ($result_ubicacion->fetchColumn() > 0) {
			//si la ubicacion existe pasamos un 1
			print 1;
		}else{
			//si la ubicacion NO existe pasamos un 0
			print 0;
		}
	}
?>