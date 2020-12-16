<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
 
/**
 * Nota Model
 */
class NotaModelNota extends JModelItem{
	public function setNota(){
		$user = $JFactory::getUser();
		$datos_user = $this->getDatos_user($user->id);
		$db = JFactory::getDBO();
		$query = "";
		$id = $db->insertid();
	}
	public function getDepartamentos_destino(){
        $db = JFactory::getDBO();
        $query = 'SELECT id, nombre, noteable, id_tipo, id_area, ley_navarino from oti_departamento where activo=1 and noteable=1 order by nombre';
        $db->setQuery($query);
        $db->query();
        $num_rows = $db->getNumRows();
        if ($num_rows === 0) {
			return array();
        }
        return $db->loadAssocList();
    }
	public function getDatos_depto($id_depto){
		$db = JFactory::getDbo();
		$query = "SELECT id, nombre, noteable, id_tipo, id_area, ley_navarino from oti_departamento where id=".$id_depto;
		$db->setQuery($query);
		$db->query();
		return $db->loadAssoc();
	}
	public function getCentros_costo(){
        $db = JFactory::getDBO();
        $query = 'SELECT id, nombre, noteable, id_tipo, id_area, ley_navarino from oti_departamento where activo=1 order by nombre';
        $db->setQuery($query);
        $db->query();
        $num_rows = $db->getNumRows();
        if ($num_rows === 0)
			return array();
		return $db->loadAssocList();
    }
	public function getDatos_user($id_user){
		$db = JFactory::getDBO();
		$query = "SELECT u.id, u.name as nombre_usuario, u.username, od.nombre as departamento, nu.id_nivel, nu.id_depto, nu.generico, od.id_area 
					from jml_users u, nota_user nu, oti_departamento od where u.id=nu.id_user and nu.id_depto=od.id and u.id=".$id_user;
		$db->setQuery($query);
		$db->query();
		if ($db->getNumRows() === 0) 
			return array();
        
        return $db->loadAssoc();
	}
	public function getPrioridad(){
		$db = JFactory::getDbo();
		$query = "select id, desde, hasta, descripcion from nota_prioridad";
		$db->setQuery($query);
		$db->query();
		return $db->loadAssocList();
	}
	public function insertar_nota($id_adepto, $id_user, $fecha, $hora, $id_prioridad, $id_depto_compra, $id_depto_costo, $proveedor, $ley_navarino, $id_tipo_pedido, $cotizacion=""){
		$db = JFactory::getDbo();
		$query = "insert into nota_remitente(id_adepto, id_user, fecha, hora, id_prioridad, id_depto_compra, borrador, id_depto_costo, proveedor, ley_navarino, id_tipo_pedido, cotizacion) 
			values(".$id_adepto.",".$id_user.", ".$fecha.", ".$hora.", ".$id_prioridad.",".$id_depto_compra.", 0, ".$id_depto_costo.", ".(strlen($proveedor) ? $proveedor : '""').",".$ley_navarino.", ".$id_tipo_pedido.", '".$cotizacion."')";
		$db->setQuery($query);
		$db->query();
		$id_remitente = $db->insertid();
		// inserción en nota_revision
		$user = JFactory::getUser();
		$valores = "0,0,0,0,0";
		if ($user->authorise('core.admin', 'com_nota')){
			$usuario = $this->getDatos_user($user->id);
			if ($usuario['id_nivel']==2){
				if ($id_adepto==4)
					$valores = "1,1,1,1,0";
				else
					$valores = "1,1,1,0,0";
			}else{
				$valores = "1,0,0,0,0";
			}
		}
		else{
			if ($user->authorise('tripulante', 'com_nota') || $user->authorise('empleado.depto', 'com_nota')){
				$valores = "1,0,0,0,0";
				//print_r('tripulante - empleado');
			}	
			if ($user->authorise('capitan.jefe', 'com_nota')){
				$valores = "1,1,0,0,0";
				//print_r('capitan');
			}
			if ($user->authorise('jefe.depto', 'com_nota') && !$user->authorise('empleado.depto', 'com_nota')){
				$valores = "1,1,1,0,0";
				//print_r('jefe - no empleado');
			}
			if ($user->authorise('adquisiciones.jefe', 'com_nota') && !$user->authorise('empleado.depto', 'com_nota')){
				$valores = "1,1,1,1,0";
				//print_r('adquisiones');
			}
		}
		
				
		$query = "insert into nota_revision(id_nota_remitente, enviado_empleado, autorizado_capitan, autorizado_jefe, autorizado_depto, aprobado_adquisiciones) 
				values(".$id_remitente.",".$valores.")";
		$db->setQuery($query);
		$db->query();
		return $id_remitente;
	}
	public function setItems($id_remitente, $cantidad, $item, $motivo, $opcion_oc, $valor, $adjunto){
		$db = JFactory::getDbo();
		$query = "insert into nota_item(id_remitente, cantidad, item, motivo, aprobado, opcion_oc, valor, adjunto) 
					values(".$id_remitente.",".$cantidad.",".$item.",".$motivo.", 0, ".$opcion_oc.", ".$valor.",'".$adjunto."')";
		$db->setQuery($query);
		$db->query();
	}

	// no es una query precisa
	public function getExiste_nota($fecha, $id_user, $id_adepto, $id_prioridad, $id_depto_compra, $id_depto_costo, $proveedor, $ley_navarino, $id_tipo_pedido){
		$db = JFactory::getDbo();
		$query = "select id from nota_remitente where fecha=".$fecha." and id_user=".$id_user." and id_adepto=".$id_adepto." 
						and id_prioridad=".$id_prioridad." and id_depto_compra=".$id_depto_compra." and id_depto_costo=".$id_depto_costo." 
						and proveedor=".$proveedor." and ley_navarino=".$ley_navarino." and id_tipo_pedido=".$id_tipo_pedido." ";
		$db->setQuery($query);
		$db->query();
		if ($db->getNumRows())
			return $db->loadResult();
		return 0;
	}

	// para subir archivos
	public function upload($archivo, $id_remitente){
		$db = JFactory::getDbo();
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		$nombre_archivo = JFile::makeSafe($archivo['name']);
		$ruta_tmp = $archivo['tmp_name'];
		$ruta = "/portal/media/notas_pedido/adjuntos/".$id_remitente;
		//$ruta_definitiva = "/var/www/portal/media/notas_pedido/adjuntos/".$id_remitente.'/'.$nombre_archivo;
		$ruta_definitiva = "/var/www/clients/client2/web4/web/portal/media/notas_pedido/adjuntos/".$id_remitente.'/'.$nombre_archivo;
		if (JFILE::upload($ruta_tmp, $ruta_definitiva)) {
			file_put_contents($ruta . '/index.html', '<HTML></HTML>' );
			return true;
		}
	}
	function notas_propias($id_user,$pagina, $parametro=""){
		$db = JFactory::getDbo();
		$query = "select nr.id, nr.id_user, nr.fecha, nrev.enviado_empleado as empleado, 
						nrev.autorizado_capitan as capitan, nrev.autorizado_jefe as jefe, 
						nrev.autorizado_depto as depto, nrev.aprobado_adquisiciones as adquisiciones ";
		$query .= "	from nota_remitente nr 
					join nota_revision nrev on nrev.id_nota_remitente=nr.id ";
		if ($parametro){
			$query .= " join nota_item ni on ni.id_remitente=nr.id and ni.item like '%".$parametro."%' ";
		}
		$query .= " where nr.id_user=".$id_user;
		$query .= " order by nr.id desc ";
		if ($parametro==''){
			$query .= " limit ".(10*$pagina).", 10";
		}
		$db->setQuery($query);
		$db->query();
		if ($db->getNumRows())
			return $db->loadAssocList();
		return array();
	}
	function notas_proveedor($id_user, $proveedor){
		$db = JFactory::getDbo();
		$query = "select nr.id, nr.id_user, nr.fecha, nrev.enviado_empleado as empleado, nrev.autorizado_capitan as capitan, 
					nrev.autorizado_jefe as jefe, nrev.autorizado_depto as depto, nrev.aprobado_adquisiciones as adquisiciones, 
					nr.proveedor, no.id as orden_compra, no.proveedor 
				from nota_ordenDeCompra no join nota_remitente nr on nr.id=no.id_remitente and nr.id_user=".$id_user." 
				join nota_revision nrev on nrev.id_nota_remitente=nr.id where no.proveedor like '%".$proveedor."%'";
		$db->setQuery($query);
		$db->query();
		if ($db->getNumRows()){
			return $db->loadAssocList();	
		}
		return array();
	}
	function getAnotacion($id_remitente){
		$db = JFactory::getDbo();
		$query = "select id, id_remitente, aprobado, anotacion, fecha from nota_anotacion where id_remitente=".$id_remitente." order by id desc limit 1";
		$db->setQuery($query);
		$db->query();
		if ($db->getNumRows())
			return $db->loadAssoc();
		return array();
	}
	function getAnotaciones($id_remitente){
		$db = JFactory::getDbo();
		$query = "select id, id_remitente, aprobado, anotacion, fecha from nota_anotacion where id_remitente=".$id_remitente;
		$db->setQuery($query);
		$db->query();
		if ($db->getNumRows())
			return $db->loadAssocList();
		return array();
	}
	function getDetalle_nota($id_remitente){
		$db = JFactory::getDbo();
		$query = "select nr.id as id_remitente, nr.id_tipo_pedido, nr.ley_navarino, u.name as nombre_usuario, u.email,
					od.nombre as depto_origen, nr.fecha, nr.proveedor, nr.id_depto_costo, nr.cotizacion,
					nnr.nombre as nombre_remitente, nu.id_depto as id_depto_origen, nr.id_user,
					nr.id_adepto, nrev.autorizado_jefe, nrev.autorizado_capitan, nrev.autorizado_depto, nrev.aprobado_adquisiciones, 
					np.descripcion as prioridad, dc.depto_compra, na.aprobado, na.anotacion, na.fecha_anotacion 
				from nota_remitente nr join jml_users u on u.id=nr.id_user 
					join nota_user nu on nu.id_user=u.id 
					join oti_departamento od on od.id=nu.id_depto 
					left join nota_nombreRemitente nnr on nnr.id_remitente=nr.id 
					join nota_revision nrev on nrev.id_nota_remitente=nr.id 
					join nota_prioridad np on np.id=nr.id_prioridad 
					left join 
						(select od.nombre as depto_compra, nr.id 
							from oti_departamento od 
							join nota_remitente nr on nr.id_depto_compra=od.id and nr.id=".$id_remitente.") dc on dc.id=nr.id
					left join 
						(select na.fecha as fecha_anotacion, na.id_remitente, na.aprobado, na.anotacion 
							from nota_anotacion na, nota_remitente nrem 
							where na.id_remitente=nrem.id and nrem.id=".$id_remitente." 
							order by na.id desc limit 1) na on na.id_remitente=nr.id 
				where nr.id=".$id_remitente;
		$db->setQuery($query);
		$db->query();
		$lista = $db->loadAssoc();
		$datos_depto = $this->getDatos_depto($lista['id_adepto']);
		$lista['depto_destino'] = $datos_depto['nombre'];
		
		// departamento centro de costo
		$datos_depto = $this->getDatos_depto($lista['id_depto_costo']);
		$lista['depto_costo'] = $datos_depto['nombre'];
		return $lista;
	}
	function getItems($id_remitente){
		$db = JFactory::getDbo();
		//$query = "select id, cantidad, item, motivo, opcion_oc, adjunto from nota_item where id_remitente=".$id_remitente;
		$query = "select ni.id, ni.cantidad, ni.item, ni.motivo, ni.opcion_oc, ni.valor, ni.adjunto, nm.nueva_cantidad, nm.id_nueva_cantidad, nm.id_tipoModificacion 
					from nota_item ni 
					join nota_remitente nr on nr.id=ni.id_remitente and nr.id=".$id_remitente." 
					left join (select nm.id as id_nueva_cantidad, nm.id_item,nm.nueva_cantidad, nm.id_tipoModificacion 
								from nota_modificada nm, nota_item ni 
								where ni.id=nm.id_item order by nm.id desc limit 1) nm on nm.id_item=ni.id";
		$db->setQuery($query);
		$db->query();
		$lista = $db->loadAssocList();
		if (sizeof($lista)){
			$i=0;
			$lista[$i]['modificacion'] = array();
			foreach ($lista as $l){
				$lista[$i]['modificacion'] = $this->getEliminados($l['id']);
				$i++;
			}
			return $lista;
		}
		return array();
	}
	function items_faltantes(){
		$db = JFactory::getDbo();
	}
	function anular_nota($id_remitente){
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$query = "update nota_revision set "; 
		if (($user->authorise('capitan.jefe', 'com_nota') || $user->authorise('capitan.sin_jefe', 'com_nota')) && !$user->authorise('core.admin', 'com_nota'))
			$query .= " autorizado_capitan=2 ";
		elseif ($user->authorise('jefe.depto', 'com_nota') && !$user->authorise('core.admin', 'com_nota'))
			$query .= " autorizado_jefe=2 ";
		elseif ($user->authorise("adquisiciones.jefe", "com_nota") && !$user->authorise('core.admin', 'com_nota'))
			$query .= " aprobado_adquisiciones=2 ";
		else
			$query .= "enviado_empleado=2 "; 
		$query .= " where id_nota_remitente=".$id_remitente;
		$db->setQuery($query);
		$db->query();
		return $query;
	}
	function anular_nota_depto($id_remitente){
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$query = "update nota_revision set autorizado_depto=2 where id_nota_remitente=".$id_remitente;
		$db->setQuery($query);
		$db->query();
	}
	function notas_jefe($datos_user, $pagina=0){
		$db = JFactory::getDbo();
		$user = JFactory::getUser();

		$deptos = array(
			51 => "",
			69 => "8,33", 
			70 => "37,19",
			71 => "18,26", // crux 
			72 => '9,29', 
			73 => '7,25,86', // bahía
			90 => "40",
			74 => "11,38", // yaghan
			75 => "34,10", 
			76 => "30,22",
			77 => "41",
			106 => "107", // anan 
			108 => "109,110,111,112,113,114" // Kaweskar, 109 sala máquinas borrado
		);
		$query = "select nr.id as id_remitente, nr.fecha, od.nombre as depto_origen, nrev.enviado_empleado as empleado, nrev.autorizado_capitan as capitan, ";
		$query .= " nrev.autorizado_jefe as jefe, nrev.autorizado_depto as depto, nrev.aprobado_adquisiciones as adquisiciones";
		$query .= " from nota_remitente nr 
						join nota_revision nrev on nrev.id_nota_remitente=nr.id 
						join nota_user nu on nu.id_user=nr.id_user
						join oti_departamento od on od.id=nu.id_depto ";
		if ($user->authorise('jefe.delgada','com_nota'))
			$query .= " and (od.id=62 or od.id=".$datos_user['id_depto'].") ";
		elseif ($user->authorise('jefe.depto', 'com_nota'))
			$query .= " and od.id=".$datos_user['id_depto']; 
		if ($user->authorise('capitan.jefe', 'com_nota') || $user->authorise('capitan.sin_jefe', 'com_nota'))
			$query .= " and od.id in (".$deptos[$datos_user['id_depto']].") ";
		$query .= " order by nr.id desc ";

		// tipos de orden según privilegio
		if ($user->authorise('jefe.depto', 'com_nota'))
			$query .= ", nrev.autorizado_jefe ";
		if ($user->authorise('capitan.jefe', 'com_nota') || $user->authorise('capitan.sin_jefe', 'com_nota'))
			$query .= ", nrev.autorizado_capitan ";
		if ($pagina) 
			$query .= ' limit '.(($pagina-1)*10).', 10';
		else
			$query .= ' limit 10';
		$db->setQuery($query);
		$db->query();
		return $db->loadAssocList();
	}
	function editar_item($id_item, $cantidad_original, $nueva_cantidad, $descripcion, $motivo, $id_tipo_modificacion, $valor=0 ){
		$db = JFactory::getDbo();
		if ($descripcion!="" || $motivo!=''){
			$query = "update nota_item set item='".$descripcion."', motivo='".$motivo."', valor=".$valor." where id=".$id_item;
		}
		if ($cantidad_original!=$nueva_cantidad){
			$user = JFactory::getUser();
			$fecha = date("Y-m-d");
			$hora = date("H:i");
			$query = "insert into nota_modificada(id_item, cantidad_original, nueva_cantidad, id_user, motivo, fecha, hora, id_tipoModificacion) 
						values(".$id_item.", ".$cantidad_original.", ".$nueva_cantidad.", ".$user->id.", '".$motivo."', '".$fecha."', '".$hora."', ".$id_tipo_modificacion.")";
		}
		$db->setQuery($query);
		$db->query();
	}
	function actualizar_revision($id_remitente, $enviado_empleado, $autorizado_capitan, $autorizado_jefe, $autorizado_depto, $aprobado_adquisiciones){
		$db = JFactory::getDbo();
		if (!$aprobado_adquisiciones){
			$query = "update nota_revision set enviado_empleado=".$enviado_empleado.", autorizado_capitan=".$autorizado_capitan.", 
						autorizado_jefe=".$autorizado_jefe.", autorizado_depto=".$autorizado_depto.", aprobado_adquisiciones=".$aprobado_adquisiciones." 
						where id_nota_remitente=".$id_remitente;
			$db->setQuery($query);
			$db->query();
		}else{ // primero revisar si se han sacado todas las OC de una nota
			
		}
	}
	public function tramitado($id_remitente, $terminado, $motivo, $generico, $id_user, $nombre){
		$db = JFactory::getDbo();
		$fecha = date("Y-m-d");
		$hora = date("H:i:00");
		$query = "insert into nota_tramitada(id_remitente, id_user, fecha, hora, terminado, motivo, nombre) 
				values(".$id_remitente.", ".$id_user.", '".$fecha."', '".$hora."', ".$terminado.", '".$motivo."', '".$nombre."')";
		$db->setQuery($query);
		$db->query();
	}
	function buscar_nota($id_user, $id_adepto, $id_prioridad, $tipo, $proveedor, $id_depto_costo, $items, $cantidades){
		$db = JFactory::getDbo();
		$fecha = date('Y-m-d');
		$query = "select id from nota_remitente where id_user=".$id_user." and id_adepto=".$id_adepto." and 
					id_prioridad=".$id_prioridad." and proveedor=".$proveedor." and id_depto_costo=".$id_depto_costo.' and fecha="'.$fecha.'"';
		$db->setQuery($query);
		$db->query();
		if ($db->getNumRows()){
			$id_remitente = $db->loadResult();
			$item = explode(';',$items);
			$cantidad = explode(';', $cantidades);
			for ($i=0;$i<sizeof($item);$i++){
				$query = "select id from nota_item where item=".$item[$i]." and cantidad=".$cantidad[$i]." and id_remitente=".$id_remitente;
				$db->setQuery($query);
				$db->query();
				if ($db->getNumRows())
					return $id_remitente;
			}
			return 0;
		}
		return 0;
	}
	function buscar_nombre($nombre){
		$lista = array();
		$db = JFactory::getDbo();
		$query = "select u.id, u.name, u.username, nu.id_depto, od.nombre as departamento, nu.id_nivel, 
					nv.nivel, nv.observacion 
				from jml_users u 
				left join nota_user nu on nu.id_user=u.id 
				left join oti_departamento od on od.id=nu.id_depto 
				left join nota_niveles nv on nv.id=nu.id_nivel
				where u.name like '%".$nombre."%'";
	
		$db->setQuery($query);
		$db->query();
		$filas = $db->getNumRows();
		if ($filas)
			$lista = $db->loadAssocList();
		return $lista;
	}
	function actualizar_depto($id_user, $id_depto, $id_depto_actual){
		$db = JFactory::getDbo();
		$query = "select id_depto from nota_user where id_user=".$id_user;
		$db->setQuery($query);
		$db->query();
		if ($db->getNumRows()){
			$query = "update nota_user set id_depto=".$id_depto." where id_user=".$id_user;
		}else{
			$query = "insert into nota_user(id_user, id_depto, id_nivel, generico) 
				values(".$id_user.", ".$id_depto.", 1, 0)";
		}
		$db->setQuery($query);
		$db->query();
		return $query;
	}
	function actualizar_nivel($id_user, $id_nivel){
		$db = JFactory::getDbo();
		$query = "update nota_user set id_nivel=".$id_nivel." where id_user=".$id_user;
		$db->setQuery($query);
		$db->query();
		return $query;
	}

	function nombre_remitente($id_remitente, $nombre){
		$db = JFactory::getDbo();
		$query = "insert into nota_nombreRemitente(id_remitente, nombre) values(".$id_remitente.", ".$nombre.")";
		$db->setQuery($query);
		$db->query();
	}

	function getNotas($fecha1, $fecha2, $nota_pedido, $orden_compra, $depto_origen=0, $estado=0){
		$fecha1 = NotaHelper::fechamysql($fecha1,2);
		$fecha2 = NotaHelper::fechamysql($fecha2,2);
		$db = JFactory::getDbo();
		$query = "select nr.id, nr.fecha, u.name as nombre_creador, nrn.nombre as tripulante, nrev.enviado_empleado as empleado, od.nombre as depto_origen,
						nrev.autorizado_capitan as capitan, nrev.autorizado_jefe as jefe, nrev.autorizado_depto as depto, nrev.aprobado_adquisiciones as adquisiciones 
				from nota_remitente nr
				join jml_users u on u.id=nr.id_user 
				join nota_user nu on nu.id_user=nr.id_user ";
		$query .= " join oti_departamento od on od.id=nu.id_depto ";
		if ($depto_origen)
			$query .= " and nu.id_depto=".$depto_origen;
		$query .= " left join nota_nombreRemitente nrn on nrn.id_remitente=nr.id 
				join nota_revision nrev on nrev.id_nota_remitente=nr.id ";
		if ($estado){
			if ($estado == 2)
				$query .= " and (nrev.enviado_empleado=2 || nrev.autorizado_capitan=2 || nrev.autorizado_jefe=2 || nrev.autorizado_depto=2 || nrev.aprobado_adquisiciones=2) ";
		}
		if ($fecha2!='')
			$query .= " where nr.fecha between '".$fecha1."' and '".$fecha2."' ";
		elseif ($nota_pedido!="")
			$query .= " where nr.id=".$nota_pedido;
		elseif ($orden_compra!="")
			$query .= " join nota_ordenDeCompra nor on nor.id=".$orden_compra." and nor.id_remitente=nr.id ";
		$query .= " order by nr.id desc";
		$db->setQuery($query);
		$db->query();
		if ($db->getNumRows())
			return $db->loadAssocList();
		return array();
	}
	function getEtapas($id_remitente){
		$db = JFactory::getDbo();
		$query = "select nt.fecha, nt.hora, nt.terminado, nt.motivo, nt.nombre nombre_tramitador, u.name nombre_usuario
					from nota_tramitada nt, jml_users u where u.id=nt.id_user and nt.id_remitente=".$id_remitente;
		$db->setQuery($query);
		$db->query();
		if ($db->getNumRows())
			return $db->loadAssocList();
		return array();
	}
	function anotacion_final($id_remitente, $aprobado, $anotacion){
		$db = JFactory::getDbo();
		$query = "insert into nota_anotacion(id_remitente, aprobado, anotacion) 
					values(".$id_remitente.", ".$aprobado.", '".$anotacion."')";
		$db->setQuery($query);
		$db->query();
	}
	function getMail_jefe($id_remitente=0){
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
		$email = array('email' => 'jmarinan@tabsa.cl');
		if ($user->authorise('tripulante', 'com_nota') && !$user->authorise('capitan.jefe', 'com_nota') && !$user->authorise('capita.sin_jefe', 'com_nota')){ 
			// entonces enviar correo a capitan de la respectiva nave
			$nave = substr($user->username,1);
			$query = "select u.email from jml_users u, nota_user nu where nu.id_nivel=3 and nu.id_user=u.id and u.username like '%".$nave."'";
			$db->setQuery($query);
			$db->query();
			if ($db->getNumRows())
				$email = $db->loadAssocList();
		} elseif($user->authorise('capita.sin_jefe', 'com_nota')){
			$query = "select nr.id, nr.id_adepto, nu.id_user, u.username, u.email 
						from nota_remitente nr, nota_user nu, jml_users u 
						where nu.id_user=u.id and nu.id_depto=nr.id_adepto and nu.id_nivel=2 and nr.id=".$id_remitente;
			$db->setQuery($query);
			$db->query();
			$email = $db->loadAssocList();
		} elseif($user->authorise('capitan.jefe', 'com_nota')){
			$query = "select id,email from jml_users";
			$db->setQuery($query);
			$db->query();
			$usuarios = $db->loadAssocList();
			foreach($usuarios as $u){
				$usuario = JFactory::getUser($u['id']);
				if ($usuario->authorise("jefe.delgada", "com_nota") && !$usuario->authorise("core.admin", "com_nota"))
					$email[] =  $u;
			}
		} elseif ($user->authorise("jefe.delgada","com_nota")){
			$query = "select u.id, u.email 
						from jml_users u, oti_departamento od, nota_user nu, nota_remitente nr 
						where u.id=nu.id_user and nu.id_depto=od.id and nu.id_depto=nr.id_adepto and nr.id=".$id_remitente;
			$db->setQuery($query);
			$db->query();
			$usuarios = $db->loadAssocList();
			foreach($usuarios as $u){
				$usuario = JFactory::getUser($u['id']);
				if ($usuario->authorise("adquisiciones.jefe", "com_nota") && !$usuario->authorise("core.admin", "com_nota") && !$usuario->block)
					$email[] =  $u;
			}
		} elseif($user->authorise("jefe.depto", "com_nota")){
			$query = " select u.id, u.email, nr.id_adepto 
						from jml_users u 
						join nota_user nu on nu.id_user=u.id 
						join oti_departamento od on od.id=nu.id_depto 
						join nota_remitente nr on nr.id_adepto=od.id and nr.id=".$id_remitente;
			$db->setQuery($query);
			$db->query();
			$usuarios = $db->loadAssocList();
			foreach($usuarios as $u){
				$usuario = JFactory::getUser($u['id']);
				if ($usuario->authorise("adquisiciones.jefe", "com_nota") && !$usuario->authorise("core.admin", "com_nota") && !$usuario->block){
					if ($u['email']!='eadquisiciones@tabsa.cl' && $u['email']!='layancan@tabsa.cl')
						$email[] =  $u;
				}
			}			
		}

		return $email;
	}
	function getPendientes(){
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
		$datos_user = $this->getDatos_user($user->id);
		$query = "";
		if ($user->authorise('capitan.jefe', 'com_nota') || $user->authorise('capitan.sin_jefe', 'com_nota')){
			$nave = substr($datos_user['username'],1);
			$query = "select count(*) as cantidad 
						from nota_remitente nr, nota_revision nrev, jml_users u, nota_user nu 
						where u.id=nr.id_user and u.id=nu.id_user and nu.id_nivel=1 
							and nrev.id_nota_remitente=nr.id and nrev.enviado_empleado=1 
							and nrev.autorizado_capitan=0 and u.username like '%".$nave."'";
		}
		if ($user->authorise('jefe.delgada', 'com_nota')){
			$query = "select count(*) as cantidad 
						from nota_remitente nr, nota_revision nrev, nota_user nu, oti_departamento od 
						where nr.id_user=nu.id_user and nu.id_depto=od.id and od.id_area=4 and nr.id=nrev.id_nota_remitente 
							and nrev.enviado_empleado=1 and nrev.autorizado_capitan=1 and nrev.autorizado_jefe=0";
		}
		if ($user->authorise('jefe.porvenir', 'com_nota')){
			$query = "select count(*) as cantidad 
						from nota_remitente nr, nota_revision nrev, nota_user nu, oti_departamento od 
						where nr.id_user=nu.id_user and nu.id_depto=od.id and od.id_area=6 and nr.id=nrev.id_nota_remitente 
							and nrev.enviado_empleado=1 and nrev.autorizado_jefe=0";
		}
		if ($user->authorise('jefe.depto', 'com_nota')){
			/*$query = "select count(*) as cantidad 
						from nota_remitente nr, nota_revision nrev, nota_user nu, oti_departamento od 
						where nr.id_user=nu.id_user and nu.id_depto=od.id and nr.id_adepto=".$datos_user['id_depto']." and nr.id=nrev.id_nota_remitente 
							and nrev.enviado_empleado=1 and nrev.autorizado_capitan=1 and nrev.autorizado_jefe=0";*/
			$query = "select count(*) as cantidad 
					from nota_remitente nr, nota_revision nrev, nota_user nu 
					where nrev.id_nota_remitente=nr.id and nrev.enviado_empleado=1 and nrev.autorizado_capitan!=2 and nrev.autorizado_jefe=0 and 
						nr.id_user!=".$datos_user['id']." and nr.id_user=nu.id_user and nu.id_depto=".$datos_user['id_depto'];
		}
		if ($user->authorise('jefe.delgada', 'com_nota') && $user->authorise('jefe.depto', 'com_nota')){
			$query = "select count(*) as cantidad 
					from nota_remitente nr, nota_revision nrev, nota_user nu 
					where nrev.id_nota_remitente=nr.id and nrev.enviado_empleado=1 and nrev.autorizado_capitan!=2 and nrev.autorizado_jefe=0 and 
						nr.id_user!=".$datos_user['id']." and nr.id_user=nu.id_user and (nu.id_depto=".$datos_user['id_depto']." or nu.id_depto=62) ";
		}
		$db->setQuery($query);
		$db->query();
		return $db->loadResult();
	}
	function getPendientes_depto(){
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
		$datos_user = $this->getDatos_user($user->id);
		$query = "select count(*) as cantidad 
				from nota_remitente nr, nota_revision nrev, nota_user nu, oti_departamento od 
				where nr.id_user=nu.id_user and nu.id_depto=od.id and nr.id_adepto=".$datos_user['id_depto']." and nr.id=nrev.id_nota_remitente 
					and nrev.enviado_empleado=1 and nrev.autorizado_jefe=1 and nrev.autorizado_depto=0";
		
		$db->setQuery($query);
		$db->query();
		return $db->loadResult();
	}
	function getPendientes_naves(){
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
		$datos_user = $this->getDatos_user($user->id);
		$query = "select count(*) as cantidad 
					from nota_remitente nr 
					join nota_revision nrev on nrev.id_nota_remitente=nr.id and nrev.enviado_empleado=1 and nrev.autorizado_capitan=1 and nrev.autorizado_jefe=0 
					join nota_user nu on nu.id_user=nr.id_user join oti_departamento od on od.id=nu.id_depto";
		if ($user->authorise("jefe.delgada", "com_nota") && $user->authorise("jefe.punta_arenas", "com_nota"))
			$query .= " and (od.id_area=4 or od.id_area=2 or od.id_area=3 or od.id_area=1)";
		elseif ($user->authorise("jefe.delgada", "com_nota"))
			$query .= " and od.id_area=4 ";
		elseif ($user->authorise("jefe.natales", "com_nota"))
			$query .= " and od.id_area=5 ";
		$query .= " and od.id_tipo=2";
		$db->setQuery($query);
		$db->query();
		return $db->loadResult();
	}
	function notas_depto($pagina=0){
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$datos_user = $this->getDatos_user($user->id);
		$query = "select nr.id, nr.fecha, nrev.enviado_empleado as empleado, nrev.autorizado_capitan as capitan, 
					nrev.autorizado_jefe as jefe, nrev.autorizado_depto as depto, nrev.aprobado_adquisiciones as adquisiciones 
				from nota_remitente nr 
				join nota_revision nrev on nrev.id_nota_remitente=nr.id and nrev.enviado_empleado=1 and 
					nrev.autorizado_capitan=1 and autorizado_jefe=1 and 
					nr.id_adepto=".$datos_user['id_depto'];
		$query .= " order by nr.id desc ";
		if ($pagina) 
			$query .= ' limit '.(($pagina-1)*10).', 10';
		else
			$query .= ' limit 0, 10';
		$db->setQuery($query);
		$db->query();
		if ($db->getNumRows()){
			return $db->loadAssocList();
		}
		return array();
	}
	function getNotas_ordenes($fecha1, $fecha2, $nota_pedido, $orden_compra, $centro_costo=0){
		$fecha1 = NotaHelper::fechamysql($fecha1,2);
		$fecha2 = NotaHelper::fechamysql($fecha2,2);
		$db = JFactory::getDbo();
		$query = "select nr.id, nr.fecha, noc.id as orden_compra, od.nombre as centro_costo, noc.proveedor, noc.opcion_oc, nf.factura 
			from nota_remitente nr 
			left join nota_ordenDeCompra noc on noc.id_remitente=nr.id 
			left join nota_factura nf on nf.id_ordenDeCompra=noc.id 
			join nota_revision nrev on nrev.id_nota_remitente=nr.id and nrev.autorizado_capitan=1 
				and nrev.autorizado_jefe=1 and nrev.autorizado_depto=1 and nrev.aprobado_adquisiciones!=2 
			join oti_departamento od on nr.id_depto_costo=od.id ";
		if ($fecha1)
			$query .= " where nr.fecha between '".$fecha1."' and '".$fecha2."' ";
		if ($nota_pedido)
			$query .= " where nr.id=".$nota_pedido;
		if ($orden_compra)
			$query .= " where noc.id=".$orden_compra;
		if ($centro_costo)
			$query .= " and nr.id_depto_costo=".$centro_costo;
		$query .= " order by nr.id desc";
		$db->setQuery($query);
		$db->query();
		if ($db->getNumRows())
			return $db->loadAssocList();
		return array();
	}
	function getDetalle_nota_orden($id_remitente, $orden_compra){
		$db = JFactory::getDbo();
		$query = "select nr.id as id_remitente, nr.id_tipo_pedido, nr.ley_navarino, u.name as nombre_usuario, u.email,
					od.nombre as depto_origen, nr.fecha, nr.proveedor, nr.id_depto_costo, 
					nnr.nombre as nombre_remitente, nu.id_depto as id_depto_origen, nr.id_user, nr.proveedor,
					nr.id_adepto, nrev.autorizado_jefe, nrev.autorizado_capitan, nrev.autorizado_depto, nrev.aprobado_adquisiciones, 
					np.descripcion as prioridad, noc.id as orden_compra, noc.proveedor as proveedor_oc, noc.opcion_oc, nf.factura 
				from nota_remitente nr 
					join jml_users u on u.id=nr.id_user 
					join nota_user nu on nu.id_user=u.id 
					join oti_departamento od on od.id=nu.id_depto 
					left join nota_nombreRemitente nnr on nnr.id_remitente=nr.id 
					join nota_revision nrev on nrev.id_nota_remitente=nr.id 
					join nota_prioridad np on np.id=nr.id_prioridad 
					left join nota_ordenDeCompra noc on noc.id_remitente=nr.id ";
		if ($orden_compra)
			$query .= " and noc.id=".$orden_compra;
		$query .= " left join nota_factura nf on nf.id_ordenDeCompra=noc.id ";
		$query .= " where nr.id=".$id_remitente;
		$db->setQuery($query);
		$db->query();
		$lista = $db->loadAssoc();
		$datos_depto = $this->getDatos_depto($lista['id_adepto']);
		$lista['depto_destino'] = $datos_depto['nombre'];
		
		// departamento centro de costo
		$datos_depto = $this->getDatos_depto($lista['id_depto_costo']);
		$lista['depto_costo'] = $datos_depto['nombre'];
		return $lista;
	}
	function actualiza_proveedor($proveedor, $orden_compra){
		$db = JFactory::getDbo();
		$query = "update nota_ordenDeCompra set proveedor='".$proveedor."' where id=".$orden_compra;
		$db->setQuery($query);
		$db->query();
	}
	function actualiza_factura($factura, $orden_compra){
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$query = "select * from nota_factura where id_ordenDeCompra=".$orden_compra;
		$db->setQuery($query);
		$db->query();
		if ($db->getNumRows()){
			$query = "update nota_factura set factura='".$factura."' where id_ordenDeCompra=".$orden_compra;
			$db->setQuery($query);
			$db->query();
		}else{
			$fecha = date('Y-m-d');
			$query = "insert into nota_factura(id_ordenDeCompra, id_user, fecha, hora, factura) 
					values(".$orden_compra.", ".$user->id.", '".date('Y-m-d')."', '".date('H:i:s')."', '".$factura."')";
			$db->setQuery($query);
			$db->query();
		}
	}
	function datos_orden($orden_compra){
		$db = JFactory::getDbo();
		$query = "select noc.id_remitente, noc.fecha, noc.proveedor, nf.factura 
				from nota_ordenDeCompra noc left join nota_factura nf on nf.id_ordenDeCompra=noc.id where noc.id=".$orden_compra;
		$db->setQuery($query);
		$db->query();
		if ($db->getNumRows())
			return $db->loadAssoc();
		return array();
	}
	function notas_naves($pagina=0, $parametro='', $deptos='', $desde='', $hasta=''){
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$datos_user = $this->getDatos_user($user->id);
		$query = "select nr.id, nr.fecha, od.nombre as depto_origen, u.name as usuario, nrev.enviado_empleado as empleado, nrev.autorizado_capitan as capitan, 
					nrev.autorizado_jefe as jefe, nrev.autorizado_depto as depto, nrev.aprobado_adquisiciones as adquisiciones 
				from nota_remitente nr 
				join nota_revision nrev on nrev.id_nota_remitente=nr.id and nrev.enviado_empleado=1 and nrev.autorizado_capitan=1  
				join nota_user nu on nu.id_user=nr.id_user join jml_users u on u.id=nu.id_user 
				join oti_departamento od on od.id=nu.id_depto ";
		if ($deptos!=''){
			$query .= ' and od.id in ('.$deptos.')';
		}
		if ($user->authorise("jefe.delgada", "com_nota") && $user->authorise("jefe.punta_arenas", "com_nota"))
			$query .= " and (od.id_area=1 or od.id_area=2 or od.id_area=3 or od.id_area=4)";
		elseif ($user->authorise("jefe.delgada", "com_nota"))
			$query .= " and od.id_area=4 ";
		elseif ($user->authorise("jefe.natales", "com_nota"))
			$query .= " and od.id_area=5 ";
		//$query .= " and od.id_tipo=2";

		if ($parametro){
			$query .= " join nota_item ni on ni.id_remitente=nr.id and ni.item like '%".$parametro."%' ";
		}
		if ($desde!=''){
			$query .= ' where nr.fecha between "'.NotaHelper::fechamysql($desde,2).'" and "'.NotaHelper::fechamysql($hasta,2).'" ';
		}
		$query .= " order by nr.id desc ";
		if ($deptos==''){
			if ($parametro!=''){

			}else{
				if ($pagina) 
					$query .= ' limit '.(($pagina-1)*10).', 10';
				else
					$query .= ' limit 10';
			}
		}
		
		$db->setQuery($query);
		$db->query();
		if ($db->getNumRows()){
			return $db->loadAssocList();
		}
		return array();
	}
	function getNiveles(){
		$db = JFactory::getDbo();
		$query = "select id, nivel, observacion from nota_niveles";
		$db->setQuery($query);
		$db->query();
		if ($db->getNumRows())
			return $db->loadAssocList();
		return array();
	}
	function cambiar_destino($id_remitente, $id_adepto){
		$db = JFactory::getDbo();
		$query = "update nota_remitente set id_adepto=".$id_adepto." where id=".$id_remitente;
		$db->setQuery($query);
		$db->query();
		return $query;
	}
	function setProveedorNota($id_remitente,$proveedor){
		$db = JFactory::getDbo();
		$query = "update nota_remitente set proveedor=".$proveedor." where id=".$id_remitente;
		$db->setQuery($query);
		$db->query();
	}
	function pendientes_revision(){
		$db = JFactory::getDbo();
		$u = JFactory::getUser();
		$query = "select nr.id, nr.fecha as fecha_creacion, na.aprobado, noc.id as orden, 
					timestampdiff(day, noc.fecha, now()) as dias_orden, timestampdiff(day, na.fecha, now()) as dias_anotacion 
				from nota_remitente nr 
				left join nota_anotacion na on na.id_remitente=nr.id 
				left join nota_ordenDeCompra noc on noc.id_remitente=nr.id 	
				where nr.fecha>'2020-02-01' and nr.id_user=".$u->id." and (timestampdiff(day, na.fecha, now())>7 or timestampdiff(day, noc.fecha, now())>7)";
		$db->setQuery($query);
		$db->query();
		if ($db->getNumRows())
			return $db->loadAssocList();
		return array();
	}
	function getEliminados($id_item){
		$db = JFactory::getDbo();
		$query = "select cantidad_original, nueva_cantidad, fecha as fecha_modificacion, id_tipoModificacion from nota_modificada where id_item=".$id_item;
		$db->setQuery($query);
		$db->query();
		if ($db->getNumRows())
			return $db->loadAssocList();
		return array();
	}
	function getListaProveedor($str, $rut=""){
		$handle = mssql_connect("flexline.tabsa.lan","sa","Tabsa123") or die("Cannot connect to server");
		$db = mssql_select_db('BDFlexline', $handle) or die("Cannot select database");
		$query = "select CtaCte, CodLegal as rut, RazonSocial, giro 
					from flexline.CtaCte 
					where tipo='proveedor' and empresa='demo'";
		if ($rut!=""){
			$query .= " and CodLegal='".$rut."'";
			$result = mssql_query($query);
			if (!mssql_num_rows($result)){
				return array();
			}
			return mssql_fetch_array($result);
		}else
			$query .= " and RazonSocial like '%".$str."%' or CodLegal like '%".$str."%' 
					order by RazonSocial";
		$result = mssql_query($query);
		$ar = array();
		$i=0;
		if (!mssql_num_rows($result)){
			return array();
		}else{
			while($row = mssql_fetch_assoc($result)){
				$ar[$i] = $row;
				$i++;
			}
		}
		return $ar;
	}
	function getProveedor($str, $rut){
		$handle = mssql_connect("flexline.tabsa.lan","sa","Tabsa123") or die("Cannot connect to server");
		$db = mssql_select_db('BDFlexline', $handle) or die("Cannot select database");
		$query = "select CtaCte, CodLegal as rut, RazonSocial, giro 
					from flexline.CtaCte 
					where tipo='proveedor' and empresa='demo' 
					and CodLegal='".$rut."' and RazonSocial='".$str."'";
		$result = mssql_query($query);
		$array = mssql_fetch_array($result);
		mssql_close($handle);
		if (!sizeof($array)){
			return array();
		}		
		return $array;
	}
	function getNaves(){
		$db = JFactory::getDbo();
		$query = "select id, nave from nota_naves order by nave";
		$db->setQuery($query);
		$db->query();
		if ($db->getNumRows())
			return $db->loadAssocList();
		return array();
	}
	function getSeccionesNaves($id_nave){
		$db = JFactory::getDbo();
		$query = "select od.id as id_depto, nn.nave, od.nombre 
				from oti_departamento od, nota_naveSeccion ns, nota_naves nn 
				where od.id=ns.id_seccion and ns.id_nave=nn.id and ns.id_nave=".$id_nave;
		$db->setQuery($query);
		$db->query();
		if ($db->getNumRows())
			return $db->loadAssocList();
		return array();
	}

	/**
	 * Llamado desde controlador principal, reporte en notas naves
	 */
	function getReporteNaves($id_nave, $desde, $hasta){
		$db = JFactory::getDbo();
		$query = "select nr.id as id_remitente, nr.fecha as fecha_nota, 
					ni.cantidad, ni.item, ni.motivo, ni.id as id_item, 
					nm.nueva_cantidad, ni.opcion_oc, nu.id_user, nu.id_depto, nn.nave, 
					nrev.autorizado_capitan as capitan, nrev.autorizado_jefe as jefe, 
					nrev.autorizado_depto as depto, nrev.aprobado_adquisiciones as emision_oc,
					noc.id as orden_compra, noc.fecha as fecha_oc, od.nombre as depto_destino
				from nota_remitente nr 
				join nota_item ni on ni.id_remitente=nr.id and nr.fecha between '".$desde."' and '".$hasta."' 
				join nota_user nu on nu.id_user=nr.id_user 
				join nota_revision nrev on nrev.id_nota_remitente=nr.id 
				join oti_departamento od on od.id=nr.id_adepto 
				left join (select nm.nueva_cantidad, nm.id_item from nota_modificada nm, nota_item ni 
					where ni.id=nm.id_item order by nm.id desc limit 1) nm on nm.id_item=ni.id 
				left join nota_ordenDeCompra noc on noc.id_remitente=nr.id and noc.opcion_oc=ni.opcion_oc 
				join nota_naveSeccion nns on nns.id_seccion=nu.id_depto 
				join nota_naves nn on nn.id=nns.id_nave";
		if ($id_nave)
			$query .=  " and nn.id=".$id_nave;
		$db->setQuery($query);
		$db->query();
		if ($db->getNumRows())
			return $db->loadAssocList();
		return array();
	}

	function setTipo_gasto($id_remitente, $id_tipo){
		$db = JFactory::getDbo();
		$query = "select id from nota_gastoInversion where id_remitente=".$id_remitente;
		$db->setQuery($query);
		$db->query();

		if (!$db->getNumRows()){
			$query = "insert into nota_gastoInversion(id_remitente, id_tipo) values(".$id_remitente.", ".$id_tipo.")";
			$db->setQuery($query);
			$db->query();
		}
	}

	function getItems_usuario($id_usuario, $item){
		$db = JFactory::getDbo();
		$query = "select ni.item 
				from nota_item ni
				join nota_remitente nr on nr.id=ni.id_remitente and nr.id_user=".$id_usuario."
				where ni.item LIKE '%".$item."%'
				group by ni.item";
		$db->setQuery($query);
		$db->query();
		if ($db->getNumRows()){
			return $db->loadAssocList();
		}
		return array();
	}
}