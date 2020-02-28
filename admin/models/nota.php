<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelform library
jimport('joomla.application.component.modeladmin');
 
/**
 * Nota Model
 */
class NotaModelNota extends JModelAdmin{
	public function getTable($type = 'Nota', $prefix = 'NotaTable', $config = array()){
		return JTable::getInstance($type, $prefix, $config);
	}
	public function getForm($data = array(), $loadData = true){
		$form = $this->loadForm('com_nota.nota', 'nota',
								array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)){
			return false;
		}
		return $form;
    }
	public function getScript(){
		return 'administrator/components/com_nota/models/forms/nota.js';
	}
	protected function loadFormData(){
		$data = JFactory::getApplication()->getUserState('com_nota.edit.nota.data', array());
		if (empty($data)){
			$data = $this->getItem();
		}
		return $data;
    }
}