<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
require_once(JPATH_COMPONENT_SITE.'/assets/helper.php');
JHTML::stylesheet('nota.css', 'components/com_nota/assets/css/');
JHTML::script('jquery.min.js', 'components/com_nota/assets/js/');
JHTML::script('jquery-ui.min.js', 'components/com_nota/assets/js/');
JHTML::script('nota.js', 'components/com_nota/assets/js/');
JHtml::_('behavior.modal'); 
$f = explode('-', $this->detalle_nota['fecha']);
?>
<input type="hidden" id="id_remitente" value="<?php echo $this->id_remitente ?>">
<br>
<div class='fila_completa centrar' style='margin-bottom: 20px;'>
	<div class='barra_nombre' style='width: 90%;'>Detalle de nota de pedido <?php echo $this->id_remitente ?></div>
</div>

<div class='centrar'>
	<div class='fila_completa bordear centrar' style='width: 90%;'></div>
</div>

<div class='centrar'>
	<div class='fila_completa bordear centrar' style='width: 90%;'>
		<div class="col-3 titulo_item">Nombre remitente</div>
		<div class="col-7"><?php echo $this->detalle_nota['nombre_remitente'] ? $this->detalle_nota['nombre_remitente'].'.' : $this->detalle_nota['nombre_usuario']; ?></div>
	</div>
</div>
<div class='centrar'>
	<div class='fila_completa bordear centrar' style='width: 90%;'>
		<div class="col-3 titulo_item">Origen</div>
		<div class="col-7"><?php echo $this->detalle_nota['depto_origen']; ?></div>
	</div>
</div>
<div class='centrar'>
	<div class='fila_completa bordear centrar' style='width: 90%;'>
		<div class="col-3 titulo_item">Fecha de creación</div>
		<div class="col-7"><?php echo $f[2].'-'.$f[1].'-'.$f[0]; ?></div>
	</div>
</div>
<div class='centrar'>
	<div class='fila_completa bordear centrar' style='width: 90%;'>
		<div class="col-3 titulo_item">Departamento destino</div>
		<div class="col-7"><?php echo $this->detalle_nota['depto_destino'] ?></div>
	</div>
</div>
<div class='centrar'>
	<div class='fila_completa bordear centrar' style='width: 90%;'>
		<div class="col-3 titulo_item">Prioridad</div>
		<div class="col-7"><?php echo $this->detalle_nota['prioridad']; ?></div>
	</div>
</div>
<div class='fila_vacia'></div>

<table style="width: 50%">
	<tr><td style="padding: 8px;">Símbolo <img style='' src='/portal/administrator/templates/hathor/images/menu/icon-16-deny.png' /> indica ítem 'Eliminado'</td></tr>
</table>
<div class='centrar' style="margin-bottom: 25px; margin-top: 10px">
	<table class='tabla_listado'>
		<tr>
			<th width='15%'>Cantidad pedida</th>
			<th width='40%'>Item</th>
			<th width='30%'>Motivo</th>
			<th width='15%'>Adjunto</th>
		</tr>
	<?php foreach ($this->items as $i){ 
		$cantidad = ($i['id_nueva_cantidad'] && $i['id_tipoModificacion']<4) ? $i['nueva_cantidad'] : $i['cantidad'];
		?>
		<tr>
			<td align='center'><?php echo $cantidad ? $cantidad : "<img style='float:left' src='/portal/administrator/templates/hathor/images/menu/icon-16-deny.png' />".$cantidad ?></td>
			<td><?php echo $i['item'] ?></td>
			<td><?php echo $i['motivo'] ?></td>
			<td align='center'>
				<?php if ($i['adjunto']){ ?>
					<a href="/portal/media/notas_pedido/adjuntos/<?php echo $this->detalle_nota['id_remitente'].'/'.$i['adjunto'] ?>" 
						class="modal">
						<img src='/portal/administrator/templates/hathor/images/menu/icon-16-archive.png' />
					</a>
				<?php } ?>
			</td>
		</tr>
	<?php } ?>
	</table>
</div><br><br>

<div class='col-5'>
<table class="tabla_listado" style="margin: 5px;">
	<tr>
		<th colspan='4'>Etapas</th>
	</tr>
	<tr>
		<th width="25%">Fecha</th>
		<th>Usuario</th>
		<th>Estado</th>
		<th>Observación</th>
	</tr>
<?php foreach ($this->etapas as $e){ ?>
	<tr>
		<td><?php echo NotaHelper::fechamysql($e['fecha']) ?></td>
		<td><?php echo $e['nombre_tramitador'] ? $e['nombre_tramitador'] : $e['nombre_usuario'] ?></td>
		<td><?php echo $e['terminado']==2 ? "Pedido incompleto" : "Aprobado" ?></td>
		<td><?php echo $e['motivo'] ?></td>
	</tr>
<?php } ?>
</table>
</div>

<div class='col-5'>
<h3>Calificaciones</h3>
<table class="tabla_listado" style="margin: 5px;">
	<tr>
		<th>Fecha</th>
		<th>Elementos faltantes</th>
	</tr>
	<?php 
		$faltantes=0;
		foreach ($this->anotaciones as $a){ 
		$f = explode(' ', $a['fecha']);
		?>
	<tr>
		<td><?php echo NotaHelper::fechamysql($f[0]) ?></td>
		<td>
			<ul>
		<?php foreach ($this->items as $i){
			if (sizeof($i['modificacion'])){ 
				$faltantes++;
				foreach ($i['modificacion'] as $m){ 
					if ($m['fecha_modificacion']==$f[0]){ ?>
						<li><?php echo $i['item']; ?></li>
					<?php } ?>
				<?php } ?>
			<?php } 
			} ?>
			</ul>
			<?php echo !$faltantes ? "Recibido ok" : "" ?>
		</td>
	</tr>
	<?php } ?>
</table>
</div>

