$(document).ready(function() {
    ;
});

function editar_destino(){
    $("#editar_destino").hide();
    $("#destino_actual").hide();

    $("#guardar_destino").show();
    $("#nuevo_destino").show();
}

function actualizar_destino(id_remitente, nombre_remitente=""){
    var id_destino = parseInt($("#nuevo_destino option:selected").val());
    var texto_nuevo_destino = $("#nuevo_destino option:selected").text();

    $.ajax({
        url: 'index.php?option=com_nota&task=carga.cambiar_destino&format=raw',
        type: 'post',
        data: {id_remitente: id_remitente, id_adepto: id_destino}
    });

    $("#editar_destino").show();
    $("#destino_actual").text(texto_nuevo_destino);
    $("#destino_actual").show();

    $("#guardar_destino").hide();
    $("#nuevo_destino").hide();
}

function actualizar_autorizacion(id_remitente, num_items){ 
    var cantidad_nueva = 0;
    var cantidad_original = 0;
    var id_item = 0;
    var tipo_modificacion = 0;
    var suma_cantidades = 0;
    for (var i=1;i<=num_items;i++){
        cantidad_original = parseInt($("#cantidad_original"+i).val());
        cantidad_nueva = parseInt($("#cantidad"+i).val());
        id_item = parseInt($("#id_item"+i).val());
        tipo_modificacion = parseInt($("#tipo_modificacion"+i+" option:selected").val());
        if (cantidad_original != cantidad_nueva){
            suma_cantidades =+ cantidad_nueva;
            $.ajax({
                url: 'index.php?option=com_nota&task=carga.actualizar_item&format=raw',
                method: 'post',
                data: {cantidad_original: cantidad_original, 
                        cantidad_nueva: cantidad_nueva, 
                        id_item: id_item, 
                        tipo_modificacion: tipo_modificacion},
                success: function(data){
                    console.log(data);
                }
            });    
        }
    }
    // actualizar nota_revision
    $.ajax({
        url: 'index.php?option=com_nota&view=com_nota&task=carga.aprobar_nota&format=raw',
        type: 'post',
       // processData: false,  // tell jQuery not to process the data
        //contentType: false,   // tell jQuery not to set contentType
        data: {id_remitente: id_remitente},
        success: function(data){
            $("#boton_anulacion").hide();
            $("#conjunto_botones").html("<h3>Nota aprobada</h3>");
        }
    });
    $("#conjunto_botones").html("<h3>Nota aprobada</h3>");
}

function dialogo_anulacion(){
    $("#boton_anulacion").hide();
    $("#dialogo_anulacion").show();
    $("#boton_guardar").hide();
}

function anular_nota(id_remitente){
    var comentario = $("#comentario").val();
    if (comentario==""){
        $("#alerta_comentario").show();
        $("#alerta_comentario").effect("highlight", { color: "skyblue" }, 1500);
        return;
    }

    $.ajax({
        url: 'index.php?option=com_nota&view=com_nota&task=anular_nota',
        type: 'post',
        data: { id_remitente: id_remitente },
        success: function(data) {
            $("#dialogo_anulacion").html("<h3>Nota anulada</h3>");
        }
    });
    $.ajax({
        url: 'index.php?option=com_nota&view=com_nota&task=tramitar_anulada',
        type: 'post',
        data: {id_remitente: id_remitente, comentario: comentario, nombre_usuario: ''}
    });
}