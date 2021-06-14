<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
require_once(JPATH_COMPONENT_SITE.'/assets/constants.php');
 
/**
 * Nota Model
 */
class NotaModelReplicacion extends JModelItem{

	public function getUser($id_user){
		$query = "select u.id, u.nombre, u.apellido, u.nivel, d.nombre, d.id_area, a.area
					from usuarios u
					join departamentos d on d.id=u.id_depto
					join area a on a.id=d.id_area 
					where u.id=".$id_user;
		$data = NotaHelper::getMssqlQuery($query);
		if (!sizeof($data))
			return array();
		return $data[0];
	}

	public function setNota($id_remitente,$id_adepto,$id_user,$id_prioridad,$id_depto_compra,$id_depto_costo,$proveedor,$ley_navarino,$id_tipo_pedido,$cotizacion,$autorizacion){
		$proveedor = utf8_encode(NotaHelper::msquote($proveedor));
		$cotizacion = utf8_encode(NotaHelper::msquote($cotizacion));
		$query = "insert into notas (id_adepto,id_usuario,id_prioridad,id_depto_compra,id_depto_costo,proveedor,ley_navarino,id_tipo_pedido,cotizacion,fecha,autorizacion) 
					values(".$id_adepto.",".$id_user.",".$id_prioridad.",".$id_depto_compra.",".$id_depto_costo.",
							".$proveedor.",".$ley_navarino.",".$id_tipo_pedido.",".$cotizacion.",getdate(),".$autorizacion.")";
		NotaHelper::getMssqlQuery($query);
	}

	public function setItems($id_remitente, $cantidad, $item, $motivo, $opcion_oc, $valor, $nombre_archivo){
		$item = utf8_encode(NotaHelper::msquote($item));
		$motivo = utf8_encode(NotaHelper::msquote($motivo));
		$nombre_archivo = utf8_encode(NotaHelper::msquote($nombre_archivo));
		$query = "insert into items (id_nota,cantidad,item,motivo,opcion_oc,valor,adjunto) 
					values(".$id_remitente.",".$cantidad.",".$item.",".$motivo.",".$opcion_oc.",".$valor.",".$nombre_archivo.")";
		NotaHelper::getMssqlQuery($query);
	}
	
	public function setNombreTripulante($id_remitente, $nombre_tripulante){
		$nombre_tripulante = utf8_encode(NotaHelper::msquote($nombre_tripulante));
		$query = "insert into tripulante(nombre, id_nota) 
					values(".$nombre_tripulante.",".$id_remitente.")";
		NotaHelper::getMssqlQuery($query);
	}

	public function setRevision($id_remitente, $autorizacion){
		$query = "insert into revision (id_nota, autorizacion) values(".$id_remitente.",".$autorizacion.")";
		NotaHelper::getMssqlQuery($query);
	}

	public function actualizaRevision($autorizacion,$id_nota){
		$query = "update notas set autorizacion=autorizacion|".$autorizacion." where id=".$id_nota;
		NotaHelper::getMssqlQuery($query);
		print_r($query);
	}

}

