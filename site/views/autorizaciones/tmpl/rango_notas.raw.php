<?php 
defined('_JEXEC') or die('Restricted access');
$user = JFactory::getUser();
JHtml::_('behavior.modal'); 

?>
<script type="text/javascript" src="/portal/components/com_nota/assets/js/adquisiciones.js?jef=1"></script>
<table class='tabla_listado'>
    <tr>
		<th width='5%'>Nº</th>
		<th width='10%'>Fecha</th>
        <th width='10%'>Origen</th>
		<th>Estado de avance</th>
		<th width='10%'>Revisión</th>
    </tr>
	<?php 
        foreach ($this->notas as $n){ ?>
        <tr>
            <td align='center'><a onclick="exportar_nota(<?php echo $n['id'] ?>)"><?php echo $n['id'] ?></a></td>
            <td align='center'><?php echo NotaHelper::fechamysql($n['fecha'],$modo=1) ?></td>
            <td><?php echo $n['depto_origen']; ?></td>
            <td>
                <div class='centrar'>
                    <div class='barra_avance <?php echo ($n['empleado']==1 ? "paso_aprobado" : "").($n['empleado']==2 ? "paso_rechazado" : "") ?>'>Enviado empleado</div>
                    <div class='barra_avance <?php echo ($n['capitan']==1 ? "paso_aprobado" : "").($n['capitan']==2 ? "paso_rechazado" : "") ?>'>Autorizado capitán</div>
                    <div class='barra_avance <?php echo ($n['jefe']==1 ? "paso_aprobado" : "").($n['jefe']==2 ? "paso_rechazado" : "") ?>'>Autorizado jefe</div>
                    <div class='barra_avance <?php echo ($n['depto']==1 ? "paso_aprobado" : "").($n['depto']==2 ? "paso_rechazado" : "") ?>'>Autorizado depto.</div>
                    <div class='barra_avance <?php echo ($n['adquisiciones']==1 ? "paso_aprobado" : "").($n['adquisiciones']==2 ? "paso_rechazado" : "") ?>'>OC generada</div>
                </div>
            </td>
            <td align='center'>
                <a onclick="SqueezeBox.fromElement(this, 
                            {handler:'iframe', 
                            size: {x: 900, y: 550}, 
                            url:'<?php echo JRoute::_('index.php?option=com_nota&task=autorizaciones.detalle_nota&id_nota='.$n['id'].'&tmpl=component'); ?>',
                            onClose:function(){window.location.reload();
                            } })">
                    <img src='/portal/administrator/templates/hathor/images/menu/icon-16-<?php echo $n['pendiente'] ? 'edit' : 'article' ?>.png' />
                </a>
            </td>
        </tr>
    <?php } ?>
</table>