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
	
	session_start();
	//Incluimos los parametros necesarios
	require_once('functions.php');
	require_once('../php/config.php');
	require_once('connection/ConnectLogin.php');
	
	//Recogemos las variables login
	$user = $_POST["user"];
	$password = $_POST["password"];
	
	//Formamos la consulta para saber si existe el usuario
	$sql = "SELECT TB1.USUARIO AS NOMBRE_USUARIO, TB2.NOMBRE AS EMPRESA, TB2.IP_SERVIDOR AS IP,
			TB2.TIPO_BASE_DATOS AS TIPO_BBDD, TB1.ENTIDAD AS ENTIDAD, TB1.ALMACEN AS ALMACEN,
			TB5.ARCHIVO AS IDIOMA, TB2.NOMBRE_BASE_DATOS AS NAME_BBDD,
			TB2.USUARIO_BASE_DATOS AS USER_BBDD, TB2.PASS_BASE_DATOS AS PASS_BBDD,
			TB2.CLIENTE AS ID_EMPRESA
			FROM zusu_usuarios TB1
			INNER JOIN ZESF_CLIENTES TB2 ON TB1.CLIENTE=TB2.CLIENTE
			INNER JOIN zapp_idiomas TB5 ON TB1.IDIOMA=TB5.ID
			WHERE TB1.USUARIO='".$user."' 
			AND TB1.PASSWORD = CONVERT(VARCHAR(32), HashBytes('MD5', '".$password."'), 2);";

	// Envía una consulta a MSSQL
	$result = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
	if (sqlsrv_num_rows($result) == 0) {
		echo "Error el usuario o la contraseña no son correctas";
	}else{
			/* establecer la caducidad de la caché a 30 minutos */
		session_cache_expire(30);
		while ($row =@sqlsrv_fetch_array($result)) {
			$_SESSION["USERNAME"] =  $row["NOMBRE_USUARIO"];
			$_SESSION["EMPRESA"] =  $row["EMPRESA"];
			$_SESSION["ID_EMPRESA"] =  $row["ID_EMPRESA"];
			$_SESSION["ENTIDAD"] =  $row["ENTIDAD"];
			$_SESSION["ALMACEN"] =  $row["ALMACEN"];
			$_SESSION["IP"] =  $row["IP"];
			$_SESSION["TIPO_BBDD"] =  $row["TIPO_BBDD"];
			$_SESSION["USER_BBDD"] =  $row["USER_BBDD"];
			$_SESSION["PASS_BBDD"] =  $row["PASS_BBDD"];
			$_SESSION["NAME_BBDD"] =  $row["NAME_BBDD"];
			$_SESSION["IDIOMA"] =  $row["IDIOMA"];
			$_SESSION['LAST_ACTIVITY'] = time();
			echo "ok";
		}
	}
	sqlsrv_close($conn);
?>
