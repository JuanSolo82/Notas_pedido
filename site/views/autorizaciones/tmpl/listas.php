<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHTML::stylesheet('bootstrap.css', 'components/com_nota/assets/bootstrap/css/');
JHTML::stylesheet('nota.css', 'components/com_nota/assets/css/');
JHTML::script('jquery.min.js', 'components/com_nota/assets/js/');
JHTML::script('jquery-ui.min.js', 'components/com_nota/assets/js/');
JHTML::script('bootstrap.js', 'components/com_nota/assets/bootstrap/js/');
JHtml::_('behavior.modal');

?>
<script type="text/javascript" src="/portal/components/com_nota/assets/js/nota.js?jef=1"></script>

<div class='fila_completa centrar' style='margin-bottom: 20px;'>
	<div class='barra_nombre' style='width: 90%;'>Lista notas</div>
</div>
<div class="fila_vacia"></div>
<table>
	<tr style="border:hidden;">
		<td><div class='barra_avance'>Etapa no tramitada</div></td>
	</tr>
	<tr style="border:hidden;">
		<td><div class='barra_avance paso_aprobado'>Etapa tramitada</div></td>
	</tr>
	<tr style="border:hidden;">
		<td><div class='barra_avance paso_rechazado'>Etapa rechazada</div></td>
	</tr>
</table>

<div style="height: 60px">
    <div class="prev_next" id="anterior" onclick="avanzar(0)">
        <img src='/portal/components/com_nota/assets/img/previous.png' /> Anterior
    </div>
    <div class="prev_next" id="siguiente" onclick="avanzar(1)">
        <img src='/portal/components/com_nota/assets/img/next.png' /> Siguiente
    </div>
</div>
<div>
    <b>Página <span id="avance">0</span></b>
</div>

<div id="pagina"></div>