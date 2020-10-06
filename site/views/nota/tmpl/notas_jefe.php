<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHTML::stylesheet('nota.css', 'components/com_nota/assets/css/');
JHTML::script('jquery.min.js', 'components/com_nota/assets/js/');
JHTML::script('jquery-ui.min.js', 'components/com_nota/assets/js/');
JHTML::script('nota.js', 'components/com_nota/assets/js/');
JHtml::_('behavior.modal'); 
//JHTML::script('bootstrap.js', 'components/com_nota/assets/bootstrap/js/');
$i=1;
$user = JFactory::getUser();
?>
<div class='fila_completa centrar' style='margin-bottom: 20px;'>
	<div class='barra_nombre' style='width: 90%;'>Notas del departamento [en desarrollo]</div>
</div>
<table>
	<tr style="border:hidden;">
		<td><div class='barra_avance'>Etapa no tramitada</div></td>
	</tr>
	<tr style="border:hidden;">
		<td><div class='barra_avance paso_aprobado'>Etapa tramitada</div></td>
	</tr>
	<tr style="border:hidden;">
		<td><div class='barra_avance paso_rechazado'>Etapa rechazada</div></td>
	</tr>
</table>
<br>
<div style="height: 60px; width: 100%;">
<div class="prev_next" id="anterior" onclick="previo_depto(2)">
	<img src='/portal/components/com_nota/assets/img/previous.png' /> Anterior
</div>
<div class="prev_next" id="siguiente" onclick="previo_depto(1)">
	<img src='/portal/components/com_nota/assets/img/next.png' /> Siguiente
</div>
</div>
<input type="hidden" id="pagina" value="1" size='5'>
<div class="fila_vacia"></div>
<div id='notas_depto'>
<h3>Página 1</h3>
	<table class='tabla_listado'>
		<tr>
			<th width='5%'>#</th>
			<th width='5%'>Nº</th>
		<?php if ($user->authorise('jefe.delgada', 'com_nota') || $user->authorise('jefe.natales', 'com_nota')){ ?>
			<th width='10%'>Origen</th>
		<?php } ?>
			<th width='10%'>Fecha</th>
			<th width='63%'>Estado de avance</th>
			<th width='6%'>Revisión</th>
		</tr>
	<?php foreach ($this->notas_jefe as $j){ 
			$f = explode("-", $j['fecha']);
	?>
		<tr>
			<td><?php echo $i ?></td>
			<td><?php echo $j['id_remitente'] ?></td>
		<?php if ($user->authorise('jefe.delgada', 'com_nota') || $user->authorise('jefe.natales', 'com_nota')){ ?>
			<td><?php echo $j['depto_origen'] ?></td>
		<?php } ?>
			<td><?php echo $f[2].'-'.$f[1].'-'.$f[0] ?></td>
			<td>
				<div class='centrar'>
					<div class='barra_avance <?php echo ($j['empleado']==1 ? "paso_aprobado" : "").($j['empleado']==2 ? "paso_rechazado" : "") ?>'>Enviado empleado</div>
					<?php if ($user->authorise('jefe.delgada', 'com_nota') || $user->authorise('jefe.natales', 'com_nota') || $user->authorise('capitan.jefe', 'com_nota') || $user->authorise('capitan.sin_jefe', 'com_nota')){ ?>
					<div class='barra_avance <?php echo ($j['capitan']==1 ? "paso_aprobado" : "").($j['capitan']==2 ? "paso_rechazado" : "") ?>'>Autorizado capitán</div>
					<?php } ?>
					<div class='barra_avance <?php echo ($j['jefe']==1 ? "paso_aprobado" : "").($j['jefe']==2 ? "paso_rechazado" : "") ?>'>Autorizado jefe</div>
					<div class='barra_avance <?php echo ($j['depto']==1 ? "paso_aprobado" : "").($j['depto']==2 ? "paso_rechazado" : "") ?>'>Autorizado depto.</div>
					<div class='barra_avance <?php echo ($j['adquisiciones']==1 ? "paso_aprobado" : "").($j['adquisiciones']==2 ? "paso_rechazado" : "") ?>'>OC generada</div>
				</div>
			</td>
			<td align="center">
			<?php 
			$url = "";
			if (($user->authorise('jefe.delgada', 'com_nota') 
					&& $j['jefe']!=0) 
				|| ($user->authorise('adquisiciones.jefe', 'com_nota') && $j['adquisiciones']) 
				|| ($user->authorise('capitan.jefe', 'com_nota') && $j['capitan']!=0)){ 
				$url = JRoute::_('index.php?option=com_nota&view=reportes&task=reportes.detalle_nota&id_nota='.$j['id_remitente'].'&tmpl=component');
			} else if (($user->authorise('jefe.depto', 'com_nota') && !$j['jefe'])
					|| (($user->authorise('capitan.jefe', 'com_nota') || $user->authorise('capitan.sin_jefe', 'com_nota')) && !$j['capitan'])
					|| ($user->authorise('jefe.delgada', 'com_nota') && !$j['jefe'])){ 
				$url = JRoute::_('index.php?option=com_nota&view=com_nota&task=detalle_notajefe&id_nota='.$j['id_remitente'].'&tmpl=component');
			}else{
				$url = JRoute::_('index.php?option=com_nota&view=com_nota&task=reportes.detalle_nota&id_nota='.$j['id_remitente'].'&tmpl=component');
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
</div>
<?php for ($j=0;$j<(25-$i);$j++) echo "<br>"; ?>
<div class='centrar'>
	<a href='<?php echo JRoute::_('index.php?option=com_nota'); ?>'>
	<div class='boton' id='volver'>
		<img src="/portal/administrator/templates/hathor/images/header/icon-48-revert.png" /><br>
		Volver
	</div>
	</a>
</div>
