<?php
// No direct access.
defined('_JEXEC') or die;

// Include dependancy of the main controllerform class
jimport('joomla.application.component.controllerform');
require_once(JPATH_COMPONENT_SITE.'/assets/helper.php');

class NotaControllerReportes extends JControllerForm
{
	public function getModel($name = '', $prefix = '', $config = array('ignore_request' => true)) {
		return parent::getModel($name, $prefix, array('ignore_request' => false));
	}

	public function display($cachable = false, $urlparams = false) {
		parent::display($cachable, $urlparams);
	}

	public function defaultview() {
		$user = JFactory::getUser();
		if ($user->authorise('adquisiciones.jefe', 'com_nota')){
			JFactory::getApplication()->input->set( 'layout', 'default' );
			$this->display();
		}else{
			echo "No posee permisos para este menú";
			return;
		}
	}
	function busqueda_notas(){
		$jinput = JFactory::getApplication()->input;
		$jinput->set('view', 'reportes');
		$jinput->set( 'layout', 'default' );
		$model = $this->getModel("nota");
		$inicio			= $jinput->get('inicio', 0, 'int');
		$desde 			= $jinput->get("desde", "", "string");
		$hasta 			= $jinput->get("hasta", "", "string");
		$nota_pedido 	= $jinput->get("nota_pedido", 0, "int");
		$orden_compra 	= $jinput->get("orden_compra", 0, "int");
		$depto_origen	= $jinput->get("depto_origen", 0, "int");
		$lista_deptos	= $model->getCentros_costo();
		$notas = array();
		if ($hasta=="" && ($nota_pedido+$orden_compra)==0){
			if ($inicio)
				JError::raiseNotice( 100, 'Ingrese los campos requeridos ');
		}elseif ($inicio){
			$notas = $model->getNotas($desde, $hasta, $nota_pedido, $orden_compra, $depto_origen);
		}
		$jinput->set("notas", $notas);
		$jinput->set("lista_deptos", $lista_deptos);
		parent::display();
	}
	public function detalle_nota(){
		$jinput = JFactory::getApplication()->input;
		$jinput->set('view', 'reportes');
		$jinput->set('layout', 'detalle_nota');
		$id_remitente = $jinput->get("id_nota", 0, 'int');
		$model 			= $this->getModel('nota');
		$detalle_nota 	= $model->getDetalle_nota($id_remitente);
		$datos_user 	= $model->getDatos_user($detalle_nota['id_user']);
		$items			= $model->getItems($id_remitente);
		$etapas			= $model->getEtapas($id_remitente);
		$jinput->set('id_remitente', $id_remitente);
		$jinput->set('detalle_nota', $detalle_nota);
		$jinput->set('items', $items);
		$jinput->set("datos_user", $datos_user);
		$jinput->set("etapas", $etapas);
		parent::display();
	}
	public function facturar_orden(){
		$jinput = JFactory::getApplication()->input;
		$jinput->set('view', 'reportes');
		$jinput->set('layout', 'facturar_orden');
		$id_remitente = $jinput->get("id_nota", 0, 'int');
		$orden_compra = $jinput->get("orden_compra", 0, "int");

		$model 			= $this->getModel('nota');
		$detalle_nota 	= $model->getDetalle_nota_orden($id_remitente, $orden_compra);
		$datos_user 	= $model->getDatos_user($detalle_nota['id_user']);
		$items			= $model->getItems($id_remitente);
		$jinput->set("id_remitente", $id_remitente);
		$jinput->set('detalle_nota', $detalle_nota);
		$jinput->set('items', $items);
		$jinput->set("datos_user", $datos_user);
		$jinput->set("orden_compra", $orden_compra);
		parent::display();
	}
	public function facturados(){
		$jinput = JFactory::getApplication()->input;
		$jinput->set('view', 'reportes');
		$jinput->set('layout', 'facturados');
		$model 			= $this->getModel('nota');
		$desde 			= $jinput->get("desde", "", "string");
		$hasta 			= $jinput->get("hasta", "", "string");
		$nota_pedido 	= $jinput->get("nota_pedido", 0, "int");
		$orden_compra 	= $jinput->get("orden_compra", 0, "int");
		$centro_costo	= $jinput->get("centro_costo", 0, "int");
		$centros_costo	= $model->getCentros_costo();
		if ($desde || $hasta || $nota_pedido || $orden_compra){
			$lista_notas = $model->getNotas_ordenes($desde, $hasta, $nota_pedido, $orden_compra, $centro_costo);
			$jinput->set("lista_notas", $lista_notas);
		}
		$jinput->set("centros_costo", $centros_costo);
		parent::display();
	}
	public function actualiza(){
		$jinput = JFactory::getApplication()->input;
		$model 			= $this->getModel('nota');
		$orden_compra	= $jinput->get("orden_compra", 0, "int");
		$proveedor		= $jinput->get("proveedor", "", "string");
		$factura		= $jinput->get("factura", "", "string");
		if ($proveedor)
			$model->actualiza_proveedor($proveedor, $orden_compra);
		else
			$model->actualiza_factura($factura, $orden_compra);
	}
}
