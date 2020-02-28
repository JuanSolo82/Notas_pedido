<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHTML::stylesheet('bootstrap.css', 'components/com_nota/assets/bootstrap/css/');
JHTML::stylesheet('nota.css', 'components/com_nota/assets/css/');
JHTML::script('jquery.min.js', 'components/com_nota/assets/js/');
JHTML::script('jquery-ui.min.js', 'components/com_nota/assets/js/');
JHTML::script('adquisiciones.js', 'components/com_nota/assets/js/');
JHTML::script('bootstrap.js', 'components/com_nota/assets/bootstrap/js/');
JHTML::script('jquery.PrintArea.js', 'components/com_nota/assets/js/');
JHtml::_('behavior.modal'); 
require_once(JPATH_COMPONENT_SITE.'/assets/helper.php');

?>
<link href="https://fonts.googleapis.com/css?family=Questrial" rel="stylesheet">
<style>
<?php echo $this->estilos(); ?>
</style>

<div class="caja_orden">
<table>
	<tr>
		<td class="encabezados_oc" width="50%">
			<b>TRANSBORDADORA AUSTRAL BROOM S.A.</b><br>
			<font size="1">
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
		</td>
		<td class="encabezados_oc" width="50%" style="padding-left: 50px;">
			<?php if (NOTAHelper::isTestSite()){ ?>
					<img src="/var/www/portal/images/logo.png">
			<?php }else{ ?>
					<img src="/var/www/clients/client2/web4/web/portal/images/logo.png">
			<?php } ?>
		</td>
	</tr>
</table><br>
<div class="datos_entrega">
		Por cuenta de Transbordadora Austral Broom S.A.<br>
		Centro de costo: <?php echo $this->datos_nota['depto_costo'] ?><br>
		Solicitado por: <?php echo $this->datos_nota['depto_origen'] ?><br>
		<?php echo $this->datos_orden['proveedor'] ? "Proveedor: ".$this->datos_orden['proveedor'] : "" ?>
	</div>
<div class="superior">
	ORDEN DE COMPRA <?php echo $this->datos_orden['id'] ?>
</div>
<div class="inferior">
	Nota de pedido nº <?php echo $this->datos_nota['id_remitente'] ?>
</div>

<table class="tabla_items" border=1 cellspacing=0 cellpadding=2>
	<tr>
		<td width="5%"><b>#</b></td>
		<td width="5%"><b>Cantidad</b></td>
		<td width="40%"><b>Item</b></td>
		<td width="40%"><b>Observaciones</b></td>
	</tr>
<?php 
$j=1;
foreach ($this->items as $i){ ?>
	<tr>
		<td><?php echo $j++ ?></td>
		<td><?php echo $i['cantidad'] ?></td>
		<td><?php echo $i['item'] ?></td>
		<td><?php echo $i['motivo'] ?></td>
	</tr>
<?php } ?>
</table>
<br><br>
<div class="beneficio">
<?php if ($this->datos_nota['ley_navarino'] && $this->datos_nota['id_tipo_pedido']==1){ ?>
	Facturar con documento especial de venta (ley 18.392) a Transbordadora Austral Broom S.A., rut 82.074.900-6, dirección Manuel Señoret #831, Porvenir, exento de IVA
<?php }else{ ?>
	Facturar a Transbordadora Austral Broom S.A., rut 82.074.900-6, dirección Juan Williams #06450, Punta Arenas, afecto a IVA
<?php } ?>
</div>

<div style="position: absolute; bottom: 20px; width: 40%; left: 400px;">
	<hr/><br>
	<p style="position: relative; left: 20px;">p.p. Transbordadora Austral Broom</p><br>
	<p>Punta Arenas, <?php echo $this->datos_nota['id_remitente']==22916 ? "22 de mayo de 2019" : $this->fecha_creacion ?></p>
</div>

</div>

