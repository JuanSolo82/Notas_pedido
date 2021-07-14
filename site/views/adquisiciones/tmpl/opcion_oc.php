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
<div class='barra_nombre' style='width: 95%;'>Opciones OC para nota <?php echo $this->id_remitente ?></div>
</div>

<div class='centrar'>
	<div class='fila_completa bordear centrar' style='width: 90%;'>
		<div class="col-3 titulo_item">Centro de costo</div>
		<div class="col-7">
			<select id='centro_costo' name='centro_costo'>
			<?php foreach ($this->centros_costo as $ct){ ?>
				<option value="<?php echo $ct['id'] ?>" <?php echo $ct['id']==$this->depto_costo['id'] ? 'selected' : '' ?>><?php echo $ct['nombre'] ?></option>
			<?php } ?>
			</select>
		</div>
	</div>
</div>
<div class='fila_vacia'></div>
<input type="hidden" id="id_remitente" value="<?php echo $this->id_remitente ?>">

<table class='tabla_listado' style="width: 97%;">
	<tr>
		<th>Ítem</th>
		<th>Opción OC</th>
	</tr>
<?php 
$j=1;
foreach ($this->items as $item){ ?>
	<tr>
		<td><?php echo $item['item'] ?></td>
		<td>
			<div id="guardado<?php echo $j ?>" style="display: none;"></div>
			<select id="opciones_oc<?php echo $j ?>">
			<?php for ($i=1;$i<=10;$i++){ ?>
				<option value="<?php echo $i ?>" <?php echo ($item['opcion_oc']==$i) ? "selected" : "" ?>><?php echo $i ?></option>
			<?php } ?>
			</select>
		</td>
	</tr>
	<input type="hidden" value="<?php echo $item['id'] ?>" id="id_item<?php echo $j ?>">
<?php $j++; } ?>
</table><br>
<div class="centrar">
	<a id="guardar_cambios" onclick="guardar_opciones(<?php echo $j-1 ?>,<?php echo $this->id_remitente ?>)">
		<div class="boton"><img src="/portal/administrator/templates/hathor/images/header/icon-48-save.png" /><br>Guardar cambios</div>
	</a>
	<div class='barra_nombre' style='width: 95%; display: none;' id="cambios_guardados">Cambios guardados</div>
</div>
