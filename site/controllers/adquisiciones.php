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
	public function lista_notas(){
		$jinput = JFactory::getApplication()->input;
		$user = JFactory::getUser();
		if ($user->authorise('adquisiciones.jefe', 'com_nota')){
			$jinput->set('view', 'adquisiciones');
			$jinput->set('layout', 'lista_notas');
			$model = $this->getModel('adquisiciones');
			$lista_notas = $model->getLista_notas();
			
			$jinput->set("lista_notas", $lista_notas);
			//$borrar_notas = $model->borrar_atrasadas();
		}else{
			$msg = JFactory::getApplication();
			$msg->enqueueMessage("No posee permisos para esta vista", 'error');
			$jinput->set('view', '');
			$jinput->set('layout', '');
		}
		
		parent::display();
	}
	public function opcion_oc(){
		$jinput = JFactory::getApplication()->input;
		$user = JFactory::getUser();
		if ($user->authorise('adquisiciones.jefe', 'com_nota')){
			$jinput->set('view', 'adquisiciones');
			$jinput->set('layout', 'opcion_oc');
			$id_remitente = $jinput->get("id_remitente", 0, "int");
			$model = $this->getModel('adquisiciones');
			$nota = $this->getModel("nota");
			$datos_nota 	= $nota->getDetalle_nota($id_remitente);
			$depto_costo 	= $nota->getDatos_depto($datos_nota['id_depto_costo']);
			$centros_costo 	= $nota->getCentros_costo();
			$items 			= $model->items($id_remitente);
			$item = array();
			foreach ($items as $i){
				$item[$i['id']]['id'] 				= $i['id'];
				$item[$i['id']]['cantidad'] 		= $i['cantidad'];
				$item[$i['id']]['item'] 			= $i['item'];
				$item[$i['id']]['motivo'] 			= $i['motivo'];
				$item[$i['id']]['adjunto'] 			= $i['adjunto'];
				$item[$i['id']]['nueva_cantidad'] 	= $i['nueva_cantidad'];
				$item[$i['id']]['opcion_oc'] 		= $i['opcion_oc'];
			}
			$jinput->set("items", $item);
			$jinput->set("id_remitente", $id_remitente);
			$jinput->set("depto_costo", $depto_costo);
			$jinput->set("centros_costo", $centros_costo);
		}else{
			$jinput->set('view', '');
			$jinput->set('layout', '');
		}
		parent::display();
	}
	public function cambiar_opcion(){
		$jinput = JFactory::getApplication()->input;
		$model = $this->getModel("adquisiciones");
		$id_item 	= $jinput->get('id_item', 0, 'int');
		$opcion 	= $jinput->get('opcion', 0, 'int');
		$model->cambiar_opcion($id_item, $opcion);
	}
	public function cambiar_cc(){
		$jinput = JFactory::getApplication()->input;
		$model = $this->getModel("adquisiciones");
		$id_remitente 		= $jinput->get("id_remitente", 0, "int");
		$id_centro_costo 	= $jinput->get("id_centro_costo", 0, "int");
		$model->actualiza_cc($id_remitente, $id_centro_costo);
	}
	public function ver_nota(){
		$jinput = JFactory::getApplication()->input;
		$jinput->set('view', 'adquisiciones');
		$jinput->set('layout', 'ver_nota');
		$model = $this->getModel("adquisiciones");
		
		$id_remitente = $jinput->get("id_remitente", 0, "int");
		$items = $model->items($id_remitente);
		$item = array();
		foreach ($items as $i){
			$item[$i['id']]['id'] 				= $i['id'];
			$item[$i['id']]['cantidad'] 		= $i['cantidad'];
			$item[$i['id']]['item'] 			= $i['item'];
			$item[$i['id']]['motivo'] 			= $i['motivo'];
			$item[$i['id']]['adjunto'] 			= $i['adjunto'];
			$item[$i['id']]['nueva_cantidad'] 	= $i['nueva_cantidad'];
			$item[$i['id']]['opcion_oc'] 		= $i['opcion_oc'];
		}
		$jinput->set("items", $item);
		$jinput->set("id_remitente", $id_remitente);
		parent::display();
	}
	public function generar_oc(){
		$jinput = JFactory::getApplication()->input;
		$user = JFactory::getUser();
		if ($user->authorise('adquisiciones.jefe', 'com_nota')){
			$jinput->set('view', 'adquisiciones');
			$jinput->set('layout', 'generar_oc');
			$model = $this->getModel('adquisiciones');
			$lista_notas = $model->getLista_oc();
			$jinput->set("lista_notas", $lista_notas);
		}else{
			$msg = JFactory::getApplication();
			$msg->enqueueMessage("No posee permisos para esta vista", 'error');
			$jinput->set('view', '');
			$jinput->set('layout', '');
		}
		parent::display();
	}
	public function regenerar_oc(){
		$jinput = JFactory::getApplication()->input;
		$user = JFactory::getUser();
		if ($user->authorise('adquisiciones.jefe', 'com_nota')){
			$jinput->set('view', 'adquisiciones');
			$jinput->set('layout', 'regenerar_oc');
			$orden_compra = $jinput->get('orden_compra', 0, 'int');
			$model = $this->getModel('adquisiciones');

			$datos_oc 	= $model->getOrden_compra($orden_compra);
			$datos_nota = $model->getDatosNotaOc($orden_compra);
			$jinput->set('orden_compra', $orden_compra);
			$jinput->set('datos_oc', $datos_oc);
			$jinput->set('datos_nota', $datos_nota);
		}else{
			$msg = JFactory::getApplication();
			$msg->enqueueMessage("No posee permisos para esta vista", 'error');
			$jinput->set('view', '');
			$jinput->set('layout', '');
		}
		parent::display();
	}
	public function orden_compra(){
		$jinput = JFactory::getApplication()->input;
		$jinput->set('view', 'adquisiciones');
		$jinput->set('layout', 'orden_compra');
		$model = $this->getModel("adquisiciones");
		$model2 = $this->getModel("nota");
		$id_remitente = $jinput->get('id_remitente', 0, 'int');
		$datos_nota = $model2->getDetalle_nota($id_remitente);
		$items = $model2->getItems($id_remitente);
		$opciones = array();
		foreach ($items as $i){
			$opciones[$i['opcion_oc']] = $i['opcion_oc'];
		}
		$orden = array();
		foreach ($opciones as $o){
			$oc = $model->getDetalle_orden($id_remitente, $o);
			if (sizeof($oc))
				$orden[$o] = 1;
			else 
				$orden[$o] = 0;
		}
		
		$jinput->set("items", $items);
		$jinput->set('id_remitente',$id_remitente);
		$jinput->set("datos_nota", $datos_nota);
		$jinput->set("orden", $orden);
		$model = $this->getModel('adquisiciones');
		parent::display();
	}
	public function nueva_reserva(){
		$jinput = JFactory::getApplication()->input;
		$jinput->set('view', 'externa');
		$jinput->set( 'layout', 'nueva_reserva' );
		$cantidad_pasajeros = $jinput->get('cantidad_pasajeros', 0, 'int');
		$fecha_salida 		= $jinput->get('fecha_salida', '', 'string');
		$origen				= $jinput->get('origen', '', 'string');
		$destino			= $jinput->get('destino', 0, 'int');
		$tipo_vehiculo		= $jinput->get('tipo_vehiculo', 0, 'int');
		$jinput->set('cantidad_pasajeros', $cantidad_pasajeros);
		$jinput->set('fecha_salida', $fecha_salida);
		$jinput->set('origen', $origen);
		$jinput->set('destino', $destino);
		$jinput->set('tipo_vehiculo', $tipo_vehiculo);
		parent::display();
	}
	public function buscar_oc(){
		$jinput = JFactory::getApplication()->input;
		$user = JFactory::getUser();
		if ($user->authorise('adquisiciones.jefe', 'com_nota') || $user->authorise('facturacion', 'com_nota')){
			$jinput->set('view', 'adquisiciones');
			$jinput->set('layout', 'buscar_oc');
			$orden_compra 	= $jinput->get('orden_compra', 0, 'int');
			$nota_pedido 	= $jinput->get('nota_pedido', 0, 'int');
			$model = $this->getModel('adquisiciones');

			$items_oc = array();
			$datos 		= $model->getDatosNotaOc($orden_compra, $nota_pedido);
			foreach ($datos as $d){
				$items_oc[$d['orden_compra']] = $model->getItems_oc($d['orden_compra']);
			}
			
			$jinput->set('orden_compra', $orden_compra);
			$jinput->set('nota_pedido', $nota_pedido);
			$jinput->set('items_oc', $items_oc);
			$jinput->set('datos', $datos);
		}else{
			$msg = JFactory::getApplication();
			$msg->enqueueMessage("No posee permisos para esta vista", 'error');
			$jinput->set('view', '');
			$jinput->set('layout', '');
		}
		parent::display();
	}

	function menuprincipal() {
		$jinput = JFactory::getApplication()->input;
		$jinput->set('view', 'r2');
		$jinput->set( 'layout', 'default' );
		parent::display(); 
	}
	public function actualiza_ley_navarino(){
		$jinput = JFactory::getApplication()->input;
		$model = $this->getModel('adquisiciones');
		$id_remitente	= $jinput->get('id_remitente', 0, 'int');
		$ley_navarino	= $jinput->get('ley_navarino', 0, 'int');
		$model->actualizarLeyNavarino($id_remitente, $ley_navarino);
	}
	public function generarOrden(){
		$jinput = JFactory::getApplication()->input;
		$id_remitente	= $jinput->get('id_remitente', 0, 'int');
		$orden_compra	= $jinput->get('orden_compra', 0, 'int');
		$opcion 		= $jinput->get('opcion', 1, 'int');
		$proveedor 		= $jinput->get('proveedor', '', 'string');
		$num_opciones	= $jinput->get('opciones', 1, 'int');
		$solo_imprimir	= $jinput->get('solo_imprimir', 0, 'int');
		$model	= $this->getModel('nota');
		$model2 = $this->getModel('adquisiciones');
		$datos_nota = $model->getDetalle_nota($id_remitente);
		$items = $model->getItems($id_remitente);
		$datos_oc = $model2->getDetalle_orden($id_remitente, $opcion);
		if (sizeof($datos_oc)) $solo_imprimir = 1;
		if (!$solo_imprimir){
			$model2->setOrden($id_remitente, $opcion, $proveedor, $num_opciones);
		}
		$items = $model2->items($id_remitente);
		$datos_oc = $model2->getDetalle_orden($id_remitente, $opcion);

        $document = JFactory::getDocument();
		$meses = array('01' => 'enero', '02' => 'febrero', '03' => 'marzo', '04' => 'abril', '05' => 'mayo',
				'06' => 'junio', '07' => 'julio', '08' => 'agosto', '09' => 'septiembre', '10' => 'octubre', '11' => 'noviembre', '12' => 'diciembre');

		if ( NotaHelper::isTestSite() ){
			$url = "/var/www/portal/media/notas_pedido/Orden_compra.pdf";
			$archivo = "/var/www/portal/libraries/joomla/document/pdf/pdf.php";
		}else{
			$url = "/var/www/clients/client2/web4/web/portal/media/notas_pedido/Orden_compra.pdf";
			$archivo = "/var/www/clients/client2/web4/web/portal/libraries/joomla/document/pdf/pdf.php";
        }
		require_once($archivo);
		$html = $this->orden_html($datos_nota, $items, $opcion, $proveedor, $datos_oc);
		$pdf = new JDocumentpdf();
		$pdf->guardar_oc($url, $html);
		//$pdf->carga_html($html);
	}
	public function generar_nota(){
		$jinput = JFactory::getApplication()->input;
		$id_remitente	= $jinput->get('id_remitente', 0, 'int');
		$model	= $this->getModel('nota');
		$model2 = $this->getModel('adquisiciones');
		$datos_nota = $model->getDetalle_nota($id_remitente);
		$items = $model->getItems($id_remitente);
		//print_r($items);
		$html = $this->nota_html($datos_nota, $items);
		echo $html;
		if ( NotaHelper::isTestSite() ){
			$url = "/var/www/portal/media/notas_pedido/nota_pedido.pdf";
			$archivo = "/var/www/portal/libraries/joomla/document/pdf/pdf.php";
		}else{
			$url = "/var/www/clients/client2/web4/web/portal/media/notas_pedido/nota_pedido.pdf";
			$archivo = "/var/www/clients/client2/web4/web/portal/libraries/joomla/document/pdf/pdf.php";
		}
		echo $url;
		require_once($archivo);
		$pdf = new JDocumentpdf();
		$pdf->guardar_oc($url, $html);
	}
	function orden_html($datos, $items, $opcion, $proveedor, $datos_oc){
		$meses = array('01' => 'enero', '02' => 'febrero', '03' => 'marzo', '04' => 'abril', '05' => 'mayo',
				'06' => 'junio', '07' => 'julio', '08' => 'agosto', '09' => 'septiembre', '10' => 'octubre', '11' => 'noviembre', '12' => 'diciembre');

		$f = explode("-", $datos['fecha']);
		$fecha_creacion = $f[2].' de '.$meses[$f[1]].' de '.$f[0];
		$file_logo = JPATH_SITE.'/images/logo.png';
		$html = "<style>".$this->estilos()."</style>";
		$html .= '<div style="font-family: sans-serif;">
		<table>
			<tr>
				<td class="encabezados_oc" width="50%">
					<b>TRANSBORDADORA AUSTRAL BROOM S.A.</b><br>
					<font size="1">
						NAVIEROS, TRANSPORTE DE CARGA Y PASAJEROS POR '.htmlentities("VÍAS").' DE<br>
						NAVEGACION, USUARIO DE ZONA FRANCA, RESTAURANTES,<br>
						OTROS TIPOS DE HOSPEDAJES TEMPORAL COMO<br>
						CAMPING, ALBERGUES, POSADAS, REFUGIOS Y SIMILARES<br>
						<i>
							<b>Casa Matriz:</b> Juan Williams #06450 - Punta Arenas<br>
							Casilla 1167 - Fono Mesa Central: 728100<br>
							<b>Sucursales: </b>Avda. Bulnes Km. 3.5 Norte - Punta Arenas<br>
							Bahia Catalina S/N - Punta Arenas<br>
							Bahia Chilota S/N - Porvenir<br>
							<b>RUT: 82.074.900-6</b>
						</i>
					</font>
				</td>
				<td class="encabezados_oc" width="50%" style="padding-left: 50px;">';
		if (NotaHelper::isTestSite()){
			$html .= '<img src="/var/www/portal/images/logo.png">';
		}else{
			$html .= '<img src="/var/www/clients/client2/web4/web/portal/images/logo.png">';
		}
		$html .= '</td>
			</tr>
		</table><br>';
		$html .= '<div class="superior">
				ORDEN DE COMPRA '.$datos_oc['id'].'
			</div>
			<div class="inferior">
				Nota de pedido n'.htmlentities('°').' '.$datos['id_remitente'].'
			</div>';
		$html .= '<div class="datos_entrega">
					Por cuenta de Transbordadora Austral Broom S.A.<br>
					Centro de costo: '.htmlentities($datos['depto_costo']).'<br>
					Solicitado por: '.htmlentities($datos['depto_origen']).'<br>
					'.($proveedor ? "Proveedor: ".htmlentities($proveedor) : "" ).'
				</div>';
		
		$html .= '<table class="tabla_items" border=1 cellspacing=0 cellpadding=2>
			<tr>
				<td width="5%"><b>#</b></td>
				<td width="5%"><b>Cantidad</b></td>
				<td width="40%"><b>Item</b></td>
				<td width="40%"><b>Observaciones</b></td>
			</tr>';
			$j=1;
		foreach ($items as $i){
			if ($i['opcion_oc']==$opcion){
				$cantidad = $i['cantidad'] ? $i['cantidad'] : $i['nueva_cantidad'];
				if ($cantidad){
					$html .= '
					<tr>
						<td>'.$j++.'</td>
						<td>'.$i['cantidad'].'</td>
						<td>'.htmlentities($i['item']).'</td>
						<td>'.htmlentities($i['motivo']).'</td>
					</tr>';
				}
			}
		}
		$html .= '</table>';
		$html .= '<br><br>
			<div class="beneficio">';
			if ($datos['ley_navarino'] && $datos['id_tipo_pedido']==1){
				$html .= 'Facturar con documento especial de venta (ley 18.392) a Transbordadora Austral Broom S.A., rut 82.074.900-6, direcci'.htmlentities('ó').'n Manuel Se'.htmlentities('ñ').'oret #831, Porvenir, exento de IVA';
			}else{
				$html .= 'Facturar a Transbordadora Austral Broom S.A., rut 82.074.900-6, direcci'.htmlentities('ó').'n Juan Williams #06450, Punta Arenas, afecto a IVA';
			}
			$html .= '
			</div>
			<div style="position: absolute; bottom: 60px; width: 40%; left: 400px; z-index: 5; font-size: 13px;">
				<hr/>
				<p style="position: relative; left: 5px; text-align: center; line-height: 1.2;">
					p.p. Transbordadora Austral Broom<br>
					Punta Arenas, '.$fecha_creacion.'
				</p>
			</div>
			</div>';
		return $html;
	}
	function nota_html($datos, $items){
		$meses = array('01' => 'enero', '02' => 'febrero', '03' => 'marzo', '04' => 'abril', '05' => 'mayo',
				'06' => 'junio', '07' => 'julio', '08' => 'agosto', '09' => 'septiembre', '10' => 'octubre', '11' => 'noviembre', '12' => 'diciembre');

		$f = explode("-", $datos['fecha']);
		$fecha_creacion = $f[2].' de '.$meses[$f[1]].' de '.$f[0];
		$html = "<style>".$this->estilos()."</style>";
		$html .= '<div style="font-family: sans-serif;">';
		$html .= '
			<div class="inferior">
				Nota de pedido n'.htmlentities('°').' '.$datos['id_remitente'].'
			</div>';
		$html .= "
			<table class='tabla_items' border=1 cellspacing=0 cellpadding=2>
				<tr>
					<td width='35%'><b>Departamento destino</b></td>
					<td>".htmlentities($datos['depto_destino'])."</td>
				</tr>
				<tr>
					<td width='35%'><b>Departamento origen</b></td>
					<td>".htmlentities($datos['depto_origen'])."</td>
				</tr>
				<tr>
					<td width='35%'><b>Emisor</b></td>
					<td>".(htmlentities($datos['nombre_remitente']) ? htmlentities($datos['nombre_remitente']) : htmlentities($datos['nombre_usuario']))."</td>
				</tr>
				<tr>
					<td width='35%'><b>Prioridad</b></td>
					<td>".$datos['prioridad']."</td>
				</tr>
				<tr>
					<td width='35%'><b>Fecha emisi&oacute;n</b></td>
					<td>".$fecha_creacion."</td>
				</tr>
			</table><br>
		";
		$html .= "<div class='inferior'>Detalle de pedido</div>";
		$html .= '<table class="tabla_items" border=1 cellspacing=0 cellpadding=2>
			<tr>
				<td width="5%"><b>#</b></td>
				<td width="5%"><b>Cantidad</b></td>
				<td width="40%"><b>Item</b></td>
				<td width="40%"><b>Observaciones</b></td>
			</tr>';
			$j=1;
		foreach ($items as $i){
			$cantidad = $i['cantidad'] ? $i['cantidad'] : $i['nueva_cantidad'];
			if ($cantidad){
				$html .= '
				<tr>
					<td>'.$j++.'</td>
					<td>'.$i['cantidad'].'</td>
					<td>'.htmlentities($i['item']).'</td>
					<td>'.htmlentities($i['motivo']).'</td>
				</tr>';
			}
		}
		$html .= '</table>';
		$html .= "</div>";
		/*$html .= "
			<table>
				<tr>
					<td class='encabezado' width='35%'><b>Departamento que recibe: </b></td>
					<td class='encabezado'></td>
				</tr>
				<tr>
					<td class='encabezado' width='35%'><b>Generado por: </b></td>
					<td class='encabezado'></td>
				</tr>
				<tr>
					<td class='encabezado' width='35%'><b>Departamento que envia: </b></td>
					<td class='encabezado'></td>
				</tr>
				<tr>
					<td class='encabezado' width='35%'><b>Caracter: </b></td>
					<td class='encabezado'></td>
				</tr>
				<tr>
					<td class='encabezado' width='35%'><b>Fecha de envio: </b></td>
					<td class='encabezado'></td>
				</tr>
				<tr>
					<td class='encabezado' width='35%'><b>Centro de costo:</b></td>
					<td class='encabezado'></td>
				</tr>
				<tr>
					<td class='encabezado'>Departamento que solicita:</td>
					<td class='encabezado'></td>
				</tr>
			</table>";*/
		return $html;
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
			margin-top: 10px;
			margin-bottom: 20px;
			border: solid black 1px;
			padding: 10px;
		}
		";
		return $style;
	}

/* ===================================================== */
/* ======================= EMAILS ====================== */
	private function enviarEmailReservaExitosa($destinatario, $datos_reserva) {
		$aux_primero_loop = true;
		$txt_ids = '';
		$cant_reservas = 0;
		foreach($datos_reserva['ar_reservas'] as $tipo=>$reserva) {
			if (!$reserva) continue;
			// solo procesar los items que sean un id de reserva
			if ($tipo=='es_nueva') continue;
			if ($aux_primero_loop) $aux_primero_loop = false;
			else $txt_ids .= ', ';
			$txt_ids .= $reserva;
			$cant_reservas++;
		}
		$plural = ($cant_reservas>1)?'s':'';
		$subject = '[TABSA] Nueva'.$plural.' reserva'.$plural.' ' . $txt_ids;
		$body = R2FormatosEmail::generarBodyReservaExitosa($datos_reserva);
		R2Helper::enviarEmail($destinatario, $subject, $body, 'reservas@tabsa.cl', true, true);
	}
}
