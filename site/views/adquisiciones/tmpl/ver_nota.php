<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHTML::stylesheet('bootstrap.css', 'components/com_nota/assets/bootstrap/css/');
JHTML::stylesheet('nota.css', 'components/com_nota/assets/css/');
JHTML::script('jquery.min.js', 'components/com_nota/assets/js/');
JHTML::script('jquery-ui.min.js', 'components/com_nota/assets/js/');
JHTML::script('adquisiciones.js', 'components/com_nota/assets/js/');
JHTML::script('bootstrap.js', 'components/com_nota/assets/bootstrap/js/');
JHtml::_('behavior.modal'); 
?>
<br>
<div class='centrar' style="margin-bottom: 30px;">
<div class='barra_nombre' style='width: 95%;'>Detalle de nota de pedido <?php echo $this->id_remitente ?></div>
</div>
<input type="hidden" id="id_remitente" value="<?php echo $this->id_remitente ?>">
<div id="contenido_editable">
<table class='tabla_listado' style="width: 97%;">
	<tr>
		<th align="center" width='10%'>Cantidad</th>
		<th align="center" width='40%'>Ítem</th>
		<th align="center" width='30%'>Motivo</th>
		<th align="center" width='10%'>Motivo modificación</th>
		<th align="center" width='10%'>Opción OC</th>
	</tr>
<?php 
	$j=0;
	foreach ($this->items as $i){ 
	if ($i['nueva_cantidad']==null || $i['nueva_cantidad']>0){
		$j++;
?>
	<input type="hidden" id="id_item<?php echo $j ?>" value="<?php echo $i['id'] ?>">
	<input type="hidden" id="cantidad_original<?php echo $j ?>" value="<?php echo $i['nueva_cantidad'] ? $i['nueva_cantidad'] : $i['cantidad'] ?>">
	<tr>
		<td><input type="number" id="cantidad<?php echo $j ?>" name="cantidad<?php echo $j ?>" style="width: 50px;" step='0.1' value="<?php echo $i['nueva_cantidad'] ? $i['nueva_cantidad'] : $i['cantidad'] ?>"></td>
		<td><input type="text" id="descripcion_item<?php echo $j ?>" name="descripcion_item<?php echo $j ?>" value="<?php echo $i['item'] ?>"></td>
		<td><input type="text" id="motivo<?php echo $j ?>" name="motivo<?php echo $j ?>" value="<?php echo $i['motivo'] ?>"></td>
		<td>
			<select id='tipo_modificacion<?php echo $j ?>' name='tipo_modificacion<?php echo $j ?>'>
				<option value='1'>Reducción por existencia</option>
				<option value='2'>Denegación</option>
			</select>
		</td>
		<td>
			<select id='opcion_oc<?php echo $j ?>'>
			<?php for ($k=1;$k<=7;$k++){ ?>
				<option <?php echo ($k==$i['opcion_oc']) ? "selected" : "" ?>><?php echo $k ?></option>
			<?php } ?>
			</select>
		</td>
	</tr>
	<?php } ?>
<?php } ?>
</table>
</div>

<div id="contenido_editado" style="display: none;">
<?php $k=0; ?>
<table class='tabla_listado' style="width: 97%;">
	<tr>
		<th align="center" width="10%">Cantidad</th>
		<th align="center" width="40%">Ítem</th>
		<th align="center" width="40%">Motivo</th>
		<th align="center" width="10%">Opción OC</th>
	</tr>
<?php for ($k=1;$k<=$j;$k++){ ?>
	<tr>
		<td id="cantidad_editado<?php echo $k ?>"></td>
		<td id="descripcion_editado<?php echo $k ?>"></td>
		<td id="motivo_editado<?php echo $k ?>"></td>
		<td id="opcion_editado<?php echo $k ?>"></td>
	</tr>
<?php } ?>
</table>
</div>
<div class="centrar">
	<div id="guardar_cambios" onclick="guardar_cambios(<?php echo $j ?>)" class="boton"><img src="/portal/administrator/templates/hathor/images/header/icon-48-save.png" /><br>Guardar cambios</div>
	<a id="generar_oc" style="display: none;" >
		<div class="boton"><img src="/portal/administrator/templates/hathor/images/header/icon-48-revert.png" /><br>Ir a generar OC</div>
	</a>
</div>