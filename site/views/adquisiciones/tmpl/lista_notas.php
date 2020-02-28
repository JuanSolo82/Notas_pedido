<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHTML::stylesheet('bootstrap.css', 'components/com_nota/assets/bootstrap/css/');
JHTML::stylesheet('nota.css', 'components/com_nota/assets/css/');
JHTML::script('jquery.min.js', 'components/com_nota/assets/js/');
JHTML::script('jquery-ui.min.js', 'components/com_nota/assets/js/');
JHTML::script('nota.js', 'components/com_nota/assets/js/');
JHTML::script('adquisiciones.js', 'components/com_nota/assets/js/');
JHTML::script('bootstrap.js', 'components/com_nota/assets/bootstrap/js/');
JHTML::stylesheet('bootstrap.css', 'components/com_nota/assets/bootstrap/css/');
JHtml::_('behavior.modal'); 
$cont=0;
?>
<br>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<div class='centrar' style="margin-bottom: 30px;">
<div class='barra_nombre' style='width: 95%;'>Notas pendientes de generar OC</div>
</div>

<table class='tabla_listado'>
	<tr>
		<th align="center" width='5%'>#</th>
		<th align="center" width='8%'>Nota</th>
		<th align="center" width='10%'>Fecha</th>
		<th align="center" width='15%'>Remitente</th>
		<th align="center" width='21%'>Departamento compra</th>
		<!--<th align="center" width='21%'>Departamento destino</th>-->
		<th align="center" width='15%'>Separación OC</th>
		<th align="center" width='10%'>Revisión</th>
	</tr>
<?php foreach ($this->lista_notas as $l){ 
		$cont++;
?>
	<tr>
		<td><?php echo $cont; ?></td>
		<td><?php echo $l['id'] ?></td>
		<td><?php echo NotaHelper::fechamysql($l['fecha'],1); ?></td>
		<td><?php echo $l['nombre_remitente'] ?></td>
		<td><?php echo $l['departamento'] ?></td>
		<!--<td><?php //echo $l['depto_destino'] ?></td>-->
		<td align="center">
			<a onclick="SqueezeBox.fromElement(this, 
						{handler:'iframe', 
						size: {x: 700, y: 500}, 
						url:'<?php echo JRoute::_('index.php?option=com_nota&view=adquisiciones&task=adquisiciones.opcion_oc&id_remitente='.$l['id'].'&tmpl=component'); ?>',
						onClose:function(){window.location.reload();} })"><img src="/portal/administrator/templates/hathor/images/menu/icon-16-themes.png" /></a>
			<?php //echo $l['aprobado_adquisiciones'] ?>
		</td>
		<td align="center">
			<a onclick="SqueezeBox.fromElement(this, 
						{handler:'iframe', 
						size: {x: 1000, y: 650}, 
						url:'<?php echo JRoute::_('index.php?option=com_nota&view=adquisiciones&task=adquisiciones.orden_compra&id_remitente='.$l['id'].'&tmpl=component'); ?>',
						onClose:function(){window.location.reload();} })"><img src="/portal/administrator/templates/hathor/images/menu/icon-16-help-docs.png" /></a>
		</td>
	</tr>
<?php } ?>
</table>

<?php for ($i=0;$i<(25-$cont);$i++) echo "<br>"; ?>
<div class="centrar">
	<a href="<?php echo JRoute::_('index.php?option=com_nota&view=adquisiciones'); ?>">
	<div class='boton'><img src='/portal/administrator/templates/hathor/images/header/icon-48-revert.png' /><br>Volver</div>
	</a>
</div>