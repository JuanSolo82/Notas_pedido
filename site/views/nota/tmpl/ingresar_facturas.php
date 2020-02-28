<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHTML::stylesheet('bootstrap.css', 'components/com_nota/assets/bootstrap/css/');
JHTML::stylesheet('nota.css', 'components/com_nota/assets/css/');
JHTML::script('jquery.min.js', 'components/com_nota/assets/js/');
JHTML::script('jquery-ui.min.js', 'components/com_nota/assets/js/');
JHTML::script('nota.js', 'components/com_nota/assets/js/');
JHTML::script('bootstrap.js', 'components/com_nota/assets/bootstrap/js/');
?>
<br>
<div class='centrar'>
<div class='barra_nombre' style='width: 100%;'>Ingreso de facturas [en desarrollo]</div>
</div>
<br><br>
<form name="adminForm" id="adminForm" method="post" onsubmit="buscar_facturar()">
	<div class='centrar'>
		<div class='fila_completa bordear centrar' style='width: 90%;'>
			<div class="col-4 titulo_item" style="border-right: 1px solid silver;">
				<div class="col-4">Desde</div><div class="col-4"><input type="text" id="desde" name="desde"></div>
				<div class="col-4">Hasta</div><div class="col-4"><input type="text" id="hasta" name="hasta"></div>
			</div>
			<div class="col-4 titulo_item" style="border-right: 1px solid silver;">
				<div class="col-4">Nota de pedido</div><div class="col-4"><input type="text" id="nota_pedido" name="nota_pedido"></div>
				<div class="col-4">OC</div><div class="col-4"><input type="text" id="orden_compra" name="orden_compra"></div>
			</div>
			<div class="col-1"><input type='button' class="boton_mediano" value="Buscar" onclick="buscar_factura()"></div>
		</div>
	</div>
</form>