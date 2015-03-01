
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
	
	function InsertarLineaInventario($entidad, $almacen, $inventario, $usuario, $ubicacion, $sscc, $et_interna, $cantidad, $kilos, $relacion){
			require('../functions/connection/connect.php');
		
			$sql = "INSERT INTO ZALM_INVENTARIO 
						(ENTIDAD, 
						ALMACEN, 
						ID_INVENTARIO, 
						USUARIO, 
						FECHA_INVENTARIO, 
						FECHA_HORA, 
						ESTADISTICO_ALMACEN, 
						SSCC, 
						ETIQUETA_INTERNA, 
						CANTIDAD_UNIDAD_VENTA, 
						KILOS,
						RELACION)
						VALUES
						('".$entidad."',
						'".$almacen."',
						'".$inventario."',
						'".$usuario."',
						'".date('d/m/Y')."',
						'".date('d/m/Y H:m:i')."',
						'".$ubicacion."',
						'".$sscc."',
						'".$et_interna."',
						'".$cantidad."',
						'".$kilos."',
						'".$relacion."');";
				$result = $conn->prepare($sql);
				$result ->execute();
	}
	
		$Relacion = '';
		$sqlRelacion = "SELECT MAX(RELACION) AS RELACION FROM  ZALM_INVENTARIO 
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
		if(TipoDatoIntroducido($_GET["PRODUCTO"]) == 'sscc'){		
			InsertarLineaInventario($_SESSION["ENTIDAD"], $_SESSION["ALMACEN"], $_GET["ID_INVENTARIO"],$_SESSION["ID_EMPRESA"], $_GET["UBICACION"], $_GET["PRODUCTO"], '', $_GET["CANTIDAD"], 0, $Relacion);
		}elseif(TipoDatoIntroducido($_GET["PRODUCTO"]) == 'et.interna'){
			InsertarLineaInventario($_SESSION["ENTIDAD"], $_SESSION["ALMACEN"], $_GET["ID_INVENTARIO"],$_SESSION["ID_EMPRESA"], $_GET["UBICACION"], '', $_GET["PRODUCTO"], $_GET["CANTIDAD"], 0, $Relacion);
		}else{
			echo 1;
		}
		
		
	}elseif($_GET["BORRAR_UBICACION_INVENTARIO"]){
		
		$sqlBorrarUbicacionInventario = "DELETE FROM ZALM_INVENTARIO WHERE ESTADISTICO_ALMACEN='".$_GET["BORRAR_UBICACION_INVENTARIO"]."';";
		$result = $conn->prepare($sqlBorrarUbicacionInventario);
		$result ->execute();
		
	}elseif($_GET["SIGUIENTE_UBICACION"]){
		$sqlUbicacionVacia = "SELECT * FROM ZALM_INVENTARIO 
											WHERE ESTADISTICO_ALMACEN='".$_GET["SIGUIENTE_UBICACION"]."'
											AND ALMACEN='".$_SESSION["ALMACEN"]."'
											AND ID_INVENTARIO='".$_GET["ID_INVENTARIO"]."'
											AND USUARIO='".$_SESSION["ID_EMPRESA"]."';";

		$result = $conn->prepare($sqlUbicacionVacia);
		$result ->execute();
		if ($result->fetchColumn() <= 0) {
			InsertarLineaInventario($_SESSION["ENTIDAD"], $_SESSION["ALMACEN"], $_GET["ID_INVENTARIO"],$_SESSION["ID_EMPRESA"], $_GET["SIGUIENTE_UBICACION"], 0, '', 0, 0, $Relacion);
		}
	}
	
	$conn = null;
	flush();
	ob_end_flush();
?>