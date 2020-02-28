<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
class NotaViewAdquisiciones extends JView{
    function display($tpl = null){
		$jinput = JFactory::getApplication()->input;
		$mes = array("01" => "enero", "02" => "febrero", "03" => "marzo", "04" => "abril", "05" => "mayo", "06" => "junio",
					"07" => "julio", "08" => "agosto", "09" => "septiembre", "10" => "octubre", "11" => "noviembre", "12" => "diciembre");
		$layout = $jinput->get("layout", "", "string");
		$document = JFactory::getDocument();
		$document->setName('Orden_compra');
		$this->setLayout($layout);
		
		$this->items 		= $jinput->get("items", array(), "array");
		$this->datos_nota 	= $jinput->get("datos_nota", array(), "array");
		$this->datos_orden	= $jinput->get("datos_orden", array(), "array");
		$f = explode("-", $this->datos_orden['fecha']);
		$this->fecha_creacion = $f[2].' de '.$mes[$f[1]].' de '.$f[0];
		parent::display($tpl);
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