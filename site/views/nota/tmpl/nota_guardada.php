<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
//JHTML::stylesheet('bootstrap.css', 'components/com_nota/assets/bootstrap/css/');
JHTML::stylesheet('nota.css', 'components/com_nota/assets/css/');
JHTML::script('jquery.min.js', 'components/com_nota/assets/js/');
JHTML::script('jquery-ui.min.js', 'components/com_nota/assets/js/');
JHTML::script('nota.js', 'components/com_nota/assets/js/');
JHtml::_('behavior.modal'); 
//JHTML::script('bootstrap.js', 'components/com_nota/assets/bootstrap/js/');
$user = JFactory::getUser();
?>
<div class='fila_completa centrar' style='margin-bottom: 20px;'>
	<div class='barra_nombre' style='width: 90%;'>Nota enviada con éxito</div>
</div>

<div class='centrar'>
	<div class='fila_completa bordear centrar' style='width: 90%;'></div>
</div>

<div class='fila_completa bordear centrar' style='width: 90%;'>
	<div class="col-3 titulo_item">Número de nota</div>
	<div class="col-7"><?php echo $this->datos_nota['id_remitente'] ?></div>
</div>

<div class='fila_completa bordear centrar' style='width: 90%;'>
	<div class="col-3 titulo_item">Centro de costo</div>
	<div class="col-7"><?php echo $this->datos_nota['depto_costo'] ?></div>
</div>
<div class='fila_completa bordear centrar' style='width: 90%;'>
	<div class="col-3 titulo_item">Departamento destino</div>
	<div class="col-7"><?php echo $this->datos_nota['depto_destino'] ?></div>
</div>
<?php if ($this->proveedor){ ?>
<div class='fila_completa bordear centrar' style='width: 90%;'>
	<div class="col-3 titulo_item">Proveedor</div>
	<div class="col-7"><?php echo $this->proveedor ?></div>
</div>
<?php } ?>
<div class='fila_vacia'></div>
<table class='adminlist'>
	<tr class='encabezado_tabla'>
		<th width='10%'>Cantidad</th>
		<th width='40%'>Descripción</th>
		<th width='25%'>Motivo</th>
		<th width='25%'>Adjunto</th>
	</tr>
<?php foreach ($this->items_nota as $i){ ?>
	<tr>
		<td><?php echo $i['cantidad'] ?></td>
		<td><?php echo $i['item'] ?></td>
		<td><?php echo $i['motivo'] ?></td>
		<td>
		<?php if ($i['adjunto']){ ?>
			<a href="/portal/media/notas_pedido/adjuntos/<?php echo $this->datos_nota['id_remitente'].'/'.$i['adjunto'] ?>" 
				class="modal">
				<img src='/portal/administrator/templates/hathor/images/menu/icon-16-archive.png' />
			</a>
		<?php } ?>
		</td>
	</tr>
<?php } ?>
</table>
<div class='fila_vacia'></div>
<div class='fila_vacia'></div>
<div class='centrar'>
	<a href='<?php echo JRoute::_('index.php?option=com_nota'); ?>'>
	<div class='boton' id='volver'>
		<img src="/portal/administrator/templates/hathor/images/header/icon-48-revert.png" /><br>
		Volver
	</div>
	</a>
	
	<?php if ($user->authorise('adquisiciones.jefe', 'com_nota') && !$user->authorise('empleado.depto', 'com_nota')){ ?>
		<a href="<?php echo JRoute::_('index.php?option=com_nota&view=adquisiciones&task=adquisiciones.lista_notas'); ?>">
			<div class="centrar">
				<div class='boton'><img src='/portal/administrator/templates/hathor/images/header/icon-48-article.png' /><br>Obtener OC</div>
			</div>
		</a>
	<?php } ?>
</div>
