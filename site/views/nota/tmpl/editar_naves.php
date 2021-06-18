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
?>
<script type="text/javascript" src="/portal/components/com_nota/assets/js/nota.js?maco=46"></script>
<input type="hidden" id="vista" value="<?php echo $this->layout; ?>">

<div class='fila_completa centrar' style='margin-bottom: 20px;'>
	<div class='barra_nombre' style='width: 90%;'>Editar naves</div>
</div>
<br>
<table class='tabla_listado'>
	<tr>
		<th width="15%">Nave</th>
		<th width="20%">Régimen especial actual</th>
		<th>Período programado</th>
		<th width="5%" align="center">Editar</th>
	</tr>

<?php foreach ($this->naves as $n){ ?>
	<tr>
		<td><?php echo $n['nave'] ?></td>
		<td>
			<span id="estado<?php echo $n['id'] ?>">
				<?php echo $n['ley_navarino'] 
					? "<img src='/portal/administrator/templates/hathor/images/menu/icon-16-checkin.png' />" 
					: "<img src='/portal/administrator/templates/hathor/images/menu/icon-16-delete.png' />" ?>
			</span>
		</td>
		<td>
			<span id="vigencia<?php echo $n['id'] ?>">
				<?php echo $n['id_vigencia'] 
					? '<b>'.($n['navarino_programado'] ? 'Régimen especial ' : 'Régimen general ' ).'</b> desde '.NotaHelper::fechamysql($n['inicio']).' hasta '.NotaHelper::fechamysql($n['fin']) 
					: 'Indefinido' ?>
			</span>
			<div id="fechas<?php echo $n['id'] ?>" style="display: none">
				<select id="navarino_actual<?php echo $n['id'] ?>">
					<option value='1' <?php echo $n['ley_navarino'] ? 'selected' : '' ?>>Régimen especial</option>
					<option value='0' <?php echo !$n['ley_navarino'] ? 'selected' : '' ?>>Régimen general</option>
				</select>&nbsp;
				Desde 
				<input type="text" onclick="definir_fechas(<?php echo $n['id'] ?>,1)" id="desde<?php echo $n['id'] ?>" size="10" autocomplete="off"> 
				hasta <input type="text" onclick="definir_fechas(<?php echo $n['id'] ?>)" id="hasta<?php echo $n['id'] ?>" size="10" autocomplete="off">
				<input type="button" value="cancelar" id="cancelar<?php echo $n['id'] ?>" onclick="ocultar_ediciones(<?php echo $n['id'] ?>)" style="cursor: pointer; float: right;">
			</div>
		</td>
		<td>
			<a onclick="editar_regimen(<?php echo $n['id'] ?>)" id="editar_regimen<?php echo $n['id'] ?>">
			<img src='/portal/administrator/templates/hathor/images/menu/icon-16-edit.png' />
			</a>
			<a onclick="guardar_regimen(<?php echo $n['id'] ?>)" id="guardar_regimen<?php echo $n['id'] ?>" style="display: none;">
			<img src='/portal/administrator/templates/hathor/images/menu/icon-16-save.png' />
			</a>
		</td>
	</tr>
<?php } ?>
</table>

<div class='centrar'>
	<a href='<?php echo JRoute::_('index.php?option=com_nota'); ?>'>
	<div class='boton' id='volver'>
		<img src="/portal/administrator/templates/hathor/images/header/icon-48-revert.png" /><br>
		Volver
	</div>
	</a>
</div>

