<?php
// No direct access.
defined('_JEXEC') or die;

// Include dependancy of the main controllerform class
jimport('joomla.application.component.controllerform');
require_once(JPATH_COMPONENT_SITE.'/assets/helper.php');

class NotaControllerAdquisiciones extends JControllerForm
{
	public function getModel($name = '', $prefix = '', $config = array('ignore_request' => true)) {
		return parent::getModel($name, $prefix, array('ignore_request' => false));
	}
	function generar_orden(){
		$jinput = JFactory::getApplication()->input;
		$model	= $this->getModel('adquisiciones');
		$model2	= $this->getModel('nota');
		$id_remitente 	= $jinput->get("id_remitente", 0, "int");
		$opcion			= $jinput->get("opcion", 0, "int");
		$enviar_correo	= $jinput->get("correo", 1, "int");
		$items_opcion	= $model->items($id_remitente);
		$datos_nota		= $model2->getDetalle_nota($id_remitente);
		$datos_orden	= $model->getDetalle_orden($id_remitente, $opcion);
		
		$items = array();
		foreach ($items_opcion as $i){
			if ($i['opcion_oc']==$opcion){
				if ($i['id_nueva_cantidad']){
					if ($i['nueva_cantidad']>0){
						$items[$i['id']]['cantidad'] = $i['nueva_cantidad'];
						$items[$i['id']]['item'] = $i['item'];
						$items[$i['id']]['motivo'] = $i['motivo'];
					}
				}else{
					$items[$i['id']]['cantidad'] = $i['cantidad'];
					$items[$i['id']]['item'] = $i['item'];
					$items[$i['id']]['motivo'] = $i['motivo'];
				}
			}
		}
		$jinput->set("datos_orden", $datos_orden);
		$jinput->set("datos_nota", $datos_nota);
		$jinput->set("items", $items);
		$jinput->set('layout','generar_orden.pdf' );
		$view = $this->getView('adquisiciones','pdf');
		if ($enviar_correo)
			$this->correo_orden_completada($datos_nota, $datos_orden);
		$view->display();
	}
	function correo_orden_completada($datos_nota, $datos_orden){
		$subject = "[Orden de compra]";
		$body = '<link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">';
		$body .= "
		<style>
		.borde{
			border-radius: 25px;
			border: 2px solid #4AA5FF;
			padding: 20px; 
			font-family: 'Open Sans', sans-serif;
			width: 50%;
			margin: 20px;
		}
		</style>
		<div class='borde'>";
		$body .= "<h3>Orden de compra emitida</h3><br>";
		$body .= "Órden(es) de compra referidas a la nota ".$datos_nota['id_remitente']." ya se ha(n) emitido, consulte con departamento de Adquisiciones.<br><br>";
		$email = array($datos_nota['email']);
		NotaHelper::mail($subject,$body,$email);
	}
}
