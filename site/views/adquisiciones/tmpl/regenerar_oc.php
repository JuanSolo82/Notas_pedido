<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHTML::stylesheet('bootstrap.css', 'components/com_nota/assets/bootstrap/css/');
JHTML::stylesheet('nota.css', 'components/com_nota/assets/css/');
JHTML::script('jquery.min.js', 'components/com_nota/assets/js/');
JHTML::script('jquery-ui.min.js', 'components/com_nota/assets/js/');
JHTML::script('adquisiciones.js', 'components/com_nota/assets/js/');
JHTML::script('bootstrap.js', 'components/com_nota/assets/bootstrap/js/');
JHtml::_('behavior.modal');

?>
<br>
<div class='centrar' style="margin-bottom: 30px;">
<div class='barra_nombre' style='width: 95%;'>Buscar OC</div>
</div>
<form action="<?php echo JRoute::_('index.php?option=com_nota&view=adquisiciones&task=adquisiciones.regenerar_oc'); ?>" method="post" id="adminForm" name="adminForm">
	<div class='cuadro_input' style="margin-bottom: 50px;">Buscar OC [En desarrollo]<input type='text' class='input-transparente' id='orden_compra' name='orden_compra'></div>
</form>

<?php if (sizeof($this->datos_oc)){ ?>
<br><br>

<!-- Orden de compra -->
<style>
<?php echo $this->estilos(); ?>
</style>
<div class="caja_orden" style="margin-top: 20px;">
	
	<div class="superior">
		ORDEN DE COMPRA <?php echo $this->orden_compra ?>
	</div>
	<div class="inferior" style="margin: 20px;">
		Nota de pedido nº <?php echo $this->datos_nota['id'] ?>
	</div>

	<table class='tabla_listado' style="width: 100%;">
		<tr>
			<th align="center" width='10%'>Escoger</th>
			<th align="center" width='10%'>Cantidad</th>
			<th align="center" width='40%'>Ítem</th>
		</tr>
	<?php 
		$cont=0;
		foreach ($this->datos_oc as $oc){ 
			$cantidad = $oc['cantidad'];
			if ($oc['id_nueva_cantidad']) 
				$cantidad = $oc['nueva_cantidad'];
			if ($cantidad){ 
				$cont++;
				?>
				<input type="hidden" id="id_item<?php echo $cont ?>" value="<?php echo $oc['id'] ?>">
			<tr>
				<td align="center"><input type='checkbox' id="item<?php echo $oc['id'] ?>"></td>
				<td><?php echo $oc['nueva_cantidad'] ? $oc['nueva_cantidad'] : $oc['cantidad'] ?></td>
				<td><?php echo $oc['item'] ?></td>
			</tr>
	<?php 	}
		} ?>
	</table><br>

	<div class="centrar">
		<div class="boton" onclick="nueva_oc(<?php echo $cont ?>)"><img src="/portal/administrator/templates/hathor/images/header/icon-48-article.png"><br>Crear nueva OC</div>
	</div>
</div>

<?php }elseif ($this->orden_compra){ ?>
	<h2>No existen datos</h2>
<?php } ?>

<?php for ($i=0;$i<(25-$cont);$i++) echo "<br>"; ?>
<div class="centrar">
	<a href="<?php echo JRoute::_('index.php?option=com_nota&view=adquisiciones'); ?>">
	<div class='boton'><img src='/portal/administrator/templates/hathor/images/header/icon-48-revert.png' /><br>Volver</div>
	</a>
</div> 