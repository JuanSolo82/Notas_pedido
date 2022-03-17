<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHTML::stylesheet('nota.css', 'components/com_nota/assets/css/');
JHTML::stylesheet('jquery-ui.css', 'components/com_nota/assets/css/');
JHTML::script('jquery.min.js', 'components/com_nota/assets/js/');
JHTML::script('jquery-ui.min.js', 'components/com_nota/assets/js/');
JHTML::script('nota.js', 'components/com_nota/assets/js/');
JHtml::_('behavior.modal'); 
require_once(JPATH_COMPONENT_SITE.'/assets/helper.php');
$j=1;
$user = JFactory::getUser();
$f = explode('-', $this->detalle_nota['fecha']);
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
		<div class="col-3 titulo_item"><?php echo ($user->authorise('tripulante', 'com_nota') && !$user->authorise('core.admin', 'com_nota')) ? 'Nave' : 'Departamento origen' ?></div>
		<div class="col-7"><?php echo $this->detalle_nota['depto_origen']; ?></div>
	</div>
</div>
<div class='centrar'>
	<div class='fila_completa bordear centrar' style='width: 90%;'>
		<div class="col-3 titulo_item">Centro de costo</div>
		<div class="col-7"><?php echo $this->detalle_nota['depto_costo']; ?></div>
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
<?php if (NotaHelper::isTestSite()){ 
	$p = explode('_', $this->detalle_nota['proveedor']);
	?>
	<div class='centrar'>
		<div class='fila_completa bordear' style='width: 90%;'>
			<div class="col-3 titulo_item">Proveedor (opcional)</div>
			<div class="col-4" id="datos_proveedor">
				<input value="<?php echo $p[0] ?>" type='text' id='proveedor_escogido' name='proveedor_escogido' autocomplete="off" size='40' onkeypress="cargar_proveedor(this.value)" placeholder="Nombre proveedor">
				<div id='proveedor'></div>
				<input value="<?php echo $p[1] ?>" type="text" name="rut_proveedor" id="rut_proveedor" placeholder="Rut">
				<input value="<?php echo $p[2] ?>" type="text" name="giro_proveedor" id="giro_proveedor" placeholder="Giro">
			</div>
			<div class="col-3" id='rut_texto'></div>
			
		</div>
	</div>
<?php } ?>
<div class='fila_vacia'></div>
<form id='formItems' name="formItems" action="<?php echo JRoute::_('index.php?option=com_nota&view=com_nota&task=editar_nota'); ?>" method='post' autocomplete='off'>
<input type="hidden" value="<?php echo $this->datos_user['generico']; ?>" id="generico" name="generico">
<div class='centrar'>
	<table class='tabla_listado' id='contenido_editable'>
		<tr>
			<th width='8%'>Cantidad</th>
			<th width='30%'>Item</th>
			<th width='<?php echo NotaHelper::isTestSite() ? 10 : 30 ?>%'>
				<?php echo NotaHelper::isTestSite() ? 'Valor unitario' : 'Motivo' ?>
			</th>
		<?php if (NotaHelper::isTestSite()){ ?>
			<th>Subtotal</th>
		<?php } ?>
			<th width='10%'>Tipo de modificación</th>
			<th width='10%'>Adjunto</th>
		</tr>
	<?php 
	$total = 0;
	foreach ($this->items as $i){ 
		$total += $i['cantidad']*$i['valor'];
		?>
		<input type="hidden" id="id_oculto<?php echo $j ?>" value="<?php echo $i['id'] ?>">
		<input type="hidden" id="cantidad_oculto<?php echo $j ?>" value="<?php echo $i['cantidad'] ?>">
		<input type="hidden" id="descripcion_oculto<?php echo $j ?>" value="<?php echo $i['item'] ?>">
		<input type="hidden" id="motivo_oculto<?php echo $j ?>" value="<?php echo $i['motivo'] ?>">
		<input type="hidden" id="valor_numerico<?php echo $i['id'] ?>" value="<?php echo $i['valor'] ?>">
		<input type="hidden" size='3' id="subtotal_numerico<?php echo $i['id'] ?>" value="<?php echo $i['valor']*$i['cantidad'] ?>">
		<tr>
			<td align='center' id="columna_cantidad<?php echo $i['id'] ?>">
				<input id='cantidad<?php echo $i['id'] ?>' onchange="actualiza_parcial(<?php echo $i['id'] ?>)" value="<?php echo $i['cantidad'] ?>" type='number' required type="number" min="0" step=".1" style='width: 70px;'>
			</td>
			<td id="columna_descripcion<?php echo $i['id'] ?>">
				<input type="text" id="nueva_descripcion<?php echo $j ?>" value="<?php echo $i['item'] ?>" style="width: 80%;">
			</td>
			<td id="columna_parcial<?php echo $i['id'] ?>">
			<?php if (NotaHelper::isTestSite()){ ?>
				<input type="number" onchange="actualiza_parcial(<?php echo $i['id'] ?>)" style="width: 90px;" id="valor_unitario<?php echo $i['id'] ?>" value="<?php echo $i['valor'] ? $i['valor'] : '' ?>">
			<?php }else{ ?>
				<input type="text" id="nuevo_motivo<?php echo $j ?>" value="<?php echo $i['motivo'] ?>" style="width: 80%;">
			<?php } ?>
			</td> 
		<?php if (NotaHelper::isTestSite()){ ?>
			<td id="parcial_texto<?php echo $i['id'] ?>"><?php echo $i['valor'] ? number_format($i['cantidad']*$i['valor'],0,'','.') : '' ?></td>
		<?php } ?>
			<td>
				<select id='tipo_modificacion<?php echo $j ?>' name='tipo_modificacion<?php echo $j ?>'>
					<option value='1'>Reducción por existencia</option>
					<option value='2'>Denegación</option>
				</select>
			</td>
			<td align='center'>
			<?php if ($i['adjunto']){ ?>
				<a href="/portal/media/notas_pedido/adjuntos/<?php echo $this->id_remitente ?>/<?php echo $i['adjunto'] ?>" 
					class="modal">
					<img src='/portal/administrator/templates/hathor/images/menu/icon-16-archive.png' />
				</a>
			<?php } ?>
				</td>
		</tr>
	<?php $j++; 
		} ?>

	</table>
</div>
<div class="centrar" id="aviso_nota_autorizada" style="display: none;">
	<div class="contenido">Nota autorizada</div>
</div>
</form>
<div class='fila_vacia'></div>
<div class='centrar' id='botones'>
<?php 

	if ($this->detalle_nota['id_adepto']==$this->datos_propios['id_depto']){
		if ($this->detalle_nota['autorizado_depto']==0){ ?>
			<div onclick="guardar_cambios_items(<?php echo sizeof($this->items) ?>, 
													<?php echo ($user->authorise('capitan.jefe', 'com_nota') || $user->authorise('capitan.sin_jefe', 'com_nota')) ? 1 : 0 ?>, 
													<?php echo ($user->authorise('jefe.depto', 'com_nota')) ? 1 : 0 ?>, 
													<?php echo $this->detalle_nota['id_adepto']==$this->datos_propios['id_depto'] ? 1 : 0 ?>)" 
					class='boton'>
						<img src='/portal/administrator/templates/hathor/images/header/icon-48-save.png' />
						<br>Autorizar nota
			</div>
			<div id="boton_anulacion" onclick="dialogo_anulacion()" class='boton'><img src='/portal/administrator/templates/hathor/images/header/icon-48-deny.png' /><br>Anular</div>
			<div id="dialogo_anulacion" class="barra_nombre" style="display: none;">
				Comentario <input type="text" id="comentario" autocomplete="off">
				<a onclick="anular_nota(<?php echo $this->id_remitente ?>)"><img src='/portal/administrator/templates/hathor/images/menu/icon-16-save.png' /></a>
			</div>
		<?php }
	}else
if (($this->datos_user['id']==$this->detalle_nota['id_user'] && $this->detalle_nota['aprobado_adquisiciones']==0) || $this->datos_user['id']==229){ 
	?>
	<div onclick="guardar_cambios_items(<?php echo sizeof($this->items) ?>, 
										<?php echo ($user->authorise('capitan.jefe', 'com_nota') || $user->authorise('capitan.sin_jefe', 'com_nota')) ? 1 : 0 ?>, 
										<?php echo ($user->authorise('jefe.depto', 'com_nota')) ? 1 : 0 ?>, 
										<?php echo $this->detalle_nota['id_adepto']==$this->datos_propios['id_depto'] ? 1 : 0 ?>)" 
		class='boton'>
			<img src='/portal/administrator/templates/hathor/images/header/icon-48-save.png' />
			<br>Autorizar nota
	</div>

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