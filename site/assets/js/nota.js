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

    $("#parametro").keyup(function(e){
        if (e.keyCode==13 && $("#parametro").val()!=""){
            buscar_notas_propias();
        }
    });
    $("#proveedor").keyup(function(e){
        if (e.keyCode==13 && $("#proveedor").val()!=""){
            buscar_notas_propias();
        }
    });
    $("#proveedor").focus(function(){
        $("#parametro").val("");
    });
    $("#parametro").focus(function(){
        $("#proveedor").val("");
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
    $("#proveedor_escogido").blur(function(){
        $("#lista_proveedores").fadeOut();
    });
    $("#cantidad1").focus(function(){
        $("#lista_proveedores").fadeOut();
    });

    if ($("#tipo_gasto").length){
        var tipo_pedido = parseInt($("#tipo_pedido option:selected").val());
        if (tipo_pedido==1)
            $("#tipo_gasto").prop('disabled', false);
        else
            $("#tipo_gasto").prop('disabled', true);
        $("#tipo_pedido").change(function(){
            var tipo_pedido = parseInt($("#tipo_pedido option:selected").val());
            if (tipo_pedido==1)
                $("#tipo_gasto").prop('disabled', false);
            else{
                $("#tipo_gasto").val(0);
                $("#tipo_gasto").prop('disabled', true);
            }
        });
    }
});

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

    var proveedor_escogido  = $("#proveedor_escogido").length ? $("#proveedor_escogido").val() : '';
    var rut_proveedor       = $("#rut_proveedor").length ? $("#rut_proveedor").val() : '';
    var giro_proveedor      = $("#giro_proveedor").length ? $("#giro_proveedor").val() : '';
    $.ajax({
        url: 'index.php?option=com_nota&task=nota_tramitada',
        timeout: 2000,
        method: 'post',
        data: {id_remitente: id_remitente, proveedor_escogido: proveedor_escogido, rut_proveedor: rut_proveedor, giro_proveedor: giro_proveedor},
        success: function(data){
            console.log(data);
        }
    });
    // replicar en bbdd sql server
    $.ajax({
        url: "index.php?option=com_nota&task=carga.actualizarRevision&format=raw",
        type: 'post',
        data: { id_remitente: id_remitente, autorizado_jefe: 1 }
    });
}

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

function anterior_previo_depto(direccion, notas_area=0){
    var pagina = parseInt($("#pagina").val());
    if (direccion==1)
        pagina++;
    else
        pagina--; 
    if (pagina<0)
        return;
    if (notas_area){
        $.ajax({
                url: 'index.php?option=com_nota&task=carga.rango_area&format=raw',
                type: 'post',
                data: {pagina: pagina},
                success: function(data){
                    $("#notas_area").html(data);
                }
            });
            $("#notas_area").css({'opacity':'0.5'});
            setTimeout(function(){
                $("#notas_area").css({'opacity':'1'});
            },500);
    }else{
        $.ajax({
            url: 'index.php?option=com_nota&task=carga.depto_rango&format=raw',
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
    }
    
    $("#pagina").val(pagina);
}

function previo_depto(direccion){
    var pagina = parseInt($("#pagina").val());
    if (direccion==1)
        pagina++;
    else
        pagina--;
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
        var nueva_cantidad = parseFloat($("#cantidad" + id_item).val());
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
            var nueva_cantidad = parseFloat($("#cantidad" + id_item).val());
            var descripcion = $.trim($("#nueva_descripcion" + i).val());
            var motivo = $.trim($("#nuevo_motivo" + i).val());
            var valor_unitario = parseInt($("#valor_unitario"+id_item).val());
            $.ajax({
                url: 'index.php?option=com_nota&task=editar_item',
                type: 'post',
                data: {
                    id_item: id_item,
                    cantidad_original: cantidad_original,
                    nueva_cantidad: nueva_cantidad,
                    id_tipo_modificacion: id_tipo_modificacion,
                    descripcion: descripcion,
                    motivo: motivo,
                    valor_unitario: valor_unitario
                }
            });
            $("#columna_cantidad"+id_item).html(nueva_cantidad);
            $("#columna_descripcion"+id_item).html(descripcion);
            $("#columna_parcial"+id_item).html(valor_unitario);
            $("#parcial_texto"+id_item).html(valor_unitario*nueva_cantidad);
        }
        $("#botones").hide();
        if (jefe == 1)
            capitan = 1;
        $.ajax({
            url: "index.php?option=com_nota&task=carga.nota_revision&format=raw",
            type: 'post',
            data: { id_remitente: id_remitente, 
                    enviado_empleado: 1, 
                    autorizado_capitan: capitan, 
                    autorizado_jefe: jefe, 
                    autorizado_depto: autorizado_depto, 
                    aprobado_adquisiciones: 0 },
            success: function(data){
                console.log(data);
                $("#conjunto_botones").hide();
                $("#enviado").css({'display': 'block'});
            }
        });
        var proveedor = $("#proveedor_escogido").val();
        var rut_proveedor = $("#rut_proveedor").val();
        var giro_proveedor = $("#giro_proveedor").val();
        $.ajax({
            url: 'index.php?option=com_nota&task=nota_tramitada',
            type: 'post',
            data: {id_remitente: id_remitente, 
                    generico: generico, 
                    nombre_remitente: nombre_remitente, 
                    proveedor_escogido: proveedor,
                    rut_proveedor: rut_proveedor,
                    giro_proveedor: giro_proveedor
            }
        });
        var html = proveedor+"<br>"+rut_proveedor+"<br>"+giro_proveedor;
        $("#datos_proveedor").html(html);
        
        if ($("#nombre_tripulante").length){
            $("#nombre_tripulante").hide();
            $("#campo_nombre").text(nombre_remitente);
        }
        $("#aviso_nota_autorizada").show(300);
    } else {
        anular_nota($("#id_remitente").val(), $("#id_user").val());
    }
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
    var proveedor       = $('#proveedor_escogido'+opcion).val();
    var rut_proveedor   = $("#rut_proveedor"+opcion).val();
    var giro_proveedor  = $("#giro_proveedor"+opcion).val();
    var cotizacion      = $("#cotizacion"+opcion).val();
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
                    id_tipo_modificacion: $("#tipo_modificacion"+opcion+"_"+i).val(),
                    valor_unitario: $("#valor_unitario"+opcion+"_"+i).val()
                },
            success: function(datos){
                console.log(datos);
            }
        });
    }
    $.ajax({
        url: 'index.php?option=com_nota&task=adquisiciones.generarOrden',
        timeout: 1500,
        method: 'post',
        data: {id_remitente: id_remitente, 
                orden_compra: orden_compra, 
                opcion: opcion, 
                proveedor: proveedor, 
                rut_proveedor: rut_proveedor,
                giro_proveedor: giro_proveedor,
                opciones: opciones,
                cotizacion: cotizacion},
        success: function(){
            window.open('/portal/media/notas_pedido/Orden_compra'+id_remitente+'-'+opcion+'.pdf', 'nombre'); 
            $("#generada_oc"+opcion).css("display", "block");
        }
    });
    $.ajax({
        url: "index.php?option=com_nota&task=carga.actualizarRevision&format=raw",
        type: 'post',
        data: { id_remitente: id_remitente, aprobado_adquisiciones: 1 }
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
            window.open('/portal/media/notas_pedido/Orden_compra'+id_remitente+'-'+opcion+'.pdf');
        }
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
            $("#destino_actual").text(texto_depto);
        }
    });
    $("#destino_actual").show();
    $("#nuevo_destino").hide();
    $('#editar_destino').show();
    $('#cambiar_destino').hide();
}

function cargar_proveedor(str, ind=0){
    if (!ind) $("#proveedor").empty();
    else $("#proveedor"+ind).empty();
    if (str.length>1){
        $.ajax({
            url: 'index.php?option=com_nota&task=carga.getListaProveedor&format=raw',
            type: 'post',
            data: { str: str, ind: ind },
            success: function(data){
                if (!ind)
                    $("#proveedor").append(data);
                else
                    $("#proveedor"+ind).append(data);
            }
        });
    }
    /*var availableTags = [
        "ActionScript",
        "AppleScript",
        "Asp",
        "BASIC",
        "C",
        "C++",
        "Clojure",
        "COBOL",
        "ColdFusion",
        "Erlang",
        "Fortran",
        "Groovy",
        "Haskell",
        "Java",
        "JavaScript",
        "Lisp",
        "Perl",
        "PHP",
        "Python",
        "Ruby",
        "Scala",
        "Scheme"
    ];
    $("#proveedor").autocomplete({
        source: availableTags
    });*/
}

function escoger_proveedor(razon_social, rut="", giro="", ind=0){
    if (!ind){
        $("#proveedor_escogido").val(razon_social);
        $("#rut_proveedor").val(rut);
        $("#giro_proveedor").val(giro);
        $("#lista_proveedores").fadeOut();
        $("#proveedor").empty();
    }else{
        $.ajax({
            url: 'index.php?option=com_nota&task=carga.getProveedor&format=raw',
            type: 'post',
            data: { razon_social: razon_social, rut: rut },
            success: function(data){
                data = JSON.parse(data);
                $("#proveedor_escogido"+ind).val(data['RazonSocial']);
                $("#rut_proveedor"+ind).val(data['rut']);
                $("#giro_proveedor"+ind).val(data['giro']);
            }
        });
        //$("#proveedor_escogido"+ind).val(razon_social);
        //$("#rut_proveedor"+ind).val(rut);
        //$("#giro_proveedor"+ind).val(rut);
        $("#lista_proveedores"+ind).fadeOut();
        $("#proveedor"+ind).empty();
    }
    $("#lista_proveedores").fadeOut();
}

function buscar_notas_propias(){
    var parametro = $.trim($("#parametro").val());
    var proveedor = $.trim($("#proveedor").val());
    var naves = parseInt($("#naves").val());
    $("#lista").html("<div class='loader'></div>");
    if (parametro!='' || proveedor!=''){
        $.ajax({
            url: 'index.php?option=com_nota&task=carga.buscar_notas&format=raw',
            type: 'post',
            data: {parametro: parametro, proveedor: proveedor, naves: naves},
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
}

function copia_oc(id_remitente, orden_compra, opcion){
    $.ajax({
        url: 'index.php?option=com_nota&task=adquisiciones.generarOrden',
        timeout: 1500,
        method: 'post',
        data: {id_remitente: id_remitente, 
                orden_compra: orden_compra, 
                opcion: opcion},
        success: function(){
            $("#generada_oc"+opcion).css("display", "block");
            window.open('/portal/media/notas_pedido/Orden_compra.pdf');
        }
    });
}

function limpiar_busqueda(){
    $("#pagina").val(2);
    $("#parametro").val('');
    $("#proveedor").val('');
    anterior_previo(2);
}

function actualiza_parcial(id_item){
    var valor_unitario = parseInt($("#valor_unitario"+id_item).val());
    var cantidad = parseInt($("#cantidad"+id_item).val());
    $("#parcial_texto"+id_item).html((valor_unitario*cantidad).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.'));
    $("#valor"+id_item).val((valor_unitario).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.'));
    $("#subtotal"+id_item).val((valor_unitario*cantidad).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.'));
    $("#valor_numerico"+id_item).val(valor_unitario);
    $("#subtotal_numerico"+id_item).val(valor_unitario*cantidad);
}



function busca_nave(id_nave){
    id_nave = parseInt(id_nave);
    var desde = $("#desde").val();
    var hasta = $("#hasta").val()
    if (id_nave && desde!='' && hasta!=''){
        $("#lista").html("<div class='loader'></div>");
        $.ajax({
            url: 'index.php?option=com_nota&task=carga.buscar_notas&format=raw',
            type: 'post',
            data: {id_nave: id_nave, desde: desde, hasta: hasta},
            success: function(data){
                $("#lista").hide();
                $("#lista_propias").html(data);
            }
        });
    }else{
        console.log('complete los campos de fecha y ferry');
    }
}

function buscar_item(ind, item, id_user){
    if (item.length>3){
        $.ajax({
            url: 'index.php?option=com_nota&task=carga.buscar_item&format=raw',
            type: 'post',
            data: {ind: ind, item: item, id_user: id_user},
            success: function(data){
                $("#items"+ind).html(data);
            }
        });
        
    }
}

function escoger_item(ind,item){
    $("#descripcion"+ind).val(item);
    $("#items"+ind).fadeOut(500);
}

function buscar_nota_area(){
    var parametro = $("#parametro").val().trim();
    if (parametro!=''){
        $("#notas_area").html("<div class='loader'></div>");
        $.ajax({
                url: 'index.php?option=com_nota&task=carga.rango_area&format=raw',
                type: 'post',
                data: {parametro: parametro},
                success: function(data){
                    $("#notas_area").html(data);
                }
            });
            $("#notas_area").css({'opacity':'0.5'});
            setTimeout(function(){
                $("#notas_area").css({'opacity':'1'});
            },500);
    }else{
        console.log('complete el campo de búsqueda');
    }
}

function editar_regimen(id_nave){
    $("#vigencia"+id_nave).hide();
    $("#fechas"+id_nave).show();
    $("#editar_regimen"+id_nave).hide();
    $("#guardar_regimen"+id_nave).show();
}

function definir_fechas(id_nave, desde=0){
    if (desde){
        $("#desde"+id_nave).datepicker({
            changeMonth: true,
            numberOfMonths: 3,
            minDate: '0'
        }).focus();
        $("#hasta").val("");
        $("#hasta").prop('disabled', false);
        $("#hasta").css('background', 'white');
    }else{
        var fecha = $("#desde"+id_nave).val().split('-');
        $("#hasta"+id_nave).datepicker({
            changeMonth: true,
            numberOfMonths: 3,
            minDate: new Date(fecha[2],fecha[1]-1,fecha[0])
        }).focus();
    }
}

function guardar_regimen(id_nave){
    var desde = $("#desde"+id_nave).val();
    var hasta = $("#hasta"+id_nave).val();
    if (desde.lenght==0 || hasta.length==0){
        alert("ingrese todos los valores");
        return;
    }
    var f1     = new Date();
    f1 = f1.getFullYear()+'-'+(f1.getMonth()+1)+'-'+f1.getDate();
    var hoy = new Date(f1);
    var inicio = desde.split('-');
    var f2 = new Date(inicio[2]+'-'+inicio[1]+'-'+inicio[0]);
    
    var ley_navarino = parseInt($("#navarino_actual"+id_nave+" option:selected").val());
    console.log(ley_navarino);
    $.ajax({
        url: 'index.php?option=com_nota&task=setNavarino',
        type: 'post',
        data: {id_nave: id_nave, desde: desde, hasta: hasta, ley_navarino: ley_navarino},
        success: function(data){
            $("#vigencia"+id_nave).html((ley_navarino ? '<b>Régimen especial</b>' : '<b>Régimen general</b>')+" desde "+desde+" hasta "+hasta);
        }
    });
    if (hoy>=f2){
        if (ley_navarino)
            $("#estado"+id_nave).html("<img src='/portal/administrator/templates/hathor/images/menu/icon-16-checkin.png' />");
        else
            $("#estado"+id_nave).html("<img src='/portal/administrator/templates/hathor/images/menu/icon-16-delete.png' />");
    }
    ocultar_ediciones(id_nave);
}

function ocultar_ediciones(id_nave){
    $("#vigencia"+id_nave).show();
    $("#fechas"+id_nave).hide();
    $("#editar_regimen"+id_nave).show();
    $("#guardar_regimen"+id_nave).hide();
}
