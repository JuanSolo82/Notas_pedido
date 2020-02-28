<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
 
// import Joomla table library
jimport('joomla.database.table');
 
/**
 * Nota Table class
 */
class NotaTableNota extends JTable{
	/*function __construct(&$db){
		parent::__construct('#__nota', 'id', $db);
	}
	public function bind($array, $ignore = ''){
		if (isset($array['params']) && is_array($array['params'])){
			// Convert the params field to a string.
			$parameter = new JRegistry;
			$parameter->loadArray($array['params']);
			$array['params'] = (string)$parameter;
		}
		return parent::bind($array, $ignore);
	}
    public function load($pk = null, $reset = true){
		if (parent::load($pk, $reset)){
			// Convert the params field to a registry.
			$params = new JRegistry;
			$params->loadJSON($this->params);
			$this->params = $params;
			return true;
		}
		else{
				return false;
		}
	}*/
}
?>