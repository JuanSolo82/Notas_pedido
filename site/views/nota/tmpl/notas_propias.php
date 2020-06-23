<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHTML::stylesheet('nota.css', 'components/com_nota/assets/css/');
JHTML::script('jquery.min.js', 'components/com_nota/assets/js/');
JHTML::script('jquery-ui.min.js', 'components/com_nota/assets/js/');
JHTML::script('nota.js', 'components/com_nota/assets/js/');
JHtml::_('behavior.modal'); 
$user = JFactory::getUser();
$num_filas=0;
?>
<div class='fila_completa centrar' style='margin-bottom: 20px;'>
	<div class='barra_nombre' style='width: 90%;'>Lista de notas propias</div>
</div>
<br>
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
<div style="height: 60px">
<div class="prev_next" id="anterior" onclick="anterior_previo(2)">
	<img src='/portal/components/com_nota/assets/img/previous.png' /> Anterior
</div>
<div class="prev_next" id="siguiente" onclick="anterior_previo(1)">
	<img src='/portal/components/com_nota/assets/img/next.png' /> Siguiente
</div>
</div>

<input type="hidden" id="pagina" value="1" size='5'>
<div class='fila_completa centrar' style='margin-bottom: 20px;'>
	<div class='barra_nombre' style='width: 90%;'>
		<input type='text' id='parametro' name='parametro'>
	</div>
</div>
<div id='lista_propias' style="position: relative; float: left; width: 100%; opacity: 1;"></div>
<div id='lista'>
	<h3>Página 1</h3>
	<table class='tabla_listado'>
		<tr>
			<th width='5%'>#</th>
			<th width='5%'>Nº</th>
			<th width='20%'>Fecha</th>
			<th width='50%'>Estado de avance</th>
			<th width='10%'>Revisión</th>
			<th width='10%'>OC</th>
		</tr>
	<?php 
		$i=1;
		foreach ($this->notas as $n){ ?>
		<tr>
			<td><?php echo $i; ?></td>
			<td align='center'>
				<a onclick="exportar_nota(<?php echo $n['id'] ?>)"><?php echo $n['id'] ?></a><br>
			</td>
			<td><?php echo NotaHelper::fechamysql($n['fecha']) ?></td>
			<td>
				<div class='centrar'>
					<div class='barra_avance <?php echo ($n['empleado']==1 ? "paso_aprobado" : "").($n['empleado']==2 ? "paso_rechazado" : "") ?>'>Enviado empleado</div>
					<?php if ($user->authorise('tripulante', 'com_nota')){ ?>
						<div class='barra_avance <?php echo ($n['capitan']==1 ? "paso_aprobado" : "").($n['capitan']==2 ? "paso_rechazado" : "") ?>'>Autorizado capitán</div>
					<?php } ?>
					<div class='barra_avance <?php echo ($n['jefe']==1 ? "paso_aprobado" : "").($n['jefe']==2 ? "paso_rechazado" : "") ?>'>Autorizado jefe</div>
					<div class='barra_avance <?php echo ($n['depto']==1 ? "paso_aprobado" : "").($n['depto']==2 ? "paso_rechazado" : "") ?>'>Autorizado depto.</div>
					<div class='barra_avance <?php echo ($n['adquisiciones']==1 ? "paso_aprobado" : "").($n['adquisiciones']==2 ? "paso_rechazado" : "") ?>'>OC generada</div>
					<div class='barra_avance <?php echo ($n['aprobado']==1 ? "paso_aprobado" : "").($n['aprobado']==2 ? "paso_rechazado" : "") ?>'>Calificación</div>
				</div>
			</td>
			<td align='center'>
				<?php if ($n['empleado']!=2 && $n['capitan']!=2 && $n['jefe']!=2 && $n['depto']!=2 && $n['adquisiciones']!=2 && (!$n['aprobado'] || $n['aprobado']==2)){ ?> 
					<a onclick="SqueezeBox.fromElement(this, 
								{handler:'iframe', 
								size: {x: 1100, y: 550}, 
								url:'<?php echo JRoute::_('index.php?option=com_nota&view=com_nota&task=detalle_nota&id_nota='.$n['id'].'&tmpl=component'); ?>',
								onClose:function(){window.location.reload();} })">
					<img src='/portal/administrator/templates/hathor/images/menu/icon-16-edit.png' /></a>
				<?php }else{ ?>
					<a onclick="SqueezeBox.fromElement(this, 
								{handler:'iframe', 
								size: {x: 1100, y: 550}, 
								url:'<?php echo JRoute::_('index.php?option=com_nota&view=com_nota&task=reportes.detalle_nota&id_nota='.$n['id'].'&tmpl=component'); ?>',
								onClose:function(){window.location.reload();} })">
					<img src='/portal/administrator/templates/hathor/images/menu/icon-16-article.png' /></a>
				<?php } ?>
				
			</td>
			<td><a><img src='/portal/administrator/templates/hathor/images/menu/icon-16-article.png' /></a></td>
		</tr>
	<?php $i++; } ?>
	</table>
</div>
<?php for ($j=0;$j<(15-$i);$j++) echo "<br>" ?>
<div class='centrar'>
	<a href='<?php echo JRoute::_('index.php?option=com_nota'); ?>'>
	<div class='boton' id='volver'>
		<img src="/portal/administrator/templates/hathor/images/header/icon-48-revert.png" /><br>
		Volver
	</div>
	</a>
</div>