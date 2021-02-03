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
	public function setNota($id_remitente){
		$query = "";
		$row = NotaHelper::getMssqlQuery($query);
	}
	
}