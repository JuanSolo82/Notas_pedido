<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHTML::stylesheet('nota.css', 'components/com_nota/assets/css/');
JHTML::stylesheet('jquery-ui.css', 'components/com_nota/assets/css/');
JHTML::script('jquery.min.js', 'components/com_nota/assets/js/');
JHTML::script('jquery-ui.min.js', 'components/com_nota/assets/js/');
JHTML::script('nota.js', 'components/com_nota/assets/js/');
JHtml::_('behavior.modal'); 
$f = explode('-', $this->detalle_nota['fecha']);
$j=1;
$user = JFactory::getUser();
?>
<br>

<input type="hidden" value="<?php echo $this->detalle_nota['id_user'] ?>" size="2" id="id_user", name="id_user">
<input type="hidden" value="<?php echo $this->id_remitente ?>" id="id_remitente" name="id_remitente">
<input type="hidden" value="<?php echo $user->authorise('tripulante', 'com_nota') ?>" id="tripulante">

<div class='fila_completa centrar' style='margin-bottom: 20px;'>
	<div class='barra_nombre' style='width: 90%;'>Detalle de nota de pedido <?php echo $this->id_remitente ?></div>
</div>
<div class='centrar'>
	<div class='fila_completa bordear centrar' style='width: 90%;'></div>
</div>
<?php if ($user->authorise('tripulante', 'com_nota')){ ?>
	<div class='centrar'>
		<div class='fila_completa bordear centrar' style='width: 90%;'>
			<div class="col-3 titulo_item">Nombre</div>
			<div class="col-7">
			<form autocomplete="off">
				<input type="text" id="nombre_tripulante" name="nombre_tripulante">
				<div id="campo_nombre"></div>
			</form>
			</div>
		</div>
	</div>
<?php } ?>
<div class='centrar'>
	<div class='fila_completa bordear centrar' style='width: 90%;'>
		<div class="col-3 titulo_item">Nombre remitente</div>
		<div class="col-7"><?php echo $this->detalle_nota['nombre_remitente'] ? $this->detalle_nota['nombre_remitente'] : $this->detalle_nota['nombre_usuario']; ?></div>
	</div>
</div>
<div class='centrar'>
	<div class='fila_completa bordear centrar' style='width: 90%;'>
		<div class="col-3 titulo_item">Fecha de creación</div>
		<div class="col-7"><?php echo $f[2].'-'.$f[1].'-'.$f[0]; ?></div>
	</div>
</div>
<div class='centrar'>
	<div class='fila_completa bordear centrar' style='width: 90%;'>
		<div class="col-3 titulo_item">Departamento destino</div>
		<div class="col-7"><?php echo $this->detalle_nota['depto_destino'] ?></div>
	</div>
</div>
<div class='centrar'>
	<div class='fila_completa bordear centrar' style='width: 90%;'>
		<div class="col-3 titulo_item">Encargado de compra</div>
		<div class="col-7"><?php echo $this->detalle_nota['depto_compra']; ?></div>
	</div>
</div>
<div class='centrar'>
	<div class='fila_completa bordear centrar' style='width: 90%;'>
		<div class="col-3 titulo_item">Prioridad</div>
		<div class="col-7"><?php echo $this->detalle_nota['prioridad']; ?></div>
	</div>
</div>
<div class='fila_vacia'></div>
<form id='formItems' name="formItems" action="<?php echo JRoute::_('index.php?option=com_nota&view=com_nota&task=editar_nota'); ?>" method='post' autocomplete='off'>
<input type="hidden" value="<?php echo $this->datos_user['generico']; ?>" id="generico" name="generico">
<div class='centrar'>
	<table class='tabla_listado' id='contenido_editable'>
		<tr>
			<th width='10%'>Cantidad</th>
			<th width='40%'>Item</th>
			<th width='30%'>Motivo</th>
			<th width='10%'>Tipo de modificación</th>
			<th width='10%'>Adjunto</th>
		</tr>
	<?php foreach ($this->items as $i){ ?>
		<input type="hidden" id="id_oculto<?php echo $j ?>" value="<?php echo $i['id'] ?>">
		<input type="hidden" id="cantidad_oculto<?php echo $j ?>" value="<?php echo $i['cantidad'] ?>">
		<input type="hidden" id="descripcion_oculto<?php echo $j ?>" value="<?php echo $i['item'] ?>">
		<input type="hidden" id="motivo_oculto<?php echo $j ?>" value="<?php echo $i['motivo'] ?>">
		<tr>
			<td align='center'><input id='cantidad<?php echo $j ?>' value="<?php echo $i['cantidad'] ?>" type='number' size='2' required type="number" min="0" step=".1" style='width: 70px;'></td>
			<td><input type="text" id="nueva_descripcion<?php echo $j ?>" value="<?php echo $i['item'] ?>" style="width: 80%;"></td>
			<td><input type="text" id="nuevo_motivo<?php echo $j ?>" value="<?php echo $i['motivo'] ?>" style="width: 80%;"></td>
			<td>
				<select id='tipo_modificacion<?php echo $j ?>' name='tipo_modificacion<?php echo $j ?>'>
					<option value='1'>Reducción por existencia</option>
					<option value='2'>Denegación</option>
				</select>
			</td>
			<td align='center'><?php if ($i['adjunto']){ ?>
					<a href="/portal/media/notas_pedido/adjuntos/<?php echo $this->id_remitente ?>/<?php echo $i['adjunto'] ?>" 
						class="modal">
						<img src='/portal/administrator/templates/hathor/images/menu/icon-16-archive.png' />
					</a>
				<?php } ?>
			</td>
		</tr>
	<?php $j++; 
		} ?>
	
	<table class='tabla_listado' id="contenido_editado" style="display: none;">
		<tr>
			<th width='10%'>Cantidad</th>
			<th width='45%'>Item</th>
			<th width='45%'>Motivo</th>
		</tr>
	<?php for ($k=1;$k<$j;$k++){ ?>
		<tr>
			<td id="cantidad_editado<?php echo $k ?>"></td>
			<td id="descripcion_editado<?php echo $k ?>"></td>
			<td id="motivo_editado<?php echo $k ?>"></td>
		</tr>
	<?php } $j--; ?>
	</table>
</div>
</form>
<div class='fila_vacia'></div>
<div class='centrar' id='botones'>
<?php if ($this->datos_user['id']==$this->detalle_nota['id_user'] && $this->detalle_nota['aprobado_adquisiciones']==0){ ?>
	<div onclick="guardar_cambios_items(<?php echo $j ?>, <?php echo ($user->authorise('capitan.jefe', 'com_nota') || $user->authorise('capitan.sin_jefe', 'com_nota')) ? 1 : 0 ?>, <?php echo ($user->authorise('jefe.depto', 'com_nota')) ? 1 : 0 ?>)" class='boton'><img src='/portal/administrator/templates/hathor/images/header/icon-48-save.png' /><br>Guardar cambios</div>
	<!--<div onclick="anular_nota(<?php echo $this->id_remitente ?>, <?php echo $this->detalle_nota['id_user'] ?>)" class='boton'>
		<img src='/portal/administrator/templates/hathor/images/header/icon-48-deny.png' /><br>
		Anular
	</div>-->
	<div id="boton_anulacion" onclick="dialogo_anulacion()" class='boton'><img src='/portal/administrator/templates/hathor/images/header/icon-48-deny.png' /><br>Anular</div>
	<div id="dialogo_anulacion" class="barra_nombre" style="display: none;">
		Comentario <input type="text" id="comentario" autocomplete="off">
		<a onclick="anular_nota(<?php echo $this->id_remitente ?>)"><img src='/portal/administrator/templates/hathor/images/menu/icon-16-save.png' /></a>
	</div>
<?php } ?>
</div>
<div id="nombre_vacio" title="Atención" style="display: none;">
 	<p>Debe ingresar su nombre</p>
</div>