<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');
/**
 * NotaList Model
 */
class NotaModelNotas extends JModelList{
	protected function getListQuery(){         
		$db = JFactory::getDBO();
		/*
		$query = $db->getQuery(true);
		$query->select('id,greeting');
		$query->from('#__nota');
		return $query;*/
	}
}