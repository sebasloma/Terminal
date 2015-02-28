//Declaramos la variables Globales
	var host = 'http://test.esferasoftware.es';
	document.title = 'ESFERA SOFTWARE';
$( document ).ready(function() {
	CargarInicio();
alertify.defaults = {
        // dialogs defaults
        modal:true,
        basic:false,
        frameless:false,
        movable:false,
        resizable:true,
        closable:true,
        maximizable:true,
        startMaximized:false,
        pinnable:true,
        pinned:true,
        padding: true,
        overflow:true,
        maintainFocus:true,
        transition:'pulse',
		focus: { element:0 },
        // notifier defaults
        notifier:{
            // auto-dismiss wait time (in seconds)  
            delay:5,
            // default position
            position:'bottom-right'
        },

        // language resources 
        glossary:{
            // dialogs default title
            title:'Aviso!',
            // ok button text
            ok: 'Aceptar',
            // cancel button text
            cancel: 'Cancelar'            
        },

        // theme settings
        theme:{
            // class name attached to prompt dialog input textbox.
            input:'ajs-input',
            // class name attached to ok button
            ok:'ajs-ok',
            // class name attached to cancel button 
            cancel:'ajs-cancel'
        }
    };
});

function MostrarInfoEsfera(){
	alertify.alert("Licencia", "TERMINAL M&Oacute;VIL <br> Versi&oacute;n 2.0 <br> Esfera Software, S. L. <br> Copyright &#169; 2010 - 2020");
}


function CargarInicio(){
	$.ajax({
		data:  '1',
		url:   host + '/functions/tmpl-session.php',
		type:  'POST',
		beforeSend: function () {
			$("section#contenido").html("<section class='loading'><img src='" + host + "/img/loading.gif' /></section>");
		},
		error: function(){
			$("section#contenido").html("<a>" + IdiomaRecogerTexto('text_aviso_error_loading_app') + "</a>");
		},
		success:  function (response) {
			if(response == 'ok'){
				$('section#contenido').load(host+'/php/aplicaciones.php');
			}else{
				$('section#contenido').load(host+'/php/AppLogin.php');
			}
		}
	});
}

//redirigimos donde diga la variable url
function Redirigir(url){
	window.location.href = url;
}
//Funcion de recogida palabra del idioma de la sesión
function IdiomaRecogerTexto(Cadena){
	var texto = '';
	var parametros = {
		"JSTXTLANG" : Cadena
	};
	$.ajax({
		async: false,
		data:  parametros,
		url:   host + '/functions/funcjs.php',
		type:  'GET',
		success:  function(response){
			texto = response;
		},
		error: function(errorno){
			texto = IdiomaRecogerTexto('text_aviso_error_loading_lang');
		}
	});
	return texto;
}


//Variable para contar las ubicaciones que damos en el inventario parcial
	var contadorUbicaciones = 1;
	var json_ubicaciones = [];
	var UbicacionParcial = '';

function InventarioParcial(url, CantidadUbicaciones, idInventario){
	UbicacionParcial = $('#title-estadistico').text();
	if(contadorUbicaciones == 1){
		var parametros = {
			"UBICACION" : ' '
		};
		$.ajax({
			async: false,
			data:  parametros,
			url:   host + url,
			type:  'POST',
			beforeSend: function () {
				$("#title-estadistico").html(IdiomaRecogerTexto('text_aviso_search_ubicaciones'));
			},
			success:  function (response) {
				 json_ubicaciones = $.parseJSON(response); // lo convierte a Array
				 $("#title-estadistico").html(json_ubicaciones[0]);
				 contadorUbicaciones++;
			},
			error:  function () {
				 $("#title-estadistico").html(IdiomaRecogerTexto("text_aviso_error_send_data"));
			}
		});
	}else if((contadorUbicaciones > 1 ) && (contadorUbicaciones <= CantidadUbicaciones )){
	//Miramos si la ubicacion esta vacia y agregamos una linea
		var ubicaciones = {
			"SIGUIENTE" : UbicacionParcial,
			"ID_INVENTARIO" : idInventario
		};
		$.ajax({
			async: false,
			data:  ubicaciones,
			url:   host + '/php/ControlInventarioInsertarLinea.php',
			type:  'GET',
			success:  function (data) {
						
			}
		});
		$("#title-estadistico").html(json_ubicaciones[contadorUbicaciones-1]);
		contadorUbicaciones++;
	}else{
		//Miramos si la ubicacion esta vacia y agregamos una linea
			var ubicaciones = {
				"SIGUIENTE" : UbicacionParcial,
				"ID_INVENTARIO" : idInventario
			};
			$.ajax({
				async: false,
				data:  ubicaciones,
				url:   host + '/php/ControlInventarioInsertarLinea.php',
				type:  'GET',
				success:  function (data) {
							
				}
			});
		contadorUbicaciones = 1;

		alertify.alert('Inventario', IdiomaRecogerTexto('text_aviso_inv_succefull'));
		setTimeout(function() {window.location.href = host;}, 2000);
	}
}

function EnviarControlInventario(idInventario){
	var Producto = $('.producto').val();
	var Cantidad = $('.cantidad').val();
	var Ubicacion = $('#title-estadistico').text();
	if((Producto != '') && (Cantidad != '') && (idInventario != '')){
		var parametros = {
			"PRODUCTO" : Producto,
			"CANTIDAD" : Cantidad,
			"ID_INVENTARIO" : idInventario,
			"UBICACION" : Ubicacion
		};
		$.ajax({
			async: false,
			data:  parametros,
			url:   host + '/php/ControlInventarioInsertarLinea.php',
			type:  'GET',
			beforeSend: function () {
				$("#title-estadistico").html("Enviando datos...");
			},
			success:  function (response) {
				$("#title-estadistico").html(Ubicacion);
				if(response == 1){
					alertify.error(IdiomaRecogerTexto("text_aviso_producto_no_existe"));
				}else if(response == 2){
					alertify.error(IdiomaRecogerTexto("text_aviso_sscc_no_existe"));
				}else if(response == 3){
					alertify.error(IdiomaRecogerTexto("text_aviso_etinterna_no_existe"));
				}
			},
			error:  function () {
				 $("#title-estadistico").html(IdiomaRecogerTexto("text_aviso_error_send_data"));
			}
		});
	}else{
		alertify.error(IdiomaRecogerTexto("text_aviso_revisar_datos_introducidos"));
	}
	
	$('.producto').val('');
	if($(".cantidad").is(":hidden")){
		$('.cantidad').val(0);
	}else{
		$('.cantidad').val('');
	}
	
	$('.producto').focus();
}

function FinalizarInventarioParcial() {
	contadorUbicaciones = 1;
	location.href = host;
}

function CargarAplicacion(url, contenido, confirmacion, pregunta){
	if (confirmacion == 'N'){
		$("#"+contenido).load(host + url);
	}else{
		alertify.confirm( pregunta, function (e) {
			if (e) {
				$("#"+contenido).load(host + url);
			} else {
				alertify.error(IdiomaRecogerTexto("text_aviso_error_inv_parcial"));
			}
		});
	}
}

//Enviar datos
function EnviarPeticion(url, valor, contenido, carga){
	this.onkeypress = function(e){
		if (!e) e = window.event;
			var keyCode = e.which || e.keyCode;
			if ((keyCode == '13') || (keyCode == 9)){
				if (carga == 'S'){
					var parametros = {
						"BUSCAR" : $('.'+valor).val(),
						"CATEGORIA": valor
					};
				}else{
					var parametros = {
						"CONSULTA" : $('.'+valor).val(),
						"CATEGORIA": valor
					};
				}
				$.ajax({
					data:  parametros,
					url:   host + url,
					type:  'POST',
					beforeSend: function () {
						$(contenido).val(IdiomaRecogerTexto("text_aviso_precess"));
					},
					error:  function () {
						$(contenido).val(IdiomaRecogerTexto("text_error_process"));
						$(".limpiar").focus();
					},
					success:  function (response) {
						if(response == 0){
							alertify.alert('Aviso!', IdiomaRecogerTexto('text_aviso_client_not_found'));
							$(contenido).val(IdiomaRecogerTexto('text_aviso_client_not_found'));
							$('#buttonconfirm').prop('disabled', true );
						}else{
							$('#buttonconfirm').prop('disabled', false );
							$(contenido).val(response);
							$(".aceptar").select();	
						}
					}
				});
				return false;
			}else if (keyCode == '27'){
				$('.'+valor).select();
			}
	}
}

//Buscar productos
function BuscarProducto(url, valor, contenido){
	if(( $('.'+valor).val() != '') && ( $('.'+valor).val() != 'undefined')) {
		this.onkeypress = function(e){
			if (!e) e = window.event;
				var keyCode = e.keyCode || e.which;
				if (keyCode == '13'){
					var parametros = {
					"BUSCAR" : $('.'+valor).val(),
					"CATEGORIA": valor
					};
				$.ajax({
					data:  parametros,
					url:   host + url,
					type:  'POST',
					beforeSend: function () {
						$(contenido).html(IdiomaRecogerTexto("text_aviso_precess"));
					},
					error:  function () {
						$(contenido).html(IdiomaRecogerTexto("text_error_process"));
					},
					success:  function (response) {
						//Quitamos el atributo desactivado a los botones
						$(".limpiar").attr('disabled', false);
						$(".aceptar").attr('disabled', false);
						//Copiamos el contenido en donde corresponda
						$(contenido).html(response);
					}
				});
				return false;
			}
		}
	}else{
		alertify.alert('Aviso!', IdiomaRecogerTexto('text_aviso_insert_search_keyword'));
	}
}

function RegistroEncontrado(codigo, descripcion, input){
	//Cerramos la ventana modal
	location.href="#close";
	//Quitamos el atributo desactivado a los botones
	$(".limpiar").attr('disabled', false);
	$(".aceptar").attr('disabled', false);
	//Ponemos los datos en su sitio
	$("."+input).val(codigo);
	$(".label-desc").val(descripcion);
	CerrarPopup();
	$('.' + input).focus();
}

function ConsultaStock(valor, contenido, mostrar_lote){
	if ((( $('.code').val() != '') && ( $('.code').val() != 'undefined') ) || 
	(( $('.label-lote').val() != '') &&( $('.label-lote').val() != 'undefined'))){
		if ( mostrar_lote == 'S') {
			var parametros = {
				"BUSCAR" : $('.'+valor).val(),
				"LOTE" : $('.label-lote').val(),
			}
		}else{
			var parametros = {
				"BUSCAR" : $('.'+valor).val(),
			};
		}
		$.ajax({
			data:  parametros,
			url:   host + '/php/QueryConsultaStock.php',
			type:  'GET',
			beforeSend: function () {
			$(contenido).html(IdiomaRecogerTexto("text_aviso_precess"));
			},
			error:  function () {
				$(contenido).html(IdiomaRecogerTexto("text_error_process"));
			},
			success:  function (response) {
			//Quitamos el atributo desactivado a los botones
				$(".limpiar").attr('disabled', false);
				$(".aceptar").attr('disabled', false);
			//Copiamos el contenido en donde corresponda
				$(contenido).html(response);
			}
		});
	}else{
		alertify.alert('Aviso!', IdiomaRecogerTexto("text_aviso_insert_product_lote"));
	}
}

function ConsultaStockEtiqueta(valor, contenido, mostrar_lote){
	this.onkeypress = function(e){
		if (!e) e = window.event;
			var keyCode = e.keyCode || e.which;
			if (keyCode == '13'){
				EnviarConsultaEtiqueta(valor, contenido, mostrar_lote);
			}
	}
}

function EnviarConsultaEtiqueta(valor, contenido, mostrar_lote){
	var parametros = {
		"BUSCAR" : $('.'+valor).val(),
	};
				
	$.ajax({
		data:  parametros,
		url:   host + '/php/QueryConsultaStockEtiquetas.php',
		type:  'GET',
		beforeSend: function () {
			$(contenido).html(IdiomaRecogerTexto("text_aviso_precess"));
		},
		error:  function () {
			$(contenido).html(IdiomaRecogerTexto("text_error_process"));
		},
		success:  function (response) {
		//Quitamos el atributo desactivado a los botones
			$(".limpiar").attr('disabled', false);
			$(".aceptar").attr('disabled', false);
		//Copiamos el contenido en donde corresponda
			$(contenido).html(response);
		}
	});
}

function ConsultaStockUbicacion(ubicacion, incompleto, EnviaAURL){
	var parametros = {
		"BUSCAR" : ubicacion,
		"INCOMPLETO": incompleto,
		"URL": EnviaAURL
	};
	
	$.ajax({
		data:  parametros,
		url:   host + '/php/QueryConsultaStockUbicacion.php',
		type:  'GET',
		beforeSend: function () {
		$('#contenido').html(IdiomaRecogerTexto("text_aviso_precess"));
		},
		error:  function () {
			$('#contenido').html(IdiomaRecogerTexto("text_error_process"));
		},
		success:  function (response) {
		//Quitamos el atributo desactivado a los botones
			$(".limpiar").attr('disabled', false);
			$(".aceptar").attr('disabled', false);
		//Copiamos el contenido en donde corresponda
			$('#contenido').html(response);
		}
	});
}

function tabular(e,obj){
	tecla = (document.all) ? e.keyCode : e.which;
	if(e.keyCode!=13) return;
		if(e.shiftKey == true) return;
			frm = obj.form;
			for(i=0;i<frm.elements.length;i++) 
				if(frm.elements[i]==obj){ 
					if (i==frm.elements.length-1) 
					i=-1;
					break 
				}
		/*ACA ESTA EL CAMBIO disabled*/
				if (frm.elements[i+1].disabled ==true)   
					tabular(e,frm.elements[i+1]);
		/*ACA ESTA EL CAMBIO readOnly */
			else if (frm.elements[i+1].readOnly ==true )    
				tabular(e,frm.elements[i+1]);
			else frm.elements[i+1].focus();
				return false;
}  

function addRow(tableID) {
	/*f = this.form;
	if (f.producto.value   == '') { 
		alert ('El producto esta vacío'); 
		f.producto.focus(); 
		return false; 
	}else if(f.cantidad.value  == ''){
		alert ('El producto esta vacío'); 
		f.cantidad.focus(); return false; 
	}else{
	}*/
		$("#"+tableID).find("input,button,textarea").attr("disabled", "disabled");
		var table = document.getElementById(tableID);
		var rowCount = table.rows.length;
		var row = table.insertRow(rowCount);
		/*PRIMER ELEMENTO ES UN INPUT*/
		var cell1 = row.insertCell(0);
		var element1 = document.createElement("input");
		element1.type = "text";
		element1.name = "producto";
		element1.size = 20;
		element1.id = "txt_input";
		element1.setAttribute("onkeypress", "return tabular(event,this)");
		cell1.appendChild(element1);
		/*SEGUNDO ELEMENTO ES UN INPUT*/
		var cell2 = row.insertCell(1);
		var element2 = document.createElement("input");
		element2.type = "text";
		element1.name = "cantidad";
		element2.size = 7;
		element2.id = "txt_input";
		element2.setAttribute("onkeypress", "return tabular(event,this)");
		cell2.appendChild(element2);
		/*TERCER ELEMENTO ES UN TEXTO CON ONCLICK*/
		var cell3 = row.insertCell(2);
		var element3 = document.createElement("a");
		element3.id = "";
		cell3.appendChild(element3);
		element3.appendChild(document.createTextNode("Confirmar"));
		element3.setAttribute("onclick", "addRow('inventario-parcial');CambiarTexto('Enviado');");
	
}

function deleteRow(tableID) {
	try {
		var table = document.getElementById(tableID);
		var rowCount = table.rows.length;
		for(var i=0; i<rowCount; i++) {
			var row = table.rows[i];
			var chkbox = row.cells[0].childNodes[0];
			if(null != chkbox && true == chkbox.checked) {
				table.deleteRow(i);
				rowCount--;
				i--;
			}
		}
	}catch(e) {
		alert(e);
	}
}

function CambiarTexto(texto){
	this.text = texto;
}

/*POPUP*/
function CargarPopup(url){
	$("#modal3").load(host + url);
	location.href='#modal3';
}

if ($('.close').length){
	$('.close').onclick(function () {
		$(".modalmask").empty();
		/*$(".modalmask").css('height','0px');*/
	});
}

function CerrarPopup(){
	$(".modalmask").empty();
	/*$(".modalmask").css('height','0px');*/
}


//INVENTARIO
function EnviarInventario(idInventario){
	var Producto = $('.producto').val();
	var Cantidad = $('.cantidad').val();
	var Ubicacion = $('#title-estadistico').text();
	if((Producto != '') && (Cantidad != '') && (idInventario != '')){
		var parametros = {
			"PRODUCTO" : Producto,
			"CANTIDAD" : Cantidad,
			"ID_INVENTARIO" : idInventario,
			"UBICACION" : Ubicacion
		};
		$.ajax({
			async: false,
			data:  parametros,
			url:   host + '/php/InventarioInsertarLinea.php',
			type:  'GET',
			beforeSend: function () {
				$("#title-estadistico").html("Enviando datos...");
			},
			success:  function (response) {
				$("#title-estadistico").html(Ubicacion);
				if(response == 1){
					alertify.error(IdiomaRecogerTexto("text_aviso_producto_no_existe"));
				}else if(response == 2){
					alertify.error(IdiomaRecogerTexto("text_aviso_sscc_no_existe"));
				}else if(response == 3){
					alertify.error(IdiomaRecogerTexto("text_aviso_etinterna_no_existe"));
				}
			},
			error:  function () {
				 $("#title-estadistico").html(IdiomaRecogerTexto("text_aviso_error_send_data"));
			}
		});
	}else{
		alertify.error(IdiomaRecogerTexto("text_aviso_revisar_datos_introducidos"));
	}
	
	$('.producto').val('');
	$('.cantidad').val('');
	$('.producto').focus();
}

function EnviarUbicacion (Ubicacion, EnviarURL){
		var parametros = {
			"UBICACION_EXISTE" : Ubicacion,
		};
		$.ajax({
			async: false,
			data:  parametros,
			url:   host + '/functions/funcjs.php',
			type:  'GET',
			success:  function (response) {
				if(response == 0){
					alertify.error(IdiomaRecogerTexto("text_aviso_ubicacion_no_existe"));
				}else if(response == 1){
					$('#contenido').load(host + EnviarURL, {UbicacionFinal:Ubicacion});
				}else{
					alertify.error(IdiomaRecogerTexto("text_aviso_error_send_data"));
				}
			}
		});
}

function FinalizarInventario(Ubicacion, idInventario){
	alertify.confirm(IdiomaRecogerTexto('text_inventario_finalizar'), function (e) {
	// str is the input text
	if (e) {
		RevisarUbicacionInventarioVacia(Ubicacion, idInventario);
	//si el usuario acepta salimos de esta pantalla de inicio
		location.href='/';
	} else {
		//si el usuario se niega mostramos la cancelación del reenvio
			alertify.alert('Aviso!', IdiomaRecogerTexto('text_inventario_no_salir_inventario'));
		}
	});
}

function RevisarUbicacionInventarioVacia(Ubicacion, idInventario){
	//Miramos si la ubicacion esta vacia y agregamos una linea
		var ubicaciones = {
			"SIGUIENTE_UBICACION" : Ubicacion,
			"ID_INVENTARIO" : idInventario
		};
		$.ajax({
			async: false,
			data:  ubicaciones,
			url:   host + '/php/InventarioInsertarLinea.php',
			type:  'GET',
			success:  function (data) {
						
			}
		});
}

function BuscarAlbaranes(CLIENTE){
	$('#contenido').load(host + '/php/ConfirmarServicio.php?CLIENTE=' + CLIENTE);
}

function BuscarLineas(ALBARAN){
	$('#contenido').load(host + '/php/ConfirmarServicio.php?ALBARAN=' + ALBARAN);
}

function SeleccionarLinea(objeto){
	//Quitamos la selección que tenemos
	$("tr").removeClass('select-registro');
	//Seleccionamos el objeto que hemos clickeado
	$(objeto).addClass('select-registro');
}

function Editarlinea(){
	if ($('.select-registro').length){
		if ($('.select-registro').hasClass('lineaescondida')){
			alertify.alert('Aviso!', IdiomaRecogerTexto('text_aviso_line_is_confirmed'));
		}else{
			$("#alertify-ok").focus();
			//Guardamos la cantidad pedida para que sea mas facil para el usuario
			var UnidadesSalida = $("tr.select-registro > td > input.unidades-pedida").val();
			//Preguntamos cuantas unidades quiere confirmar
			alertify.prompt(IdiomaRecogerTexto('text_aviso_cantidad_editar'), 
				UnidadesSalida,
				function(evt, value){ 
					if(value === null){
						$("tr").removeClass('select-registro');
					}else{
						$("tr.select-registro > td > input.unidades-confirm").val(value);
						ComprobarConfirmacionLinea();
					}
				}
			);
		}
	}else{
		alertify.alert('Aviso!', IdiomaRecogerTexto('text_aviso_confirm_line_no_select'));
	}
}

function AcumularAlTotal(){
	if ($('.select-registro').length){
		if ($('.select-registro').hasClass('lineaescondida')){
			alertify.alert('Aviso!', IdiomaRecogerTexto('text_aviso_line_is_confirmed'));
		}else{
			$("#alertify-ok").focus();
			//Guardamos la cantidad pedida para que sea mas facil para el usuario
			var UnidadesSalida = parseInt($("tr.select-registro > td > input.unidades-confirm").val());
			//Preguntamos cuantas unidades quiere confirmar
			alertify.prompt(IdiomaRecogerTexto('text_aviso_cantidad_acumular'), 
			 UnidadesSalida,
			 function(evt, value){ 
					if(value === null){
						$("tr").removeClass('select-registro');
					}else{
						value = parseInt(value);
						$("tr.select-registro > td > input.unidades-confirm").val(value + UnidadesSalida);
						ComprobarConfirmacionLinea();
					}
				}
			);
		}
	}else{
		alertify.alert('Aviso!', IdiomaRecogerTexto('text_aviso_confirm_line_no_select'));
	}
}

function ComprobarConfirmacionLinea(){
	var UnidadesConfirmadas = parseInt($("tr.select-registro > td > input.unidades-confirm").val());
	var UnidadesPedidas = parseInt($("tr.select-registro > td > input.unidades-pedida").val());
	var parametros = {
		"ALBARAN" :  $("tr.select-registro > td > input.albaran").val(),
		"PRODUCTO" :  $("tr.select-registro > td > input.producto").val(),
		"LINEA" :  parseInt($("tr.select-registro > td > input.relacion").val()),
		"UNIDADES": UnidadesConfirmadas,
		"ACTUALIZARLINEA": 'S'
	};
	$.ajax({
		async: false,
		data:  parametros,
		url:   host + '/php/ConfirmarServicio.php',
		type:  'POST',
		success:  function () {
		},
		error: function(){
			alertify.error(IdiomaRecogerTexto("text_aviso_confirm_line_no_change"));
		}
	});
	//Si las unidades confirmadas son mayores a que las pedidas pondremos el color 
	//de las unidades en rojo
	if (UnidadesConfirmadas > UnidadesPedidas){
		$("tr.select-registro > td > input.unidades-confirm").css('color', 'red');
	}else{
		$("tr.select-registro > td > input.unidades-confirm").css('color', '#000');
	}
}

function ConfirmarLinea(){
	if ($('.select-registro').length){
		if ($('.select-registro').hasClass('lineaescondida')){
			alertify.alert('Aviso!', IdiomaRecogerTexto('text_aviso_line_is_confirmed'));
		}else{
			var parametros = {
				"ALBARAN" :  $("tr.select-registro > td > input.albaran").val(),
				"PRODUCTO" :  $("tr.select-registro > td > input.producto").val(),
				"LINEA" :  parseInt($("tr.select-registro > td > input.relacion").val()),
				"CONFIRMAR": "S"
			};
			$.ajax({
				async: false,
				data:  parametros,
				url:   host + '/php/ConfirmarServicio.php',
				type:  'POST',
				success:  function (response) {
					alertify.success(IdiomaRecogerTexto("text_aviso_confirm_line_confirm"));
					$("tr.select-registro").fadeOut();
					$("tr.select-registro").addClass('lineaescondida');
					$('tr.select-registro').attr('onclick', 'DesconfirmarLinea();');
					$("tr").removeClass('select-registro');
					ComprobarFinalTabla();
				},
				error: function(){
					alertify.error(IdiomaRecogerTexto("text_aviso_confirm_line_no_confirm"));
				}
			});
		}
	}else{
		alertify.alert('Aviso!', IdiomaRecogerTexto('text_aviso_confirm_line_no_select'));
	}
}

function MostrarOcultarLineas(){
	if($('.lineaescondida').css('display') == 'table-row'){
		$('.lineaescondida').fadeOut();
		$('.todaslineasconfirmadas').fadeIn();
	}else{
		$('.todaslineasconfirmadas').fadeOut();
		$('.lineaescondida').fadeIn();
	}
}

function ComprobarFinalTabla(){
	if ($('table#product-search > tbody > tr.lineaescondida').length == $('table#product-search > tbody > tr').length){
		$('table#product-search > tbody > tr:last').after('<tr class="todaslineasconfirmadas"><td>' + IdiomaRecogerTexto("text_aviso_todas_lineas_confirmadas") + '</td></tr>');
	}
}

function DesconfirmarLinea() {
	alertify.confirm( IdiomaRecogerTexto('text_question_disconfirm_line'), function (e) {
		if (e) {
			var parametros = {
				"ALBARAN" :  $("tr.select-registro > td > input.albaran").val(),
				"PRODUCTO" :  $("tr.select-registro > td > input.producto").val(),
				"LINEA" :  parseInt($("tr.select-registro > td > input.relacion").val()),
				"CONFIRMAR": "D"
			};
			$.ajax({
				async: false,
				data:  parametros,
				url:   host + '/php/ConfirmarServicio.php',
				type:  'POST',
				success:  function (response) {
					alertify.success(IdiomaRecogerTexto("text_aviso_disconfirm_line_confirm"));
					$("tr.select-registro").removeClass('lineaescondida');
					$('tr.select-registro').removeAttr('onclick');
					$('tr.select-registro').attr('onclick', 'SeleccionarLinea(this)');
					$("tr").removeClass('select-registro');
					ComprobarFinalTabla();
				},
				error: function(){
					alertify.error(IdiomaRecogerTexto("text_aviso_confirm_line_no_confirm"));
				}
			});
		}
	})
}

function CrearPedido(){
	var parametros = {
		"PEDIDO" :  $(".num-pedido").val(),
		"CLIENTE" :  $(".cliente").val(),
		"MOVIMIENTO" :  $(".movimiento").val(),
		"FECHA_PEDIDO": $(".fecha-actual").val(),
		"FECHA_PROGRAMADA": $(".fecha-prog").val(),
		"ACCION":"INSERT_CABECERA"
	};
	$.ajax({
		async: false,
		data:  parametros,
		url:   host + '/php/QueryPedidos.php',
		type:  'POST',
		success:  function () {
			alertify.success(IdiomaRecogerTexto("text_aviso_pedido_creado"));
			AbrirPedido($(".num-pedido").val(), $(".cliente").val());
		},
		error: function(){
			alertify.error(IdiomaRecogerTexto("text_aviso_pedido_no_creado"));
		}
	});
}

function AbrirPedido(pedido, cliente){
	if((pedido != 'null') && (pedido != ' ') && (typeof(pedido) != "undefined") &&
		(typeof(cliente) != 'null') && (cliente != ' ') && (typeof(cliente) != "undefined")){
		$('#contenido').load(host + '/php/Pedidos.php?PEDIDO=' + pedido + '&CLIENTE=' + cliente);
	}else{
		alertify.alert('Aviso!', IdiomaRecogerTexto('text_aviso_pedido_no_existe'));
	}
}

function CapturarEnter(e){
	var keyCode = e.keyCode || e.which;
	if (keyCode == '13'){
		PedirCantidad();
	}
}
	
function PedirCantidad(){
	//Comprobamos que el producto exista en el almacen
	var parametros = {
		"COMPROBAR_PRODUCTO":  $(".code").val()
	};
	$.ajax({
		async: false,
		data:  parametros,
		url:   host + '/php/QueryPedidos.php',
		type:  'GET',
		success:  function (response) {
			if(response == 0){
				alertify.alert('Error!', IdiomaRecogerTexto('text_aviso_producto_no_existe'));
				$('.code').val('');
				$('.code').focus();
			}else if(response == 1){
				//Comprobamos la cantidad en almacen
				var parametros = {
					"COMPROBAR_CANTIDAD":  $(".code").val()
				};
				$.ajax({
					async: false,
					data:  parametros,
					url:   host + '/php/QueryPedidos.php',
					type:  'GET',
					success:  function (response) {
						if (response > 0){
						//Preguntamos cuantas unidades quiere pedir
							alertify.prompt(IdiomaRecogerTexto('text_aviso_cant_pedida') + response, 
							'',
								function(evt, value){
									if(response >= parseInt(value)){
									//Si la cantidad es 0 nula o cualquier otro valor que no sea numerico da error
										if((value === null) || (value == 0) || (typeof(value) == 'undefined')) {
											alertify.alert('Error!', IdiomaRecogerTexto('text_aviso_pedido_no_existe'));
											$('.code').val('');
											$('.code').focus();
										}else{
											//Enviamos la linea para ver si podemos introducirla en nuestro pedido
											var parametros = {
												"NUMERO" :  $(".pedido").val(),
												"PRODUCTO" :  $(".code").val(),
												"CANTIDAD" :  parseInt(value),
												"ACCION": "INSERT_LINEA"
											};
											$.ajax({
												async: false,
												data:  parametros,
												url:   host + '/php/QueryPedidos.php',
												type:  'GET',
												success:  function (response) {
													if (response == 1){
													//Linea introducida correctamente
														alertify.success(IdiomaRecogerTexto("text_aviso_ok_intro_linea_pedido"));
														ReferescarFilasTabla($(".pedido").val());
														$('.code').val('');
														$('.code').focus();
													}else if (response == 2){
														//El producto existe en el pedido preguntamos si quieren sumar la cantidad
														alertify.confirm( IdiomaRecogerTexto('text_aviso_producto_existe_en_pedido'), function (e) {
															if (e) {
																var parametros = {
																	"NUMERO" :  $(".pedido").val(),
																	"PRODUCTO" :  $(".code").val(),
																	"CANTIDAD" :  parseInt(value),
																	"ACCION": "SUMAR_CANTIDAD_LINEA"
																};
																$.ajax({
																	async: false,
																	data:  parametros,
																	url:   host + '/php/QueryPedidos.php',
																	type:  'GET',
																	success:  function (response) {
																		alertify.success(IdiomaRecogerTexto("text_aviso_actualizada_intro_linea_pedido"));
																		$('.code').val('');
																		$('.code').focus();
																	}
																});
															}else{
																$('.code').val('');
																$('.code').focus();
															}
														});
													}
												},
												error: function(){
													alertify.error(IdiomaRecogerTexto("text_aviso_error_intro_linea_pedido"));
													$('.code').val('');
													$('.code').focus();
												}
											});
										}
									}else{
										alertify.alert('Error!', IdiomaRecogerTexto('text_aviso_cantida_intro_supera_stock'));
										$('.code').val('');
										$('.code').focus();
									}
								}
							);
						}else{
							alertify.alert('Error!', IdiomaRecogerTexto('text_aviso_no_hay_cantidad'));
							$('.code').val('');
							$('.code').focus();
						}
						
					}
				});
			}
		}
	});
}

function ReferescarFilasTabla(pedido){
	$('#modal3').load(host + '/php/QueryPedidos.php?ACCION=REFRESCAR_TABLA&NUMERO=' + pedido);
	$('#modal3').load(host + '/php/QueryPedidos.php?ACCION=REFRESCAR_TOTAL&NUMERO=' + pedido);
}

function SeleccionarLineaPedido(direccion){

	if ($('.select-registro').length){
		$('tr.select-registro').addClass('quitar-seleccion');
		if(direccion == '+'){
			$('tr.select-registro').next().addClass('select-registro');
		}else{
			$('tr.select-registro').prev().addClass('select-registro');
		}

		$('tr.quitar-seleccion').removeClass('select-registro');
		$('tr.quitar-seleccion').removeClass('quitar-seleccion');
		$("html, body").animate({ scrollTop:  ($('.select-registro').offset().top -$('.select-registro').height() - 10)}, "slow");
	}else{
		$('tr:first').addClass('select-registro');
		$("html, body").animate({ scrollTop:  (0)}, "slow");
	}
}

function EliminarLineaPedido(){
	if ($('.select-registro').length){
		alertify.confirm( IdiomaRecogerTexto('text_question_eliminar_linea'), function (e) {
			if (e) {
				var producto = $('tr.select-registro > td > a.producto').html();
				var pedido =  $(".pedido").val();
				$('#modal3').load(host + '/php/QueryPedidos.php?ACCION=ELIMINAR_LINEA&NUMERO=' + pedido + '&PRODUCTO='+producto);
				$('tr.select-registro').remove();
			}
		});
	}else{
		alertify.alert('Aviso!', IdiomaRecogerTexto('text_aviso_confirm_line_no_select'));
	}
}
