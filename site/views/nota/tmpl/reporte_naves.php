<?php
defined('_JEXEC') or die('Restricted access');
?>

<table class='tabla_listado'>
	<tr>
		<th>NP</th>
		<th>Nave</th>
		<th>Fecha</th>
		<th>Cantidad</th>
        <th>Descripción</th>
        <th>Destino</th>
        <th>Aprobado capitán</th>
        <th>Aprobado jefe encargado</th>
        <th>Aprobado departamento destino</th>
        <th>Aprobado Adquisiciones</th>
        <th>Orden de compra</th>
        <th>Fecha orden de compra</th>
    </tr>
<?php 
    $array_aprobaciones = array(0 => '', 1 => 'si', 2 => 'no');
    foreach ($this->reporte as $r){ 
    ?>
    <tr>
        <td><?php echo $r['id_remitente'] ?></td>
        <td><?php echo $r['nave'] ?></td>
        <td><?php echo NotaHelper::fechamysql($r['fecha_nota']) ?></td>
        <td><?php echo $r['nueva_cantidad'] ? $r['nueva_cantidad'] : $r['cantidad'] ?></td>
        <td><?php echo $r['item'] ?></td>
        <td><?php echo $r['depto_destino'] ?></td>
        <td><?php echo $array_aprobaciones[$r['capitan']] ?></td>
        <td><?php echo $array_aprobaciones[$r['jefe']] ?></td>
        <td><?php echo $array_aprobaciones[$r['depto']] ?></td>
        <td><?php echo $array_aprobaciones[$r['emision_oc']] ?></td>
        <td><?php echo $r['orden_compra'] ? $r['orden_compra'] : '-' ?></td>
        <td><?php echo NotaHelper::fechamysql($r['fecha_oc']) ?></td>
    </tr>
<?php } ?>
</table> 