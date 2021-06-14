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
		<th width='5%'>#</th>
		<th width='5%'>Nº</th>
		<th width='20%'>Fecha</th>
		<th width='60%'>Estado de avance</th>
		<th width='10%'>Revisión</th>
	</tr>
</table>
<?php print_r($this->naves); ?>
<div class='centrar'>
	<a href='<?php echo JRoute::_('index.php?option=com_nota'); ?>'>
	<div class='boton' id='volver'>
		<img src="/portal/administrator/templates/hathor/images/header/icon-48-revert.png" /><br>
		Volver
	</div>
	</a>
</div>

