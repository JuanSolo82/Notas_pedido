<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHTML::stylesheet('bootstrap.css', 'components/com_nota/assets/bootstrap/css/');
JHTML::stylesheet('nota.css', 'components/com_nota/assets/css/');
JHTML::stylesheet('jquery.timepicker.min.css', 'components/com_nota/assets/css/');
JHTML::stylesheet('jquery-ui.css', 'components/com_nota/assets/css/');
JHTML::script('jquery.min.js', 'components/com_nota/assets/js/');
JHTML::script('jquery-ui.min.js', 'components/com_nota/assets/js/');
JHTML::script('nota.js', 'components/com_nota/assets/js/');
JHTML::script('bootstrap.js', 'components/com_nota/assets/bootstrap/js/');
JHTML::script('datepicker-es.js', 'components/com_nota/assets/js/');
JHTML::_('behavior.modal');
?>

<br>
<div class='centrar' style="margin-bottom: 30px;">
<div class='barra_nombre' style='width: 95%;'>Reportes (beta)</div>
</div>
<form name="adminForm" id="adminForm" method="post" action="<?php echo JRoute::_('index.php?option=com_nota&task=reportes.busqueda_notas'); ?>">
	<input type='hidden' value='1' id='inicio' name='inicio'>
	<div class='centrar' style="margin-bottom: 20px;">
		<div class='fila_completa bordear centrar' style='width: 90%;'>
			<div class="col-4 titulo_item" style="border-right: 1px solid silver;">
				<div class="col-4">Desde</div><div class="col-4"><input autocomplete="off" type="text" id="desde" name="desde" value=""></div>
				<div class="col-4">Hasta</div><div class="col-4"><input autocomplete="off" type="text" id="hasta" name="hasta" value="" disabled style="background: silver;"></div>
				
				<div class="col-4">Origen</div>
				<div class="col-4">
					<select style="width: 170px;" id='depto_origen' name='depto_origen'>
						<option value='0'>Todos los departamentos</option>
					<?php foreach ($this->lista_deptos as $d){ ?>
						<option value="<?php echo $d['id'] ?>"><?php echo $d['nombre'] ?></option>
					<?php } ?>
					</select>
				</div>
				<div class="col-4">Estado</div>
				<div class="col-4">
					<select id='estado' name='estado'>
						<option value='0'>Todas</option>
						<option value='2'>Eliminadas</option>
					</select>
				</div>
			</div>
			<div class="col-4 titulo_item" style="border-right: 1px solid silver;">
				<div class="col-4">Nota de pedido</div><div class="col-4"><input autocomplete="off" type="text" id="nota_pedido" name="nota_pedido"></div>
				<div class="col-4">OC</div><div class="col-4"><input autocomplete="off" type="text" id="orden_compra" name="orden_compra"></div>
			</div>
			<div class="col-1"><input type='submit' class="boton_mediano" value="Buscar"></div>
		</div>
	</div>
</form>
<?php if (sizeof($this->notas)){ ?>
<table class='tabla_listado'>
	<tr>
		<th width="7%">Nota</th>
		<th width="10%">Fecha</th>
		<th width="15%">Origen</th>
		<th>Etapas</th>
		<th width="10%" align="center">Detalle</th>
	</tr>
<?php foreach ($this->notas as $n){ ?>
	<tr>
		<td><?php echo $n['id'] ?></td>
		<td><?php echo NotaHelper::fechamysql($n['fecha']) ?></td>
		<td><?php echo $n['depto_origen'] ?></td>
		<td>
			<div class="centrar">
				<div class='barra_avance <?php echo ($n['empleado']==1 ? "paso_aprobado" : "").($n['empleado']==2 ? "paso_rechazado" : "") ?>'>Enviado empleado</div>
				<div class='barra_avance <?php echo ($n['capitan']==1 ? "paso_aprobado" : "").($n['capitan']==2 ? "paso_rechazado" : "") ?>'>Autorizado capitán</div>
				<div class='barra_avance <?php echo ($n['jefe']==1 ? "paso_aprobado" : "").($n['jefe']==2 ? "paso_rechazado" : "") ?>'>Autorizado jefe</div>
				<div class='barra_avance <?php echo ($n['depto']==1 ? "paso_aprobado" : "").($n['depto']==2 ? "paso_rechazado" : "") ?>'>Autorizado depto.</div>
				<div class='barra_avance <?php echo ($n['adquisiciones']==1 ? "paso_aprobado" : "").($n['adquisiciones']==2 ? "paso_rechazado" : "") ?>'>OC generada</div>
				<div class='barra_avance <?php echo ($n['aprobado']==1 ? "paso_aprobado" : "").($n['aprobado']==2 ? "paso_rechazado" : "") ?>'>Calificación</div>
			</div>
		</td>
		<td align="center">
			<a onclick="SqueezeBox.fromElement(this, {
						handler:'iframe', 
						size: {x: 1000, y: 600}, 
						url:'<?php echo JRoute::_('index.php?option=com_nota&view=nota&task=reportes.detalle_nota&id_nota='.$n['id'].'&tmpl=component'); ?>',
						//onClose:function(){window.location.reload();} 
					})"><img src="/portal/administrator/templates/hathor/images/menu/icon-16-edit.png" /></a>
		</td>
	</tr>
<?php } ?>
</table>
<?php } ?>
<?php if (sizeof($this->notas)<15){
	for ($i=0;$i<=15-sizeof($this->notas);$i++) echo "<br>";
} ?>
<div class="centrar" style="display: flex;">
	<a href="<?php echo JRoute::_('index.php?option=com_nota'); ?>">
	<div class='boton'><img src='/portal/administrator/templates/hathor/images/header/icon-48-revert.png' /><br>Volver</div>
	</a>
</div>
