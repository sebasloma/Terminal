
<?php
    if (isset($_SERVER['HTTP_ORIGIN'])) {  
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");  
        header('Access-Control-Allow-Credentials: true');  
        header('Access-Control-Max-Age: 86400');   
    }  
      
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {  
      
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))  
            header("Access-Control-Allow-Methods: GET, GET, PUT, DELETE, OPTIONS");  
      
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))  
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");  
    }
	
	@session_start();
	require '../functions/functions.php';
	require '../languages/'.$_SESSION["IDIOMA"];
	require('config.php');
	require('../functions/connection/connect.php');
	
	$Subestructura = '';
	$sqlDatos = "SELECT SUBESTRUCTURA FROM ZADM_ALMACENES TB WHERE ALMACEN='".$_SESSION["ALMACEN"]."';";
	$result = $conn->prepare($sqlDatos, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
	$result ->execute();
	foreach ($conn->query($sqlDatos) as $row) {
		$Subestructura = $row["SUBESTRUCTURA"];
	}
	
	$Relacion = '';
	$sqlRelacion = "SELECT MAX(RELACION) AS RELACION FROM  ZALM_CONTROL_INVENTARIO 
					WHERE ID_INVENTARIO='".$_GET["ID_INVENTARIO"]."'
					AND USUARIO='".$_SESSION["ID_EMPRESA"]."';";

	$result = $conn->prepare($sqlRelacion, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
	$result ->execute();
	if ($result->fetchColumn() > 0) {
		foreach ($conn->query($sqlRelacion) as $row) {
			$Relacion = $row["RELACION"] + 1;
		}
	}else{
		$Relacion = 1;
	}

	if($_GET["PRODUCTO"]){
		
		if(TipoDatoIntroducido($_GET["PRODUCTO"]) == 'producto'){
			$sqlProductoExiste = "SELECT * FROM ZADM_PRODUCTOS WHERE PRODUCTO = '".$_GET["PRODUCTO"]."';";
			$result = $conn->prepare($sqlProductoExiste, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
			$result ->execute();
			if ($result->fetchColumn() > 0) {
				InsertarLineaInventarioParcial($_SESSION["ENTIDAD"], $_SESSION["ALMACEN"], $_GET["ID_INVENTARIO"], $Relacion, $Subestructura, $_GET["UBICACION"], $_GET["PRODUCTO"],'', $_GET["CANTIDAD"], 0, $_SESSION["ID_EMPRESA"], '');
			}else{
				echo 1;
			}
		}elseif(TipoDatoIntroducido($_GET["PRODUCTO"]) == 'sscc'){
			
			/* quitamos por ahora AND SUBESTRUCTURA='".$Subestructura."' */
			$sqlSSCCExiste = "SELECT ETIQUETA_INTERNA, PRODUCTO, SSCC FROM ZALM_ETIQUETAS 
									WHERE ENTIDAD='".$_SESSION["ENTIDAD"]."' 
									AND ALMACEN ='".$_SESSION["ALMACEN"]."'
									AND SSCC = '".$_GET["PRODUCTO"]."';";
			
			$result = $conn->prepare($sqlSSCCExiste, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
			$result ->execute();
			if ($result->fetchColumn() > 0) {
				foreach ($conn->query($sqlSSCCExiste) as $row) {
					InsertarLineaInventarioParcial($_SESSION["ENTIDAD"], $_SESSION["ALMACEN"], $_GET["ID_INVENTARIO"], $Relacion, $Subestructura, $_GET["UBICACION"], $row["PRODUCTO"],$row["SSCC"], $_GET["CANTIDAD"], 0, $_SESSION["ID_EMPRESA"],$row["ETIQUETA_INTERNA"]);
				}
			}else{
				echo 2;
			}
		}elseif(TipoDatoIntroducido($_GET["PRODUCTO"]) == 'et.interna'){
			/* quitamos por ahora AND SUBESTRUCTURA='".$Subestructura."' */		
			$sqlEtriqutaInternaExiste = "SELECT ETIQUETA_INTERNA, PRODUCTO, SSCC FROM ZALM_ETIQUETAS 
									WHERE ENTIDAD='".$_SESSION["ENTIDAD"]."'
									AND ALMACEN ='".$_SESSION["ALMACEN"]."'
									AND ETIQUETA_INTERNA = '".$_GET["PRODUCTO"]."';";

			$result = $conn->prepare($sqlEtriqutaInternaExiste, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
			$result ->execute();
			if ($result->fetchColumn() > 0) {
				foreach ($conn->query($sqlEtriqutaInternaExiste) as $row) {
					InsertarLineaInventarioParcial($_SESSION["ENTIDAD"], $_SESSION["ALMACEN"], $_GET["ID_INVENTARIO"], $Relacion, $Subestructura, $_GET["UBICACION"], $row["PRODUCTO"],$row["SSCC"], $_GET["CANTIDAD"], 0, $_SESSION["ID_EMPRESA"],$row["ETIQUETA_INTERNA"]);
				}
			}else{
				echo 3;
			}
		}else{
			echo 1;
		}
	}elseif($_GET["SIGUIENTE"]){
		$sqlUbicacionVacia = "SELECT * FROM ZALM_CONTROL_INVENTARIO WHERE ENTIDAD='".$_SESSION["ENTIDAD"]."'
								AND ALMACEN ='".$_SESSION["ALMACEN"]."' AND ID_INVENTARIO = '".$_GET["ID_INVENTARIO"]."' 
								AND ESTADISTICO_ALMACEN='".$_GET["SIGUIENTE"]."';";
		$result = $conn->prepare($sqlUbicacionVacia, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result ->execute();
		if ($result->fetchColumn() <= 0) {
			InsertarLineaInventarioParcial($_SESSION["ENTIDAD"], $_SESSION["ALMACEN"], $_GET["ID_INVENTARIO"], $Relacion, $Subestructura, $_GET["SIGUIENTE"], '','', '', '', $_SESSION["ID_EMPRESA"],'');
		}
	}
	
	function InsertarLineaInventarioParcial($entidad, $almacen, $inventario, $relacion, $subestructura, $ubicacion, $producto, $sscc, $cantidad, $kilos, $empresa, $etiqueta_interna){
		require('../functions/connection/connect.php');
	
		$sql = "INSERT INTO ZALM_CONTROL_INVENTARIO (ENTIDAD,
												 ALMACEN,
												 ID_INVENTARIO,
												 RELACION,
												 SUBESTRUCTURA_ALMACEN,
												 ESTADISTICO_ALMACEN,
												 PRODUCTO,
												 FECHA_HORA,
												 SSCC,
												 CANTIDAD_UNIDAD_VENTA,
												 KILOS,
												 USUARIO,
												 ETIQUETA_INTERNA)
				VALUES ('".$entidad."',
						'".$almacen."',
						'".$inventario."',
						'".$relacion."',
						'".$subestructura."',
						'".$ubicacion."',
						'".$producto."',
						'".date('d/m/Y H:m:i')."',
						'".$sscc."',
						'".$cantidad."',
						'".$kilos."',
						'".$empresa."',
						'".$etiqueta_interna."');";
			$result = $conn->prepare($sql);
			$result ->execute();
	}
?>