<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHTML::stylesheet('bootstrap.css', 'components/com_nota/assets/bootstrap/css/');
JHTML::stylesheet('nota.css', 'components/com_nota/assets/css/');
JHTML::script('jquery.min.js', 'components/com_nota/assets/js/');
JHTML::script('jquery-ui.min.js', 'components/com_nota/assets/js/');
JHTML::script('nota.js', 'components/com_nota/assets/js/');
JHTML::script('bootstrap.js', 'components/com_nota/assets/bootstrap/js/');
/*if (NotaHelper::isTestSite()){
	echo "prueba de api clima<br>";
	$data = json_decode( file_get_contents('http://api.openweathermap.org/data/2.5/uvi?appid=a2d3e70172051b1e36cbe378b2aa811a&lat=-53.156723&lon=-70.908422'), true );
	print_r($data);
}*/

?>
<br>
<div class='centrar'>
<div class='barra_nombre' style='width: 45%;'>Bienvenido <?php echo $this->datos_user['nombre_usuario'] ?></div>
<div class='barra_nombre' style='width: 45%;'>Departamento: <?php echo $this->datos_user['departamento'] ?></div>
</div>


<div class='centrar' style="display: block;">
	<?php echo $this->setMenu(); ?>
</div>