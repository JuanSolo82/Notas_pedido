<?php
// No direct access.
defined('_JEXEC') or die;

// Include dependancy of the main controllerform class
jimport('joomla.application.component.controllerform');
require_once(JPATH_COMPONENT_SITE.'/assets/helper.php');
require_once(JPATH_COMPONENT_SITE.'/assets/phpqrcode.php');

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
		$proveedor = array();
		if ($datos_nota['proveedor']){
			$p = explode('_', $datos_nota['proveedor']);
			$proveedor['RazonSocial'] = $p[0];
			$proveedor['rut'] = $p[1];
			$proveedor['giro'] = $p[2];
		}
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
		$jinput->set("proveedor", $proveedor);
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
		$user = JFactory::getUser();
		$id_remitente	= $jinput->get('id_remitente', 0, 'int');
		$orden_compra	= $jinput->get('orden_compra', 0, 'int');
		$opcion 		= $jinput->get('opcion', 1, 'int');
		$proveedor 		= $jinput->get('proveedor', '', 'string');
		$rut_proveedor	= $jinput->get('rut_proveedor', '', 'string');
		$giro_proveedor	= $jinput->get('giro_proveedor', '', 'string');
		$num_opciones	= $jinput->get('opciones', 1, 'int');
		$solo_imprimir	= $jinput->get('solo_imprimir', 0, 'int');
		$cotizacion		= $jinput->get('cotizacion', 'no', 'string');
		$model	= $this->getModel('nota');
		$model2 = $this->getModel('adquisiciones');
		$replicacion = $this->getModel('replicacion');
		//$datos_proveedor = $model->getProveedor($proveedor, $rut_proveedor);
		$usuario = $model->getDatos_user($user->id);
		$datos_nota = $model->getDetalle_nota($id_remitente);
		$items = $model->getItems($id_remitente);
		$datos_oc = $model2->getDetalle_orden($id_remitente, $opcion);
		if (sizeof($datos_oc)) $solo_imprimir = 1;
		if (!$solo_imprimir){
			$model2->setOrden($id_remitente, $opcion, $num_opciones, $proveedor."_".$rut_proveedor."_".$giro_proveedor, $rut_proveedor, $giro_proveedor, $cotizacion);
			// sql server
			$query = $replicacion->setOrdenCompra($id_remitente, $opcion, $proveedor, $rut_proveedor, $giro_proveedor, $cotizacion,$usuario);
			print_r($query.'>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
		}
		$datos_oc = $model2->getDetalle_orden($id_remitente, $opcion);
		$p = explode('_', $datos_oc['proveedor']);
		$proveedor = $p[0];
		$rut_proveedor = $p[1];
		$giro_proveedor = $p[2];
        $document = JFactory::getDocument();
		$meses = array('01' => 'enero', '02' => 'febrero', '03' => 'marzo', '04' => 'abril', '05' => 'mayo',
				'06' => 'junio', '07' => 'julio', '08' => 'agosto', '09' => 'septiembre', '10' => 'octubre', '11' => 'noviembre', '12' => 'diciembre');
		$url = JPATH_SITE.'/media/notas_pedido/Orden_compra'.$id_remitente.'-'.$opcion.'.pdf';
		/**
		 * Generador QR
		 */
		$filename = JPATH_SITE.'/media/notas_pedido/qr_ordenes/qr_'.$datos_oc['id'].'.png';
		$matrixPointSize = 3;
		$errorCorrectionLevel = 'L';
		/**
		 * Por mientras para pruebas se usarán los elementos en producción (extensión hecha ahí)
		 */
		$tqr = "http://www.tabsa.cl/portal/index.php/es/validar-nota?ordenCompra=".$id_remitente;
		//$tqr = JUri::base().'index.php/es/validar-nota?ordenCompra='.$id_remitente;

		if (NotaHelper::isTestSite())
			QRcode::png($tqr, $filename, $errorCorrectionLevel, $matrixPointSize, 2);

		require_once(JPATH_LIBRARIES.'/joomla/document/pdf/pdf.php');
		$html = $this->orden_html($datos_nota, $items, $opcion, $datos_oc, $proveedor, $rut_proveedor, $giro_proveedor);
		$pdf = new JDocumentpdf();
		$pdf->guardar_oc($url, $html);

		// envío de correo con adjunto
		if (!NotaHelper::isTestSite())
			$this->enviarOrdenCorreo($id_remitente, JPATH_SITE.'/media/notas_pedido/Orden_compra.pdf');
	}

	public function generar_nota(){
		$jinput = JFactory::getApplication()->input;
		$id_remitente	= $jinput->get('id_remitente', 0, 'int');
		$model	= $this->getModel('nota');
		$model2 = $this->getModel('adquisiciones');
		$datos_nota = $model->getDetalle_nota($id_remitente);
		$items = $model->getItems($id_remitente);
		$html = $this->nota_html($datos_nota, $items);
		require_once(JPATH_LIBRARIES.'/joomla/document/pdf/pdf.php');
		$pdf = new JDocumentpdf();
		$pdf->guardar_oc(JPATH_SITE.'/media/notas_pedido/nota_pedido.pdf', $html);
	}
	function emision_masiva(){
		$jinput = JFactory::getApplication()->input;
		$user = JFactory::getUser();
		$id_remitente	= $jinput->get('id_remitente', 0, 'int');
		$opciones = array();
		$datos_oc = array();
		$pagina = '';
		$model	= $this->getModel('nota');
		$model2 = $this->getModel('adquisiciones');
		$replicacion = $this->getModel('replicacion');
		$datos_nota = $model->getDetalle_nota($id_remitente);
		$usuario = $model->getDatos_user($user->id);
		$items = $model->getItems($id_remitente);
		foreach ($items as $i)
			$opciones[$i['opcion_oc']]=$i['opcion_oc'];
		
		$proveedor='';
		$rut_proveedor='';
		$giro_proveedor='';
		$cotizacion='';
		foreach ($opciones as $o){
			$model2->setOrden($id_remitente, $o, sizeof($opciones), "","","","");
			$datos_oc = $model2->getDetalle_orden($id_remitente, $o, $proveedor, $rut_proveedor, $giro_proveedor, $cotizacion);
			$pagina .= $this->orden_html($datos_nota,$items,$o,$datos_oc,'','','');
			$pagina .= '<div style="page-break-after: always;"></div>';
			$this->generarQR($id_remitente,$datos_oc['id']);
			// sql server
			$replicacion->setOrdenCompra($id_remitente, $o, $proveedor, $rut_proveedor, $giro_proveedor, $cotizacion,$usuario);
		}
		
		$url = JPATH_SITE.'/media/notas_pedido/Orden_compra'.$id_remitente.'.pdf';
		require_once(JPATH_LIBRARIES.'/joomla/document/pdf/pdf.php');
		$pdf = new JDocumentpdf();
		$pdf->guardar_oc($url, $pagina);
		//$model2->revision_adquisiciones($id_remitente);
	}
	function generarQR($id_remitente,$id_oc){
		$filename = JPATH_SITE.'/media/notas_pedido/qr_ordenes/qr_'.$id_oc.'.png';
		$matrixPointSize = 3;
		$errorCorrectionLevel = 'L';
		$tqr = "http://www.tabsa.cl/portal/index.php/es/validar-nota?ordenCompra=".$id_remitente;
		QRcode::png($tqr, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
	}
	function orden_html($datos, $items, $opcion, $datos_oc, $proveedor, $rut_proveedor, $giro_proveedor){
		$meses = array('01' => 'enero', '02' => 'febrero', '03' => 'marzo', '04' => 'abril', '05' => 'mayo',
				'06' => 'junio', '07' => 'julio', '08' => 'agosto', '09' => 'septiembre', '10' => 'octubre', '11' => 'noviembre', '12' => 'diciembre');

		$f = explode("-", $datos['fecha']);
		$fecha_creacion = $f[2].' de '.$meses[$f[1]].' de '.$f[0].', '.$datos_oc['hora'];
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
		$url_firma = '';
		$html .= '<img src="'.JPATH_SITE.'/images/logo.png">';
		$html .= '</td>
			</tr>
		</table><br>';
		
		$html .= '<div class="superior">
					ORDEN DE COMPRA '.$datos_oc['id'].'
				</div>
				<div class="inferior">
					Nota de pedido n'.htmlentities('°').' '.$datos['id_remitente'].'
				</div>';
		$html .= '<table><tr>';
		$html .= '	<td width="75%" style="font-size: 12px;">
						<div class="datos_entrega">
						Por cuenta de Transbordadora Austral Broom S.A.<br>
						Centro de costo: '.htmlentities($datos['depto_costo']).'<br>
						Solicitado por: '.htmlentities($datos['depto_origen']).'<br>';
		if ($proveedor){
			$html .= 'Proveedor: '.htmlentities(ucwords(strtolower(htmlentities($proveedor)))).'<br>';
			$html .= 'Rut: '.$rut_proveedor.'<br>';
			$html .= 'Giro: '.$giro_proveedor;
		}
		if ($datos_oc['cotizacion']!='')
			$html .= '<br>Cotizacion: '.$datos_oc['cotizacion'];
		$html .= '		</div>
					</td>';
		if (NotaHelper::isTestSite()){
			$html .= '	<td align="right" width="25%">
						<div style="border: 1px solid silver; 
								height: 110px; 
								width: 110px;
								position: relative;
								left: 200px;
								background: url(/portal/media/notas_pedido/qr_ordenes/qr_'.$datos_oc['id'].'.png) no-repeat center;
								background-size: cover;
								z-index: 5;">
						</div>';
			$html .= '	</td>';
		}

		$html .= '</tr>
				</table>';
		$html .= '<table class="tabla_items" border=1 cellspacing=0 cellpadding=2>
			<tr>
				<td width="5%"><b>#</b></td>
				<td width="5%"><b>Cantidad</b></td>
				<td width="30%"><b>Item</b></td>
				<td width="30%"><b>Observaciones</b></td>';
			if ($items[0]['valor']){
				$html .= '<td width="10%"><b>Valor unitario</b></td>';
				$html .= '<td width="10%"><b>Subtotal</b></td>';
			}

		$html .= '</tr>';
		$j=1;
		$total = 0;
		foreach ($items as $i){
			if ($i['opcion_oc']==$opcion){
				$cantidad = $i['cantidad'] ? $i['cantidad'] : $i['nueva_cantidad'];
				if ($cantidad){
					$html .= '
					<tr>
						<td>'.$j++.'</td>
						<td>'.$i['cantidad'].'</td>
						<td>'.htmlentities($i['item']).'</td>
						<td>'.htmlentities($i['motivo']).'</td>';
				if ($i['valor']){
					$html .= '<td align="right">'.number_format($i['valor'],0,'','.').'</td>';
					$html .= '<td align="right">'.number_format($i['valor']*$cantidad,0,'','.').'</td>';
					$total += $i['valor']*$cantidad;
				}
				$html .= '</tr>';
				}
			}
		}
		if ($items[0]['valor']){
			$html .= "<tr>";
			$html .= "<td align='right' colspan='5'>Total</td>";
			$html .= "<td align='right'>".number_format($total,0,'','.')."</td>";
			$html .= "</tr>";
		}

		$html .= '</table>';
		$html .= '<br><br>
			<div class="beneficio">';
			if ($datos['ley_navarino']){
				if ($datos['id_tipo_pedido']==1)
					$html .= 'Facturar con documento especial de venta (ley 18.392) a 
								Transbordadora Austral Broom S.A., rut 82.074.900-6, 
								direcci'.htmlentities('ó').'n Manuel Se'.htmlentities('ñ').'oret #831, Porvenir, exento de IVA';
				elseif ($datos['id_tipo_pedido']==2)
					$html .= 'Facturar con documento a Transbordadora Austral Broom S.A., rut 82.074.900-6, direcci'.htmlentities('ó').'n 
								Manuel Se'.htmlentities('ñ').'oret #831, Porvenir, afecto a IVA';
			}else{
				$html .= 'Facturar a Transbordadora Austral Broom S.A., rut 82.074.900-6, direcci'.htmlentities('ó').'n Juan Williams #06450, Punta Arenas, afecto a IVA';
			}
			$html .= '
			</div>
			<div style="position: absolute; bottom: 60px; width: 40%; left: 400px; z-index: 5; font-size: 13px;">';
			if (NotaHelper::isTestSite()){
				$html .= '<div style="text-align: center; position: relative;">
							<img src="'.JPATH_SITE.'/components/com_nota/assets/img/firma.jpg" width="180" height="130">
							<div style="position: absolute; margin-left: 37%; margin-top: 8.6%;">'.$f[2].'-'.$f[1].'-'.$f[0].'</div>
						</div>';
			}

			$html .= '<hr/>
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
			position: relative;
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

	private function enviarOrdenCorreo($id_remitente, $adjunto="") {
		$model = $this->getModel('nota');
		$detalle_nota = $model->getDetalle_nota($id_remitente);
		$subject = '[TABSA] Orden de compra';
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
		$body .= "<h3>Se ha generado su orden de compra</h3><br>";
		$body .= "<p>Se adjunta la copia de orden de compra generada a partir de la nota 
					de pedido nº ".$id_remitente."</p>";
		$body .= "</div>";
		if (NotaHelper::isTestSite())
			NotaHelper::mailAdjunto($subject, $body, 'jmarinan@tabsa.cl', $adjunto);
		else
			NotaHelper::mail("[TABSA] Orden de compra", $body, $detalle_nota['email']);
		
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
