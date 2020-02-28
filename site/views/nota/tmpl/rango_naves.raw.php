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
		$i = ($this->pagina-1)*10+1;
		foreach ($this->notas_naves as $nd){ ?>
		<tr>
			<td><?php echo $i; ?></td>
			<td align='center'><?php echo $nd['id'] ?></td>
			<td><?php echo NotaHelper::fechamysql($nd['fecha']) ?></td>
			<td>
				<div class='centrar'>
					<div class='barra_avance <?php echo ($nd['empleado']==1 ? "paso_aprobado" : "").($nd['empleado']==2 ? "paso_rechazado" : "") ?>'>Enviado empleado</div>
					<div class='barra_avance <?php echo ($nd['capitan']==1 ? "paso_aprobado" : "").($nd['capitan']==2 ? "paso_rechazado" : "") ?>'>Autorizado capitán</div>
					<div class='barra_avance <?php echo ($nd['jefe']==1 ? "paso_aprobado" : "").($nd['jefe']==2 ? "paso_rechazado" : "") ?>'>Autorizado jefe</div>
					<div class='barra_avance <?php echo ($nd['depto']==1 ? "paso_aprobado" : "").($nd['depto']==2 ? "paso_rechazado" : "") ?>'>Autorizado depto.</div>
					<div class='barra_avance <?php echo ($nd['adquisiciones']==1 ? "paso_aprobado" : "").($nd['adquisiciones']==2 ? "paso_rechazado" : "") ?>'>OC generada</div>
					<div class='barra_avance <?php echo ($nd['aprobado']==1 ? "paso_aprobado" : "").($nd['adquisiciones']==2 ? "paso_rechazado" : "") ?>'>Calificación</div>
				</div>
			</td>
			<td align='center'>
			<?php 
				if ($nd['empleado']==1 && $nd['capitan']==1 && $nd['jefe']==0 && $nd['depto']==0 && $nd['adquisiciones']==0){ ?>
					<a onclick="SqueezeBox.fromElement(this, 
								{handler:'iframe', 
								size: {x: 900, y: 550}, 
								url:'<?php echo JRoute::_('index.php?option=com_nota&view=com_nota&task=detalle_nota&id_nota='.$nd['id'].'&tmpl=component'); ?>',
								onClose:function(){window.location.reload();} })">
					<img src='/portal/administrator/templates/hathor/images/menu/icon-16-edit.png' /></a>
				<?php }else{ ?>
					<a onclick="SqueezeBox.fromElement(this, 
								{handler:'iframe', 
								size: {x: 900, y: 550}, 
								url:'<?php echo JRoute::_('index.php?option=com_nota&view=com_nota&task=reportes.detalle_nota&id_nota='.$nd['id'].'&tmpl=component'); ?>',
								onClose:function(){window.location.reload();} })">
					<img src='/portal/administrator/templates/hathor/images/menu/icon-16-article.png' /></a>
			<?php } ?>
			</td>
		</tr>
	<?php $i++; } ?>
</table>
