<?php
// No direct access to this file
defined('_JEXEC') or die;

abstract class NotaHelper{
	public static function addSubmenu($submenu){
		JSubMenuHelper::addEntry(JText::_('COM_NOTA_SUBMENU_MESSAGES'),
								 'index.php?option=com_nota', $submenu == 'messages');
		JSubMenuHelper::addEntry(JText::_('COM_NOTA_SUBMENU_CATEGORIES'),
								 'index.php?option=com_categories&view=categories&extension=com_nota',
								 $submenu == 'categories');
		// set some global property
		$document = JFactory::getDocument();
		if ($submenu == 'categories'){
			$document->setTitle(JText::_('COM_NOTA_ADMINISTRATION_CATEGORIES'));
		}
	}
}