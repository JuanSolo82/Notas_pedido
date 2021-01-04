<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHTML::stylesheet('nota.css', 'components/com_nota/assets/css/');
JHTML::stylesheet('jquery-ui.css', 'components/com_nota/assets/css/');
JHTML::script('jquery.min.js', 'components/com_nota/assets/js/');
JHTML::script('jquery-ui.min.js', 'components/com_nota/assets/js/');
//JHTML::script('nota.js', 'components/com_nota/assets/js/');
JHTML::script('jquery.typeahead.min.js', 'components/com_nota/assets/js/');

//echo JRequest::checkToken('get')."?";
$user = JFactory::getUser();
?>
<script type="text/javascript" src="/portal/components/com_nota/assets/js/nota.js?ges=128"></script>
<div class='fila_completa centrar' style='margin-bottom: 20px;'>
	<div class='barra_nombre' style='width: 90%;'>Nueva nota</div>
</div>

<div class='centrar'>
	<div class='fila_completa bordear centrar' style='width: 90%;'></div>
</div>
<form id='form_nota' name='form_nota' action="<?php echo JRoute::_('index.php?option=com_nota&view=com_nota&task=nota_guardada'); ?>" class="form-validate" method='post' enctype="multipart/form-data">
	<input type='hidden' name='tipo' id='tipo' value='<?php echo $this->datos_user['tipo'] ?>'>
	<input type='hidden' name='id_user' id='id_user' value='<?php echo $this->datos_user['id'] ?>'>
	<?php if ($user->authorise('tripulante', 'com_nota') && !($user->authorise("core.admin", "com_nota"))){ ?>
		<div class='centrar'>
			<div class='fila_completa bordear centrar' style='width: 90%;'>
				<div class="col-3 titulo_item">Nombre</div>
				<div class="col-7">
					<input type="text" id="nombre_tripulante" name="nombre_tripulante" autocomplete="off">
				</div>
			</div>
		</div>
	<?php } ?>
	<div class='centrar'>
	<div class='fila_completa bordear centrar' style='width: 90%;'>
		<div class="col-3 titulo_item">Departamento destino</div>
			<div class="col-7">
	<?php if ($user->authorise('tripulante.maquina', 'com_nota') && !($user->authorise("core.admin", "com_nota"))){ ?>
			Departamento de mantención
	<?php }else{ ?>
				<select id="depto_destino" name="depto_destino">
				<?php foreach ($this->deptos as $d){ ?>
					<?php if ($d['id']!=$this->datos_user['id_depto']){ ?>
						<option value='<?php echo $d['id'] ?>' <?php echo ($d['id']==4 ? 'selected' : '') ?>><?php echo $d['nombre'] ?></option>
					<?php } ?>
					
				<?php } ?>
				</select>
	<?php } ?>
	</div>
		</div>
			</div>
	<?php 
	if ($user->authorise('jefe.depto', 'com_nota') || $user->authorise('capitan.sin_jefe', 'com_nota') || 
				$user->authorise('jefe.instalacion', 'com_nota') || $user->authorise('adquisiciones.jefe', 'com_nota') || $user->authorise('centro_costo', 'com_nota')){ ?>
		<div class='centrar'>
			<div class='fila_completa bordear centrar' style='width: 90%;'>
				<div class="col-3 titulo_item">Centro de costo</div>
				<div class="col-7">
					<select id='centro_costo' name='centro_costo'>
					<?php foreach ($this->centros_costos as $c){ ?>
						<?php //if ($this->datos_user['id_area']==$c['id_area']){ ?>
							<option value='<?php echo $c['id'] ?>' <?php echo ($c['id']==$this->datos_user['id_depto'] ? 'selected' : '') ?>><?php echo $c['nombre'] ?></option>
						<?php //} ?>
					<?php } ?>
					</select>
				</div>
			</div>
		</div>
	<?php } ?>
	<?php if ($user->authorise('depto_compra', 'com_nota')){ ?>
	<div class='centrar'>
		<div class='fila_completa bordear' style='width: 90%;'>
			<div class="col-3 titulo_item">Responsable compra</div>
			<div class="col-7">
				<select name="id_depto_compra" id="id_depto_compra">
					<option value='4'>Adquisiciones</option>
					<option value='0'>Depto. destino</option>
					<option value='<?php echo $this->datos_user['id_depto'] ?>'><?php echo $this->datos_user['departamento'] ?></option>
				</select>
			</div>
		</div>
	</div>
	<?php } ?>

	<div class='centrar'>
		<div class='fila_completa bordear' style='width: 90%;'>
			<div class="col-3 titulo_item">Prioridad</div>
			<div class="col-7">
				<select name="prioridad" id="prioridad">
				<?php foreach ($this->prioridad as $p){ ?>
					<option value='<?php echo $p['id'] ?>' <?php echo $p['id']==3 ? "selected" : "" ?>><?php echo $p['descripcion'].' ('.$p['desde'].' a '.$p['hasta'].' días)' ?></option>
				<?php } ?>
				</select>
			</div>
		</div>
	</div>
	<?php if ($user->authorise('capitan.jefe', 'com_nota') || $user->authorise('capitan.sin_jefe', 'com_nota') || $user->authorise('jefe.depto', 'com_nota') 
				|| $user->authorise('jefe.delgada', 'com_nota') || $user->authorise('jefe.natales', 'com_nota') || $user->authorise('jefe.porvenir', 'com_nota')
				|| $user->authorise('jefe.punta_arenas', 'com_nota') || $user->authorise('adquisiciones.jefe', 'com_nota') || $user->authorise('procedimientos', 'com_nota') 
				|| ($user->authorise('empleado.depto', 'com_nota') && !$user->authorise('tripulante', 'com_nota'))){ ?>
		<div class='centrar'>
			<div class='fila_completa bordear' style='width: 90%;'>
				<div class="col-3 titulo_item">Tipo de pedido</div>
				<div class="col-3">
					<select name="tipo_pedido" id="tipo_pedido">
						<option value='1'>Producto</option>
						<option value='2'>Servicio</option>
					</select>
				</div>
			<?php if ($user->authorise('core.admin', 'com_nota')){ ?>
				<div class="col-4" id="gasto_inversion">
				<div class="col-4">
					<b>Inversión o gasto: </b> 
				</div>
				<div class="col-5">
					<select name="tipo_gasto" id="tipo_gasto">
						<option value='0'>Sin calificar</option>
						<option value='1'>Inversión</option>
						<option value='2'>Gasto</option>
					</select>
				</div>
				</div>
			<?php } ?>
				
			</div>
		</div>
		<?php if ($user->authorise('jefe.depto','com_nota') || $user->authorise('adquisiciones.jefe','com_nota')){ ?>
		<div class='centrar'>
			<div class='fila_completa bordear' style='width: 90%;'>
				<div class="col-3 titulo_item">Proveedor (opcional)</div>
				<div class="col-4">
					<input type='text' id='proveedor_escogido' name='proveedor_escogido' size='40'
						 onkeypress="cargar_proveedor(this.value)" placeholder="Nombre proveedor">
					<div id='proveedor'></div>
					<input type="text" name="rut_proveedor" id="rut_proveedor" placeholder="Rut">
					<input type="text" name="giro_proveedor" id="giro_proveedor" placeholder="Giro">
				</div>
				<div class="col-3" id='rut_texto'></div>
			</div>
		</div>
		<div class='centrar'>
			<div class='fila_completa bordear' style='width: 90%;'>
				<div class="col-3 titulo_item">Cotización (opcional)</div>
				<div class="col-7">
					<input type="text" name="cotizacion" id="cotizacion">
				</div>
			</div>
		</div>
		<?php } ?>
	<?php } ?>
	
	<div class='fila_vacia'></div>

	<table class='adminlist'>
		<tr class='encabezado_tabla'>
			<th width='8%'>Cantidad</th>
			<th width='25%'>Descripción</th>
			<th width='20%'>Motivo</th>
		<?php if ($user->authorise('adquisiciones.jefe','com_nota')){ ?>
			<th width='12%'>Valor unitario</th>
		<?php } ?>
			<th width='10%'>Distribución</th>
			<th width='25%'>Adjunto</th>
		</tr>
	<?php for ($i=1;$i<=15;$i++){ ?>
		<tr>
			<td><input class='entrada' id='cantidad<?php echo $i ?>' name='cantidad<?php echo $i ?>' type='number' size='2' required type="number" min="0" step=".1" style='width: 70px;'></td>
			<td>
				<input class='entrada' id='descripcion<?php echo $i ?>' name='descripcion<?php echo $i ?>' type='text' style='width: 90%;' autocomplete='on'
				<?php /*if ($user->username=='mjaman'){ ?>
					onkeypress="buscar_item(<?php echo $i ?>,this.value,<?php echo $this->datos_user['id'] ?>)"		
				<?php }*/ ?>
				>
					<div id='items<?php echo $i ?>'></div>
			</td>
			<td><input class='entrada' id='motivo<?php echo $i ?>' name='motivo<?php echo $i ?>' type='text' style='width: 90%;'></td>
			<?php if ($user->authorise('adquisiciones.jefe','com_nota')){ ?>
				<td><input class='entrada' id='valor<?php echo $i ?>' name='valor<?php echo $i ?>' type='text' style='width: 90%;'></td>
			<?php } ?>
			<td>
				<select id='opcion<?php echo $i ?>' name='opcion<?php echo $i ?>'>
					<option value='1'>1</option>
					<option value='2'>2</option>
					<option value='3'>3</option>
					<option value='4'>4</option>
					<option value='5'>5</option>
					<option value='6'>6</option>
				</select>
			</td>
			<td><input type='file' id='archivo<?php echo $i ?>' name='archivo<?php echo $i ?>'></td>
		</tr>
	<?php } ?>
	</table><br>
	<input type='hidden' id='control_ingreso' name='control_ingreso' value='0'>
	<?php echo JHtml::_('form.token'); ?>
</form>
<div class='centrar'>
	<div class='boton' id='enviar_nota' onclick="revisar_formulario()">
		<img src="/portal/administrator/templates/hathor/images/header/icon-48-upload.png" /><br>
		Enviar nota
	</div>
	<a href='<?php echo JRoute::_('index.php?option=com_nota'); ?>'>
	<div class='boton' id='volver'>
		<img src="/portal/administrator/templates/hathor/images/header/icon-48-revert.png" /><br>
		Volver
	</div>
	</a>
</div>

<div id="valores_formulario" title="Atención" style="display: none;">
 	<p>Debe ingresar valores al formulario</p>
</div>
<div id="distinto_cero" title="Atención" style="display: none;">
 	<p>Debe ingresar valores mayores que cero</p>
</div>
<div id="nombre_vacio" title="Atención" style="display: none;">
 	<p>Debe ingresar su nombre</p>
</div>
