$(document).ready(function() {
    for (var i = 1; i <= 15; i++) {
        $("#cantidad" + i).change(function() {
            $("#control_ingreso").val(1);
        });
        $("#descripcion" + i).change(function() {
            $("#control_ingreso").val(1);
        });
        $("#motivo" + i).change(function() {
            $("#control_ingreso").val(1);
        });
        $("#archivo" + i).change(function() {
            $("#control_ingreso").val(1);
        });
    }
    // esto desactiva un posible doble click para no generar dos notas iguales
    document.addEventListener('dblclick', function(event) {
        //alert("Double-click disabled!");
        event.preventDefault();
        event.stopPropagation();
    }, true);

    $("#nombre").keyup(function(e) {
        if (e.keyCode == 13) {
            if ($.trim($("#nombre").val())==""){
                alert("Ingrese valor a buscar");
                $("#nombre").val("");
                $("#nombre").focus();
                return;
            } else {
                buscar();
            }
        }
    });
    
    $("#desde").click(function(){
        $("#desde").datepicker({
            changeMonth: true,
            numberOfMonths: 3,
            maxDate: '0'
        }).focus();
    });
    $("#desde").change(function(){
        $("#hasta").val("");
        $("#hasta").prop('disabled', false);
        $("#hasta").css('background', 'white');
    });
    $("#hasta").click(function(){
        var fecha = $("#desde").val().split('-');
        $("#hasta").datepicker({
            changeMonth: true,
            numberOfMonths: 3,
            minDate: new Date(fecha[2],fecha[1]-1,fecha[0]),
            maxDate: '0'
        }).focus();
    });
    
});

function anterior_previo(direccion){
    var pagina = parseInt($("#pagina").val());
    if (direccion==1)
        pagina++;
    else
        pagina--;
    if (!pagina)
        return;
    var url_controller = 'index.php?option=com_nota&task=carga.notas_rango&format=raw';
    if ($("#vista").val()=="notas_naves")
        url_controller = 'index.php?option=com_nota&task=carga.rango_naves&format=raw';
    $.ajax({
        url: url_controller,
        type: 'post',
        data: {pagina: pagina},
        success: function(data){
            $("#lista").hide();
            $("#lista_propias").html(data);
        }
    });
    $("#lista_propias").css({'opacity':'0.5'});
    setTimeout(function(){
        $("#lista_propias").css({'opacity':'1'});
    },500);
    $("#pagina").val(pagina);
}

function anterior_previo_depto(direccion){
    var pagina = parseInt($("#pagina").val());
    if (direccion==1)
        pagina++;
    else
        pagina--; 
    if (pagina<0)
        return;
    $.ajax({
        url: 'index.php?option=com_nota&task=carga.depto_rango&format=raw',
        type: 'post',
        data: {pagina: pagina},
        success: function(data){
            //$("#lista").hide();
            $("#notas_depto").html(data);
        }
    });
    $("#notas_depto").css({'opacity':'0.5'});
    setTimeout(function(){
        $("#notas_depto").css({'opacity':'1'});
    },500);
    $("#pagina").val(pagina);
}

function previo_depto(direccion){
    var pagina = parseInt($("#pagina").val());
    if (direccion==1)
        pagina++;
    else
        pagina--;
    console.log(pagina);
    if (!pagina){
        return;
    }
    $.ajax({
        url: 'index.php?option=com_nota&task=carga.previo_depto&format=raw',
        type: 'post',
        data: {pagina: pagina},
        success: function(data){
            $("#notas_depto").html(data);
        }
    });
    $("#notas_depto").css({'opacity':'0.5'});
    setTimeout(function(){
        $("#notas_depto").css({'opacity':'1'});
    },500);
    
   $("#pagina").val(pagina);
}

function mostrar_opciones(id_remitente) {
    $("#opciones" + id_remitente).dialog({
        width: 300,
        height: 300
    });
}

function revisar_formulario() {
    var flag = 1;
    if ($("#nombre_tripulante").length){
        var nombre = $("#nombre_tripulante").val();
        if (nombre.trim()==""){
            lanzar_alarma("nombre_vacio");
            return;
        }
    }else{
        console.log("no existe variable nombre");
    }
    for (var i = 1; i <= 15; i++) {
        if (revision_fila(i) == 1) {
            alert('Complete todos los datos de la fila');
            return;
        } else if (revision_fila(i) == 2) {
            lanzar_alarma("distinto_cero");
            //alert("Ingrese valores mayores que cero");
            return;
        }
    }
    if (!parseInt($("#control_ingreso").val())) {
        lanzar_alarma("valores_formulario");
        //alert("Ingrese valores al formulario");
        return;
    }
    $("#form_nota").submit();
}

function revision_fila(i) {
    var cantidad = parseFloat($("#cantidad" + i).val());
    if ($("#cantidad" + i).val() > 0) {
        if ($.trim($("#descripcion" + i).val()) == '')
            return 1;
    }
    if ($.trim($("#descripcion" + i).val()) != '') {
        if ($("#cantidad" + i).val() == '')
            return 1;
        if (cantidad == 0)
            return 2;
    }
    return 0;
}
function dialogo_anulacion(){
    if ($("#boton_anulacion").is(':visible')){
        $("#boton_anulacion").css({'display': 'none'});
        $("#dialogo_anulacion").css({'display': 'block'});
        $(".boton").hide();
    }else{
        $("#boton_anulacion").css({'display': 'block'});
        $("#dialogo_anulacion").css({'display': 'none'});
    }
}
function anular_nota(id_remitente) {
    var comentario = '';
    if ($("#comentario").lenght)
        comentario = $("#comentario").val();
    else
        comentario = 'Eliminación por rebaja total de ítems';
    var nombre_tripulante = "";
    if ($("#nombre_tripulante").length)
        nombre_tripulante = $("#nombre_tripulante").val();
    if (comentario.trim()==''){
        alert("Debe ingresar un comentario");
        return;
    }
    $.ajax({
        url: 'index.php?option=com_nota&view=com_nota&task=anular_nota',
        type: 'post',
        data: { id_remitente: id_remitente },
        success: function(data) {
            $("#dialogo_anulacion").html("<h3>Nota anulada</h3>");
            if ($("#num_opciones").length){
                var num_opciones = parseInt($("#num_opciones").val());
                for (var i=1;i<=num_opciones;i++)
                    $("#previa_oc"+i).hide();
            }
            //console.log(data);
        }
    });
    $.ajax({
        url: 'index.php?option=com_nota&view=com_nota&task=tramitar_anulada',
        type: 'post',
        data: {id_remitente: id_remitente, comentario: comentario, nombre_usuario: nombre_tripulante}
    });
}

function anular_nota_depto(id_remitente){
    var comentario = '';
    if ($("#comentario").length){
        comentario = $("#comentario").val();
    }else
        comentario = 'Eliminación por rebaja total de ítems';
    var nombre_tripulante = "";
    if ($("#nombre_tripulante").length)
        nombre_tripulante = $("#nombre_tripulante").val();
    if (comentario.trim()==''){
        alert("Debe ingresar un comentario");
        return;
    }
    
    $.ajax({
        url: 'index.php?option=com_nota&view=com_nota&task=anular_nota_depto',
        type: 'post',
        data: {id_remitente: id_remitente},
        success: function(){
            $("#boton_anulacion").hide();
            $("#dialogo_anulacion").html("<b>Nota anulada</b>");
        }
    });
    $.ajax({
        url: 'index.php?option=com_nota&view=com_nota&task=tramitar_anulada',
        type: 'post',
        data: {id_remitente: id_remitente, comentario: comentario, nombre_usuario: ''}
    });
}

function guardar_calificacion(num_items){
    var terminado = 1;
    var id_tipoModificacion = 0;
    for (var i=1;i<=num_items;i++){
        var autorizado = parseFloat($("#cantidad_autorizado"+i).val());
        var faltante = parseFloat($("#cantidad_faltante"+i).val());
        var id_item = parseInt($("#id_item"+i).val());
        faltante = autorizado-faltante;
        
        if (faltante!=autorizado){
            terminado=2;
            id_tipoModificacion = 5;
            $.ajax({
                url: 'index.php?option=com_nota&task=editar_item',
                type: 'post',
                data: { id_item: id_item, cantidad_original: autorizado, nueva_cantidad: faltante, id_tipo_modificacion: id_tipoModificacion },
                success: function(){}
            });
        }
    }
    var id_remitente = $("#id_remitente").val();
    var comentario = $("#comentario").val();
    $.ajax({
        url: 'index.php?option=com_nota&task=nota_tramitada',
        type: 'post',
        data: {terminado: terminado, id_remitente: id_remitente, motivo: comentario}
    });
    $.ajax({
        url: 'index.php?option=com_nota&task=nota_anotacion',
        type: 'post',
        timeout: 2000,
        data: {id_remitente: id_remitente, aprobado: terminado, anotacion: comentario}
    });
    window.parent.location.reload();
}

function recibio(valor){
    $("#recibio").val(valor);
}

function buscar(){
    var nombre = $.trim($("#nombre").val());
    if (nombre==""){
        alert("Ingrese valor a buscar");
        $("#nombre").val("");
        $("#nombre").focus();
        return;
    }
    $.ajax({
        url: 'index.php?option=com_nota&task=carga.resultado_busqueda&format=raw',
        type: 'post',
        data: {nombre: nombre},
        success: function(datos){
            $('#resultado').html(datos);
        }
    });
}

function guardar_cambios_items(num_items, capitan, jefe, autorizado_depto=0) {
    var total = 0;
    var id_remitente = $("#id_remitente").val();
    var nombre_remitente = "";
    var generico = 0;
    for (var i = 1; i <= num_items; i++) {
        var id_item = $("#id_oculto" + i).val();
        var cantidad_original = $("#cantidad_oculto" + i).val();
        var nueva_cantidad = parseInt($("#cantidad" + i).val());
        total += nueva_cantidad;
    }
    if ($("#nombre_tripulante").length){
        nombre_remitente = $("#nombre_tripulante").val().trim();
        if (nombre_remitente==""){
            lanzar_alarma("nombre_vacio");
            return;
        }
        generico = 1;
    }

    if (total) {
        for (var i = 1; i <= num_items; i++) {
            var id_item = $("#id_oculto" + i).val();
            var cantidad_original = $("#cantidad_oculto" + i).val();
            var id_tipo_modificacion = $("#tipo_modificacion" + i).val();
            var nueva_cantidad = parseFloat($("#cantidad" + i).val());
            var descripcion = $.trim($("#nueva_descripcion" + i).val());
            var motivo = $.trim($("#nuevo_motivo" + i).val());
            $.ajax({
                url: 'index.php?option=com_nota&task=editar_item',
                type: 'post',
                data: {
                    id_item: id_item,
                    cantidad_original: cantidad_original,
                    nueva_cantidad: nueva_cantidad,
                    id_tipo_modificacion: id_tipo_modificacion,
                    descripcion: descripcion,
                    motivo: motivo
                }
            });
            $("#cantidad_editado" + i).text(nueva_cantidad);
            $("#descripcion_editado" + i).text(descripcion);
            $("#motivo_editado" + i).text(motivo);
        }
        $("#contenido_editable").hide();
        $("#contenido_editado").show();
        $("#botones").hide();
        if (jefe == 1)
            capitan = 1;
        $.ajax({
            url: "index.php?option=com_nota&task=carga.nota_revision&format=raw",
            type: 'post',
            data: { id_remitente: id_remitente, enviado_empleado: 1, autorizado_capitan: capitan, autorizado_jefe: jefe, autorizado_depto: autorizado_depto, aprobado_adquisiciones: 0 },
            success: function(data){
                $("#conjunto_botones").hide();
                $("#enviado").css({'display': 'block'});
            }
        });
        // tabla nota_tramitada
        $.ajax({
            url: 'index.php?option=com_nota&task=nota_tramitada',
            type: 'post',
            data: {id_remitente: id_remitente, generico: generico, nombre_remitente: nombre_remitente},
            success: function(data){
                //console.log(data);
            }
        });
        if ($("#nombre_tripulante").length){
            $("#nombre_tripulante").hide();
            $("#campo_nombre").text(nombre_remitente);
        }
    } else {
        anular_nota($("#id_remitente").val(), $("#id_user").val());
    }
    $("#botones").html("<h3>Cambios guardados</h3>");
}

function ocultar_mostrar(id){
    $("#editar"+id).hide();
    $("#guardar_edicion"+id).show();
    $("#departamento"+id).show();
    $("#nivel"+id).show();
    $("#nivel_actual_texto"+id).html("");
    $("#depto_actual_texto"+id).html("");
}

function asignar(id, id_depto_actual){
    var depto_escogido = parseInt($("#departamento"+id).val());
    var id_nivel       = parseInt($("#nivel"+id).val());
    if (!depto_escogido){
        alert('Alarma');
        return;
    }
    $.ajax({
        url: 'index.php?option=com_nota&task=actualizar_depto',
        type: 'post',
        data: { id_user: id, id_depto: depto_escogido, id_depto_actual: id_depto_actual }
    });
    $.ajax({
        url: 'index.php?option=com_nota&task=actualizar_nivel',
        type: 'post',
        data: { id_user: id, id_nivel: id_nivel }
    });
    $("#editar"+id).show();
    $("#guardar_edicion"+id).hide();
    $("#departamento"+id).hide();
    $("#nivel"+id).hide();
    $("#nivel_actual_texto"+id).html($("#nivel"+id+" option:selected").text());
    $("#depto_actual_texto"+id).html($("#departamento"+id+" option:selected").text());
}

function buscar_facturar(){
    if ($("#orden_compra").val().trim()=="" && $("#nota_pedido").val().trim()==""){
        if ($("#desde").val().trim()=="" || $("#desde").val().trim()==""){
            console.log("Ingresar valor");
            return;
        }
    }
    else if ($("#orden_compra").val().trim()=="" && $("#nota_pedido").val().trim()==""){
        console.log("ingrese valor");
        return;
    }
    console.log("ok");
}

function lanzar_alarma(obj){
    $("#"+obj).dialog({
        width: 350
    });
}

function actualizar_ln(id_remitente){
    var ley_navarino = $("#ley_navarino").prop('checked') ? 1 : 0;
    $.ajax({
        url: 'index.php?option=com_nota&task=adquisiciones.actualiza_ley_navarino',
        type: 'post',
        data: {id_remitente: id_remitente, ley_navarino: ley_navarino}
    });
}
function cargar_pdf(id_remitente, orden_compra, opcion, opciones){
    var proveedor = $('#proveedor'+opcion).val();
    var items_orden = $("#items_orden"+opcion).val();
    for (var i=1;i<=items_orden;i++){
        $.ajax({
            url: 'index.php?option=com_nota&task=editar_item',
            timeout: 1000,
            type: 'post',
            data: {id_item: $("#id_item"+opcion+"_"+i).val(), 
                    cantidad_original: $("#cantidad_original"+opcion+"_"+i).val(),
                    nueva_cantidad: $("#cantidad"+opcion+"_"+i).val(),
                    descripcion: $("#descripcion_item"+opcion+"_"+i).val(),
                    motivo: $("#motivo"+opcion+"_"+i).val(),
                    id_tipo_modificacion: $("#tipo_modificacion"+opcion+"_"+i).val()
                }
        });
    }

    $.ajax({
        url: 'index.php?option=com_nota&task=adquisiciones.generarOrden',
        timeout: 1500,
        method: 'post',
        data: {id_remitente: id_remitente, orden_compra: orden_compra, opcion: opcion, proveedor: proveedor, opciones: opciones},
        success: function(){
            $("#generada_oc"+opcion).css("display", "block");
            window.open('/portal/media/notas_pedido/Orden_compra.pdf');
        }
    });
}

function exportar_nota(id_remitente){
    $.ajax({
        url: 'index.php?option=com_nota&task=adquisiciones.generar_nota',
        timeout: 2000,
        method: 'post',
        data: {id_remitente: id_remitente},
        success: function(){
            window.open('/portal/media/notas_pedido/nota_pedido.pdf');
        }
    });
}

function cargar_lista(){
    var desde           = $("#desde").val();
    var hasta           = $("#hasta").val();
    var orden_compra    = $("#orden_compra").val();
    var nota_pedido     = $("#nota_pedido").val();
    if (desde+hasta=='' && orden_compra=='' && nota_pedido==''){
        lanzar_alarma("alarma_valores");
    }else if ((desde+hasta)!=''){
        if (hasta==''){
            lanzar_alarma("alarma_valores");
            return;
        }
        $("#facturados").submit();
    }
    if (orden_compra || nota_pedido){
        $("#facturados").submit();
    }
}

function mostrar_lista_copiar(){
    $("#tabla_copiar").dialog({
        width: 1000,
        height: 500,
        open: function(e) {
            $(e.target).parent().css('background-color','white');
        }
    });
}

function actualiza_proveedor(orden_compra){
    $.ajax({
        url: 'index.php?option=com_nota&task=reportes.actualiza',
        type: 'post',
        data: { orden_compra: orden_compra, proveedor: $("#proveedor").val() },
        success: function(){
            $("#actualiza_proveedor").html("<img src='/portal/administrator/templates/hathor/images/menu/icon-16-checkin.png' />");
            
        }
    });
}
function actualiza_factura(orden_compra){
    $.ajax({
        url: 'index.php?option=com_nota&task=reportes.actualiza',
        type: 'post',
        data: { orden_compra: orden_compra, factura: $("#factura").val() },
        success: function(){
            $("#actualiza_factura").html("<img src='/portal/administrator/templates/hathor/images/menu/icon-16-checkin.png' />");
            $("#factura_lista"+orden_compra).html($("#factura").val());
        }
    });
}

function getFactura(orden_compra){
    $.ajax({
        url: 'index.php?option=com_nota&task=carga.getDatos_orden&format=raw',
        type: 'post',
        data: {orden_compra: orden_compra},
        success: function(data){
            data = JSON.parse(data);
            $("#factura_lista"+orden_compra).html(data['factura']);
            $("#factura_copia"+orden_compra).html(data['factura']);
            $("#proveedor_lista"+orden_compra).html(data['proveedor']);
            $("#proveedor_copia"+orden_compra).html(data['proveedor']);
        }
    });
}

function bajar_pdf(id_remitente, orden_compra, opcion){
    $.ajax({
        url: 'index.php?option=com_nota&task=adquisiciones.generarOrden',
        timeout: 2000,
        method: 'post',
        data: {id_remitente: id_remitente, orden_compra: orden_compra, opcion: opcion},
        success: function(){
            $("#generada_oc"+opcion).css("display", "block");
            window.open('/portal/media/notas_pedido/Orden_compra.pdf');
        }
    });
}

function aprobar_naves(id_remitente, items){
    for (var i=1;i<=items;i++){
        $.ajax({
            url: 'index.php?option=com_nota&task=editar_item',
            type: 'post',
            data: {id_item: $("#id_oculto"+i).val(), 
                    cantidad_original: $("#cantidad_oculto"+i).val(),
                    nueva_cantidad: $("#cantidad"+i).val(),
                    descripcion: $("#nueva_descripcion"+i).val(),
                    motivo: $("#nuevo_motivo"+i).val(),
                    id_tipo_modificacion: $("#tipo_modificacion"+i).val()
                }
        });
    }

    $.ajax({
        url: 'index.php?option=com_nota&task=nota_revision',
        method: 'post',
        data: {id_remitente: id_remitente, enviado_empleado:1, autorizado_capitan:1, autorizado_jefe:1, autorizado_depto:0, aprobado_adquisiciones:0},
        success: function(){
            $("#boton_guardar").html("<h3>Enviado</h3>");
        }
    });

    $.ajax({
        url: 'index.php?option=com_nota&task=nota_tramitada',
        timeout: 2000,
        method: 'post',
        data: {id_remitente: id_remitente}
    });
}

function editar_destino(){
    $("#destino_actual").hide();
    $("#nuevo_destino").show();
    $('#editar_destino').hide();
    $('#cambiar_destino').show();
}

function cambiar_destino(id_remitente, nombre_remitente){
    id_adepto = $('#depto_destino option:selected').val();
    texto_depto = $('#depto_destino option:selected').text();
    
    $.ajax({
        url: 'index.php?option=com_nota&task=cambiar_destino',
        type: 'post',
        data: {id_remitente: id_remitente, id_adepto: id_adepto},
        success: function(data){
            console.log(data);
            $("#destino_actual").text(texto_depto);
        }
    });
    $("#destino_actual").show();
    $("#nuevo_destino").hide();
    $('#editar_destino').show();
    $('#cambiar_destino').hide();
}