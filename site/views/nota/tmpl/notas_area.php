<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHTML::stylesheet('nota.css', 'components/com_nota/assets/css/');
JHTML::script('jquery.min.js', 'components/com_nota/assets/js/');
JHTML::script('jquery-ui.min.js', 'components/com_nota/assets/js/');
JHtml::_('behavior.modal'); 
//JHTML::script('bootstrap.js', 'components/com_nota/assets/bootstrap/js/');
$i=1;
$user = JFactory::getUser();
?>
<script type="text/javascript" src="/portal/components/com_nota/assets/js/nota.js?ges=128"></script>
<input type='hidden' size='3' id='pagina' value='1'>
<div class='fila_completa centrar' style='margin-bottom: 20px;'>
	<div class='barra_nombre' style='width: 90%;'>Notas enviadas del área</div>
</div>

<div class="fila_vacia"></div>
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

<div class='fila_completa centrar' style='margin-bottom: 20px;'>
	<div class='barra_nombre' style='width: 50%;'>
		<h3 class="titulo_item">Búsqueda por ítem</h3>
		<input type='text' id='parametro' name='parametro' autocomplete="off">
		<input type='button' class="boton_simple"  onclick="buscar_nota_area()" value="Buscar">
		<input style="float: right;" type='button' onclick="limpiar_busqueda()" value="Limpiar" class="boton_simple" >
	</div>
<!--	<div class='barra_nombre' style='width: 50%;'>
		<h3 class="titulo_item">Búsqueda por proveedor</h3>
		<input type='text' id='proveedor' name='proveedor' autocomplete="off">
		<input type='button' class="boton_simple" onclick="buscar_notas_propias()" value="Buscar">
	</div> -->
</div>


<div style="height: 60px">
<div class="prev_next" id="anterior" onclick="anterior_previo_depto(2,1)">
	<img src='/portal/components/com_nota/assets/img/previous.png' /> Anterior
</div>
<div class="prev_next" id="siguiente" onclick="anterior_previo_depto(1,1)">
	<img src='/portal/components/com_nota/assets/img/next.png' /> Siguiente
</div>
</div>ss

<input type="hidden" id="pagina" value="1" size='5'>

<div id='notas_area'>
<h3>Página 1</h3>
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
