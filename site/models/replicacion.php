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
	public function setNota($id_remitente,$id_adepto,$id_user,$id_prioridad,$id_depto_compra,$id_depto_costo,$proveedor,$ley_navarino,$id_tipo_pedido,$cotizacion){
		$proveedor = utf8_encode(NotaHelper::msquote($proveedor));
		$cotizacion = utf8_encode(NotaHelper::msquote($cotizacion));
		$query = "insert into notas (id_adepto,id_usuario,id_prioridad,id_depto_compra,id_depto_costo,proveedor,ley_navarino,id_tipo_pedido,cotizacion,fecha) 
					values(".$id_adepto.",".$id_user.",".$id_prioridad.",".$id_depto_compra.",".$id_depto_costo.",
							".$proveedor.",".$ley_navarino.",".$id_tipo_pedido.",".$cotizacion.",getdate())";
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
}

