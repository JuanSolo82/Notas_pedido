<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
class NotaViewReportes extends JView{
    function display($tpl = null){
		$jinput = JFactory::getApplication()->input;
		$layout = $jinput->get("layout", "", "string");
		switch($layout){
			case "default": 
				$this->notas 		= $jinput->get("notas", array(), "array");
				$this->lista_deptos = $jinput->get("lista_deptos", array(), "array");
				$this->desde		= $jinput->get("desde", "", "string");
				$this->hasta		= $jinput->get("hasta", "", "string");
				break;
			case "detalle_nota":
				$this->id_remitente = $jinput->get("id_remitente", 0, "int");
				$this->detalle_nota = $jinput->get("detalle_nota", array(), "array");
				$this->items 		= $jinput->get("items", array(), "array");
				$this->datos_user	= $jinput->get("datos_user", array(), "array");
				$this->etapas		= $jinput->get("etapas", array(), "array");
				$this->anotaciones	= $jinput->get("anotaciones", array(), "array");
				break;
			case "facturados": 
				$this->lista_notas 		= $jinput->get("lista_notas", array(), "array");
				$this->centros_costo	= $jinput->get("centros_costo", array(), "array");
				break;
			case "facturar_orden":
				$this->id_remitente = $jinput->get("id_remitente", 0, "int");
				$this->detalle_nota = $jinput->get("detalle_nota", array(), "array");
				$this->items		= $jinput->get("items", array(), "array");
				$this->datos_user	= $jinput->get("datos_user", array(), "array");
				$this->orden_compra	= $jinput->get("orden_compra", 0, "int");
				break;
		}
        parent::display($tpl);
    }
	function getBoton($label, $icono, $task) {
		$boton = '<a href="'.JRoute::_('index.php?option=com_nota&view=adquisiciones&task=adquisiciones.'.$task).'">';
		$boton .= '  <img src="/portal/administrator/templates/hathor/images/header/icon-48-' . $icono . '.png" />' . PHP_EOL;
		$boton .= '  <br><span class="titulo_item">' . $label . '</span>' . PHP_EOL;
		$boton .= '</a>';
        return $boton;
	}
}