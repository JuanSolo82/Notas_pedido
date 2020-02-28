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
<div class='barra_nombre' style='width: 100%;'>Gestion de usuarios</div>
</div>
<br><br>
<div class='centrar'>
	<div class='fila_completa bordear centrar' style='width: 90%;'>
		<div class="col-2 titulo_item">Usuario</div>
		<div class="col-3"><input type="text" id="nombre"></div>
		<div class="col-3"><input type='button' onclick="buscar()" value="Buscar"></div>
	</div>
</div>
<div style="margin: 20px;" id="resultado">

</div>