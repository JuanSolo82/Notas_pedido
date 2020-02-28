<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
require_once(JPATH_COMPONENT_SITE.'/assets/helper.php');
JHTML::stylesheet('nota.css', 'components/com_nota/assets/css/');
JHTML::script('jquery.min.js', 'components/com_nota/assets/js/');
JHTML::script('jquery-ui.min.js', 'components/com_nota/assets/js/');
JHTML::script('nota.js', 'components/com_nota/assets/js/');
JHtml::_('behavior.modal');
$f = explode('-', $this->detalle_nota['fecha']);
?>
<input type="hidden" id="id_remitente" value="<?php echo $this->id_remitente ?>">
<br>
<div class='fila_completa centrar' style='margin-bottom: 20px;'>
	<div class='barra_nombre' style='width: 90%;'>Detalle de orden de compra <?php echo $this->detalle_nota['orden_compra'] ?></div>
</div>

<div class='centrar'>
	<div class='fila_completa bordear centrar' style='width: 90%;'></div>
</div>

<div class='centrar'>
	<div class='fila_completa bordear centrar' style='width: 90%;'>
		<div class="col-3 titulo_item">Nombre remitente</div>
		<div class="col-7"><?php echo $this->detalle_nota['nombre_remitente'] ? $this->detalle_nota['nombre_remitente'].'.' : $this->detalle_nota['nombre_usuario']; ?></div>
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
		<div class="col-3 titulo_item">Prioridad</div>
		<div class="col-7"><?php echo $this->detalle_nota['prioridad']; ?></div>
	</div>
</div>
<?php if ($this->orden_compra){ ?>
<div class='centrar'>
	<div class='fila_completa bordear centrar' style='width: 90%;'>
		<div class="col-3 titulo_item">Proveedor</div>
		<div class="col-7">
			<input type='text' style="float: left;" id='proveedor' autocomplete="off" value="<?php echo $this->detalle_nota['proveedor_oc'] ? $this->detalle_nota['proveedor_oc'] : $this->detalle_nota['proveedor'] ?>">
			<div style="float: left; padding: 4px; margin-left: 5px;" id='actualiza_proveedor'><a onclick="actualiza_proveedor(<?php echo $this->detalle_nota['orden_compra'] ?>)"><img src='/portal/administrator/templates/hathor/images/menu/icon-16-save.png' /></a></div>
		</div>
	</div>
</div>
<div class='centrar'>
	<div class='fila_completa bordear centrar' style='width: 90%;'>
		<div class="col-3 titulo_item">Facturas</div>
		<div class="col-7">
			<input type='text' style="float: left;" id='factura' autocomplete="off" value="<?php echo $this->detalle_nota['factura'] ?>">
			<div style="float: left; padding: 4px; margin-left: 5px;" id='actualiza_factura'><a onclick="actualiza_factura(<?php echo $this->detalle_nota['orden_compra'] ?>)"><img src='/portal/administrator/templates/hathor/images/menu/icon-16-save.png' /></a></div>
		</div>
	</div>
</div>
<?php } ?>

<div class='fila_vacia'></div>
<div class='centrar' style="margin-bottom: 25px;">
	<table class='tabla_listado'>
		<tr>
			<th width='15%'>Cantidad pedida</th>
			<th width='40%'>Item</th>
			<th width='30%'>Motivo</th>
			<th width='15%'>Adjunto</th>
		</tr>
	<?php foreach ($this->items as $i){ ?>
		<tr>
			<td align='center'><?php echo $i['cantidad'] ?></td>
			<td><?php echo $i['item'] ?></td>
			<td><?php echo $i['motivo'] ?></td>
			<td align='center'>
				<?php if ($i['adjunto']){ ?>
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
