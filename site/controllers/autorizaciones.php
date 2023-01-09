<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.controllerform');
require_once(JPATH_COMPONENT_SITE.'/assets/helper.php');
require_once(JPATH_COMPONENT_SITE.'/assets/phpqrcode.php');

class NotaControllerAutorizaciones extends JControllerForm{
    public function getModel($name = '', $prefix = '', $config = array('ignore_request' => true)) {
		return parent::getModel($name, $prefix, array('ignore_request' => false));
	}

	public function display($cachable = false, $urlparams = false) {
		parent::display($cachable, $urlparams);
	}

    public function notas_naves(){
        $jinput = JFactory::getApplication()->input;
        $jinput->set('view', 'autorizaciones');
        $jinput->set('layout', 'listas');
        $model 			= $this->getModel('nota');
		parent::display();
    }

    function detalle_nota(){
		$jinput = JFactory::getApplication()->input;
		$jinput->set('view', 'autorizaciones');
		$jinput->set( 'layout', 'detalle_nota' );
		$id_remitente = $jinput->get("id_nota", 0, 'int');
		$model 			= $this->getModel('nota');
		$user = JFactory::getUser();

		$datos_jefe = array();
		$lista_deptos	= $model->getDepartamentos_destino();
		if ($user->authorise('jefe.depto', 'com_nota'))
			$datos_jefe = $model->getDatos_user($user->id);
		$detalle_nota 	= $model->getDetalle_nota($id_remitente);
		$datos_user 	= $model->getDatos_user($detalle_nota['id_user']);
		$items			= $model->getItems($id_remitente);
        
		$jinput->set('id_remitente', $id_remitente);
		$jinput->set('detalle_nota', $detalle_nota);
		$jinput->set('items', $items);
		$jinput->set("datos_user", $datos_user);
		$jinput->set("datos_jefe", $datos_jefe);
		$jinput->set("id_user_actual", $user->id);
		$jinput->set('lista_deptos', $lista_deptos);
		parent::display();
	}

}