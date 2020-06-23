<?php 
defined('_JEXEC') or die('Restricted access');
$user = JFactory::getUser();

?>
<h3>Página <?php echo $this->pagina ?></h3>
<table class='tabla_listado'>
	<tr>
		<th width='5%'>#</th>
		<th width='5%'>Nº</th>
		<th width='20%'>Fecha</th>
		<th width='60%'>Estado de avance</th>
		<th width='10%'>Revisión</th>
	</tr>
<?php 
$i=1;
foreach ($this->notas_depto as $nd){ ?>
	<tr>
		<td><?php echo $i++ ?></td>
		<td><?php echo $nd['id'] ?></td>
		<td><?php echo $nd['fecha'] ?></td>
		<td>
			<div class='centrar'>
				<div class='barra_avance <?php echo ($nd['empleado']==1 ? "paso_aprobado" : "").($nd['empleado']==2 ? "paso_rechazado" : "") ?>'>Enviado empleado</div>
				<?php if ($user->authorise('tripulante', 'com_nota')){ ?>
					<div class='barra_avance <?php echo ($nd['capitan']==1 ? "paso_aprobado" : "").($nd['capitan']==2 ? "paso_rechazado" : "") ?>'>Autorizado capitán</div>
				<?php } ?>
				<div class='barra_avance <?php echo ($nd['jefe']==1 ? "paso_aprobado" : "").($nd['jefe']==2 ? "paso_rechazado" : "") ?>'>Autorizado jefe</div>
				<div class='barra_avance <?php echo ($nd['depto']==1 ? "paso_aprobado" : "").($nd['depto']==2 ? "paso_rechazado" : "") ?>'>Autorizado depto.</div>
				<div class='barra_avance <?php echo ($nd['adquisiciones']==1 ? "paso_aprobado" : "").($nd['adquisiciones']==2 ? "paso_rechazado" : "") ?>'>OC generada</div>
				<div class='barra_avance <?php echo ($nd['aprobado']==1 ? "paso_aprobado" : "").($nd['adquisiciones']==2 ? "paso_rechazado" : "") ?>'>Calificación</div>
			</div>
		</td>
		<td>
		<?php 
			if ($nd['empleado']!=2 && $nd['capitan']!=2 && $nd['jefe']!=2 && $nd['depto']!=2 && $nd['adquisiciones']!=2){ ?>
					<a onclick="SqueezeBox.fromElement(this, 
								{handler:'iframe', 
								size: {x: 900, y: 550}, 
								url:'<?php echo JRoute::_('index.php?option=com_nota&view=com_nota&task=detalle_nota&id_nota='.$nd['id'].'&tmpl=component'); ?>',
								onClose:function(){//window.location.reload();
								} })">
					<img src='/portal/administrator/templates/hathor/images/menu/icon-16-edit.png' /></a>
				<?php }else{ ?>
					<a onclick="SqueezeBox.fromElement(this, 
								{handler:'iframe', 
								size: {x: 900, y: 550}, 
								url:'<?php echo JRoute::_('index.php?option=com_nota&view=com_nota&task=reportes.detalle_nota&id_nota='.$nd['id'].'&tmpl=component'); ?>',
								onClose:function(){//window.location.reload();
								} })">
					<img src='/portal/administrator/templates/hathor/images/menu/icon-16-edit.png' /></a>
			<?php } ?>
		</td>
	</tr>
<?php } ?>
</table>