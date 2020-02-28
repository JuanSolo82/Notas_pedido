$(document).ready(function(){
	$("#orden_compra").keypress(function(e){
		if (e.which==13){
			buscar_oc($("#orden_compra").val(),'');
		}
	});
	$("#nota_pedido").keypress(function(e){
		if (e.which==13){
			buscar_oc('',$("#nota_pedido").val());
		}
	});
});

function buscar_oc(orden_compra, nota_pedido){
	if (orden_compra.trim()!=''){
		// buscar
		console.log('ok');
		$("#adminForm").submit();
	}else if (nota_pedido.trim()!=''){
		/*alert('Búsqueda por número de nota aún en desarrollo');
		$("#nota_pedido").val("");
		return;*/
		$("#adminForm").submit();
	}else{
		alert("Complete los datos");
		return;
	}
}

function guardar_cambios(cont){
	for (var i=1;i<=cont;i++){
		var id_item 				= $("#id_item"+i).val();
		var id_tipo_modificacion 	= $("#tipo_modificacion"+i).val();
		var cantidad_original 		= parseFloat($("#cantidad_original"+i).val());
		var nueva_cantidad 			= parseFloat($("#cantidad"+i).val());
		var descripcion 			= $.trim($("#descripcion_item"+i).val());
		var motivo					= $.trim($("#motivo"+i).val());
		$.ajax({
			url: 'index.php?option=com_nota&task=editar_item',
			type: 'post',
			data: {id_item: id_item, 
					cantidad_original: cantidad_original, 
					nueva_cantidad: nueva_cantidad, 
					id_tipo_modificacion: id_tipo_modificacion, 
					descripcion: descripcion, 
					motivo: motivo},
			success: function(query){
				//console.log(id_item+','+cantidad_original+','+nueva_cantidad+','+descripcion+','+motivo+';'+' '+cont);
				console.log(query);
			}
		});
		
		$("#cantidad_editado"+i).text($("#cantidad"+i).val());
		$("#descripcion_editado"+i).text($("#descripcion_item"+i).val());
		$("#motivo_editado"+i).text($("#motivo"+i).val());
		$("#opcion_editado"+i).text($("#opcion_oc"+i).val());
	}
	var id_remitente = $("#id_remitente").val();
	$.ajax({
		url: 'index.php?option=com_nota&task=nota_revision',
		type: 'post',
		data: {id_remitente: id_remitente,
				enviado_empleado: 1,
				autorizado_capitan: 1,
				autorizado_jefe: 1,
				autorizado_depto: 1,
				aprobado_adquisiciones: 0}
	});
	$("#contenido_editable").hide();
	$("#contenido_editado").show();
	$("#guardar_cambios").hide();
	$("#generar_oc").show();
}

function previa_oc(opcion, num_items){
	if ($.trim($("#proveedor"+opcion).val())!='')
		$("#proveedor_oc"+opcion).html("Proveedor: "+$("#proveedor"+opcion).val());
	else
		$("#proveedor_oc"+opcion).html("");
	var html = "<table class='items_oc'><tr>";
	html += "<td class='numero_oc'><b>#</b></td>";
	html += "<td class='cantidad_oc'><b>Cantidad</b></td>";
	html += "<td class='descripcion_oc'><b>Item</b></td>";
	html += "<td class='descripcion_oc'><b>Observaciones</b></td>";
	html += "</tr>";
	for (var i=1;i<=num_items;i++){
		html += "<tr>";
		html += "<td style='border: solid black 1px;'>"+i+"</td>";
		html += "<td style='border: solid black 1px;'>"+$("#cantidad"+opcion+"_"+i).val()+"</td>";
		html += "<td style='border: solid black 1px;'>"+$("#descripcion_item"+opcion+"_"+i).val()+"</td>";
		html += "<td style='border: solid black 1px;'>"+$("#motivo"+opcion+"_"+i).val()+"</td>";
		html += "</tr>";
	}
	html += "</table>";
	
	$("#contenido_tabla"+opcion).html(html);
	if ($("#ley_navarino").is(":checked")){
		html = "Facturar con documento especial de venta (ley 18.392) a Transbordadora Austral Broom S.A., rut 82.074.900-6, dirección Manuel Señoret #831, Porvenir, exento de IVA";
	}else{
		html = "Facturar a Transbordadora Austral Broom S.A., rut 82.074.900-6, dirección Juan Williams #06450, Punta Arenas, afecto a IVA";
	}
	$("#beneficio"+opcion).html(html);
	$("#imprimir"+opcion).show();
	$("#previa"+opcion).dialog({
		width: 800,
        height: 500
	});
}
function imprimir_area(opcion){
	$("#imprimir"+opcion).hide();
	$("#oc_completa"+opcion).printArea();
}

function guardar_opciones(num_items, id_remitente){
	for (var i=1;i<=num_items;i++){
		var opcion = $("#opciones_oc"+i+" option:selected").val();
		var id_item = $("#id_item"+i).val();
		$("#guardado"+i).text(opcion);
		$("#guardado"+i).show();
		$("#opciones_oc"+i).hide();
		$.ajax({
			url: 'index.php?option=com_nota&task=adquisiciones.cambiar_opcion',
			type: 'post',
			data: {id_item: id_item, opcion: opcion},
			success: function(){
				$("#guardar_cambios").hide();
				$("#cambios_guardados").show();
			}
		});
	}
	var id_centro_costo = $("#centro_costo option:selected").val();
	$.ajax({
		url: 'index.php?option=com_nota&task=adquisiciones.cambiar_cc',
		type: 'post',
		data: {id_remitente: id_remitente, id_centro_costo: id_centro_costo},
		success: function(data){
			console.log(data);
		}
	});
}

function preparar_oc(url_pdf, id_remitente, opcion, num_items){
	var ley_navarino = 0;
	if ($("#ley_navarino").is(":checked"))
		ley_navarino=1;
	
	for (var i=1;i<=num_items;i++){
		var id_item				= $("#id_item"+opcion+"_"+i).val();
		var cantidad_original	= $("#cantidad_original"+opcion+"_"+i).val();
		var nueva_cantidad		= $("#cantidad"+opcion+"_"+i).val();
		var item 				= $("#descripcion_item"+opcion+"_"+i).val();
		var motivo 				= $("#motivo"+opcion+"_"+i).val();
		var id_tipo_modificacion 	= $("#tipo_modificacion"+opcion+"_"+i).val();
		if (cantidad_original!=nueva_cantidad){
			$.ajax({
				url: "index.php?option=com_nota&task=editar_item",
				type: 'post',
				data: {id_item: id_item, cantidad_original: cantidad_original, nueva_cantidad: nueva_cantidad, descripcion: item, motivo: motivo, id_tipo_modificacion: id_tipo_modificacion},
				success: function(){
					console.log('editado');
				}
			});
		}
	}
	// actualizar nota_revision
	$.ajax({
		url: "index.php?option=com_nota&task=nota_revision", 
		type: "post",
		data: {id_remitente: id_remitente, enviado_empleado: 1, autorizado_capitan:1, autorizado_jefe:1, autorizado_depto:1, aprobado_adquisiciones:1},
		success: function(){
			console.log("actualizado");
		}
	});
	// actualizar ley_navarino
	$.ajax({
		url: "index.php?option=com_nota&task=ley_navarino",
		type: "post",
		data: {id_remitente: id_remitente,ley_navarino: ley_navarino},
		success: function(){
			console.log();
		}
	});

	var proveedor = $("#proveedor"+opcion).val();
	var num_opciones = $("#num_opciones").val();
	
	/*$.ajax({
		url: "index.php?option=com_nota&task=generar_oc",
		type: "post",
		data: {id_remitente: id_remitente, opcion: opcion, proveedor: proveedor, num_opciones: num_opciones},
		success: function(data){
			$("#generada_oc"+opcion).css("display", "block");
		}
	});*/

	$.ajax({
        url: 'index.php?option=com_nota&task=adquisiciones.generarOrden',
        method: 'post',
		data: {id_remitente: id_remitente, opcion: opcion, proveedor: proveedor},
		success: function(){
			$("#generada_oc"+opcion).css("display", "block");
			console.log("mostrar verde");
		}
    });
    window.open('/portal/media/notas_pedido/Orden_compra.pdf');
	setTimeout(function(){
		$(location).attr('href',url_pdf);
	}, 800);
}

function nueva_oc(num_items){
	var validos=0;
	for (var i=1; i<=num_items ; i++){
		var id_item = $("#id_item"+i).val();
		if ($("#item"+id_item).is(":checked")){
			console.log($("#id_item"+i).val());
			validos++;
		}
	}
	if (!validos){
		alert('Escoja al menos un ítem');
		return;
	}
}

