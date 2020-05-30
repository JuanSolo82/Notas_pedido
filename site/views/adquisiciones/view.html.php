<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
class NotaViewAdquisiciones extends JView{
    function display($tpl = null){
		$jinput = JFactory::getApplication()->input;
		$layout = $jinput->get("layout", "", "string");
		$model = $this->getModel("adquisiciones");
		switch($layout){
			case "":
				$this->notas_pendientes = $model->getPendientesOc();
				break;
			case "lista_notas": 
				$this->lista_notas = $jinput->get("lista_notas", array(), "array");
				break;
			case "ver_nota":
				$this->id_remitente = $jinput->get("id_remitente", 0, "int");
				$this->items		= $jinput->get("items", array(), "array");
				break;
			case "orden_compra":
				$this->id_remitente = $jinput->get("id_remitente", 0, "int");
				$this->items		= $jinput->get("items", array(), "array");
				$this->datos_nota	= $jinput->get("datos_nota", array(), "array");
				$this->orden		= $jinput->get("orden", array(), "array");
				$this->proveedor	= $jinput->get("proveedor", array(), "array");
				break;
			case "opcion_oc":
				$this->id_remitente = $jinput->get("id_remitente", 0, "int");
				$this->items		= $jinput->get("items", array(), "array");
				$this->depto_costo	= $jinput->get("depto_costo", array(), "array");
				$this->centros_costo = $jinput->get("centros_costo", array(), "array");
				break;
			case "regenerar_oc":
				$this->orden_compra = $jinput->get("orden_compra", 0, "int");
				$this->datos_nota	= $jinput->get("datos_nota", array(), "array");
				$this->datos_oc		= $jinput->get("datos_oc", array(), "array");
				break;
			case "buscar_oc":
				$this->orden_compra	= $jinput->get("orden_compra", 0, "int");
				$this->datos		= $jinput->get("datos", array(), "array");
				$this->items_oc 	= $jinput->get("items_oc", array(), "array");
				break;
		}
        parent::display($tpl);
    }
	function getBoton($label, $icono, $task, $notificacion=0) {
		$boton = '<a href="'.JRoute::_('index.php?option=com_nota&view=adquisiciones&task=adquisiciones.'.$task).'">';
		if ($notificacion)
			$boton .= '<div class="notificacion">'.$this->notas_pendientes.'</div>';
		$boton .= '  <img src="/portal/administrator/templates/hathor/images/header/icon-48-' . $icono . '.png" />' . PHP_EOL;
		$boton .= '  <br><span class="titulo_item">' . $label . '</span>' . PHP_EOL;
		$boton .= '</a>';
        return $boton;
	}
	function estilos(){
		$style = "
		.centrar {
			display: flex;
			justify-content: center;
			position: relative;
			width: 100%;
			float: left;
		}
		.caja_orden{
			font-family: 'Questrial', sans-serif;
		}
		.encabezados_oc {
			width: 50%;
			justify-content: center; 
			text-align: center;
		}
		.datos_entrega {
			width: 100%;
			float: left;
			margin: 10px;
		}
		.superior {
			margin-top: 30px;
			color: darkslategray;
			padding: 5px;
			text-align: center;
			font-weight: bold;
			font-size: 20px;
		}
		.inferior {
			text-align: center;
			font-weight: bold;
			size: 15px;
		}
		.tabla_items {
			width: 100%;
			margin-top: 30px;
		}
		.tabla_items td, .tabla_items tr {
			font-size: 8pt;
		}
		.pie_firma{
			position: absolute; 
			bottom: 20px; 
			width: 40%; 
			display: flex;
			justify-content: center;
		}
		.beneficio{
			width: 100%;
			float: left;
			font-style: italic;
			font-family: sans-serif;
			font-weight: bold;
			font-size: 14px;
			margin-top: 30px;
			margin-bottom: 20px;
			border: solid black 1px;
			padding: 10px;
		}
		";
		return $style;
	}
}