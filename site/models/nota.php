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
				$valores = "1,1,1,1,0";
			}else{
				$valores = "1,0,0,0,0";
			}
		}
		else{
			if ($user->authorise('tripulante', 'com_nota') || $user->authorise('empleado.depto', 'com_nota')){
				$valores = "1,0,0,0,0";
			}	
			if ($user->authorise('capitan.jefe', 'com_nota')){
				$valores = "1,1,0,0,0";
			}
			if ($user->authorise('jefe.depto', 'com_nota') && !$user->authorise('empleado.depto', 'com_nota')){
				$valores = "1,1,1,0,0";
			}
			if ($user->authorise('adquisiciones.jefe', 'com_nota') && !$user->authorise('empleado.depto', 'com_nota')){
				$valores = "1,1,1,1,0";
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
		return 0;//49510
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
		$query = "select nr.id as id_remitente, nr.id_adepto, nr.id_tipo_pedido, nr.ley_navarino, u.name as nombre_usuario, u.email,
					od.nombre as depto_origen, nr.fecha, nr.proveedor, nr.id_depto_costo, nr.cotizacion,
					nnr.nombre as nombre_remitente, nu.id_depto as id_depto_origen, nr.id_user,
					nr.id_adepto, nrev.enviado_empleado, nrev.autorizado_jefe, nrev.autorizado_capitan, nrev.autorizado_depto, nrev.aprobado_adquisiciones, 
					np.descripcion as prioridad, dc.depto_compra, na.aprobado, na.anotacion, na.fecha_anotacion, ne.exenta,
                    nln.lugar, nln.valor, ";
        $query .= " (nrev.autorizado_capitan=1 and nrev.autorizado_jefe=0) as pendiente";
        //$query .= ", (((nln.valor=2 and nrev.autorizado_jefe=1 and nrev.autorizado_depto=0) or 
        //            (nln.valor=1 and nrev.autorizado_capitan=1 and nrev.autorizado_jefe=0))) as pendiente ";
        $query .= " from nota_remitente nr join jml_users u on u.id=nr.id_user 
					join nota_user nu on nu.id_user=u.id 
					join oti_departamento od on od.id=nu.id_depto 
					left join nota_nombreRemitente nnr on nnr.id_remitente=nr.id 
					join nota_revision nrev on nrev.id_nota_remitente=nr.id 
					join nota_prioridad np on np.id=nr.id_prioridad ";
        $query .= " left join nota_deptoLugar ndl on ndl.id_depto=nr.id_depto_costo 
					left join nota_lugarNave nln on nln.id=ndl.id_lugar ";
        $query .= " left join (select od.nombre as depto_compra, nr.id 
							from oti_departamento od 
							join nota_remitente nr on nr.id_depto_compra=od.id and nr.id=".$id_remitente.") dc on dc.id=nr.id
					left join 
						(select na.fecha as fecha_anotacion, na.id_remitente, na.aprobado, na.anotacion 
							from nota_anotacion na, nota_remitente nrem 
							where na.id_remitente=nrem.id and nrem.id=".$id_remitente." 
							order by na.id desc limit 1) na on na.id_remitente=nr.id 
                    left join nota_exenta ne on ne.id_remitente=nr.id
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
	function getItems($id_remitente, $opcion_oc=0){
		$db = JFactory::getDbo();
		//$query = "select id, cantidad, item, motivo, opcion_oc, adjunto from nota_item where id_remitente=".$id_remitente;
		$query = "select ni.id, ni.cantidad, ni.item, ni.motivo, ni.opcion_oc, ni.valor, ni.adjunto ";
        $query .= ", nm.nueva_cantidad, nm.id_nueva_cantidad, nm.id_tipoModificacion ";
        $query .= " from nota_item ni 
					join nota_remitente nr on nr.id=ni.id_remitente and nr.id=".$id_remitente;
        
        $query .= " left join (select nm.id as id_nueva_cantidad, nm.id_item,nm.nueva_cantidad, nm.id_tipoModificacion 
					from nota_modificada nm, nota_item ni 
					where ni.id=nm.id_item order by nm.id desc limit 1) nm on nm.id_item=ni.id";
        if ($opcion_oc)
            $query .= " where ni.opcion_oc=".$opcion_oc;
		$db->setQuery($query);
		$db->query();
		$lista = $db->loadAssocList();
		if (sizeof($lista)){
			$i=0;
			$lista[$i]['modificacion'] = array();
			foreach ($lista as $l){
				$lista[$i]['modificacion'] = $this->getEliminados($l['id']);
                /*$lista[$i]['id_nueva_cantidad'] = 0;
                $lista[$i]['nueva_cantidad'] = 0;
                $lista[$i]['id_tipo_modificacion'] = 0;
                // modificaciones si es que existen
                $query = "select id as id_nueva_cantidad, nueva_cantidad, id_tipoModificacion 
                            from nota_modificada 
                            where id_item = ".$l['id']." 
                            order by id desc limit 1";
                $db->setQuery($query);
                $db->query();
                if ($db->getNumRows()){
                    $item = $db->loadAssoc();
                    $lista[$i]['id_nueva_cantidad'] = $item['id_nueva_cantidad'];
                    $lista[$i]['nueva_cantidad'] = $item['nueva_cantidad'];
                    $lista[$i]['id_tipo_modificacion'] = $item['id_tipo_modificacion'];
                }
                */
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
			76 => "30,22,99",
			77 => "41",
			106 => "107", // anan  
			108 => "109,110,111,112,113,114", // Kaweskar, 109 sala máquinas borrado
            94 => "94,14,120", // administracion y finanzas, encargado de departamentos de personal y bodega (120 en produccion, 116 servidor de pruebas)
            149 => "150,151" // Kenos
		);
		$query = "select nr.id as id_remitente, nr.fecha, od.nombre as depto_origen, nrev.enviado_empleado as empleado, nrev.autorizado_capitan as capitan, ";
		$query .= " nrev.autorizado_jefe as jefe, nrev.autorizado_depto as depto, nrev.aprobado_adquisiciones as adquisiciones";
		$query .= " from nota_remitente nr 
						join nota_revision nrev on nrev.id_nota_remitente=nr.id 
						join nota_user nu on nu.id_user=nr.id_user
						join oti_departamento od on od.id=nu.id_depto ";
		if ($user->authorise('jefe.delgada','com_nota'))
			$query .= " and (od.id=62 or od.id=".$datos_user['id_depto'].") ";
		elseif ($user->authorise('jefe.depto', 'com_nota')){
            if ($datos_user['id_depto']==94){ // finanzas
                $query .= " and od.id in (".$deptos[$datos_user['id_depto']].") ";
            }else{
                $query .= " and od.id=".$datos_user['id_depto']; 
            }
            
        }
		if ($user->authorise('capitan.jefe', 'com_nota') || $user->authorise('capitan.sin_jefe', 'com_nota'))
			$query .= " and od.id in (".$deptos[$datos_user['id_depto']].") ";
		$query .= " order by nr.id desc ";
		// tipos de orden según privilegio
		if ($user->authorise('jefe.depto', 'com_nota')){
            $query .= ", nrev.autorizado_jefe ";
        }
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
						autorizado_jefe=".$autorizado_jefe.", autorizado_depto=".$autorizado_depto;
            $query .= ", aprobado_adquisiciones=".$aprobado_adquisiciones." 
						where id_nota_remitente=".$id_remitente;
			$db->setQuery($query);
			$db->query();
		}else{ // primero revisar si se han sacado todas las OC de una nota
			
		}
        return $query;
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
		$email = array();
		if ($user->authorise('tripulante', 'com_nota') && !$user->authorise('capitan.jefe', 'com_nota') && !$user->authorise('capitan.sin_jefe', 'com_nota')){ 
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
            $id_area=0;
            $query = "select od.id_area 
                        from oti_departamento od, nota_user nu 
                        where od.id=nu.id_depto and nu.id_user=".$user->id;
            $db->setQuery($query);
            $db->query();
            $id_area = $db->loadResult();

			$query = "select u.id, od.id as id_depto, u.email, u.name 
                        from jml_users u, nota_user nu, oti_departamento od 
                    where u.id=nu.id_user and nu.id_depto=od.id ";
            if ($id_area==5)
                $query .= " and od.id_area=".$id_area;
			$db->setQuery($query);
			$db->query();
			$usuarios = $db->loadAssocList();
			foreach($usuarios as $u){
				$usuario = JFactory::getUser($u['id']);
                if (!$usuario->authorise("core.admin", "com_nota") && !$usuario->block){
                    if (($usuario->authorise("jefe.delgada", "com_nota") && $u['id_depto']==51) || ($usuario->authorise("jefe.natales", "com_nota") && $id_area==5))
					    $email[] =  $u;
                }
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
		}elseif ($user->authorise("empleado.depto","com_nota")){
            if ($user->authorise('resumen_area','com_nota')){ // los funcionarios de oficina de PD que no son tripulantes
                $query = "select u.id, u.name, u.email, u.block 
                            from jml_users u 
                            join nota_user nu on nu.id_user=u.id and nu.id_depto=51 and nu.id_nivel=2 and u.block=0 
                            where u.id<290";
            }else{
                $query = "SELECT nu.id_depto, jefe.email FROM nota_user nu
						join (select u.email, nu.id_depto, u.block from jml_users u, nota_user nu where u.id=nu.id_user and nu.id_nivel=2) jefe on jefe.id_depto=nu.id_depto
						WHERE nu.id_user=".$user->id;
            }
			$db->setQuery($query);
			$db->query();
			$usuarios = $db->loadAssocList();
			foreach ($usuarios as $u){
				$email[] =  $u;
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
							and nrev.autorizado_capitan=0 and u.name like '%".$nave."%'";
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
            $query = "select count(*) as cantidad 
                    from nota_remitente nr, nota_revision nrev, nota_user nu, oti_departamento od  
                    where nrev.id_nota_remitente=nr.id and nrev.enviado_empleado=1 
                        and nrev.autorizado_capitan!=2 and nrev.autorizado_jefe=0 and nrev.aprobado_adquisiciones=0 
                        and nr.id_user!=".$datos_user['id']." and nr.id_user=nu.id_user and od.id=nu.id_depto ";
            if ($datos_user['id_depto']==94){
                $query .= " and nu.id_depto in (14,120)";
            }else
                $query .= " and nu.id_depto=".$datos_user['id_depto'];
		}
		if ($user->authorise('jefe.delgada', 'com_nota') && $user->authorise('jefe.depto', 'com_nota')){
			$query = "select count(*) as cantidad 
					from nota_remitente nr, nota_revision nrev, nota_user nu 
					where nrev.id_nota_remitente=nr.id and nrev.enviado_empleado=1 and nrev.autorizado_capitan!=2 and nrev.autorizado_jefe=0 and 
						nr.id_user!=".$datos_user['id']." and nr.id_user=nu.id_user and (nu.id_depto=".$datos_user['id_depto']." or nu.id_depto=62) ";
		}
        $query .= " and nrev.aprobado_adquisiciones=0";
        $query .= " and nr.fecha>'2022-01-01'";
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
				where nr.id_user=nu.id_user and nu.id_depto=od.id ";
        if ($user->username=='stimis')
            $query .= " and nr.id_adepto in (".$datos_user['id_depto'].", 51)";
        else
            $query .= " and nr.id_adepto=".$datos_user['id_depto'];
        $query .= " and nr.id=nrev.id_nota_remitente 
					and nrev.enviado_empleado=1 and nrev.autorizado_jefe=1 and nrev.autorizado_depto=0 and nrev.aprobado_adquisiciones=0";
        if ($user->id==106 || $user->id==321)
            $query .= " and od.id_tipo!=2 ";
        $query .= " and nr.fecha>'2022-01-01'";
		$db->setQuery($query);
		$db->query();
        
		return $db->loadResult();
	}
	function getPendientes_naves(){
        //notas_naves($pagina=0, $parametro='', $deptos='', $desde='', $hasta='')
        $cantidad = $this->notas_naves(0,'','','','',1);
        return $cantidad;
		/*$user = JFactory::getUser();
		$db = JFactory::getDbo();
		$datos_user = $this->getDatos_user($user->id);
        $ar_dependencias = array(
            (127) => "18,71, 8,69, 9,72, 11,74", // pablo sierpe -> puentes: Crux, Patagonia, Fueguino, Yaghan
            (NotaHelper::isTestSite() ? 293 : 305) => '77, 21,90, 7,73, 22,76,99,102', // Luis Rosales -> puentes: Toucan, skua, Bahía Azul, Melinka
            78 => "108,111,112, 19,36,70,89, 106, 10,35,75,87", // Gustavo Mancilla -> Kaweskar, Pionero, Anan, Pathagon
			81 => "108,111,112, 19,36,70,89, 106, 10,35,75,87", // Sebsatián Timis -> Kaweskar, Pionero, Anan, Pathagon
			326 => "108,111,112, 19,36,70,89, 106, 10,35,75,87", // Nelson Ormeño -> Kaweskar, Pionero, Anan, Pathagon
            106 => '25,26,29,30,33,34,37,38,40,41,100,107,113', // hgonzalez, todos maquina
            226 => "18,71, 8,69, 9,72, 11,74,77, 21,90, 7,73, 22,76,99,102,108,111,112, 79,36,70,89, 106, 10,35,75,87,19" // Edmundo Villarroel, todos
        );
        $ar_maquinas = array(127 => '26,29,33,38',
                            (NotaHelper::isTestSite() ? 293 : 305) => '25,30,40,41',
                            78 => '34,113,37,107',
							326 => '34,113,37,107');
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$datos_user = $this->getDatos_user($user->id);
        $query = "select count(*)";
        $query .= " from nota_remitente nr";
        $query .= " join jml_users u on u.id=nr.id_user";
        $query .= " join nota_revision nrev on nrev.id_nota_remitente=nr.id and nrev.enviado_empleado=1 and nrev.autorizado_capitan=1 ";
        $query .= " join nota_user nu on nu.id_user=u.id ";
        $query .= " join oti_departamento od on od.id=nu.id_depto and od.id_tipo=2 and ((od.id in (".$ar_dependencias[$user->id].") and nrev.autorizado_jefe=0) ";
        if ($user->id!=106 && $user->id!=321)
            $query .= " or (od.id in(".$ar_maquinas[$user->id].") and nrev.autorizado_jefe=1)";
        $query .= ")";
        $query .= " and nrev.autorizado_depto=0 and nrev.aprobado_adquisiciones=0 and nr.fecha>'2022-03-01'";
		$db->setQuery($query);
		$db->query();
		return $db->loadResult();*/
	}
	function notas_depto($pagina=0, $conteo=0){
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
        $ar_maquinas = array(321 => '25,26,29,30,33,34,37,38,40,41,100,107,113',
                            106 => '25,26,29,30,33,34,37,38,40,41,100,107,113');
		$datos_user = $this->getDatos_user($user->id);
        if (!$conteo){
            $query = "select nr.id, nr.fecha, nrev.enviado_empleado as empleado, nrev.autorizado_capitan as capitan, 
            nrev.autorizado_jefe as jefe, nrev.autorizado_depto as depto, nrev.aprobado_adquisiciones as adquisiciones ";
        }else {
            $query = "select count(*) ";
        }
		$query .= " from nota_remitente nr 
				join nota_revision nrev on nrev.id_nota_remitente=nr.id and nrev.enviado_empleado=1 and 
					nrev.autorizado_capitan=1 and autorizado_jefe=1 ";
        if ($user->username=='stimis')
            $query .= " and nr.id_adepto in (".$datos_user['id_depto'].", 51)";
        else
            $query .= " and nr.id_adepto=".$datos_user['id_depto'];
        $query .= " join oti_departamento od on od.id=nr.id_depto_costo ";
        $query .= " where nr.fecha>'2022-01-01'";
        if ($user->id==106 || $user->id==321)
            $query .= " and od.id_tipo!=2 ";
		$query .= " order by nrev.autorizado_depto, nr.id desc ";
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
					np.descripcion as prioridad, noc.id as orden_compra, noc.proveedor as proveedor_oc, noc.opcion_oc, nf.factura,
                    ne.exenta 
				from nota_remitente nr 
					join jml_users u on u.id=nr.id_user 
					join nota_user nu on nu.id_user=u.id 
					join oti_departamento od on od.id=nu.id_depto 
					left join nota_nombreRemitente nnr on nnr.id_remitente=nr.id 
					join nota_revision nrev on nrev.id_nota_remitente=nr.id 
					join nota_prioridad np on np.id=nr.id_prioridad 
					left join nota_ordenDeCompra noc on noc.id_remitente=nr.id ";
        /*if ($orden_compra)
            $query .= " and noc.id=".$orden_compra;*/
        $query .=   "left join nota_exenta ne on ne.id_remitente=nr.id ";
		
		$query .= " left join nota_factura nf on nf.id_ordenDeCompra=noc.id ";
		$query .= " where nr.id=".$id_remitente." and noc.id=".$orden_compra;
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
	function notas_naves($pagina=0, $parametro='', $deptos='', $desde='', $hasta='', $pendientes=0){
        
        $user = JFactory::getUser();
        $ar_naves = array(127 => "18,26,27,71,84,9,28,29,59,72,105,8,32,33,69,88,11,38,39,48,74", // psierpe Crux, Fueguino, Patagonia, Yaghan
                            81 => "18,26,27,71,84,9,28,29,59,72,105,8,32,33,69,88,11,38,39,48,74", // stimis
                            (NotaHelper::isTestSite() ? 293 : 305) => "20,41,77,78,21,40,79,90,7,24,25,73,86,22,30,31,76,85,149,150,151", // lrosales Toucan, Skua, Bahía azul, Melinka
                            78 => "108,110,111,112,113,114,19,36,37,70,89,106,107,10,34,35,75,87,87,8,32,33,69,88", // gmancilla Kaweskar, Pionero, Anan, Pathagon
                            (NotaHelper::isTestSite() ? 295 : 326) => "108,110,111,112,113,114,19,36,37,70,89,106,107,10,34,35,75,87,8,32,33,69,88", // Nelson Ormeño -> 
                            226 => "18,26,27,71,84,9,28,29,59,72,105,8,32,33,69,88,11,38,39,48,74,20,41,77,78,21,40,79,90,7,24,25,73,86,22,30,31,76,85,108,110,111,112,113,114,19,36,37,70,89,106,107,10,34,35,75,87,87,8,32,33,69,88"
                        ); 
        
        $db = JFactory::getDbo();
        if ($pendientes){
            $query = "select count(*) as cantidad 
                        from nota_remitente nr 
                        join nota_revision nrev on nrev.id_nota_remitente=nr.id 
                        join oti_departamento od on od.id=nr.id_depto_costo 
                    where nr.id_depto_costo in (".$ar_naves[$user->id].") 
                        and nrev.enviado_empleado=1 
                        and nrev.autorizado_capitan=1 
                        and nrev.autorizado_jefe=0";
            $query .= " and nr.fecha>'2022-01-01' ";
            $db->setQuery($query);
            $db->query();
            return $db->loadResult();
        }else{
            $query = "select nr.id, od.nombre as depto_origen, od.id as id_nave, nr.fecha, 
                nrev.enviado_empleado as empleado,
                nrev.autorizado_capitan as capitan, 
                nrev.autorizado_jefe as jefe,
                nrev.autorizado_depto as depto,
                nrev.aprobado_adquisiciones as adquisiciones,
                (nrev.enviado_empleado=1 and nrev.autorizado_capitan=1 and nrev.autorizado_jefe=0) as pendiente ";
            $query .= " from nota_remitente nr 
                        join nota_revision nrev on nrev.id_nota_remitente=nr.id 
                        join oti_departamento od on od.id=nr.id_depto_costo 
                    where nr.id_depto_costo in (".$ar_naves[$user->id].") 
                        and nrev.enviado_empleado=1 
                        and nrev.autorizado_capitan=1 ";
            $query .= " and nr.fecha>'2022-01-01' ";
            $query .= " order by nrev.autorizado_jefe, nr.id desc";
            $query .= ' limit '.(($pagina-1)*10).', 10';
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
		$handle = mssql_connect("flexline.corp.tabsa.cl","sa","Tabsa123") or die("Cannot connect to server");
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
		$handle = mssql_connect("flexline.corp.tabsa.cl","sa","Tabsa123") or die("Cannot connect to server");
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

	function notas_area($pagina=0, $parametro=""){
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$datos_user = $this->getDatos_user($user->id);
		$query = "select nr.id, nr.fecha, od.nombre as depto, u.name as usuario, nrev.enviado_empleado as empleado";
		$query .= ", nrev.autorizado_capitan as capitan,nrev.autorizado_jefe as jefe, nrev.autorizado_depto as depto";
		$query .= ", nrev.aprobado_adquisiciones as adquisiciones";
		$query .= " from nota_remitente nr join nota_revision nrev on nrev.id_nota_remitente=nr.id";
		$query .= " join nota_user nu on nu.id_user=nr.id_user";
		$query .= " join jml_users u on u.id=nu.id_user";
		$query .= " join oti_departamento od on od.id=nu.id_depto and od.id_area=".$datos_user['id_area'];
		if ($parametro!=""){
			$query .= " join nota_item ni on ni.id_remitente=nr.id and ni.item like '%".$parametro."%' order by nr.fecha desc ";
		}else{
			$query .= " order by nr.fecha desc ";
			if ($pagina) 
				$query .= ' limit '.(($pagina-1)*10).', 10';
			else
				$query .= ' limit 0, 10';
		}
		$db->setQuery($query);
		$db->query();
		if ($db->getNumRows()){
			return $db->loadAssocList();
		}
		return array();
	}

	function editar_nave(){
		$db = JFactory::getDbo();
		$query = "update oti_departamento set ley_navarino=1 where id=8";
		//$query = "update jml_users set ley_navarino=1 where id=8";
		$db->setQuery($query);
		$db->query();
		return $query;
	}

	function regimen_naves(){
		$db = JFactory::getDbo();
		$query = "select nn.id, nn.nave, nd.id_nave, nd.id_depto, od.nombre
				from nota_naves nn
				join nota_naveDepto nd on nd.id_nave=nn.id 
				left join oti_departamento od on od.id=nd.id_depto";
		$db->setQuery($query);
		$db->query();
		if ($db->getNumRows())
			return $db->loadAssocList();
		return array();
	}

	function getLista_naves(){
		$db = JFactory::getDbo();
		$fecha_actual = date('Y-m-d');
		$query = "select nn.id, nn.nave, nn.ley_navarino, nv.ley_navarino as navarino_programado, nv.inicio, nv.fin, nv.id as id_vigencia
					from nota_naves nn left join (select id, id_nave, ley_navarino, inicio, fin 
						from nota_vigenciaNavarino 
						where id in(select max(id) from nota_vigenciaNavarino group by id_nave)) nv 
					on nv.id_nave=nn.id where nn.id!=13";
		$db->setQuery($query);
		$db->query();
		if ($db->getNumRows())
			return $db->loadAssocList();
		return array();
	}

	function setNavarino($id_nave, $inicio, $fin, $ley_navarino){
		$user = JFactory::getUser();
		$inicio = NotaHelper::fechamysql($inicio,2);
		$fin = NotaHelper::fechamysql($fin,2);
		$db = JFactory::getDbo();
		$query = "insert into nota_vigenciaNavarino(id_nave,ley_navarino,inicio,fin,id_user) 
					values (".$id_nave.", ".$ley_navarino.",'".$inicio."','".$fin."',".$user->id.")";
		$db->setQuery($query);
		$db->query();
	}

	function actualizar_navarino($id_nave,$ley_navarino){
		$db = JFactory::getDbo();
		$query = "update nota_naves set ley_navarino=".$ley_navarino." where id=".$id_nave;
		$db->setQuery($query);
		$db->query();

		// actualizar tablas utilizadas para obtención de régimen especial
		$query = "update oti_departamento od 
					inner join nota_naveDepto nd on nd.id_depto=od.id 
					set od.ley_navarino=".$ley_navarino." 
					where nd.id_nave=".$id_nave;
		$db->setQuery($query);
		$db->query();
	}

    function getProducto($producto){
        $producto = NotaHelper::msquote('%'.$producto.'%');
        $query = "select id, nombre from productos where nombre like ".$producto;

        $res = NotaHelper::getMssqlQuery($query);
        return $res;
    }

    function actualizar_item($id_item, $cantidad_original, $nueva_cantidad, $id_tipo_modificacion, $motivo=''){
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $fecha = date("Y-m-d");
        $hora = date("H:i");
        $query = "insert into nota_modificada(id_item, cantidad_original, nueva_cantidad, id_user, motivo, fecha, hora, id_tipoModificacion) 
            values(".$id_item.", ".$cantidad_original.", ".$nueva_cantidad.", ".$user->id.", '".$motivo."', '".$fecha."', '".$hora."', ".$id_tipo_modificacion.")";
        $db->setQuery($query);
        $db->query();
        if ($nueva_cantidad){
            $query = "update nota_item set aprobado=1 where id=".$id_item;
            $db->setQuery($query);
            $db->query();
        }
        
        /*
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
         */
    }
}