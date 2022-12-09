<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');
require_once(JPATH_COMPONENT_SITE.'/assets/helper.php');

/**
 * RAW View class for the Reserva Component
 */
class NotaViewAutorizaciones extends JView {

	public function display($tpl = null) {
		$app	 = JFactory::getApplication();
		$jinput  = $app->input;
		parent::display($tpl);
	}

	public function rango_notas($tpl = null){
		$jinput	 = JFactory::getApplication()->input;
		$this->setLayout('rango_notas.raw');
		$this->notas 		= $jinput->get("notas", array(), "array");
		$this->datos_user 	= $jinput->get("datos_user", array(), "array");
		$this->pagina		= $jinput->get("pagina", 1, "int");
		parent::display($tpl);
	}
}
