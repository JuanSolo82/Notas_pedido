<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHTML::stylesheet('nota.css', 'components/com_nota/assets/css/');
JHTML::script('jquery.min.js', 'components/com_nota/assets/js/');
JHtml::_('behavior.modal');
$user = JFactory::getUser();
?>
<script type="text/javascript" src="/portal/components/com_nota/assets/js/jquery.min.js"></script>

<br>
<input type="hidden" id="id_remitente" value="">
<input type="hidden" id="id_user" value="<?php echo $user->id ?>">

<div class='fila_completa centrar' style='margin-bottom: 20px;'>
	<div class='barra_nombre' style='width: 90%;'>Detalle de nota de pedido <?php echo $this->detalle_nota['id_remitente'] ?></div>
</div>
<div class='centrar'>
	<div class='fila_completa bordear centrar' style='width: 90%;'></div>
</div>
<div class='centrar'>
	<div class='fila_completa bordear centrar' style='width: 90%;'>
		<div class="col-3 titulo_item">Remitente</div>
		<div class="col-7"><?php echo $this->detalle_nota['nombre_remitente'] ? $this->detalle_nota['nombre_remitente'] : $this->detalle_nota['nombre_usuario'] ?></div>
	</div>
</div>
<div class='centrar'>
	<div class='fila_completa bordear centrar' style='width: 90%;'>
		<div class="col-3 titulo_item"><?php echo ($user->authorise('tripulante', 'com_nota') && !$user->authorise('core.admin', 'com_nota')) ? 'Nave' : 'Departamento origen' ?></div>
		<div class="col-7"><?php echo $this->detalle_nota['depto_origen']; ?></div>
	</div>
</div>
<div class='centrar'>
	<div class='fila_completa bordear centrar' style='width: 90%;'>
		<div class="col-3 titulo_item">Fecha de creación</div>
		<div class="col-7"><?php echo NotaHelper::fechamysql($this->detalle_nota['fecha'],1) ?></div>
	</div>
</div>
<div class='centrar'>
    <div class='fila_completa bordear centrar' style='width: 90%;'>
        <div class="col-3 titulo_item">Departamento destino</div>
        <div class='col-6'>
            <span id="destino_actual" style="display: block"><?php echo $this->detalle_nota['depto_destino'] ?></span>
            <select id='nuevo_destino' style="display: none">
				<?php foreach ($this->lista_deptos as $d){ ?>
					<option value='<?php echo $d['id'] ?>' <?php echo $this->detalle_nota['id_adepto']==$d['id'] ? 'selected' : '' ?>><?php echo $d['nombre'] ?></option>
				<?php } ?>
			</select>
        </div>
            
        <div class="col-1">
        <?php 
        if ($user->authorise('gestion_naves','com_nota') 
                    && $this->detalle_nota['enviado_empleado']==1 
                    && $this->detalle_nota['autorizado_jefe']==0 
                    && $this->detalle_nota['aprobado_adquisiciones']==0){ ?>
            <a id="editar_destino" onclick="editar_destino()" style="display: block;">
                <img src="/portal/administrator/templates/hathor/images/menu/icon-16-edit.png">
            </a>
            <!--<a id="guardar_destino" onclick="actualizar_destino(<?php echo $this->detalle_nota['id_remitente'] ?>)" style="display: none;">
                <img src="/portal/administrator/templates/hathor/images/menu/icon-16-save.png">
            </a>-->
        <?php } ?>
        </div>
    </div>
</div>
<div class='centrar'>
	<div class='fila_completa bordear centrar' style='width: 90%;'>
		<div class="col-3 titulo_item">Prioridad</div>
		<div class="col-7"><?php echo $this->detalle_nota['prioridad']; ?></div>
	</div>
</div>
<div class='centrar'>
	<div class='fila_completa bordear centrar' style='width: 90%;'>
		<div class="col-3 titulo_item">Encargado de compra</div>
		<div class="col-7"><?php echo $this->detalle_nota['depto_compra']; ?></div>
	</div>
</div>
<div class='fila_vacia'></div>

<div class='centrar'>
    <table class='tabla_listado'>
		<tr>
			<th width='10%'>Cantidad</th>
			<th>Item</th>
			<th width='30%'>Motivo</th>
		</tr>
    <?php 
    $cont=0;
    foreach ($this->items as $i){ 
        $cantidad = $i['nueva_cantidad'] ? $i['nueva_cantidad'] : $i['cantidad'];
        if ($cantidad){ 
            $cont++;
            ?>
        <tr>
            <td>
                <input type='hidden' id='id_item<?php echo $cont ?>' value='<?php echo $i['id'] ?>'>
                <input type='hidden' id='cantidad_original<?php echo $cont ?>' value='<?php echo $cantidad ?>'>
                <input type="number" id='cantidad<?php echo $cont ?>' min="0" value="<?php echo $cantidad ?>" style="width: 50px;">
            </td>
            <td>
                <span id='descripcion_item<?php echo $cont ?>'><?php echo $i['item'] ?></span>
            </td>
            <td>
                <select id='tipo_modificacion<?php echo $cont ?>'>
					<option value='1'>Reducción por existencia</option>
					<option value='2'>Denegación</option>
				</select>
            </td>
        </tr>
<?php   }
        ?>
        
    <?php } ?>
    </table>
</div>

<div class='fila_vacia'></div>
<div class='centrar'>
    <?php 
    if ($this->detalle_nota['pendiente']){ ?>
        <div id="conjunto_botones">
			<div class='boton' onclick="actualizar_autorizacion(<?php echo $this->detalle_nota['id_remitente'] ?>,<?php echo $cont ?>, <?php echo $this->detalle_nota['id_adepto'] ?>)" id="boton_guardar">
				<img src='/portal/administrator/templates/hathor/images/header/icon-48-save.png' /><br>
				Guardar cambios
			</div>
            <div id="boton_anulacion" onclick="dialogo_anulacion()" class='boton'>
                <img src='/portal/administrator/templates/hathor/images/header/icon-48-deny.png' /><br>Anular
            </div>
            <div id="dialogo_anulacion" class="barra_nombre" style="display: none;">
                Comentario <input type="text" id="comentario" autocomplete="off">
                <i style="
                        font-size: smaller;
                        font-weight: lighter;
                        color: red;
                        position: absolute;
                        float: left;
                        bottom: 50px;
                        width: 150px;
                        left: 80px;
                        display: none;"
                        id="alerta_comentario">Ingrese comentario</i>
                <a onclick="anular_nota(<?php echo $this->detalle_nota['id_remitente'] ?>)">
                    <img src='/portal/administrator/templates/hathor/images/menu/icon-16-save.png' />
                </a>
            </div>
        </div>
    <?php } ?>
</div>

<script type="text/javascript" src="/portal/components/com_nota/assets/js/editar_nota.js?docs=2"></script>