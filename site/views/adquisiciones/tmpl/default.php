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
<div class='centrar' style="margin-bottom: 30px;">
<div class='barra_nombre' style='width: 95%;'>Gestion OC</div>
</div>

<div class='centrar'>
	<div class='boton'><?php echo $this->getBoton('Tramitar órdenes de compra', 'featured', 'lista_notas',1) ?></div>
	<div class='boton'><?php echo $this->getBoton('Buscar OC', 'featured', 'buscar_oc') ?></div>
	<!--<div class='boton'><?php echo $this->getBoton('Regenerar OC', 'featured', 'regenerar_oc') ?></div>-->
</div>
<div class="centrar">
	<a href="<?php echo JRoute::_('index.php?option=com_nota'); ?>">
	<div class='boton'><img src='/portal/administrator/templates/hathor/images/header/icon-48-revert.png' /><br>Volver</div>
	</a>
</div>
