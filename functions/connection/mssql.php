<?php
	$serverName = "SERVER-PC\SQLEXPRESS"; //serverName\instanceName
	$database = 'ESFERA';
	$user = 'ALMACEN';
	$password = '33956576';
	$prefix = 'Z';
	if($parametro["database"]=='mssql') {
		$connectionInfo = array( "Database"=> $database, "UID"=>$user, "PWD"=>$password, "CharacterSet" => "UTF-8");
		$conn = sqlsrv_connect( $serverName, $connectionInfo);
		if( !$conn ) {
			 echo "Conexión no se pudo establecer.<br />";
			 die( print_r( sqlsrv_errors(), true));
		}
		$query = 'sqlsrv_query';
		$contar = 'sqlsrv_num_rows';
		$mostrar_registros = 'sqlsrv_fetch_array';
	}elseif($parametro["database"]=='oracle'){
		// Conectar al servicio XE (es deicr, la base de datos) en la máquina "localhost"
		$conn = oci_connect($user, $password, $serverName.'/'.$database);
		if (!$conn) {
			$e = oci_error();
			trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
		}
		$query = 'oci_parse';
	}elseif($parametro["database"]=='mysql'){
		
	}else{
		print "Base de datos no definida";
	}
?>
