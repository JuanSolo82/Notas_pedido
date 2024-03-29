<?php
	error_reporting(E_ALL & ~E_STRICT);
	ini_set('display_errors', true);
// No direct access.
defined('_JEXEC') or die;

// Include dependancy of the main controllerform class
jimport('joomla.application.component.controllerform');
require_once(JPATH_COMPONENT_SITE.'/assets/helper.php');

class NotaControllerCarga extends JControllerForm
{
	public function getModel($name = '', $prefix = '', $config = array('ignore_request' => true)) {
		return parent::getModel($name, $prefix, array('ignore_request' => false));
	}

	public function display($cachable = false, $urlparams = false) {
		JFactory::getApplication()->input->set( 'view', 'externa' );
		parent::display($cachable, $urlparams);
	}
	public function defaultview() {
		JFactory::getApplication()->input->set( 'layout', 'default' );
		$this->display();
	}
	public function centros(){
		$jinput = JFactory::getApplication()->input;
		$model = $this->getModel('nota');
		$centros_costo = $model->getCentros_costo();
		$centros_costo = json_encode($centros_costo);
		echo $centros_costo;
	}
	function nota_revision(){
		$jinput = JFactory::getApplication()->input;
		$model = $this->getModel('nota');
		$id_remitente 			= $jinput->get("id_remitente", 0, "int");
		$enviado_empleado 		= $jinput->get("enviado_empleado", -1, "int");
		$autorizado_capitan 	= $jinput->get("autorizado_capitan", -1, "int");
		$autorizado_jefe 		= $jinput->get("autorizado_jefe", -1, "int");
		$autorizado_depto 		= $jinput->get("autorizado_depto", -1, "int");
		$aprobado_adquisiciones	= $jinput->get("aprobado_adquisiciones", -1, "int");
        $autorizado_operaciones = $jinput->get("aprobado_adquisiciones", 0, "int");
		$generico				= $jinput->get("generico", -1, "int");
		$model->actualizar_revision($id_remitente, $enviado_empleado, $autorizado_capitan, $autorizado_jefe, $autorizado_depto, $autorizado_operaciones, $aprobado_adquisiciones);
		$user = JFactory::getUser();
		
		$nombre = "";
		if (!$generico)
			$nombre = $user->name;
			
		// actualización de tabla sql server
		if (NotaHelper::isTestSite()){
			$replicacion = $this->getModel('replicacion');
			$autorizacion = 0;
			if ($autorizado_capitan>0)
				$autorizacion = $autorizacion|2;
			if ($autorizado_jefe>0)
				$autorizacion = $autorizacion|4;
			if ($autorizado_depto>0)
				$autorizacion = $autorizacion|8;
			if ($aprobado_adquisiciones>0)
				$autorizacion = $autorizacion|16;
			$replicacion->actualizaRevision($autorizacion,$id_remitente);
		}
	}

	function actualizarRevision(){
		if (NotaHelper::isTestSite()){
			$jinput = JFactory::getApplication()->input;
			$replicacion = $this->getModel('replicacion');
			$id_remitente 			= $jinput->get("id_remitente", 0, "int");
			$autorizado_capitan 	= $jinput->get("autorizado_capitan", -1, "int");
			$autorizado_jefe 		= $jinput->get("autorizado_jefe", -1, "int");
			$autorizado_depto 		= $jinput->get("autorizado_depto", -1, "int");
			$aprobado_adquisiciones	= $jinput->get("aprobado_adquisiciones", -1, "int");
			$autorizacion = 0;
			if ($autorizado_capitan>0)
				$autorizacion = $autorizacion|2;
			if ($autorizado_jefe>0)
				$autorizacion = $autorizacion|4;
			if ($autorizado_depto>0)
				$autorizacion = $autorizacion|8;
			if ($aprobado_adquisiciones>0)
				$autorizacion = $autorizacion|16;
			$replicacion->actualizaRevision($autorizacion,$id_remitente);
		}
	}

	function resultado_busqueda(){
		$jinput = JFactory::getApplication()->input;
		$model = $this->getModel('nota');
		$nombre = $jinput->get("nombre", "", "string");
		$view = $this->getView('nota','raw');

		$nombres = $model->buscar_nombre($nombre);
		$centros = $model->getCentros_costo();
		$niveles = $model->getNiveles();
		$centros_costo = array();
		foreach ($centros as $c){
			if ($c['id_tipo']<3)
				$centros_costo[] = $c;
		}
		$jinput->set("nombre", $nombre);
		$jinput->set("nombres", $nombres);
		$jinput->set("centros_costo", $centros_costo);
		$jinput->set("niveles", $niveles);
        $view->resultado_busqueda();
	}
	function notas_rango(){
		$jinput = JFactory::getApplication()->input;
		$view = $this->getView('nota','raw');
		$model = $this->getModel('nota');
		$user = JFactory::getUser();
		$pagina		= $jinput->get("pagina", 1,"int");
		if ($user->authorise('gerencia_operaciones','com_nota'))
			$notas = $model->notas_naves($pagina);
		else
			$notas 		= $model->notas_propias($user->id, $pagina);
		$datos_user = $model->getDatos_user($user->id);
		$i=0;
		foreach ($notas as $n){
			$aprobacion = $model->getAnotacion($n['id']);
			if (sizeof($aprobacion)){
				$notas[$i]['aprobado']			= $aprobacion['aprobado'];
				$notas[$i]['anotacion'] 		= $aprobacion['anotacion'];
				$notas[$i]['fecha_aprobacion'] 	= $aprobacion['fecha'];
			}else{
				$notas[$i]['aprobado']			= 0;
				$notas[$i]['anotacion'] 		= '';
				$notas[$i]['fecha_aprobacion'] 	= '';
			}
			$notas[$i]['ordenes']			= $model->getNotas_ordenes('', '', $n['id'], 0);
			$i++;
		}
		$jinput->set("notas", $notas);
		$jinput->set("pagina", $pagina);
		$jinput->set("datos_user", $datos_user);
		$view->notas_rango();
	}
	function buscar_notas(){
		$jinput = JFactory::getApplication()->input;
		$model = $this->getModel('nota');
		$view = $this->getView('nota','raw');
		$user = JFactory::getUser();
		$naves		= $jinput->get('naves', 0, 'int');
		$parametro	= $jinput->get('parametro', '', 'string');
		$proveedor	= $jinput->get('proveedor', '', 'string');
		$id_nave	= $jinput->get('id_nave', 0, 'int');
		$desde 		= $jinput->get('desde', '', 'string');
		$hasta 		= $jinput->get('hasta', '', 'string');
		print_r("rango");
		$notas = array();
		if ($id_nave){
			$id_naves = $model->getSeccionesNaves($id_nave);
			$str_naves = '';
			foreach ($id_naves as $n){
				$str_naves .= $n['id_depto'].',';
			}
			$str_naves = trim($str_naves,',');
			$notas = $model->notas_naves(0,'', $str_naves, $desde, $hasta);
		}else if ($naves){
			$notas = $model->notas_naves(0,$parametro);
		}else{
			if ($parametro)
				$notas	= $model->notas_propias($user->id, 1, $parametro);
			elseif ($proveedor)
				$notas	= $model->notas_proveedor($user->id, $proveedor);
		}
		$i=0;
		foreach ($notas as $n){
			$aprobacion = $model->getAnotacion($n['id']);
			$notas[$i]['ordenes']			= $model->getNotas_ordenes('', '', $n['id'], 0);
			if (sizeof($aprobacion)){
				$notas[$i]['aprobado']			= $aprobacion['aprobado'];
				$notas[$i]['anotacion'] 		= $aprobacion['anotacion'];
				$notas[$i]['fecha_aprobacion'] 	= $aprobacion['fecha'];
			}else{
				$notas[$i]['aprobado']			= 0;
				$notas[$i]['anotacion'] 		= '';
				$notas[$i]['fecha_aprobacion'] 	= '';
			}
			$i++;
		}
		$jinput->set("notas", $notas);
		$view->notas_rango();
	}
	
	function depto_rango(){
		$jinput = JFactory::getApplication()->input;
		$view = $this->getView('nota','raw');
		$model = $this->getModel('nota');
		$user = JFactory::getUser();
		$pagina			= $jinput->get("pagina", 1,"int");
		$notas_depto 	= $model->notas_depto($pagina);
		$jinput->set('notas_depto', $notas_depto);
		$jinput->set('pagina', $pagina);
		$view->notas_depto();
	}
	function previo_depto(){
		$jinput = JFactory::getApplication()->input;
		$view = $this->getView('nota','raw');
		$user = JFactory::getUser();
		$model = $this->getModel('nota');
		$pagina			= $jinput->get("pagina", 1,"int");
		$datos_user = $model->getDatos_user($user->id);
		$notas_jefe = $model->notas_jefe($datos_user, $pagina);
		$jinput->set('notas', $notas_jefe);
		$view->previo_depto();
	}
	function getDatos_orden(){
		$jinput = JFactory::getApplication()->input;
		$model = $this->getModel('nota');
		$orden_compra = $jinput->get("orden_compra", 0, "int");
		$datos_orden = $model->datos_orden($orden_compra);
		echo json_encode($datos_orden);
	}
	function rango_naves(){
		$jinput = JFactory::getApplication()->input;
		$view = $this->getView('nota','raw');
		$model = $this->getModel('nota');
		$user = JFactory::getUser();
		$pagina			= $jinput->get("pagina", 1,"int");
		$rango_naves 	= $model->notas_naves($pagina);
		$jinput->set('rango_naves', $rango_naves);
		$jinput->set('pagina', $pagina);
		$view->rango_naves();
	}
	function getListaProveedor(){
		$jinput = JFactory::getApplication()->input;
		$model = $this->getModel('nota');
		$str = $jinput->get('str', '', 'string');
		$ind = $jinput->get('ind', 0, 'int');
		$proveedor = $model->getListaProveedor($str);
		
		$html = "<ul id='lista_proveedores".($ind ? $ind : '')."' onchange='escoger_proveedor(\"\",\"0\", 0)' class='lista_proveedores'>";
		if (sizeof($proveedor)){
			foreach ($proveedor as $p){
				$html .= "<li id='".$p['RazonSocial']."' onclick='escoger_proveedor(\"".utf8_encode(ucwords(strtolower($p['RazonSocial'])))."\", \"".$p['rut']."\",\"".ucwords(strtolower($p['giro']))."\", ".$ind.")'>".utf8_encode($p['RazonSocial'])."</li>";
			}
		}
		$html .= "</ul>";
		echo $html;
	}
	function getProveedor(){
		$jinput = JFactory::getApplication()->input;
		$model = $this->getModel('nota');
		$razon_social 	= $jinput->get("razon_social", "", "string");
		$rut 			= $jinput->get("rut", "", "string");
		$proveedor = $model->getProveedor($razon_social, $rut);
		if (sizeof($proveedor)){
			if ($proveedor['RazonSocial']) $proveedor['RazonSocial'] = ucwords(strtolower($proveedor['RazonSocial']));
			if ($proveedor['giro']) $proveedor['giro'] = ucwords(strtolower($proveedor['giro']));
		}
		echo json_encode($proveedor);
	}

	function buscar_item(){
		$jinput = JFactory::getApplication()->input;
		$id_user 	= $jinput->get("id_user", 0, "int");
		$item 		= $jinput->get("item", "", "string");
		$model = $this->getModel('nota');
		$items = $model->getItems_usuario($id_user, $item);
		$valor = "pantalla";
		$ind = $jinput->get("ind",0,"int");

		$html = "<ul id='lista_items".$ind."' class='lista_proveedores'>";
		foreach ($items as $i)
			$html .= "<li onclick='escoger_item(".$ind.",\"".$i['item']."\")'>".$i['item']."</li>";
		$html .= "</ul>";
		echo $html;
	}
	function rango_area(){
		$jinput = JFactory::getApplication()->input;
		$view = $this->getView('nota','raw');
		$model = $this->getModel('nota');
		$parametro  = $jinput->get("parametro", "", "string");
		$pagina		= $parametro=="" ? $jinput->get("pagina", 1,"int") : 0;
		$notas_area = $model->notas_area($pagina, $parametro);
		$jinput->set("notas_area", $notas_area);
		$jinput->set("notas", $notas_area);
		$jinput->set("pagina", $pagina);
		$view->rango_area();
	}

    function getProducto(){
		$jinput = JFactory::getApplication()->input;
		$model = $this->getModel('nota');
		$str 	= $jinput->get("str", "", "string");
        $ind    = $jinput->get("ind",0,"int");
		$producto = $model->getProducto($str);

        $html = '';
        if (sizeof($producto)){
            $html = "<ul id='lista_productos".$ind."' onchange='escoger_producto(\"\",0)' class='lista_proveedores'>";
			foreach ($producto as $p){
				$html .= "<li id='".$p['id']."' onclick='escoger_producto(\"".$p['nombre']."\",".$ind.")'>".$p['nombre']."</li>";
			}
            $html .= "</ul>";
		}
		echo $html;
	}

    function getNotas_naves(){
        $jinput = JFactory::getApplication()->input;
        $view = $this->getView('autorizaciones','raw');
		$model = $this->getModel('nota');
        $pagina = $jinput->get("pagina",0,"int");
        
        $notas = $model->notas_naves($pagina);
        $jinput->set("notas", $notas);
        $view->rango_notas();
    }

    function cambiar_destino(){
        $jinput = JFactory::getApplication()->input;
        $model = $this->getModel('nota');
        $id_remitente = $jinput->get("id_remitente",0,"int");
        $id_adepto    = $jinput->get("id_adepto",0,"int");
        $model->cambiar_destino($id_remitente, $id_adepto);
    }
    function actualizar_item(){
        $jinput = JFactory::getApplication()->input;
        $model = $this->getModel('nota');
        $cantidad_original  = $jinput->get("cantidad_original",0,"int");
        $cantidad_nueva     = $jinput->get("cantidad_nueva",0,"int");
        $id_item            = $jinput->get("id_item",0,"int");
        $tipo_modificacion  = $jinput->get("tipo_modificacion",0,"int");

        $query = $model->actualizar_item($id_item, $cantidad_original, $cantidad_nueva, $tipo_modificacion);
    }
    function aprobar_nota(){
        $user = JFactory::getUser();
        $jinput = JFactory::getApplication()->input;
        $model = $this->getModel('nota');
        $id_remitente = $jinput->get('id_remitente',0,'int');
        
        if ($user->authorise('gestion_naves','com_nota')){
            $model->actualizar_revision($id_remitente,1,1,1,0,0);
            $model->tramitado($id_remitente,0,'',0,$user->id,'');
        }
    }
}