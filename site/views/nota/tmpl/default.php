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
<div class='barra_nombre' style='width: 45%;'>Bienvenido <?php echo $this->datos_user['nombre_usuario'] ?></div>
<div class='barra_nombre' style='width: 45%;'>Departamento: <?php echo $this->datos_user['departamento'] ?></div>
</div>


<div class='centrar' style="display: block;">
	<?php echo $this->setMenu(); ?>
</div>