<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');
require_once(JPATH_COMPONENT_SITE.'/assets/helper.php');

/**
 * RAW View class for the Reserva Component
 */
class NotaViewNota extends JView {

	public function display($tpl = null) {
		$app	 = JFactory::getApplication();
		$jinput  = $app->input;
		parent::display($tpl);
	}

	public function resultado_busqueda($tpl = null) {
		$jinput	 = JFactory::getApplication()->input;
		$this->nombre 	= $jinput->get("nombre", "", "string");
		$this->nombres 	= $jinput->get("nombres", array(), "array");
		$this->centros 	= $jinput->get("centros_costo", array(), "array");
		$this->niveles	= $jinput->get("niveles", array(), "array");
		$this->setLayout('resultado_busqueda.raw');
		parent::display($tpl);
	}
	public function notas_rango($tpl = null){
		$jinput	 = JFactory::getApplication()->input;
		$this->setLayout('notas_rango.raw');
		$this->notas 		= $jinput->get("notas", array(), "array");
		$this->datos_user 	= $jinput->get("datos_user", array(), "array");
		$this->pagina		= $jinput->get("pagina", 1, "int");
		parent::display($tpl);
	}
	public function notas_depto($tpl = null){
		$jinput	 = JFactory::getApplication()->input;
		$this->setLayout('rango_depto.raw');
		$this->notas_depto	= $jinput->get("notas_depto", array(), "array");
		$this->pagina 		= $jinput->get("pagina", 1, "int");
		parent::display($tpl);
	}
	public function previo_depto($tpl = null){
		$jinput	 = JFactory::getApplication()->input;
		$this->setLayout('previo_depto.raw');
		$this->notas		= $jinput->get("notas", array(), "array");
		$this->pagina 		= $jinput->get("pagina", 1, "int");
		parent::display($tpl);
	}
	public function rango_naves($tpl = null){
		$jinput	 = JFactory::getApplication()->input;
		$this->setLayout('rango_naves.raw');
		$this->notas_naves	= $jinput->get("rango_naves", array(), "array");
		$this->pagina 		= $jinput->get("pagina", 1, "int");
		parent::display($tpl);
	}
}
