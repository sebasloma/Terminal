var host = 'http://test.esferasoftware.es';
//Recuperamos la cantidad de segmentos que tiene el almacen

var SegmentoActual = parseInt(1);
var LongitudAnterior = parseInt(1);
var ConsultaUbicacion = '';

function LimpiarConsultaUbicaciones(){
	SegmentoActual = parseInt(1);
	LongitudAnterior = parseInt(1);
	ConsultaUbicacion = '';
}
function ConseguirUbicacion(EnviarAURL, confirmacion){
	if (confirmacion == 'S'){
		alertify.confirm(IdiomaRecogerTexto('text_inventario_cambiar_ubicaion'), function (e) {
		// str is the input text
			if (e) {
			//Miramos si la ubicacion esta vacia
			RevisarUbicacionInventarioVacia($('#title-estadistico').text(), $('.IdInventario').val());
			//si el usuario acepta salimos de esta pantalla y vamos a la seleccion de las ubicaciones
				ProcesoConseguirUbicacion();
			} else {
			//si el usuario se niega mostramos la cancelación del reenvio
				alertify.alert(IdiomaRecogerTexto('text_inventario_no_salir_ubicacion'));
				}
			});
	}else{
		ProcesoConseguirUbicacion();
	}
	function ProcesoConseguirUbicacion(){
		var parametros = {
			"UbicacionActual" : SegmentoActual,
			"LongitudAnterior" : LongitudAnterior,
			"UbicacionConsultar": ConsultaUbicacion,
		};
		$.ajax({
			async: false,
			data:  parametros,
			url:   host + EnviarAURL,
			type:  'POST',
			beforeSend: function () {
				$("#contenido").html(IdiomaRecogerTexto("text_loading_search_ubicaciones"));
			},
			success:  function (response) {
				$("#contenido").html(response);
				if (SegmentoActual > 1 ){
					$('.leer-codigo').css('display', 'none');
					$('.aceptar').css('width', '100%');
					$('.volver-ubicacion').css('display', 'block');
				}
			},
			error:  function () {
				$("#contenido").html(IdiomaRecogerTexto("text_aviso_error_send_data"));
			}
		});
	}}

function SeleccionarUbicacion(UbicacionParcial, Longitud, UrlAEnviar){
	var datos = {
		"RecuperarSegmentos" :  'SEGMENTOS',
	};
	$.ajax({
		async: false,
		data:  datos,
		url:   host + UrlAEnviar,
		type:  'GET',
		success:  function (Segmentos) {
			NumSegmentos =   Segmentos;
		},
		error:  function () {
			//Si no contesta le pondremos 3
			NumSegmentos =  3;
		}
	});

	if (SegmentoActual < NumSegmentos){
		LongitudAnterior = LongitudAnterior + parseInt(Longitud);
		SegmentoActual++;
		ConsultaUbicacion = ConsultaUbicacion + UbicacionParcial;
		ConseguirUbicacion(UrlAEnviar, 'N');
		$("#buttonconfirm").attr("Onclick", "ConsultaStockUbicacion('" + ConsultaUbicacion + "','S')");
	}else{
		ConsultaUbicacion = ConsultaUbicacion + UbicacionParcial;
		MostrarUbicacion(ConsultaUbicacion, UrlAEnviar);
		LimpiarConsultaUbicaciones();
	}
}

function MostrarUbicacion(Ubicacion, UrlAEnviar){	
	var UbicacionExaminar = {
		"UbicacionFinal" : Ubicacion,
	};
	
	$.ajax({
		async: false,
		data:  UbicacionExaminar,
		url:   host + UrlAEnviar,
		type:  'POST',
		beforeSend: function () {
			$("#contenido").html(IdiomaRecogerTexto("text_loading_search_ubicaciones"));
		},
		success:  function (response) {
			$("#contenido").html(response);
			$("#buttonconfirm").attr("Onclick", "ConsultaStockUbicacion('" + Ubicacion + "','N')");
			$('.leer-codigo').css('display', 'none');
			$('.aceptar').css('width', '100%');
		},
		error:  function () {
			 $("#contenido").html(IdiomaRecogerTexto("text_aviso_error_send_data"));
		}
	});
}