<?php
	//Limitamos la memoria utilizada a 16 megas para no tener error de buffer
	ini_set('memory_limit','16M');
	
	function limpiarCadena($valor){
		$valor = str_ireplace("SELECT","",$valor);
		$valor = str_ireplace("COPY","",$valor);
		$valor = str_ireplace("DELETE","",$valor);
		$valor = str_ireplace("DROP","",$valor);
		$valor = str_ireplace("DUMP","",$valor);
		$valor = str_ireplace(" OR ","",$valor);
		$valor = str_ireplace("%","",$valor);
		$valor = str_ireplace("LIKE","",$valor);
		$valor = str_ireplace("--","",$valor);
		$valor = str_ireplace("^","",$valor);
		$valor = str_ireplace("[","",$valor);
		$valor = str_ireplace("]","",$valor);
		$valor = str_ireplace("\\","",$valor);
		$valor = str_ireplace("!","",$valor);
		$valor = str_ireplace("¡","",$valor);
		$valor = str_ireplace("?","",$valor);
		$valor = str_ireplace("=","",$valor);
		$valor = str_ireplace("&","",$valor);
		return $valor;
	}
	
	function MostrarCodigo($string){
		print '<pre>'.$string.'</pre>';
	}
	
	function ConseguirApp($usuario, $empresa){
		require 'connection/ConnectLogin.php';
		$sql = "SELECT TB3.APLICACION AS ID, TB3.NOMBRE AS NOMBRE 
				FROM ZAPP_APLICACIONES_ASIGNADAS TB1 
				INNER JOIN ZESF_CLIENTES TB2 ON TB1.CLIENTE=TB2.CLIENTE 
				INNER JOIN ZAPP_APLICACIONES TB3 ON TB3.APLICACION= TB1.APLICACION 
				INNER JOIN zusu_usuarios TB4 ON TB4.CLIENTE=TB2.CLIENTE 
				WHERE TB4.USUARIO = '".$usuario."'
				AND TB2.NOMBRE='".$empresa."';";
		$result = sqlsrv_query( $conn, $sql , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
		if (sqlsrv_num_rows($result) == 0){
			$Aplicaciones[] = 'NO_APP';
		}else{
			while($row=@sqlsrv_fetch_array($result)){
				$Aplicaciones[] = $row["ID"];
			}
		}
		sqlsrv_close($conn);
		return $Aplicaciones;
	}
	
	function ImprimirNumero($numero, $decimales = 0){
		return round($numero, $decimales);
	}
	
	function ImprimirImporte($numero, $decimales = 2){
		return round($numero, $decimales). ' &euro;';
	}
	
	function ImprimirMoneda($numero, $decimales = 2, $divisa = '&euro;'){
		return round($numero, $decimales). ' '. $divisa;
	}
	
	function ImprimirFecha($fecha){
		return date('d/m/Y', strtotime($fecha));
	}
	
	function DevolverParametro($parametro){
		@session_start();
		include ('connection/connect.php');
		$sql = "SELECT VALOR_PARAMETRO AS VALOR FROM SYSTEMPA TB1 
				WHERE PARAMETRO='".$parametro."'
				AND GRUPO_PARAMETRO='TERMINAL_MOVIL';";
		
		$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result ->execute();
		if ($result->fetchColumn() <= 0) {
			$DevolverParametro = 'N';
		}else{
			foreach ($conn->query($sql) as $row) {
				$DevolverParametro = $row["VALOR"];
			}
		}
		return $DevolverParametro;
	}
	
	function ConseguirLongitudUbicacion($segmento){
		@session_start;
		include ('connection/connect.php');
		
		$sql ="SELECT LONGITUD AS LONGITUD FROM ZADM_ALM_ESTRUCTURA WHERE SEGMENTO=".$segmento.";";
		
		$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result ->execute();
		foreach ($conn->query($sql) as $row) {
			$logitud = (int) $row["LONGITUD"];
		}
		return $logitud;
	}

	function MostrarUbicacion($ubicacion){
		@session_start;
		include ('connection/connect.php');
		$PosicionAnterior = 0;
		$DesgloseUbicacion = '<br>';
		$sql = "SELECT LONGITUD, DESCRIPCION FROM ZADM_ALM_ESTRUCTURA;";
		$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result ->execute();
		foreach ($conn->query($sql) as $row) {
			$DesgloseUbicacion .= substr($ubicacion, $PosicionAnterior,$row["LONGITUD"]).' : ' . $row["DESCRIPCION"].'<br>';
			$PosicionAnterior = $PosicionAnterior + $row["LONGITUD"];
		}
		return $DesgloseUbicacion;
	}
	
	function LimpiarCadenaUbicacion($cadena = ''){
		@session_start;
		include ('connection/connect.php');
		$sql = "SELECT DESCRIPCION FROM ZADM_ALM_ESTRUCTURA;";
		$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result ->execute();
		foreach ($conn->query($sql) as $row) {
			$cadena = str_replace(' : ' . $row["DESCRIPCION"],'', $cadena);
		}
		$cadena = str_replace('<br>','', $cadena);
		return $cadena;
	}
	
	function RecortarDescripcion($string, $caracteres=23){
		if (strlen($string) >  $caracteres){
			$string = substr($string, 0, 19) . '[..]';
		}
		
		return $string;
	}
	
	/*
	function TipoDatoIntroducido($string){
		@session_start;
		include ('connection/connect.php');
		$sqlEtquetaInterna = "SELECT ETIQUETA_INTERNA FROM ZALM_ETIQUETAS WHERE ETIQUETA_INTERNA='".$string."' AND ALMACEN='".$_SESSION["ALMACEN"]."';";
		$result_etiqueta = $conn->prepare($sqlEtquetaInterna,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result_etiqueta ->execute();
		
		$sqlSSCC = "SELECT SSCC FROM ZALM_ETIQUETAS WHERE SSCC='".$string."' AND ALMACEN='".$_SESSION["ALMACEN"]."';";
		$result_SSCC = $conn->prepare($sqlSSCC,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result_SSCC ->execute();

		if ($result_SSCC->fetchColumn() > 0) {
			return 'sscc';
		}elseif($result_etiqueta->fetchColumn() > 0){
			return 'et.interna';
		}else{
			return 'producto';
		}
	}
	*/
	
	function TipoDatoIntroducido($string){
		if ((substr($string,0,2) == '00') && strlen($string)== 10 ) {
			return 'et.interna';
		}elseif(strlen($string) > 15) {
			return 'sscc';
		}else{
			return 'producto';
		}
	}
	
	function ConseguirNumeroPedido(){
		@session_start;
		include ('connection/connect.php');
		$sql = "SELECT PROXIMO_VALOR FROM ZADM_CONTADORES WHERE GRUPO='NUM_PEDIDO';";
		$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result ->execute();
		if ($result->fetchColumn() > 0) {
			foreach ($conn->query($sql) as $row) {
				return $row["PROXIMO_VALOR"];
			}
		}else{
			return 'error';
		}
	}
	
	function ActualizarNumeroPedido(){
		@session_start;
		include ('connection/connect.php');
		$sql = "UPDATE ZADM_CONTADORES SET PROXIMO_VALOR = PROXIMO_VALOR + 1 WHERE GRUPO='NUM_PEDIDO';";
		$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result ->execute();
	}
	
	function RelacionPedido($pedido){
		@session_start;
		include ('connection/connect.php');
		$sql = "SELECT MAX(RELACION) + 1 AS RELACION FROM ZMOV_PEDIDOS_LINEAS WHERE NUMERO='" . $pedido . "';";
		$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result ->execute();
		if ($result->fetchColumn() > 0) {
			foreach ($conn->query($sql) as $row) {
				if($row["RELACION"] <> ''){
					return $row["RELACION"];
				}else{
					return 1;
				}
			}
		}else{
			return 1;
		}
	}
	
	function ConseguirDescripcion($producto){
		@session_start;
		include ('connection/connect.php');
		$sql = "SELECT DESCRIPCION FROM ZADM_PRODUCTOS WHERE PRODUCTO='" . $producto . "';";
		$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result ->execute();
		foreach ($conn->query($sql) as $row) {
			return $row["DESCRIPCION"];
		}
	}
	
	function ConsegirStockProducto($producto, $almacen=''){
		@session_start;
		include ('connection/connect.php');
		if($almacen <> ''){
			$filtro_almacen = " AND TB1.ALMACEN= '".$_SESSION["ALMACEN"]."'";
		}else{
			$filtro_almacen = '';
		}

		$sql = "SELECT SUM(TB1.CANTIDAD_UNIDAD_VENTA) AS UNIDADES FROM ZALM_ALMACEN_OCUPADO TB1
		WHERE TB1.PRODUCTO='" . $producto . "' ".$filtro_almacen.";";

		$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result ->execute();
		if ($result->fetchColumn() > 0) {
			foreach ($conn->query($sql) as $row) {
				return $row["UNIDADES"];
			}
		}else{
			return 0;
		}
	}
	
	function ConseguirCantidadPedidas($producto){
		@session_start;
		include ('connection/connect.php');
		$sql = "SELECT SUM(TB1.CANTIDAD_UNIDAD_VENTA) AS UNIDADES FROM ZMOV_PEDIDOS_LINEAS TB1
		WHERE TB1.PRODUCTO='" . $producto . "';";
		
		$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result ->execute();
		if ($result->fetchColumn() > 0) {
			foreach ($conn->query($sql) as $row) {
				return $row["UNIDADES"];
			}
		}else{
			return 0;
		}
	}
	
	function ConseguirCantidadPendienteServir($producto, $almacen = ''){
		@session_start;
		include ('connection/connect.php');
		if($almacen <> ''){
			$filtro_almacen = " AND TB2.ALMACEN= '".$_SESSION["ALMACEN"]."'";
		}else{
			$filtro_almacen = '';
		}
		
		$sql = "SELECT ISNULL(SUM(CANTIDAD_UNIDAD_VENTA),0) AS PEDIDAS 
					FROM
						ZMOV_SAL_LINEAS TB1,
						ZMOV_SAL_CABECERA TB2
					WHERE   TB1.PRODUCTO = '".$producto."'
					AND TB1.NUMERO = TB2.NUMERO
					". $filtro_almacen ."
						AND TB1.FLAG_ESTADO IN ('N', 'H', 'P')
						AND TB1.PRODUCTO NOT IN 
							(SELECT TB3.PRODUCTO FROM ZMOV_SAL_LIN_ETIQUETAS TB3 
								WHERE TB3.PRODUCTO= '".$producto."' AND TB3.NUMERO=TB1.NUMERO);";
								
		$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result ->execute();
		if ($result->fetchColumn() > 0) {
			foreach ($conn->query($sql) as $row) {
				return $row["UNIDADES"];
			}
		}else{
			return 0;
		}
	}

	function ConseguirImporteTotalPedido($pedido){
		@session_start;
		include ('connection/connect.php');
		$sql = "SELECT SUM(TB1.CANTIDAD_UNIDAD_VENTA*PRECIO) AS TOTAL FROM ZMOV_PEDIDOS_LINEAS TB1
		WHERE TB1.NUMERO='" . $pedido . "';";
		
		$result = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$result ->execute();
		if ($result->fetchColumn() > 0) {
			foreach ($conn->query($sql) as $row) {
				return ImprimirMoneda($row["TOTAL"], 2, ' &euro;');
			}
		}else{
			return ImprimirMoneda(0, 2, ' &euro;');
		}
	}
	
	gc_disable();
?>