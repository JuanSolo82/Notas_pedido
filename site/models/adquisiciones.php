<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
 
/**
 * Nota Model
 */
class NotaModelAdquisiciones extends JModelItem{
	public function getLista_notas(){
		$db = JFactory::getDbo();
		$query = "select nr.id, nr.fecha, u.name as nombre_remitente, od.nombre as departamento,nrev.autorizado_depto, nrev.aprobado_adquisiciones 
				from nota_remitente nr 
				join jml_users u on u.id=nr.id_user 
				join nota_revision nrev on nrev.id_nota_remitente=nr.id 
					and nrev.enviado_empleado=1 
					and nrev.autorizado_capitan=1 
					and nrev.autorizado_jefe=1 
					and ((nrev.autorizado_depto=0 and nr.id_adepto=4) || nrev.autorizado_depto=1)
					and nrev.aprobado_adquisiciones=0 
				join nota_user nu on nu.id_user=u.id 
				join oti_departamento od on od.id=nr.id_depto_compra 
				where nr.fecha>'2019-01-01' order by nr.id desc, nr.fecha desc";
		$db->setQuery($query);
		$db->query();
		if (!$db->getNumRows())
			return null;
		return $db->loadAssocList();
	}
	public function getPendientesOc(){
		return sizeof($this->getLista_notas());
	}
	public function getLista_oc(){
		$db = JFactory::getDbo();
		$query = "select nr.id, nr.fecha, u.name as nombre_remitente, od.nombre as departamento 
					from nota_remitente nr, jml_users u, oti_departamento od, nota_user nu, nota_revision nrev 
				where nr.id_user=u.id and nr.id_user=nu.id_user and nu.id_depto=od.id and 
					nr.id=nrev.id_nota_remitente and nrev.enviado_empleado=1 and nrev.autorizado_capitan=1 and 
					nrev.autorizado_jefe=1 and nrev.autorizado_depto=1 and nrev.aprobado_adquisiciones=0";
		$db->setQuery($query);
		$db->query();
		if (!$db->getNumRows())
			return null;
		return $db->loadAssocList();
	}
	public function items($id_remitente){
		$db = JFactory::getDbo();
		$query = "select ni.id, ni.cantidad, ni.item, ni.motivo, ni.adjunto, nm.id as id_nueva_cantidad, nm.nueva_cantidad, ni.opcion_oc 
				from nota_item ni left join nota_modificada nm on nm.id_item=ni.id where ni.id_remitente=".$id_remitente;
		$db->setQuery($query);
		$db->query();
		if (!$db->getNumRows())
			return null;
		return $db->loadAssocList();
	}
	public function cambiar_opcion($id_item, $opcion){
		$db = JFactory::getDbo();
		$query = "update nota_item set opcion_oc=".$opcion." where id=".$id_item;
		$db->setQuery($query);
		$db->query();
	}
	public function setOrden($id_remitente, $opcion, $num_opciones, $proveedor='', $rut_proveedor='', $giro_proveedor='', $cotizacion=''){
		echo $cotizacion.'_';
		$datos_oc = $this->getDetalle_orden($id_remitente, $opcion);
		$db = JFactory::getDbo();
		$fecha = date("Y-m-d");
		$hora = date("H:i:s");
		$user = JFactory::getUser();
		if (!sizeof($datos_oc)){
			$query = "insert into nota_ordenDeCompra(id_remitente, id_user, opcion_oc, fecha, hora, proveedor, activo, cotizacion) 
						values(".$id_remitente.", ".$user->id.", ".$opcion.", '".$fecha."', '".$hora."', '".$proveedor."',1, '".$cotizacion."')";
			$db->setQuery($query);
			$db->query();
			print_r($query);
		}else{
			$query = "update nota_ordenDeCompra set activo=0 where id_remitente=".$id_remitente." and opcion_oc=".$opcion;
			$db->setQuery($query);
			$db->query();
			$query = "insert into nota_ordenDeCompra(id_remitente, id_user, opcion_oc, fecha, hora, proveedor, activo) 
						values(".$id_remitente.", ".$user->id.", ".$opcion.", '".$fecha."', '".$hora."', '".$proveedor."',1)";
			$db->setQuery($query);
			$db->query();
		} 
		$query = "select id from nota_ordenDeCompra where activo=1 and id_remitente=".$id_remitente;
		$db->setQuery($query);
		$db->query();
		$ordenes = $db->getNumRows();
		$terminado = 0;
		
		if ($ordenes == $num_opciones){
			$query = "update nota_revision set autorizado_depto=1, aprobado_adquisiciones=1 where id_nota_remitente=".$id_remitente;
			$db->setQuery($query);
			$db->query();
			$terminado = 1;
		}


		// insercion en nota_tramitada
		$query = "insert into nota_tramitada(id_remitente, id_user, fecha, hora, terminado, motivo, nombre) 
					values(".$id_remitente.", ".$user->id.", '".$fecha."', '".$hora."', ".$terminado.", '', '')";
		$db->setQuery($query);
		$db->query();

		// autoaprobación en caso de ser necesario
		if ($terminado){
			$query = "select nr.id_depto_compra, nu.id_depto from nota_remitente nr join nota_user nu on nu.id_user=nr.id_user where nr.id=".$id_remitente;
			$db->setQuery($query);
			$db->query();
			$res = $db->loadAssoc();
			if ($res['id_depto_compra']==$res['id_depto']){
				$query = "insert into nota_anotacion(id_remitente, aprobado, anotacion) values(".$id_remitente.", 1, '')";
				$db->setQuery($query);
				$db->query();
			}
		}
	}
	public function getDetalle_orden($id_remitente, $opcion){
		$db = JFactory::getDbo();
		$query = "select id, id_remitente, id_user, opcion_oc, fecha, hora, proveedor 
				from nota_ordenDeCompra where id_remitente=".$id_remitente." and opcion_oc=".$opcion;
		$db->setQuery($query);
		$db->query();
		if (!$db->getNumRows())
			return array();
		$fila = $db->loadAssoc();
		return $fila;
	}
	public function actualizarLeyNavarino($id_remitente, $ley_navarino){
		$db = JFactory::getDbo();
		$query = "update nota_remitente set ley_navarino=".$ley_navarino." where id=".$id_remitente;
		$db->setQuery($query);
		$db->query();
	}

	public function getItems_oc($oc){
		$db = JFactory::getDbo();
		$query = "select ni.id,ni.cantidad, ni.item, ni.motivo, ni.adjunto, modificada.nueva_cantidad, oc.id_remitente, modificada.id_nueva_cantidad 
					from nota_item ni join nota_remitente nr on nr.id=ni.id_remitente 
					join nota_ordenDeCompra oc on oc.id_remitente=nr.id and oc.opcion_oc=ni.opcion_oc and oc.id=".$oc." 
					left join (select nm.id as id_nueva_cantidad, nm.nueva_cantidad, nm.id_item from nota_modificada nm, nota_item ni 
					where nm.id_item=ni.id order by nm.id desc limit 1) modificada on modificada.id_item=ni.id";
		$db->setQuery($query);
		$db->query();
		if ($db->getNumRows()){
			return $db->loadAssocList();
		}
		return array();
	}
	public function getDatosNotaOc($oc, $nota_pedido){
		$db = JFactory::getDbo();
		$query = "select nr.id, nr.fecha, nr.proveedor, u.name as nombre_emisor, centro.id as id_centro_costo, 
						centro.depto_costo, od.nombre as depto_destino, origen.depto_origen, nr.ley_navarino, nr.id_tipo_pedido,
						oc.id as orden_compra, oc.opcion_oc, oc.proveedor  
				from nota_remitente nr 
				join nota_ordenDeCompra oc on oc.id_remitente=nr.id and ";
		if ($oc) $query .= "oc.id=".$oc." ";
		elseif ($nota_pedido) $query .= " nr.id=".$nota_pedido." ";
		$query .= "join jml_users u on u.id=nr.id_user 
				join (select od.id, od.nombre as depto_costo from oti_departamento od) centro on centro.id=nr.id_depto_costo 
				join oti_departamento od on od.id=nr.id_adepto 
				join (select od.nombre as depto_origen, nr.id 
						from oti_departamento od, nota_user nu, nota_remitente nr 
						where nu.id_user=nr.id_user and nu.id_depto=od.id) origen on origen.id=nr.id";
		$db->setQuery($query);
		$db->query();
		if ($db->getNumRows()){
			$ar = $db->loadAssocList();
			return $ar;
		}
		return array();
	}
	public function actualiza_cc($id_remitente, $id_centro_costo){
		$db = JFactory::getDbo();
		$query = "update nota_remitente set id_depto_costo=".$id_centro_costo." where id=".$id_remitente;
		$db->setQuery($query);
		$db->query();
	}

	function borrar_atrasadas(){
		$db = JFactory::getDbo();
		$query = "select nr.id, nr.fecha from nota_remitente nr 
					join nota_revision nrev on nrev.id_nota_remitente=nr.id and nrev.enviado_empleado=1 and nrev.autorizado_depto=1 and nrev.aprobado_adquisiciones=0 
					where nr.fecha <='2019-12-09' ";
		$db->setQuery($query);
		$db->query();
		if ($db->getNumRows()){
			$lista = $db->loadAssocList();
			$str = "";
			foreach ($lista as $n){
				$str .= $n['id'].',';
			}
			$str = substr($str,0, strlen($str)-1);
			$query = "update nota_revision set aprobado_adquisiciones=2 where id_nota_remitente in (".$str.")";
			print_r($query);
			$db->setQuery($query);
			$db->query();
		}
	}
}