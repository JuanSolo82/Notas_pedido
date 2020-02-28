<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHTML::stylesheet('bootstrap.css', 'components/com_nota/assets/bootstrap/css/');
JHTML::stylesheet('nota.css', 'components/com_nota/assets/css/');
JHTML::script('jquery.min.js', 'components/com_nota/assets/js/');
JHTML::script('jquery-ui.min.js', 'components/com_nota/assets/js/');
JHTML::script('nota.js', 'components/com_nota/assets/js/');
JHTML::script('bootstrap.js', 'components/com_nota/assets/bootstrap/js/');
?>
<br>
<div class='centrar'>
<div class='barra_nombre' style='width: 100%;'>Usuarios encontrados</div>
</div>
<table class='adminlist'>
    <tr class='encabezado_tabla'>
        <th width='5%'>Id</th>
        <th width='35%'>Nombre</th>
        <th width='35%'>Departamento</th>
        <th width='15%'>Nivel</th>
        <th width='10%'>Edición</th>
    </tr>
<?php foreach ($this->nombres as $n){ ?>
    <tr style="height: 30px;">
        <td><?php echo $n['id'] ?></td>
        <td style="vertical-align: middle;"><?php echo $n['name'] ?></td>
        <td style="vertical-align: middle;">
            <div id="depto_actual_texto<?php echo $n['id'] ?>"><?php echo $n['id_depto'] ? $n['departamento'] : "No asignado" ?></div>
            <select name='departamento<?php echo $n['id'] ?>' id='departamento<?php echo $n['id'] ?>' style="display: none">
                <option>Seleccione departamento</option>
            <?php foreach ($this->centros as $c){ ?>
                <option value="<?php echo $c['id'] ?>" <?php echo $c['id']==$n['id_depto'] ? 'selected' : '' ?>><?php echo $c['nombre'] ?></option>
            <?php } ?>
            </select>
        </td>
        <td style="vertical-align: middle;">
            <div id="nivel_actual_texto<?php echo $n['id'] ?>"><?php echo $n['nivel'] ? $n['nivel'] : 'No asignado' ?></div>
            <select id="nivel<?php echo $n['id'] ?>" style="display: none">
                <option value='0'>No asignado</option>
                <?php foreach ($this->niveles as $l){ ?>
                    <option value="<?php echo $l['id'] ?>" <?php echo $l['id']==$n['id_nivel'] ? 'selected' : '' ?>><?php echo $l['nivel'] ?></option>
                <?php } ?>
            </select>
        </td>
        <td style="vertical-align: middle;" align="center">
            <a id='editar<?php echo $n['id'] ?>' onclick="ocultar_mostrar(<?php echo $n['id'] ?>)"><img src='/portal/administrator/templates/hathor/images/menu/icon-16-edit.png' /></a>
            <a id='guardar_edicion<?php echo $n['id'] ?>' onclick="asignar(<?php echo $n['id'] ?>, <?php echo $n['id_depto'] ? $n['id_depto'] : 0 ?>)" style="display: none">
                <img src='/portal/administrator/templates/hathor/images/menu/icon-16-save.png' /></a>
        </td>
    </tr>
<?php } ?>
</table>
