<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
//JHTML::script('adquisiciones.js', 'components/com_nota/assets/js/');
JHTML::stylesheet('nota.css', 'components/com_nota/assets/css/');
JHTML::script('jquery.min.js', 'components/com_nota/assets/js/');
JHTML::script('jquery-ui.min.js', 'components/com_nota/assets/js/');
//JHTML::script('nota.js', 'components/com_nota/assets/js/');
JHtml::_('behavior.modal'); 
require_once(JPATH_COMPONENT_SITE.'/assets/helper.php');
$opciones = array();
foreach ($this->items as $i)
	$opciones[$i['opcion_oc']] = $i['opcion_oc'];

$user = JFactory::getUser();
?>
<script type="text/javascript" src="/portal/components/com_nota/assets/js/nota.js?Lor=010"></script>
<script type="text/javascript" src="/portal/components/com_nota/assets/js/adquisiciones.js?tim=5544"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<br>
<input type="hidden" id="sitio_pruebas" value="<?php echo NotaHelper::isTestSite() ?>">
<div class='centrar'>
<div class='barra_nombre' style='width: 95%;'>Ordenes de compras para nota <?php echo $this->id_remitente ?></div>
</div>

<input type="hidden" id="id_remitente" value="<?php echo $this->id_remitente ?>">
<input type="hidden" id="id_tipo_pedido" value="<?php echo $this->datos_nota['id_tipo_pedido'] ?>">
<div style="margin: 20px; float: left; width: 100%; font-family: 'Questrial', sans-serif;">
	<input style="float: left; margin-right: 25px; cursor: pointer;" type="checkbox" id="ley_navarino" name="ley_navarino" <?php echo $this->datos_nota['ley_navarino'] ? "checked" : "" ?> onchange="actualizar_ln(<?php echo $this->id_remitente ?>)">
	<label for="ley_navarino" style="cursor: pointer;"><b>Afecta a Ley Navarino</b></label>
</div>
<br>
<div style="float: left; width: 45%; margin: 10px;">
	<fieldset class="contenido" style="width: 90%; border: 1px solid red;" id="dialogo_anulacion">
		<b>Eliminar nota de pedido: </b><input type="text" placeholder="comentario" id='comentario' autocomplete="off">
		<button onclick="anular_nota(<?php echo $this->id_remitente ?>)">Eliminar nota</button>
	</fieldset>
</div>
<?php if (NotaHelper::isTestSite() && sizeof($opciones)>1){ ?>
<div style="float: left; width: 45%; margin: 10px;">
	<button class="boton_simple" style="width: 150px;" onclick="emision_masiva(1,<?php echo sizeof($opciones) ?>)">Emitir todo</button>
</div>
<?php } ?>

<input type="hidden" value="<?php echo sizeof($opciones) ?>" id="num_opciones">
<?php 
foreach ($opciones as $opcion){ ?>
<fieldset class="contenido"><legend style="font-size: 13px; width: auto">Nota <?php echo $opcion; ?></legend>

<?php
$proveedor = explode('_',$this->datos_nota['proveedor']);
?>
<div style="border: 1px solid grey; height: auto; width: 400px; margin-bottom: 20px; padding: 10px;">
	<div style="float: left; width: 35%;"><b>Proveedor</b></div>
	<input type="text" size="30" autocomplete="off" onkeypress="cargar_proveedor(this.value, <?php echo $opcion ?>)" id="proveedor_escogido<?php echo $opcion ?>" value="<?php echo sizeof($this->proveedor) ? ucwords(strtolower($this->proveedor['RazonSocial'])) : "" ?>"><br>
	<div id='proveedor<?php echo $opcion ?>'></div>
	<div style="float: left; width: 35%;"><b style="width: 25%;">Rut</b></div>
	<input type="text" size="30" id="rut_proveedor<?php echo $opcion ?>" value="<?php echo sizeof($this->proveedor) ? $this->proveedor['rut'] : "" ?>"><br>
	<div style="float: left; width: 35%;"><b style="width: 25%;">Giro</b></div>
	<input type="text" size="30" id="giro_proveedor<?php echo $opcion ?>" value="<?php echo sizeof($this->proveedor) ? ucwords(strtolower(htmlentities($this->proveedor['giro']))) : "" ?>">
	<div style="float: left; width: 35%;"><b style="width: 25%;">Cotización (opcional)</b></div>
	<input type="text" size="30" autocomplete="off" id="cotizacion<?php echo $opcion ?>" value="<?php echo $this->datos_nota['cotizacion'] ? $this->datos_nota['cotizacion'] : "" ?>">
</div>
<div style="width: 80%; float: left;">
	<table class='tabla_listado' style="width: 97%;">
		<tr>
			<th align="center" width='10%'>Cantidad</th>
			<th align="center" width='30%'>Ítem</th>
			<th align="center" width='20%'>Motivo</th>
			<th align="center">Motivo modificación</th>
			<th align="center">Adjunto</th>
			<th align="center">Valor unitario</th>
			<th align="center">Subtotal</th>
		</tr>
	<?php 
		$items_orden = 0;
		foreach ($this->items as $i){
			if (($i['nueva_cantidad']==null || $i['nueva_cantidad']>0) && $i['opcion_oc']==$opcion)
				$items_orden++;
		}
		$j=0;
		$total = 0;
		foreach ($this->items as $i){
		if (($i['nueva_cantidad']==null || $i['nueva_cantidad']>0) && $i['opcion_oc']==$opcion){
			$j++;
		if ($i['valor'])
			$total += $i['cantidad']*$i['valor'];
	?>
		<input type="hidden" id="id_item<?php echo $opcion.'_'.$j ?>" value="<?php echo $i['id'] ?>">
		<input type="hidden" id="cantidad_original<?php echo $opcion.'_'.$j ?>" value="<?php echo $i['nueva_cantidad'] ? $i['nueva_cantidad'] : $i['cantidad'] ?>">
		<input type="hidden" id="valor<?php echo $opcion.'_'.$j ?>" value='<?php echo number_format($i['valor'],0,'','.') ?>'>
		<input type="hidden" id="subtotal<?php echo $opcion.'_'.$j ?>" value='<?php echo number_format($i['cantidad']*$i['valor'],0,'','.') ?>'>
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
				<td>
					<input type="number" min="0" step="1" id="valor_unitario<?php echo $opcion.'_'.$j ?>" onchange="actualiza_parcial('<?php echo $opcion.'_'.$j ?>')" type="text" style="width: 60px" value="<?php echo $i['valor'] ? $i['valor'] : '' ?>">
					<div style="display: none;" id="parcial<?php echo $opcion.'_'.$j ?>"><?php echo $i['valor'] ? number_format($i['valor'],0,'','.') : '' ?></div>
				</td>
				<td id="parcial_texto<?php echo $opcion.'_'.$j ?>"><?php echo $i['valor'] ? number_format($i['cantidad']*$i['valor'],0,'','.') : '' ?></td>
				<input type="hidden" id="valor_numerico<?php echo $opcion.'_'.$j ?>" value="<?php echo $i['valor'] ?>">
				<input type="hidden" id="subtotal_numerico<?php echo $opcion.'_'.$j ?>" value="<?php echo $i['cantidad']*$i['valor'] ?>">
		</tr>
		<?php } ?>
	<?php } ?>
		<?php if ($total){ ?>
			<tr>
				<td align='right' colspan='6'><b>Total</b></td>
				<td id="total"><b><?php echo number_format($total,0,'','.') ?></b></td>
			</tr>
			<input type="hidden" id="total<?php echo $opcion ?>" value="<?php echo $total ? number_format($total,0,'','.') : '' ?>">
			<input type="hidden" id="total_numerico<?php echo $opcion ?>" value="<?php echo $total ?>">
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
<?php if (NotaHelper::isTestSite()){ ?>
	<a href="/portal/media/notas_pedido/Orden_compra.pdf" target="_blank">OC test</a>
<?php } ?>

<a onclick="cargar_pdf(<?php echo $this->id_remitente ?>,0, <?php echo $opcion ?>, <?php echo sizeof($opciones) ?>)">
	<div class='boton'><img src='/portal/administrator/templates/hathor/images/header/icon-48-print.png' /><br>Imprimir</div>
</a>
<br>
</div>

<?php $j=0; ?>
</div>
<?php } ?>

<div id="proveedor_vacio" title="Atención" style="display: none;">
 	<p>Debe ingresar todos los datos de proveedor</p>
</div>

