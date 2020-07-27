<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHTML::stylesheet('nota.css', 'components/com_nota/assets/css/');
JHTML::script('jquery.min.js', 'components/com_nota/assets/js/');
JHTML::script('jquery-ui.min.js', 'components/com_nota/assets/js/');
JHTML::script('nota.js', 'components/com_nota/assets/js/');
JHtml::_('behavior.modal');
$user = JFactory::getUser();
$f = explode('-', $this->detalle_nota['fecha']);
?>
<br>
<input type="hidden" id="id_remitente" value="<?php echo $this->id_remitente ?>">
<input type="hidden" id="id_user" value="<?php echo $user->id ?>">

<div class='fila_completa centrar' style='margin-bottom: 20px;'>
	<div class='barra_nombre' style='width: 90%;'>Detalle de nota de pedido <?php echo $this->id_remitente ?></div>
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
		<div class="col-7"><?php echo $f[2].'-'.$f[1].'-'.$f[0]; ?></div>
	</div>
</div>
<div class='centrar'>
	<div class='fila_completa bordear centrar' style='width: 90%;'>
		<div class="col-3 titulo_item">Departamento destino</div>
		<div class="<?php echo ($user->authorise('jefe.depto', 'com_nota') && $this->detalle_nota['aprobado_adquisiciones']==0) ? 'col-6' : 'col-7' ?>" id='destino_actual'><?php echo $this->detalle_nota['depto_destino'] ?></div>
		<?php if ($user->authorise('jefe.depto', 'com_nota')){ ?>
			<div class="col-7" style="display: none" id='nuevo_destino'>
				<select id='depto_destino'>
				<?php foreach ($this->lista_deptos as $d){ ?>
					<option value='<?php echo $d['id'] ?>' <?php echo $this->detalle_nota['id_adepto']==$d['id'] ? 'selected' : '' ?>><?php echo $d['nombre'] ?></option>
				<?php } ?>
				</select>
			</div>
			<?php if ($this->detalle_nota['aprobado_adquisiciones']==0){ ?>
			<div class="col-1">
				<a onclick="editar_destino()" id='editar_destino'><img src="/portal/administrator/templates/hathor/images/menu/icon-16-edit.png"></a>
				<a onclick="cambiar_destino(<?php echo $this->id_remitente ?>,'<?php echo $this->detalle_nota['nombre_remitente'] ? $this->detalle_nota['nombre_remitente'] : '' ?>')" id='cambiar_destino' style="display: none"><img src="/portal/administrator/templates/hathor/images/menu/icon-16-save.png"></a>
			</div>
			<?php } ?>
		<?php } ?>
		
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

<?php if (NotaHelper::isTestSite()){ 
	$p = explode('_', $this->detalle_nota['proveedor']);
	?>
	<div class='centrar'>
		<div class='fila_completa bordear' style='width: 90%;'>
			<div class="col-3 titulo_item">Proveedor (opcional)</div>
			<div class="col-4">
			<?php if ($this->detalle_nota['id_adepto']==$this->datos_jefe['id_depto'] && !$this->detalle_nota['autorizado_depto']){ ?>
				<input value="<?php echo $p[0] ?>" type='text' id='proveedor_escogido' name='proveedor_escogido' autocomplete="off" size='40' onkeypress="cargar_proveedor(this.value)" placeholder="Nombre proveedor">
				<div id='proveedor'></div>
				<input value="<?php echo $p[1] ?>" type="text" name="rut_proveedor" id="rut_proveedor" placeholder="Rut">
				<input value="<?php echo $p[2] ?>" type="text" name="giro_proveedor" id="giro_proveedor" placeholder="Giro">
			<?php }else{ ?>
				<p><?php echo $p[0] ?></p>
				<p><?php echo $p[1] ?></p>
				<p><?php echo $p[2] ?></p>
			<?php } ?>
				
			</div>
			<div class="col-3" id='rut_texto'></div>
			
		</div>
	</div>
<?php } ?>

<div class='fila_vacia'></div>
<div class='centrar'>
	<table class='tabla_listado'>
		<tr>
			<th width='15%'>Cantidad pedida</th>
			<th width='30%'>Item</th>
			<th width='30%'>Motivo</th>
		<?php if (!$this->detalle_nota['autorizado_depto']){ ?>
			<th width='10%'>Tipo de modificación</th>
		<?php } ?>
			<th width='15%'>Adjunto</th>
		</tr>
	<?php 
	$j=0;
	foreach ($this->items as $i){ 
		$j++;
		?>
		<input type="hidden" id="id_oculto<?php echo $j ?>" value="<?php echo $i['id'] ?>">
		<tr>
			<td align='center'>
			<?php
				if ($this->detalle_nota['autorizado_depto']) 
					echo $i['cantidad']; 
				else{ ?>
					<input type='hidden' id='cantidad_oculto<?php echo $j ?>' value='<?php echo $i['cantidad'] ?>'>
					<input type='number' size='3' autocomplete="off" id='cantidad<?php echo $j ?>' value='<?php echo $i['cantidad'] ?>'>
			<?php }
			?>
			</td>
			<td>
			<?php 
				if ($this->detalle_nota['autorizado_depto'])
					echo $i['item'];
				else{ ?>
				<input type='text' autocomplete="off" id='nueva_descripcion<?php echo $j ?>' value='<?php echo $i['item'] ?>'>
			<?php }
			?>
			</td>
			<td>
			<?php 
				if ($this->detalle_nota['autorizado_depto'])
					echo $i['motivo'];
				else{ ?>
				<input type='text' autocomplete="off" id='nuevo_motivo<?php echo $j ?>' value='<?php echo $i['motivo'] ?>'>
			<?php }
			?>
			</td>
			<?php if (!$this->detalle_nota['autorizado_depto']){ ?>
			<td>
				<select id='tipo_modificacion<?php echo $j ?>' name='tipo_modificacion<?php echo $j ?>'>
					<option value='1'>Reducción por existencia</option>
					<option value='2'>Denegación</option>
				</select>
			</td>
			<?php } ?>
			<td align='center'><?php if ($i['adjunto']){ ?>
					<a href="/portal/media/notas_pedido/adjuntos/<?php echo $this->detalle_nota['id_remitente'].'/'.$i['adjunto'] ?>" 
						class="modal">
						<img src='/portal/administrator/templates/hathor/images/menu/icon-16-archive.png' />
					</a>
				<?php } ?>
			</td>
		</tr>
	<?php } ?>
	</table>
</div>
<div class='fila_vacia'></div>
<div class='centrar'>
<?php 

if ($user->authorise('jefe.depto', 'com_nota')){
	if ($this->detalle_nota['id_adepto']==$this->datos_jefe['id_depto'] && !$this->detalle_nota['autorizado_depto']){ ?>
		<div id="enviado" class="barra_nombre" style="display: none;">Aprobado</div>
		<div id="conjunto_botones">
			<div class='boton' onclick="guardar_cambios_items(<?php echo $j ?>, 1, 1, 1)">
				<img src='/portal/administrator/templates/hathor/images/header/icon-48-save.png' /><br>
				Guardar cambios
			</div>
			<div id="boton_anulacion" onclick="dialogo_anulacion()" class='boton'><img src='/portal/administrator/templates/hathor/images/header/icon-48-deny.png' /><br>Anular</div>
			<div id="dialogo_anulacion" class="barra_nombre" style="display: none;">
				Comentario <input type="text" id="comentario" autocomplete="off">
				<a onclick="anular_nota_depto(<?php echo $this->id_remitente ?>)"><img src='/portal/administrator/templates/hathor/images/menu/icon-16-save.png' /></a>
			</div>
		</div>
<?php }
}
if ($this->id_user==$this->detalle_nota['id_user'] && $this->detalle_nota['aprobado_adquisiciones']==0){ ?>
	<div id="boton_anulacion" onclick="dialogo_anulacion()" class='boton'><img src='/portal/administrator/templates/hathor/images/header/icon-48-deny.png' /><br>Anular</div>
	<div id="dialogo_anulacion" class="barra_nombre" style="display: none;">
		Comentario <input type="text" id="comentario" autocomplete="off">
		<a onclick="anular_nota(<?php echo $this->id_remitente ?>)"><img src='/portal/administrator/templates/hathor/images/menu/icon-16-save.png' /></a>
	</div>
<?php }

if ($this->id_user==$this->detalle_nota['id_user'] && $this->datos_nota['aprobado_adquisiciones']){ ?>
	<div style="width: 50%;">
	<table>
		<tr><td style="padding: 8px; background-color: #F0B27A; color: white;">Ítems no completados</td></tr>
	</table>
		<div class='fila_completa centrar'><h2>Calificación</h2></div>
		<table class="tabla_listado">
		<tr>
			<th>Ítem</th>
			<th width="25%">Cantidad aprobada</th>
			<th width="25%">Faltante</th>
		</tr>
		<?php $num_items=0; ?>
		<?php foreach ($this->items as $i){ 
			$num_items++; 
			?>
		<tr>
			<td <?php echo sizeof($i['modificacion']) ? 'style="padding: 8px; background-color: #F0B27A; color: white;"' : '' ?>> 
				<?php echo $i['item'] ?>
			</td>
			<td <?php echo sizeof($i['modificacion']) ? 'style="padding: 8px; background-color: #F0B27A; color: white;"' : '' ?>>
				<?php echo ($i['nueva_cantidad'] && $i['id_tipoModificacion']==1) ? $i['nueva_cantidad'] : $i['cantidad'] ?>
				<input type="hidden" id="cantidad_autorizado<?php echo $num_items ?>" value="<?php echo ($i['nueva_cantidad'] && $i['id_tipoModificacion']==1) ? $i['nueva_cantidad'] : $i['cantidad'] ?>">
			</td>
			<td <?php echo sizeof($i['modificacion']) ? 'style="padding: 8px; background-color: #F0B27A; color: white;"' : '' ?> align='center'>
				<input type="hidden" value="<?php echo $i['id'] ?>" id='id_item<?php echo $num_items ?>'>
				<?php if (($this->detalle_nota['aprobado']==2 && sizeof($i['modificacion'])) || !$this->detalle_nota['aprobado']){ ?>
					<input type="number" style="width: 55%" required type="number" min="0" step=".1" id='cantidad_faltante<?php echo $num_items ?>' value="0"> 
				<?php }else{ ?>
					<input type='hidden' id='cantidad_faltante<?php echo $num_items ?>' value='0'>0
				<?php } ?>
				
			</td>
		</tr>
		<?php } ?>
		<tr>
			<td><b>Recibí confome</b></td>
			<td colspan='2'>
				<input type="radio" name="alternativa" id='si' value='si' onclick="recibio(1)" checked><label for='si'>Si</label><br>
				<input type="radio" name="alternativa" id='no' value='no' onclick="recibio(0)"><label for='no'>No</label>
				<input type='hidden' id='recibio'>
			</td>
		</tr>
		<tr>
			<td><b>Comentario</b></td>
			<td colspan='2'><input type="text" id="comentario"></td>
		</tr>
		<tr>
			<td colspan='3' align='center'>
				<a onclick="guardar_calificacion(<?php echo $num_items; ?>)"><img src='/portal/administrator/templates/hathor/images/header/icon-48-save.png' /></a>
			</td>
		</tr>
		<div class='fila_completa centrar'></div>
		</table>
	</div>
<?php } ?>

<?php if (($user->authorise('jefe.delgada','com_nota') || $user->authorise('jefe.natales', 'com_nota')) && !$user->authorise('core.admin', 'com_nota') && $this->detalle_nota['autorizado_jefe']==0){ ?>
<div id='boton_guardar'>
	<a onclick="aprobar_naves(<?php echo $this->id_remitente ?>, <?php echo $j ?>)">
		<div class='boton' style="height: auto;"><img src='/portal/administrator/templates/hathor/images/header/icon-48-save.png' /><br>Autorizar nota</div>
	</a>
</div>
<?php } ?>
</div>

