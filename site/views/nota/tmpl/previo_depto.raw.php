<?php 
defined('_JEXEC') or die('Restricted access');
$user = JFactory::getUser();
?>
<h3>Página <?php echo $this->pagina ?></h3>
<table class='tabla_listado'>
    <tr>
        <th width='5%'>#</th>
        <th width='5%'>Nº</th>
		<?php if ($user->authorise('jefe.delgada', 'com_nota') || $user->authorise('jefe.natales', 'com_nota')){ ?>
			<th width='10%'>Origen</th>
		<?php } ?>
        <th width='20%'>Fecha</th>
        <th width='60%'>Estado de avance</th>
        <th width='10%'>Revisión</th>
    </tr>
	<?php 
		$i = ($this->pagina-1)*10+1;
		foreach ($this->notas as $n){ ?>
		<tr>
			<td><?php echo $i; ?></td>
			<td align='center'><?php echo $n['id_remitente'] ?></td>
			<?php if ($user->authorise('jefe.delgada', 'com_nota') || $user->authorise('jefe.natales', 'com_nota')){ ?>
				<td><?php echo $n['depto_origen'] ?></td>
			<?php } ?>
			<td><?php echo NotaHelper::fechamysql($n['fecha']) ?></td>
			<td>
				<div class='centrar'>
					<div class='barra_avance <?php echo ($n['empleado']==1 ? "paso_aprobado" : "").($n['empleado']==2 ? "paso_rechazado" : "") ?>'>Enviado empleado</div>
					<?php if ($user->authorise('tripulante', 'com_nota') || $user->authorise('jefe.delgada', 'com_nota') || $user->authorise('jefe.natales', 'com_nota') || $user->authorise('capitan.jefe', 'com_nota') || $user->authorise('capitan.sin_jefe', 'com_nota')){ ?>
						<div class='barra_avance <?php echo ($n['capitan']==1 ? "paso_aprobado" : "").($n['capitan']==2 ? "paso_rechazado" : "") ?>'>Autorizado capitán</div>
					<?php } ?>
					<div class='barra_avance <?php echo ($n['jefe']==1 ? "paso_aprobado" : "").($n['jefe']==2 ? "paso_rechazado" : "") ?>'>Autorizado jefe</div>
					<div class='barra_avance <?php echo ($n['depto']==1 ? "paso_aprobado" : "").($n['depto']==2 ? "paso_rechazado" : "") ?>'>Autorizado depto.</div>
					<div class='barra_avance <?php echo ($n['adquisiciones']==1 ? "paso_aprobado" : "").($n['adquisiciones']==2 ? "paso_rechazado" : "") ?>'>OC generada</div>
				</div>
			</td>
			<td align='center'>
			<?php 
			$url = "";
			if (($user->authorise('jefe.delgada', 'com_nota') && $n['jefe']!=0) || 
				($user->authorise('adquisiciones.jefe', 'com_nota') && $n['adquisiciones']) || 
				($user->authorise('capitan.jefe', 'com_nota') && $n['capitan']!=0)){ 
				$url = JRoute::_('index.php?option=com_nota&view=reportes&task=reportes.detalle_nota&id_nota='.$n['id_remitente'].'&tmpl=component');
			} elseif (($n['jefe']==0 && ($user->authorise('jefe.depto', 'com_nota') && !$n['capitan'])) || 
						(($user->authorise('capitan.jefe', 'com_nota') || $user->authorise('capitan.sin_jefe', 'com_nota')) && $n['capitan']==0) && 
						$n['empleado']!=2 || $user->authorise('jefe.delgada', 'com_nota')){ 
				$url = JRoute::_('index.php?option=com_nota&view=com_nota&task=detalle_notajefe&id_nota='.$n['id_remitente'].'&tmpl=component');
			}else{
				$url = JRoute::_('index.php?option=com_nota&view=com_nota&task=reportes.detalle_nota&id_nota='.$n['id_remitente'].'&tmpl=component');
			}
			if ($url!=""){ ?>
				<a onclick="SqueezeBox.fromElement(this, 
								{handler:'iframe', 
								size: {x: 900, y: 450}, 
								url:'<?php echo $url; ?>',
								onClose:function(){var js =window.location.reload();} })">
				<img src='/portal/administrator/templates/hathor/images/menu/icon-16-edit.png' /></a>
			<?php } ?>
			</td>
		</tr>
	<?php $i++; } ?>
</table>
