<?php 
defined('_JEXEC') or die('Restricted access');
$user = JFactory::getUser();
?>
<?php if ($this->pagina){
	echo "<h3>Página ".$this->pagina."</h3>";
} ?>

<table class='tabla_listado'>
	<tr>
		<th width='5%'>#</th>
		<th width='5%'>Nº</th>
		<th width='8%'>Fecha</th>
		<th width='12%'>Usuario</th>
		<th width='60%'>Estado de avance</th>
		<th width='10%'>Revisión</th>
	</tr>
<?php 
$i=1;
foreach ($this->notas_area as $nd){ ?>
	<tr>
		<td><?php echo $i++ ?></td>
		<td><?php echo $nd['id'] ?></td>
		<td><?php echo NotaHelper::fechamysql($nd['fecha'],2) ?></td>
		<td><?php echo $nd['usuario'] ?></td>
		<td>
			<div class='centrar'>
				<div class='barra_avance <?php echo ($nd['empleado']==1 ? "paso_aprobado" : "").($nd['empleado']==2 ? "paso_rechazado" : "") ?>'>Enviado empleado</div>
				<?php if ($user->authorise('tripulante', 'com_nota')){ ?>
					<div class='barra_avance <?php echo ($nd['capitan']==1 ? "paso_aprobado" : "").($nd['capitan']==2 ? "paso_rechazado" : "") ?>'>Autorizado capitán</div>
				<?php } ?>
				<div class='barra_avance <?php echo ($nd['jefe']==1 ? "paso_aprobado" : "").($nd['jefe']==2 ? "paso_rechazado" : "") ?>'>Autorizado jefe</div>
				<div class='barra_avance <?php echo ($nd['depto']==1 ? "paso_aprobado" : "").($nd['depto']==2 ? "paso_rechazado" : "") ?>'>Autorizado depto</div>
				<div class='barra_avance <?php echo ($nd['adquisiciones']==1 ? "paso_aprobado" : "").($nd['adquisiciones']==2 ? "paso_rechazado" : "") ?>'>OC generada</div>
				<div class='barra_avance <?php echo ($nd['aprobado']==1 ? "paso_aprobado" : "").($nd['adquisiciones']==2 ? "paso_rechazado" : "") ?>'>Calificación</div>
			</div>
		</td>
		<td>
			<a onclick="SqueezeBox.fromElement(this, 
								{handler:'iframe', 
								size: {x: 900, y: 550}, 
								url:'<?php echo JRoute::_('index.php?option=com_nota&view=com_nota&task=reportes.detalle_nota&id_nota='.$nd['id'].'&tmpl=component'); ?>',
								})">
				<img src='/portal/administrator/templates/hathor/images/menu/icon-16-edit.png' /></a>
		</td>
	</tr>
<?php } ?>
</table>