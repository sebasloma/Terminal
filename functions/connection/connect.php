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
	
	define("SERVIDOR", $_SESSION["IP"], true);
	define("BASEDATOS", $_SESSION["NAME_BBDD"], true);
	define("USUARIO", $_SESSION["USER_BBDD"], true);
	define("PASS", $_SESSION["PASS_BBDD"], true);
	define("PREFIJO", "Z", true);	

	if($_SESSION["TIPO_BBDD"]=='mssql') {
		
		try{
			$conn = new PDO("sqlsrv:Server=".SERVIDOR.";Database=".BASEDATOS."", "".USUARIO."", "".PASS."",array(
				PDO::ATTR_PERSISTENT => true
			));
		}catch (PDOException $e){
			echo $e->getMessage();
			die();
		} 
	}elseif($_SESSION["TIPO_BBDD"]=='oracle'){
		
	$tns = "(DESCRIPTION =
				(ADDRESS_LIST =
					(ADDRESS = (PROTOCOL = TCP)(HOST = ".SERVIDOR.")(PORT = 1521))
				)
					(CONNECT_DATA =
					  (SERVICE_NAME = orcl)
					)
				  )
					   ";
				$db_username = "ALMACEN";
				$db_password = "33956576";
				try{
					$conn = new PDO("oci:dbname=".$tns,$db_username,$db_password);
				}catch(PDOException $e){
					echo ($e->getMessage());
				}
	}elseif($_SESSION["TIPO_BBDD"]=='mysql'){
		$conn = new PDO('mysql:dbname=ESFERA;host=localhost;charset=UTF-8', 'root', 'pass',
			array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	}else{
		print "Base de datos no definida";
	}
?>
