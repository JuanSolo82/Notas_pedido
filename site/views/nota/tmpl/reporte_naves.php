<?php
defined('_JEXEC') or die('Restricted access');
JHTML::script('jquery.min.js', 'components/com_nota/assets/js/');
JHTML::script('jquery-ui.min.js', 'components/com_nota/assets/js/');

if (!sizeof($this->reporte)){
    echo "<h3>No existen registros con los parámetros dados</h3>";
}else{ 
    /*header("Pragma: public");
    header("Expires: 0");
    $filename = "reporte.xls";
    header("Content-type: application/x-msdownload");
    header("Content-Disposition: attachment; filename=$filename");
    header("Pragma: no-cache");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");*/
    ?>
<!--<meta http-equiv="content-type" content="text/plain; charset=UTF-8"/>
<meta charset="utf-8">-->
<meta charset="iso-8859-1">
<script>
function exportar(){
    var table= document.getElementById('dvData');
    var html = table.outerHTML;
    //window.open('data:application/vnd.ms-excel,' + encodeURIComponent($('#dvData').html()));
    window.open('data:application/vnd.ms-excel,' + escape(html));
}
</script>

<input type="button" value="Exportar" id="exportar" onclick="exportar()">
<div id="dvData" style="font-size: smaller;">
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
        <td><?php echo $r['orden_compra'] ?></td>
        <td><?php echo NotaHelper::fechamysql($r['fecha_oc']) ?></td>
    </tr>
<?php } ?>
</table>
</div>
<?php } ?>

