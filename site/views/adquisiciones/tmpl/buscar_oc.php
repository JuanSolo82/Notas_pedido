<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHTML::stylesheet('bootstrap.css', 'components/com_nota/assets/bootstrap/css/');
JHTML::stylesheet('nota.css', 'components/com_nota/assets/css/');
JHTML::script('jquery.min.js', 'components/com_nota/assets/js/');
JHTML::script('jquery-ui.min.js', 'components/com_nota/assets/js/');
JHTML::script('adquisiciones.js', 'components/com_nota/assets/js/');
JHTML::script('nota.js', 'components/com_nota/assets/js/');
JHTML::script('bootstrap.js', 'components/com_nota/assets/bootstrap/js/');
JHtml::_('behavior.modal');
require_once(JPATH_COMPONENT_SITE.'/assets/phpqrcode.php');
$cont=0;
?>
<br>

<div class='centrar' style="margin-bottom: 30px;">
<div class='barra_nombre' style='width: 95%;'>Buscar OC [en desarrollo]</div>
</div>
<form action="<?php echo JRoute::_('index.php?option=com_nota&view=adquisiciones&task=adquisiciones.buscar_oc'); ?>" method="post" id="adminForm" name="adminForm" autocomplete="off">
	<div class='barra_nombre' style='width: 45%;'>Buscar OC <input type='text' style="position: relative; left: 50px; color: grey;" class='input-transparente' id='orden_compra' name='orden_compra' value="<?php echo $this->orden_compra ? $this->orden_compra : "" ?>"></div>
	<div class='barra_nombre' style='width: 45%;'>Nota de pedido <input type='text' style="position: relative; left: 50px; color: grey;" class='input-transparente' id='nota_pedido' name='nota_pedido'></div>
</form>

<?php if (sizeof($this->datos)){ ?>
<br><br>
<input type="hidden" id="proveedor" value="<?php echo $this->datos[0]['proveedor'] ?>">
<div class="fila_completa bordear" style="margin-top: 10px;">
	<div class='centrar'>
		<div class='col-3 titulo_item'>Fecha nota</div>
		<div class='col-7'><?php echo NotaHelper::fechamysql($this->datos[0]['fecha']) ?></div>
	</div>
</div>
<div class="fila_completa bordear">
	<div class='centrar'>
		<div class='col-3 titulo_item'>Nota de pedido</div>
		<div class='col-7'><?php echo $this->datos[0]['id'] ?></div>
	</div>
</div>
<div class="fila_completa bordear">
	<div class='centrar'>
		<div class='col-3 titulo_item'>Usuario</div>
		<div class='col-7'><?php echo $this->datos[0]['nombre_emisor'] ?></div>
	</div>
</div>
<div class="fila_completa bordear" style="margin-bottom: 30px;">
	<div class='centrar'>
		<div class='col-3 titulo_item'>Departamento destino</div>
		<div class='col-7'><?php echo $this->datos[0]['depto_destino'] ?></div>
	</div>
</div>
<br><br>
<?php foreach ($this->datos as $d){ ?>
<div class='fila_completa'>
<div class='col-2'>
	<div class="boton" onclick="cargar_pdf(<?php echo $this->datos[0]['id'] ?>, <?php echo $d['orden_compra'] ?>,<?php echo $d['opcion_oc'] ?>,1)">
		<img src="/portal/administrator/templates/hathor/images/header/icon-48-article.png"><br>
		<span class="titulo_item" style="color: white">Obtener copia de la orden</span>
	</div>
</div>
<div class='col-8'>
	<table class="adminList">
		<tr class="encabezado_tabla">
			<th width="10%">Cantidad</th>
			<th width="30%">Item</th>
			<th width="35%">Motivo</th>
			<th>Adjunto (en desarrollo)</th>
		</tr>
	<?php foreach ($this->items_oc[$d['orden_compra']] as $i){ 
		$cantidad = $i['nueva_cantidad'] ? $i['nueva_cantidad'] : $i['cantidad'];
		if ($cantidad){
		?>
			<tr>
				<td><?php echo $cantidad ?></td>
				<td><?php echo $i['item'] ?></td>
				<td><?php echo $i['motivo'] ?></td>
				<td></td>
			</tr>
		<?php } ?>
	<?php } ?>
	</table>
</div>
</div>
<?php } ?>

<?php }elseif($this->orden_compra!=0){ ?>
	<div class="fila_completa bordear" style="margin-top: 30px;">
	<div class='centrar'>
		<div class='col-5 titulo_item'>No se encuentra órden con la búsqueda ingresada</div>
	</div>
</div>
<?php } ?>

<?php for ($i=0;$i<(25-$cont);$i++) echo "<br>"; ?>
<div class="centrar">
	<a href="<?php echo JRoute::_('index.php?option=com_nota&view=adquisiciones'); ?>"><br>
	<div class='boton'><img src='/portal/administrator/templates/hathor/images/header/icon-48-revert.png' /><br>Volver</div>
	</a>
</div>