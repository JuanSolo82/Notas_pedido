<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHTML::script('adquisiciones.js', 'components/com_nota/assets/js/');
JHTML::stylesheet('nota.css', 'components/com_nota/assets/css/');
JHTML::script('jquery.min.js', 'components/com_nota/assets/js/');
JHTML::script('jquery-ui.min.js', 'components/com_nota/assets/js/');
JHTML::script('nota.js', 'components/com_nota/assets/js/');
JHtml::_('behavior.modal'); 
require_once(JPATH_COMPONENT_SITE.'/assets/helper.php'); 
$opciones = array();
foreach ($this->items as $i)
	$opciones[$i['opcion_oc']] = $i['opcion_oc'];
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<br>

<div class='centrar'>
<div class='barra_nombre' style='width: 95%;'>Ordenes de compras para nota <?php echo $this->id_remitente ?></div>
</div>

<input type="hidden" id="id_remitente" value="<?php echo $this->id_remitente ?>">
<div style="margin: 20px; float: left; width: 100%; font-family: 'Questrial', sans-serif;">
	<input style="float: left; margin-right: 25px; cursor: pointer;" type="checkbox" id="ley_navarino" name="ley_navarino" <?php echo $this->datos_nota['ley_navarino'] ? "checked" : "" ?>>
	<label for="ley_navarino" style="cursor: pointer;"><b>Afecta a Ley Navarino</b></label>
</div>
<br>
<fieldset class="contenido" style="width: 50%; border: 1px solid red;" id="dialogo_anulacion">
<b>Eliminar nota de pedido: </b><input type="text" placeholder="comentario" id='comentario' autocomplete="off"> <button onclick="anular_nota(<?php echo $this->id_remitente ?>)">Eliminar nota</button>
</fieldset>
<input type="hidden" value="<?php echo sizeof($opciones) ?>" id="num_opciones">
<?php 
foreach ($opciones as $opcion){ ?>
<fieldset class="contenido"><legend style="font-size: 13px; width: auto">Nota <?php echo $opcion; ?></legend>
<div style="border: 1px solid grey; height: auto; width: 200px; margin-bottom: 20px; padding: 10px;">
	<b>Proveedor</b><input type="text" id="proveedor<?php echo $opcion ?>" value="<?php echo $this->datos_nota['proveedor'] ? $this->datos_nota['proveedor'] : "" ?>">
	
</div>
<div style="width: 80%; float: left;">
	<table class='tabla_listado' style="width: 97%;">
		<tr>
			<th align="center" width='10%'>Cantidad</th>
			<th align="center" width='30%'>Ítem</th>
			<th align="center" width='20%'>Motivo</th>
			<th align="center">Motivo modificación</th>
			<th align="center">Adjunto</th>
		</tr>
	<?php 
		$j=0;
		foreach ($this->items as $i){
		if (($i['nueva_cantidad']==null || $i['nueva_cantidad']>0) && $i['opcion_oc']==$opcion){
			$j++;
	?>
		<input type="hidden" id="id_item<?php echo $opcion.'_'.$j ?>" value="<?php echo $i['id'] ?>">
		<input type="hidden" id="cantidad_original<?php echo $opcion.'_'.$j ?>" value="<?php echo $i['nueva_cantidad'] ? $i['nueva_cantidad'] : $i['cantidad'] ?>">
		
		<tr>
			<td><input type="number" autocomplete="off" id="cantidad<?php echo $opcion.'_'.$j ?>" name="cantidad<?php echo $opcion.'_'.$j ?>" style="width: 50px;" step='0.1' value="<?php echo $i['nueva_cantidad'] ? $i['nueva_cantidad'] : $i['cantidad'] ?>"></td>
			<td>
				<input type="text" autocomplete="off" id="descripcion_item<?php echo $opcion.'_'.$j ?>" name="descripcion_item<?php echo $opcion.'_'.$j ?>" value="<?php echo $i['item'] ?>">
			</td>
			<td><input type="text" autocomplete="off" size='8' id="motivo<?php echo $opcion.'_'.$j ?>" name="motivo<?php echo $opcion.'_'.$j ?>" value="<?php echo $i['motivo'] ?>"></td>
			<td>
				<select id='tipo_modificacion<?php echo $opcion.'_'.$j ?>' name='tipo_modificacion<?php echo $j ?>'>
					<option value='1'>Reducción por existencia</option>
					<option value='2'>Denegación</option>
				</select>
			</td>
			<td>
			<?php if ($i['adjunto']){ ?> 
				<a href="/portal/media/notas_pedido/adjuntos/<?php echo $this->id_remitente.'/'.$i['adjunto'] ?>" class="modal">
					<img src='/portal/administrator/templates/hathor/images/menu/icon-16-archive.png' />
				</a>
			<?php } ?>
			</td>
		</tr>
		<?php } ?>
	<?php } ?>
	</table>
	<input type="hidden" id="items_orden<?php echo $opcion ?>" value="<?php echo $j ?>">
</div>

<div style="width: 20%; float: left;" id="previa_oc<?php echo $opcion ?>">
	<div class='barra_avance paso_aprobado' style="position: relative; left: 40px; display: <?php echo $this->orden[$opcion] ? "bock" : "none" ?>;" id="generada_oc<?php echo $opcion ?>">Generada OC</div>
	<div class="centrar">
		<a onclick="previa_oc(<?php echo $opcion ?>, <?php echo $j ?>)">
			<div class='boton'><img src='/portal/administrator/templates/hathor/images/header/icon-48-featured.png' /><br>Vista previa OC</div>
		</a>
		<!--<a href="<?php echo JRoute::_('index.php?option=com_nota&task=adquisiciones.generarOrden&id_remitente='.$this->id_remitente); ?>">Test</a>-->
	</div>
</div>

</fieldset><br>


<!-- OC para imprimir (oculto) -->
<div id="previa<?php echo $opcion ?>" style="font-family: 'Questrial', sans-serif; display: none;">
<div class="oc_completa" id="oc_completa<?php echo $opcion ?>">
	<div class="encabezados_oc">
		<b>TRANSBORDADORA AUSTRAL BROOM S.A.</b><br>
		<font size='1'>
			NAVIEROS, TRANSPORTE DE CARGA Y PASAJEROS POR VÍAS DE<br>
			NAVEGACION, USUARIO DE ZONA FRANCA, RESTAURANTES,<br>
			OTROS TIPOS DE HOSPEDAJES TEMPORAL COMO<br>
			CAMPING, ALBERGUES, POSADAS, REFUGIOS Y SIMILARES<br>
			<i>
				<b>Casa Matriz:</b> Juan Williams #06450 - Punta Arenas<br>
				Casilla 1167 - Fono Mesa Central: 728100<br>
				<b>Sucursales: </b>Avda. Bulnes Km. 3.5 Norte - Punta Arenas<br>
				Bahia Catalina S/N - Punta Arenas<br>
				Bahia Chilota S/N - Porvenir<br>
				<b>RUT: 82.074.900-6</b>
			</i>
		</font>
	</div>
	<div class="encabezados_oc">
		<?php if (NOTAHelper::isTestSite()){ ?>
				<img src="/portal/images/logo.png">
		<?php }else{ ?>
				<img src="/var/www/clients/client2/web4/web/portal/images/logo.png">?
		<?php } ?>
	</div>
	<div style="width: 100%; float: left; margin: 10px; font-family: 'Questrial', sans-serif;">
		Por cuenta de Transbordadora Austral Broom S.A.<br>
		Centro de costo: <?php echo $this->datos_nota['depto_costo'] ?><br>
		Solicitado por: <?php echo $this->datos_nota['depto_origen'] ?><br>
		<span id="proveedor_oc<?php echo $opcion ?>"></span>
	</div>
	<div class="centrar">
		<div class="superior">ORDEN DE COMPRA Nº <span></span></div>
	</div>
	<div class="centrar">
		<div class="inferior">Nota de pedido nº <?php echo $this->id_remitente ?></div>
	</div>
	<div style="width: 100%; float: left;" id="contenido_tabla<?php echo $opcion ?>"></div>
	<div class="beneficio" id="beneficio<?php echo $opcion ?>"></div>
	<br>

<div style="float: right;">
	<div class='centrar'>
		__________________________________
	</div>
	<div class="centrar">
		p.p. Transbordadora Austral Broom
	</div>
</div>
<?php 
$url = JRoute::_('index.php?option=com_nota&task=adquisiciones.generar_orden&format=pdf&tmpl=component&id_remitente='.$this->id_remitente.'&opcion='.$opcion); 
?>
	<!--<a onclick="preparar_oc('<?php echo $url ?>', <?php echo $this->id_remitente ?>, <?php echo $opcion ?>, <?php echo $j ?>)">-->
	<a onclick="cargar_pdf(<?php echo $this->id_remitente ?>,0, <?php echo $opcion ?>, <?php echo sizeof($opciones) ?>)">
		<div class='boton'><img src='/portal/administrator/templates/hathor/images/header/icon-48-print.png' /><br>Imprimir</div>
	</a>
	<a>
	</a>
	<!--<input type='button' onclick="cargar_pdf(<?php echo $this->id_remitente ?>)" value="boton">-->
	<br>
</div>

<?php $j=0; ?>
</div>
<?php } ?>

