<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');
require_once(JPATH_COMPONENT_SITE.'/assets/helper.php');
require_once(JPATH_COMPONENT_SITE.'/assets/constants.php');
 
/**
 * Nota Component Controller
 */
class NotaController extends JController{
	public function display ($cachable = false, $urlparams = false) {
		$jinput = JFactory::getApplication()->input;
		$model = $this->getModel('nota');
		$user = JFactory::getUser();
		$datos_user = $model->getDatos_user($user->id);
		//$notas_sin_revisar = $model->pendientes_revision();
		//print_r(sizeof($notas_sin_revisar));
		$notas_pendientes=0;

		if ($user->authorise('jefe.depto', 'com_nota') || $user->authorise('capitan.jefe', 'com_nota') || $user->authorise('capitan.sin_jefe', 'com_nota')){
			$notas_pendientes = $model->getPendientes();
			$pendientes_depto = $model->getPendientes_depto();
		}
		if ($user->authorise('jefe.delgada', 'com_nota') || $user->authorise('jefe.natales', 'com_nota'))
			$pendientes_naves = $model->getPendientes_naves();
		$jinput->set("datos_user", $datos_user);
		$jinput->set("notas_pendientes", $notas_pendientes);
		$jinput->set("pendientes_depto", $pendientes_depto);
		$jinput->set("pendientes_naves", $pendientes_naves);
        parent::display();
	}
	public function nueva_nota(){
		$jinput = JFactory::getApplication()->input;
		$jinput->set('view', 'nota');
		$jinput->set( 'layout', 'nueva_nota' );
		$model = $this->getModel('nota');
		$user = JFactory::getUser();
		$departamentos 	= $model->getDepartamentos_destino();
		$centros_costos	= $model->getCentros_costo();
		//print_r(sizeof($centros_costos));
		$prioridad		= $model->getPrioridad();
		$datos_user 	= $model->getDatos_user($user->id);
		
		$jinput->set("departamentos", $departamentos);
		$jinput->set("centros_costos", $centros_costos);
		$jinput->set("prioridad", $prioridad);
		$jinput->set("datos_user", $datos_user);
		
		parent::display();
	}
	function notas_propias(){
		$jinput = JFactory::getApplication()->input;
		$jinput->set('view', 'nota');
		$jinput->set( 'layout', 'notas_propias' );
		$model = $this->getModel('nota');
		$user = JFactory::getUser();
		$notas = $model->notas_propias($user->id);
		$i=0;
		$i=0;
		foreach ($notas as $n){
			$aprobacion = $model->getAnotacion($n['id']);
			$notas[$i]['aprobado']			= $aprobacion['aprobado'];
			$notas[$i]['anotacion'] 		= $aprobacion['anotacion'];
			$notas[$i]['fecha_aprobacion'] 	= $aprobacion['fecha'];
			$notas[$i]['ordenes']			= $model->getNotas_ordenes('', '', $n['id'], 0);
			$i++;
		}
		$datos_user = $model->getDatos_user($user->id);
		$jinput->set("notas", $notas);
		$jinput->set("datos_user", $datos_user);
		parent::display();
	}
	function detalle_nota(){
		$jinput = JFactory::getApplication()->input;
		$jinput->set('view', 'nota');
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
		
		
		$datos_nota 	= $model->getDetalle_nota($id_remitente);
		$jinput->set('id_remitente', $id_remitente);
		$jinput->set('detalle_nota', $detalle_nota);
		$jinput->set('items', $items);
		$jinput->set("datos_user", $datos_user);
		$jinput->set("datos_jefe", $datos_jefe);
		$jinput->set("datos_nota", $datos_nota);
		$jinput->set("id_user_actual", $user->id);
		$jinput->set('lista_deptos', $lista_deptos);
		parent::display();
	}
	function detalle_notajefe(){
		$jinput = JFactory::getApplication()->input;
		$user = JFactory::getUser();
		$jinput->set('view', 'nota');
		$jinput->set( 'layout', 'detalle_notajefe' );
		$id_remitente 	= $jinput->get("id_nota", 0, 'int');
		$model 			= $this->getModel('nota');
		
		$detalle_nota 	= $model->getDetalle_nota($id_remitente);
		$datos_user 	= $model->getDatos_user($detalle_nota['id_user']);
		$datos_propios	= $model->getDatos_user($user->id);
		$items			= $model->getItems($id_remitente);
		$jinput->set('id_remitente', $id_remitente);
		$jinput->set('detalle_nota', $detalle_nota);
		$jinput->set('items', $items);
		$jinput->set("datos_user", $datos_user);
		$jinput->set("datos_propios", $datos_propios);
		parent::display();
	}
	function nota_guardada(){
		$user = JFactory::getUser();
		JRequest::checkToken() or jexit('token inválido');
		$jinput = JFactory::getApplication()->input;
		$jinput->set('view', 'nota');
		$jinput->set( 'layout', 'nota_guardada' );
		$fecha = date("Y-m-d");
		$fecha = NotaHelper::msquote($fecha);
		$hora = date("H:i:s");
		$hora = NotaHelper::msquote($hora);
		$model = $this->getModel('nota');
		$replicacion = $this->getModel('replicacion');
		$user = JFactory::getUser();
		$tipo 				= $jinput->get("tipo", 0, "int");
		$id_user 			= $jinput->get("id_user", 0, "int");
		$datos_user 		= $model->getDatos_user($id_user);
		if (!sizeof($datos_user))
			JError::raiseWarning( 100, 'Usuario no agregado, contactar a departamento TIC' );
		$id_adepto 			= $jinput->get("depto_destino", 0, "int");
		if ($user->authorise('tripulante.maquina', 'com_nota') && !$user->authorise("core.admin", "com_nota"))
			$id_adepto = 1; // fijo para naves
		
		$id_prioridad 		= $jinput->get("prioridad", 0, "int");
		$nombre_tripulante	= $jinput->get("nombre_tripulante", "", "string");
		$id_depto_compra	= $jinput->get("id_depto_compra", 4, "int");; // para revisar
		$id_tipo_pedido 	= $jinput->get("tipo_pedido", 1, "int"); // 1 producto, 2 servicio
		$proveedor 			= $jinput->get("proveedor_escogido", "", "string");
		$rut_proveedor		= $jinput->get("rut_proveedor", "", "string");
		$giro_proveedor		= $jinput->get("giro_proveedor", "", "string");
		$cotizacion 		= $jinput->get("cotizacion", "", "string");
		$tipo_gasto			= $jinput->get("tipo_gasto", 0, "int");

		if (!$id_depto_compra) $id_depto_compra = $id_adepto;
		
		if (strlen($proveedor)){
			$proveedor 			= NotaHelper::msquote($proveedor."_".$rut_proveedor."_".$giro_proveedor);
			$proveedor 			= htmlentities($proveedor);
		}
		
		$nombre_tripulante	= NotaHelper::msquote($nombre_tripulante);
		$nombre_tripulante	= htmlentities($nombre_tripulante);
		
		$id_depto_costo 	= $jinput->get("centro_costo", $datos_user["id_depto"], "int");
		$datos_depto		= $model->getDatos_depto($id_depto_costo);

		$items = '';
		$cantidad = 0;
		for ($i=1;$i<=15;$i++){
			$cantidad 		.= $jinput->get("cantidad".$i, 0, 'float').";";
			if (!$cantidad) break;
			$items 		.= utf8_encode(NotaHelper::msquote($jinput->get("descripcion".$i, '', 'string'))).";";
		}
		$id_remitente=0;
		//$id_remitente = $model->buscar_nota($id_user, $id_adepto, $id_prioridad, $id_tipo_pedido, $proveedor, $id_depto_costo, $items, $cantidad);
		
/*		$fecha1 = new DateTime('2016-11-30 03:55:06');//fecha inicial
		$fecha2 = new DateTime('2016-11-30 11:55:06');//fecha de cierre
		$intervalo = $fecha1->diff($fecha2);
		print_r($intervalo->format('%Y años %m meses %d days %H horas %i minutos %s segundos')); */
		$valor = 0;
		if (!$id_remitente){
			$id_remitente = $model->insertar_nota($id_adepto, $id_user, $fecha, $hora, $id_prioridad, $id_depto_compra, $id_depto_costo, $proveedor, $datos_depto['ley_navarino'], $id_tipo_pedido, $cotizacion);
			// preparar inserción en bbdd sql server
			$autorizacion = 0;
			$autorizacion = 0;
			if ($datos_user['id_nivel']==1) $autorizacion = $autorizacion|1;
			if ($datos_user['id_nivel']==2){
				if ($id_adepto!=4) $autorizacion = $autorizacion|2;
				else $autorizacion = $autorizacion|4;
			}
			if ($datos_user['id_depto']==4) $autorizacion = $autorizacion|8;
			//if ($user->username=='eflota')
			$replicacion->setNota($id_remitente, $id_adepto, $id_user, $id_prioridad, $id_depto_compra, $id_depto_costo, $proveedor, $datos_depto['ley_navarino'], $id_tipo_pedido, $cotizacion,$autorizacion);
            $replicacion->setNotaExenta($id_remitente, 0);
            
			// fin inserción registro nota sql server
			
			for ($i=1;$i<=15;$i++){
				$nombre_archivo = '';
				$cantidad 		= $jinput->get("cantidad".$i, 0, 'float');
				if (!$cantidad) break;
				$item 		= utf8_encode(NotaHelper::msquote($jinput->get("descripcion".$i, '', 'string')));
				$motivo		= utf8_encode(NotaHelper::msquote($jinput->get("motivo".$i, '', 'string')));
				$opcion_oc	= $jinput->get("opcion".$i, 0, 'string');
				$valor		= $jinput->get("valor".$i, 0, "int");
				$file 		= $jinput->files->get('archivo'.$i);
				if ($file['name']){
					$nombre_archivo = $file['name'];
					$model->upload($file, $id_remitente);
				}
				// copia de registro en sql server
				$replicacion->setItems($id_remitente, $cantidad, $item, $motivo, $opcion_oc, $valor, $nombre_archivo);
				$model->setItems($id_remitente, $cantidad, $item, $motivo, $opcion_oc, $valor, $nombre_archivo);
			}
		}
		if (trim($nombre_tripulante)){
			$model->nombre_remitente($id_remitente, $nombre_tripulante);
			$replicacion->setNombreTripulante($id_remitente, $nombre_tripulante);
		}
		if ($tipo_gasto)
			$model->setTipo_gasto($id_remitente, $tipo_gasto);
		$datos_nota = $model->getDetalle_nota($id_remitente);
		$items_nota = $model->getItems($id_remitente);
		$jinput->set("datos_nota", $datos_nota);
		$jinput->set("items_nota", $items_nota);
		if (NotaHelper::isTestSite()){
			$this->replicar_sql($id_remitente);
		}else{
			if ($user->authorise('empleado.depto','com_nota') || ($user->authorise('jefe.depto','com_nota') && $id_adepto!=$datos_user["id_depto"]) && $user->username!='eflota'){
				$this->preparar_correo($id_remitente, $nombre_tripulante);
			}else{
				print_r("no correo");
			}
		}
        $email = $model->getMail_jefe($id_remitente);
		parent::display();
	}

	function replicar_sql($id_remitente){
		$user = JFactory::getUser();
		$model = $this->getModel('replicacion');
		$datos_user = $model->getUser($user->id);
		//$model->setRevision($id_remitente, $datos_user['nivel']);
	}

	function preparar_correo($id_remitente, $nombre_tripulante=""){
		$jinput = JFactory::getApplication()->input;
		$model = $this->getModel('nota');
		$datos_nota = $model->getDetalle_nota($id_remitente);
		$user = JFactory::getUser();
		$datos_user = $model->getDatos_user($user->id);
		$remitente = $datos_user['nombre_usuario'];
		if ($nombre_tripulante!="")
			$remitente = $nombre_tripulante;
			
		$subject = "[Nota de pedido]";
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
		$body .= "<h3>Se ha generado una nota de pedido.</h3><br>";

		if(($user->authorise('capitan.jefe', 'com_nota') || $user->authorise('capita.sin_jefe', 'com_nota') || $user->authorise("jefe.delgada","com_nota")) && $datos_nota['id_user']!=$datos_user['id']){
			$body .= "La nota de pedido n° ".$id_remitente." está disponible para ser tramitada.";
		}else{
			$body .= "<b>".$remitente."</b> ha generado la nota de pedido n° ".$id_remitente." para su gestión.<br>";
		}
		if (NotaHelper::isTestSite()){
			$body .= "<br><br><i>[Nota emitida en servidor de prueba (no válido)]</i>";
		}
		$body .= "</div>";
		$email = $model->getMail_jefe($id_remitente);
		//if ($user->username=='jmarinan') print_r($email);
		/*if ($email['email']=='jmarinan@tabsa.cl')
			$body .= "<br>[error]";*/ 
		NotaHelper::mail($subject,$body,$email);
	}
	function anular_nota(){
		$jinput = JFactory::getApplication()->input;
		$model = $this->getModel('nota');
		$id_remitente 	= $jinput->get("id_remitente", 0, "int");
		$id_user		= $jinput->get("id_user", 0, "int");
		$datos_user 	= $model->getDatos_user($id_user);
		$query = $model->anular_nota($id_remitente); 
		print_r($query);
	}
	function anular_nota_depto(){
		$jinput = JFactory::getApplication()->input;
		$model = $this->getModel('nota');
		$id_remitente 	= $jinput->get("id_remitente", 0, "int");
		$model->anular_nota_depto($id_remitente);
	}
	function tramitar_anulada(){
		$jinput = JFactory::getApplication()->input;
		$user = JFactory::getUser();
		$model = $this->getModel("nota");
		$id_remitente 	= $jinput->get("id_remitente", 0, "int");
		$nombre_usuario = $jinput->get("nombre_usuario", "", "string");
		$comentario		= $jinput->get("comentario", "", "string");
		if ($nombre_usuario) $generico=0;
		else $generico=1;
		$model->tramitado($id_remitente, 2, $comentario, $generico, $user->id, $nombre_usuario);
		//tramitado($id_remitente, $terminado, $motivo, $generico, $id_user, $nombre)
	}
	function notas_jefe(){
		$jinput = JFactory::getApplication()->input;
		$jinput->set('view', 'nota');
		$jinput->set( 'layout', 'notas_jefe' );
		$model = $this->getModel('nota');
		$user = JFactory::getUser();
		$datos_user = $model->getDatos_user($user->id);
		$notas_jefe = $model->notas_jefe($datos_user);
		$jinput->set("notas_jefe", $notas_jefe);
		parent::display();
	}
	function editar_item(){
		$jinput = JFactory::getApplication()->input;
		$model = $this->getModel('nota');
		$id_item 				= $jinput->get("id_item", 0, "int");
		$cantidad_original 		= $jinput->get("cantidad_original", 0, "float");
		$nueva_cantidad 		= $jinput->get("nueva_cantidad", 0, "float");
		$descripcion			= $jinput->get("descripcion", "", "string");
		$motivo					= $jinput->get("motivo", "", "string");
		$id_tipo_modificacion 	= $jinput->get("id_tipo_modificacion", 0, "int");
		$valor_unitario			= $jinput->get("valor_unitario", 0, "int");
		$model->editar_item($id_item, $cantidad_original, $nueva_cantidad, $descripcion, $motivo, $id_tipo_modificacion, $valor_unitario);
	}
	function nota_revision(){
		$jinput = JFactory::getApplication()->input;
		$model = $this->getModel('nota');
        $user = JFactory::getUser();
		$id_remitente 			= $jinput->get("id_remitente", 0, "int");
		$enviado_empleado 		= $jinput->get("enviado_empleado", -1, "int");
		$autorizado_capitan 	= $jinput->get("autorizado_capitan", -1, "int");
		$autorizado_jefe 		= $jinput->get("autorizado_jefe", -1, "int");
		$autorizado_depto 		= $jinput->get("autorizado_depto", -1, "int");
        $autorizado_operaciones	= $jinput->get("autorizado_operaciones", -1, "int");
		$aprobado_adquisiciones	= $jinput->get("aprobado_adquisiciones", -1, "int");
        if ($user->authorise('gerencia_operaciones','com_nota'))
            $autorizado_depto=1;
        
		$query = $model->actualizar_revision($id_remitente, $enviado_empleado, $autorizado_capitan, $autorizado_jefe, $autorizado_depto, $autorizado_operaciones, $aprobado_adquisiciones);
        print_r($query.'-------------------');
	}
	function generar_oc(){
		$jinput = JFactory::getApplication()->input;
		$id_remitente 	= $jinput->get("id_remitente", 0, "int"); 
		$opcion 		= $jinput->get("opcion", 0, "int");
		$num_opciones 	= $jinput->get("num_opciones", 0, "int");
		$proveedor		= $jinput->get("proveedor", "", "string");
		$ley_navarino	= $jinput->get("ley_navarino", 0, "int");
		$model = $this->getModel('adquisiciones');
		$model->setOrden($id_remitente, $opcion, $proveedor, $num_opciones);
	}
	
	function gestion_usuarios(){
		$jinput = JFactory::getApplication()->input;
		$jinput->set('view', 'nota');
		$jinput->set( 'layout', 'gestion_usuarios' );
		$model = $this->getModel('nota');
		$user = JFactory::getUser();
		parent::display();
	}
	function actualizar_depto(){
		$jinput = JFactory::getApplication()->input;
		$model = $this->getModel("nota");
		$id_user 			= $jinput->get("id_user", 0, "int");
		$id_depto 			= $jinput->get("id_depto", 0, "int");
		$id_depto_actual 	= $jinput->get("id_depto_actual", 0, "int");
		$query = $model->actualizar_depto($id_user, $id_depto, $id_depto_actual);
	}
	function actualizar_nivel(){
		$jinput = JFactory::getApplication()->input;
		$model = $this->getModel("nota");
		$id_user 	= $jinput->get("id_user", 0, "int");
		$id_nivel	= $jinput->get("id_nivel", 1, "int");
		$query = $model->actualizar_nivel($id_user, $id_nivel);
	}
	function ley_navarino(){
		$jinput = JFactory::getApplication()->input;
		$model = $this->getModel("adquisiciones");
		$ley_navarino = $jinput->get("ley_navarino", -1, "int");
		$id_remitente = $jinput->get("id_remitente", 0, "int");
		$model->actualizarLeyNavarino($id_remitente, $ley_navarino);
	}
	function ingresar_facturas(){
		$jinput = JFactory::getApplication()->input;
		$jinput->set('view', 'nota');
		$jinput->set( 'layout', 'ingresar_facturas' );
		parent::display();
	}
	function busqueda_notas(){
		$jinput = JFactory::getApplication()->input;
		$jinput->set('view', 'reportes');
		$jinput->set( 'layout', 'default' );
		$model = $this->getModel("nota");
		$desde 			= $jinput->get("desde", "", "string");
		$hasta 			= $jinput->get("hasta", "", "string");
		$nota_pedido 	= $jinput->get("nota_pedido", 0, "int");
		$orden_compra 	= $jinput->get("orden_compra", 0, "int");
		$depto_origen	= $jinput->get("depto_origen", 0, "int");
		$lista_deptos	= $model->getCentros_costo();
		$notas = array();
		
		if ($hasta=="" && ($nota_pedido+$orden_compra)==0){
			JError::raiseNotice( 100, 'Ingrese los campos requeridos');
		}else{
			$notas = $model->getNotas($desde, $hasta, $nota_pedido, $orden_compra, $depto_origen);
		}
		$jinput->set("notas", $notas);
		$jinput->set("lista_deptos", $lista_deptos);
		parent::display();
	}
	function nota_tramitada(){
		$jinput = JFactory::getApplication()->input;
		$model = $this->getModel("nota");
		$user = JFactory::getUser();
		$id_remitente 	= $jinput->get("id_remitente", 0, "int");
		$terminado 		= $jinput->get("terminado", 0, "int");
		$motivo 		= $jinput->get("motivo", "", "string");
		$generico 		= $jinput->get("generico", 0, "int");
		$nombre 		= $jinput->get("nombre_remitente", "", "string");
		$proveedor		= $jinput->get("proveedor_escogido", "", "string");
		$rut_proveedor	= $jinput->get("rut_proveedor", "", "string");
		$giro_proveedor	= $jinput->get("giro_proveedor", "", "string");
		if (strlen($proveedor)){
			$this->setProveedor($id_remitente, $proveedor, $rut_proveedor, $giro_proveedor);
		}
		$model->tramitado($id_remitente, $terminado, $motivo, $generico, $user->id, $nombre);
		$this->preparar_correo($id_remitente);
	}
	function setProveedor($id_remitente, $proveedor, $rut_proveedor, $giro_proveedor){
		$model 		= $this->getModel("nota");
		$proveedor 	= NotaHelper::msquote($proveedor."_".$rut_proveedor."_".$giro_proveedor);
		$model->setProveedorNota($id_remitente,$proveedor);
	}
	function nota_anotacion(){
		$jinput = JFactory::getApplication()->input;
		$model = $this->getModel("nota");
		$id_remitente 	= $jinput->get("id_remitente", 0, "int");
		$aprobado		= $jinput->get("aprobado", 0, "int");
		$anotacion		= $jinput->get("anotacion", "Sin comentario", "string");
		$model->anotacion_final($id_remitente, $aprobado, $anotacion);
	}
	function notas_depto(){
		$jinput = JFactory::getApplication()->input;
		$jinput->set('view', 'nota');
		$jinput->set( 'layout', 'notas_depto' );
		$model = $this->getModel('nota');
		$notas_depto = $model->notas_depto();
		$jinput->set("notas_depto", $notas_depto);
		parent::display();
	}
	function notas_naves(){
		$jinput = JFactory::getApplication()->input;
		$jinput->set('view', 'nota');
		$jinput->set( 'layout', 'notas_naves' );
		$model = $this->getModel('nota');
		$naves = $model->getNaves();
		$notas_naves = $model->notas_naves();
		$jinput->set("notas_naves", $notas_naves);
		$jinput->set("naves", $naves);
		parent::display();
	}
	function cambiar_destino(){
		$jinput = JFactory::getApplication()->input;
		$model = $this->getModel('nota');
		$id_remitente 		= $jinput->get('id_remitente', 0, 'int');
		$id_adepto			= $jinput->get('id_adepto', 0, 'int');
		$nombre_remitente	= $jinput->get('nombre_remitente', '', 'string');
		$model->cambiar_destino($id_remitente, $id_adepto);
		$this->preparar_correo($id_remitente, $nombre_remitente);
	}
	function reporte_naves(){
		$jinput = JFactory::getApplication()->input;
		$model = $this->getModel('nota');
		$id_nave	= $jinput->get("id_nave", 0, "int");
		$desde		= $jinput->get("desde", "", "string");
		$hasta		= $jinput->get("hasta", "", "string");
		$reporte = array();
		$desde = NotaHelper::fechamysql($desde,2);
		$hasta = NotaHelper::fechamysql($hasta,2);
		$reporte = $model->getReporteNaves($id_nave, $desde, $hasta);
		$desde = NotaHelper::fechamysql($desde,1);
		$hasta = NotaHelper::fechamysql($hasta,1);
		$jinput->set("reporte", $reporte);
		$jinput->set("id_nave",$id_nave);
		$jinput->set("desde", $desde);
		$jinput->set("hasta", $hasta);
		$jinput->set('view', 'nota');
		$jinput->set( 'layout', 'reporte_naves' );
		parent::display();
	}
	function notas_area(){
		$jinput = JFactory::getApplication()->input;
		$jinput->set('view', 'nota');
		$jinput->set( 'layout', 'notas_area' );
		$model = $this->getModel('nota');
		$notas_area = $model->notas_area();
		$jinput->set("notas_area", $notas_area);
		parent::display();
	}

	function editar_naves(){
		$jinput = JFactory::getApplication()->input;
		$jinput->set('view', 'nota');
		$jinput->set( 'layout', 'editar_naves' );
		$model = $this->getModel('nota');
		$naves = $model->getLista_naves();
		$fecha_actual = strtotime(date('Y-m-d'));
		$i=0;
		foreach ($naves as $n){
			$naves[$i]['activo'] = 1;
			if ($fecha_actual<=strtotime($n['fin'])){
				if ($fecha_actual<strtotime($n['inicio']))
					$naves[$i]['activo'] = 0;
			}else{
				$naves[$i]['activo'] = 0;
			}
			$i++;
		}
		$jinput->set("naves", $naves);
		parent::display();
	}

	function setNavarino(){
		$jinput = JFactory::getApplication()->input;
		$model = $this->getModel('nota');
		$id_nave 		= $jinput->get('id_nave',0,'int');
		$desde			= $jinput->get('desde','','string');
		$hasta 			= $jinput->get('hasta','','string');
		$ley_navarino 	= $jinput->get('ley_navarino',-1,'int');
		$model->setNavarino($id_nave, $desde, $hasta, $ley_navarino);

		// verificar si la edición debe hacerse desde el mismo día de efectuado
		$this->revision_navarino();
	}
	function revision_navarino(){
		$model = $this->getModel('nota');
		$replicacion = $this->getModel('replicacion');
		$fecha_actual = strtotime(date('Y-m-d'));
		$naves = $model->getLista_naves();
		foreach ($naves as $n){
			if ($n['id_vigencia']){
				if ($fecha_actual>=strtotime($n['inicio'])){
					if ($fecha_actual<=strtotime($n['fin'])){
						$model->actualizar_navarino($n['id'],$n['navarino_programado']);
						
					}
					else{
						$model->actualizar_navarino($n['id'],($n['navarino_programado'] ? 0 : 1));
					}
				}
			}
		}
	}

	/*
	Consulta timis para buscar notas por nombre de item aproximado
	 select nr.id as nota, nr.fecha, ni.cantidad, ni.item, u.name as usuario, nrem.nombre as tripulante, od.nombre as depto_origen, cc.centro_costo 
from nota_remitente nr
join nota_item ni on ni.id_remitente=nr.id and ni.item like '%extintor%'
join nota_user nu on nu.id_user=nr.id_user
join jml_users u on u.id=nr.id_user
join oti_departamento od on od.id=nu.id_depto 
join (select nr.id, od.nombre as centro_costo from oti_departamento od, nota_remitente nr where nr.id_depto_costo=od.id) cc on cc.id=nr.id 
left join nota_nombreRemitente nrem on nrem.id_remitente=nr.id 
where nr.fecha > '2019-01-01'
	 * 
	 */
}