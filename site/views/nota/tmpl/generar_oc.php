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
$cont=0;
?>
<br>
<div class='centrar' style="margin-bottom: 30px;">
<div class='barra_nombre' style='width: 95%;'>Listado de notas para generar OC</div>
</div>

<table class='tabla_listado'>
	<tr>
		<th align="center" width='10%'>Nota</th>
		<th align="center" width='10%'>Fecha</th>
		<th align="center" width='15%'>Remitente</th>
		<th align="center" width='55%'>Departamento origen</th>
		<th align="center" width='10%'>Revisión</th>
	</tr>
<?php foreach ($this->lista_notas as $n){ 
		$f = explode('-',$n['fecha']);
?>
	<tr>
		<td><?php echo $n['id'] ?></td>
		<td><?php echo $f[2].'-'.$f[1].'-'.$f[0] ?></td>
		<td><?php echo $n['nombre_remitente'] ?></td>
		<td><?php echo $n['departamento'] ?></td>
		<td><a href="<?php echo JRoute::_('index.php?option=com_nota&view=adquisiciones&task=adquisiciones.orden_compra&id_remitente='.$n['id']); ?>">
				<img src="/portal/administrator/templates/hathor/images/menu/icon-16-help-docs.png" /></a>
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