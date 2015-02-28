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
	@session_start;
	$parametro["database"] = $_SESSION["TIPO_BBDD"];
	$parametro["raiz"] = '';
	$parametro["/"] = '/';
	$parametro["css"] = $parametro["raiz"].'/css/';
	$parametro["js"] = $parametro["raiz"].'/js/';
	$parametro["img"] = $parametro["raiz"].'img/';
	$parametro["func"] = $_SERVER['DOCUMENT_ROOT'].$parametro["/"].'functions/';
	$parametro["php"] =$_SERVER['DOCUMENT_ROOT'].$parametro["/"].'php/';
?>