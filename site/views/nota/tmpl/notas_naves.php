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
/*
$ar_maquinas = array(127 => '26,29,33,38',
                            (NotaHelper::isTestSite() ? 293 : 305) => '25,30,40,41',
                            78 => '34,113,37,107');
*/
$ar_maquinas = array(127 => array(26=>26,29=>29,33=>33,38=>38),
                    (NotaHelper::isTestSite() ? 293 : 305) => array(25=>25,30=>30,40=>40,41=>41),
                    78 => array(34=>34,113=>113,37=>37,107=>107));
?>
<script type="text/javascript" src="/portal/components/com_nota/assets/js/nota.js?jef=1"></script>
<input type="hidden" id="vista" value="<?php echo $this->layout; ?>">
<input type='hidden' size='3' id='pagina' value='1'>
<div class='fila_completa centrar' style='margin-bottom: 20px;'>
	<div class='barra_nombre' style='width: 90%;'>Notas naves</div>
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
<br>
<div style="height: 60px">
<div class="prev_next" id="anterior" onclick="anterior_previo(2)">
	<img src='/portal/components/com_nota/assets/img/previous.png' /> Anterior
</div>
<div class="prev_next" id="siguiente" onclick="anterior_previo(1)">
	<img src='/portal/components/com_nota/assets/img/next.png' /> Siguiente
</div>
</div>
<input type="hidden" id="pagina" value="1" size='5'>
<input type='hidden' id="naves" value='1'>
<div class='fila_completa centrar' style='margin-bottom: 20px;'>
	<div class='barra_nombre' style='width: 50%;'>
		<h3 class="titulo_item">Búsqueda por origen</h3>
		<div>
			<select id="nave_origen">
				<option value='0'>Todas las naves</option>
			<?php foreach ($this->naves as $n){ ?>
				<option value="<?php echo $n['id'] ?>"><?php echo $n['nave'] ?></option>
			<?php } ?>
			</select>
			<br>
			<input type='text' id='desde' autocomplete='off'> hasta <input type='text' id='hasta' autocomplete='off'>
		</div>
		<input type='button' class="boton_simple" onclick="busca_nave(nave_origen.value)" value="Buscar">
		<input type="button" class="boton_simple" value="Reporte"
			onclick="SqueezeBox.fromElement(this, 
							{handler:'iframe', 
							size: {x: screen.width*0.7, y: 550}, 
							url: '<?php echo JRoute::_('index.php?option=com_nota&task=reporte_naves&tmpl=component&id_nave='); ?>'
								+nave_origen.value+'&desde='+desde.value+'&hasta='+hasta.value,
							})">
	</div>
	<div class='barra_nombre' style='width: 50%;'>
		<h3 class="titulo_item">Búsqueda por ítem</h3>
		<input type='text' id='parametro' name='parametro' autocomplete="off">
		<input type='button' class="boton_simple"  onclick="buscar_notas_propias()" value="Buscar">
		<input style="float: right;" type='button' onclick="limpiar_busqueda()" value="Limpiar" class="boton_simple" >
	</div>
	<!--<div class='barra_nombre' style='width: 50%;'>
		<h3 class="titulo_item">Búsqueda por proveedor</h3>
		<input type='text' id='proveedor' name='proveedor' autocomplete="off">
		<input type='button' class="boton_simple" onclick="buscar_notas_propias()" value="Buscar">
	</div>-->
</div>
<div id='lista_propias' style="position: relative; float: left; width: 100%; opacity: 1;"></div>
<div id='lista'>
<h3>Página 1</h3>
<table class='tabla_listado'>
	<tr>
		<th width='5%'>#</th>
		<th width='5%'>Nº</th>
		<th width='20%'>Fecha</th>
		<th width='60%'>Estado de avance</th>
		<th width='10%'>Revisión</th>
	</tr>
<?php 
$i=1;
foreach ($this->notas_naves as $nd){ ?>
	<tr>
		<td><?php echo $i++ ?></td>
		<td><?php echo $nd['id'] ?></td>
		<td><?php echo $nd['fecha'] ?></td>
		<td>
			<div class='centrar'>
				<div class='barra_avance <?php echo ($nd['empleado']==1 ? "paso_aprobado" : "").($nd['empleado']==2 ? "paso_rechazado" : "") ?>'>Enviado empleado</div>
					<div class='barra_avance <?php echo ($nd['capitan']==1 ? "paso_aprobado" : "").($nd['capitan']==2 ? "paso_rechazado" : "") ?>'>Autorizado capitán</div>
				<div class='barra_avance <?php echo ($nd['jefe']==1 ? "paso_aprobado" : "").($nd['jefe']==2 ? "paso_rechazado" : "") ?>'>Autorizado jefe</div>
				<div class='barra_avance <?php echo ($nd['depto']==1 ? "paso_aprobado" : "").($nd['depto']==2 ? "paso_rechazado" : "") ?>'>Autorizado depto</div>
                <?php /*if (NotaHelper::isTestSite()){ ?>
                    <div class='barra_avance <?php echo ($nd['operaciones']==1 ? "paso_aprobado" : "").($nd['operaciones']==2 ? "paso_rechazado" : "") ?>'>Gerencia operaciones</div>
                <?php }*/ ?>
                
				<div class='barra_avance <?php echo ($nd['adquisiciones']==1 ? "paso_aprobado" : "").($nd['adquisiciones']==2 ? "paso_rechazado" : "") ?>'>OC generada</div>
				<div class='barra_avance <?php //echo ($nd['aprobado']==1 ? "paso_aprobado" : "").($nd['adquisiciones']==2 ? "paso_rechazado" : "") ?>'>Calificación</div>
			</div>
		</td>
		<td align='center'>
		<?php   //echo array_key_exists($nd['id_depto'],$ar_maquinas[$user->id]) ? 'si' : 'no';
                if (($nd['empleado']==1 && $nd['capitan']==1 && $nd['jefe']==0 && $nd['depto']==0 && $nd['adquisiciones']==0) 
                    || (array_key_exists($nd['id_depto'],$ar_maquinas[$user->id]) && $nd['jefe']==1 && $nd['depto']==0)){ 
                    $url_nota = JRoute::_('index.php?option=com_nota&view=com_nota&task=detalle_nota&id_nota='.$nd['id'].'&tmpl=component');
                    $icon = 'edit';
                }else{ 
                    $url_nota = JRoute::_('index.php?option=com_nota&view=com_nota&task=reportes.detalle_nota&id_nota='.$nd['id'].'&tmpl=component');
                    $icon = 'article';
                } ?>
                
                <a onclick="SqueezeBox.fromElement(this, 
                            {handler:'iframe', 
                            size: {x: 900, y: 550}, 
                            url:'<?php echo $url_nota ?>',
                            onClose:function(){window.location.reload();} })">
                <img src='/portal/administrator/templates/hathor/images/menu/icon-16-<?php echo $icon ?>.png' /></a>
		</td>
	</tr>
<?php } ?>
</table>
</div>

<?php for ($j=0;$j<(25-$i);$j++) echo "<br>"; ?>
<div class='centrar'>
	<a href='<?php echo JRoute::_('index.php?option=com_nota'); ?>'>
	<div class='boton' id='volver'>
		<img src="/portal/administrator/templates/hathor/images/header/icon-48-revert.png" /><br>
		Volver
	</div>
	</a>
</div>

