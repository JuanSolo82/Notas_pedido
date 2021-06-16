<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHTML::stylesheet('nota.css', 'components/com_nota/assets/css/');
JHTML::script('jquery.min.js', 'components/com_nota/assets/js/');
JHTML::script('jquery-ui.min.js', 'components/com_nota/assets/js/');
JHTML::stylesheet('jquery.timepicker.min.css', 'components/com_nota/assets/css/');
JHTML::script('datepicker-es.js', 'components/com_nota/assets/js/');
JHTML::stylesheet('jquery.timepicker.min.css', 'components/com_nota/assets/css/');
JHTML::stylesheet('jquery-ui.css', 'components/com_nota/assets/css/');
JHtml::_('behavior.modal');
$i=1;
$user = JFactory::getUser();
?>
<script type="text/javascript" src="/portal/components/com_nota/assets/js/nota.js?col=46"></script>
<input type="hidden" id="vista" value="<?php echo $this->layout; ?>">

<div class='fila_completa centrar' style='margin-bottom: 20px;'>
	<div class='barra_nombre' style='width: 90%;'>Editar naves</div>
</div>
<br>
<table class='tabla_listado'>
	<tr>
		<th>Nave</th>
		<th>Subarea</th>
		<th>Revisión</th>
	</tr>

<?php foreach ($this->naves as $n){ ?>
	<tr>
		<td><?php echo $n['nave'] ?></td>
		<td><?php echo $n['nombre'] ?></td>
		<td></td>
	</tr>
<?php } ?>
</table>

<div class='centrar'>
	<a href='<?php echo JRoute::_('index.php?option=com_nota'); ?>'>
	<div class='boton' id='volver'>
		<img src="/portal/administrator/templates/hathor/images/header/icon-48-revert.png" /><br>
		Volver
	</div>
	</a>
</div>

