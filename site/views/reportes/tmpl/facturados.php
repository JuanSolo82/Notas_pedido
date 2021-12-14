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
<div class='barra_nombre' style='width: 95%;'>Reporte facturación (beta)</div>
</div>
<form name="facturados" id="facturados" method="post" action="<?php echo JRoute::_('index.php?option=com_nota&view=reportes&task=reportes.facturados'); ?>">
	<div class='centrar' style="margin-bottom: 20px;">
		<div class='fila_completa bordear centrar' style='width: 90%;'>
			<div class="col-4 titulo_item" style="border-right: 1px solid silver;">
				<div class="col-4">Desde</div><div class="col-4"><input autocomplete="off" type="text" id="desde" name="desde"></div>
				<div class="col-4">Hasta</div><div class="col-4"><input autocomplete="off" type="text" id="hasta" name="hasta" disabled style="background: silver;"></div>
				<!--<div class='fila_vacia'></div>-->
				<div class='col-4'>Centro de costo</div>
				<div class='col-6'>
					<select id='centro_costo' name='centro_costo' style="width: 80%;">
						<option value='0'>Todos</option>
					<?php foreach ($this->centros_costo as $cc){ ?>						
						<option value='<?php echo $cc['id'] ?>'><?php echo $cc['nombre'] ?></option>
					<?php } ?>
					</select>
				</div>
			</div>
			<div class="col-4 titulo_item" style="border-right: 1px solid silver;">
				<div class="col-4">Nota de pedido</div><div class="col-4"><input autocomplete="off" type="text" id="nota_pedido" name="nota_pedido"></div>
				<div class="col-4">OC</div><div class="col-4"><input autocomplete="off" type="text" id="orden_compra" name="orden_compra"></div>
			</div>
			<div class="col-1"><input type='button' class="boton_mediano" value="Buscar" onclick="cargar_lista()"></div>
		</div>
	</div>
</form>
<?php if (sizeof($this->lista_notas)){ ?>
<div class="col-1"><input type='button' class="boton_mediano" value="Exportar" onclick="mostrar_lista_copiar()"></div>
<table class='tabla_listado'>
	<tr>
		<th width="7%">Nota</th>
		<th width="10%">Fecha</th>
		<th width="10%">Orden de compra</th>
		<th>Centro de costo</th>
		<th width="10%">Factura</th>
		<th width="10%">Proveedor</th> 
		<th width="10%" align="center">Detalle</th>
	</tr>
<?php foreach ($this->lista_notas as $n){ ?>
	<tr>
		<td><?php echo $n['id'] ?></td>
		<td><?php echo NotaHelper::fechamysql($n['fecha']) ?></td>
		<!--<td><a href="<?php echo JRoute::_('index.php?option=com_nota&task=adquisiciones.generar_orden&format=pdf&tmpl=component&id_remitente='.$n['id'].'&opcion='.$n['opcion_oc'].'&correo=0'); ?>"><?php echo $n['orden_compra'] ?></a></td>-->
		<td><a onclick="bajar_pdf(<?php echo $n['id'] ?>,<?php echo $n['orden_compra'] ?>,<?php echo $n['opcion_oc'] ?>)"><?php echo $n['orden_compra'] ?></a></td>
		<td><?php echo $n['centro_costo'] ?></td>
		<td id='factura_lista<?php echo $n['orden_compra'] ?>'><?php echo $n['factura'] ?></td>
		<td id='proveedor_lista<?php echo $n['orden_compra'] ?>'><?php echo $n['proveedor'] ?></td>
		<td align="center">
			<a onclick="SqueezeBox.fromElement(this, {
						handler:'iframe', 
						size: {x: 1000, y: 600}, 
						url:'<?php echo JRoute::_('index.php?option=com_nota&view=nota&task=reportes.facturar_orden&id_nota='.$n['id'].'&orden_compra='.($n['orden_compra'] ? $n['orden_compra'] : 0).'&opcion_oc='.($n['opcion_oc'] ? $n['opcion_oc'] : 0).'&tmpl=component'); ?>',
						//onClose:function(){window.location.reload();} 
						onClose: function(){
                            console.log(<?php echo $n['orden_compra'] ?>);
							getFactura(<?php echo $n['orden_compra'] ?>);
						}
					})"><img src="/portal/administrator/templates/hathor/images/menu/icon-16-edit.png" /></a>
		</td>
	</tr>
<?php } ?>
</table>
<?php } ?>
<div style="display: none; font-size: small;" id="tabla_copiar" title="Listado">
<!--<input type='button' class="boton_mediano" value="Copiar">-->
<table id="tabla_listado">
	<tr>
		<th width="7%" style="border: 2px solid grey;"><b>Nota</b></th>
		<th style="border: 2px solid grey;"><b>Centro de costo</b></th>
		<th style="border: 2px solid grey;"><b>Fecha</b></th>
		<th style="border: 2px solid grey;"><b>Orden de compra</b></th>
		<th style="border: 2px solid grey;"><b>Factura</b></th>
		<th style="border: 2px solid grey;"><b>Proveedor</b></th>
	</tr>
<?php foreach ($this->lista_notas as $n){ ?>
	<tr>
		<td style="border: 1px solid grey;"><?php echo $n['id'] ?></td>
		<td style="border: 1px solid grey;"><?php echo $n['centro_costo'] ?></td>
		<td style="border: 1px solid grey;"><?php echo NotaHelper::fechamysql($n['fecha']) ?></td>
		<td style="border: 1px solid grey;"><?php echo $n['orden_compra'] ?></td>
		<td style="border: 1px solid grey;" id='factura_copia<?php echo $n['orden_compra'] ?>'><?php echo $n['factura'] ?></td>
		<td style="border: 1px solid grey;" id='proveedor_copia<?php echo $n['orden_compra'] ?>'><?php echo $n['proveedor'] ?></td>
	</tr>
<?php } ?>
</table>
</div>
<?php if (sizeof($this->lista_notas)<15){
	for ($i=0;$i<=15-sizeof($this->notas);$i++) echo "<br>";
} ?>
<div class="centrar" style="display: flex;">
	<a href="<?php echo JRoute::_('index.php?option=com_nota'); ?>">
	<div class='boton'><img src='/portal/administrator/templates/hathor/images/header/icon-48-revert.png' /><br>Volver</div>
	</a>
</div>

<div id='alarma_valores' style='display: none' title="Atención">Ingrese valor a buscar</div>
