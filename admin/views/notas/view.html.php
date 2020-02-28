<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * Notas View
 */
class NotaViewNotas extends JView{
    function display($tpl = null) {
        // Get data from the model
        /*$items = $this->get('Items');
        $pagination = $this->get('Pagination');*/

        // Check for errors.
        if (count($errors = $this->get('Errors'))){
                JError::raiseError(500, implode('<br />', $errors));
                return false;
        }
        // Assign data to the view
        /*$this->items = $items;
        $this->pagination = $pagination;*/

        $this->addToolBar();
        // Display the template
        parent::display($tpl);

        $this->setDocument();
    }

    protected function addToolBar(){
        $user = JFactory::getUser();
		$userId = $user->id;
		JToolBarHelper::title('Administración de módulo Notas', 'nota');
		JToolBarHelper::preferences('com_nota');
    }

    protected function setDocument(){
       /* $document = JFactory::getDocument();
        $document->setTitle(JText::_('COM_NOTA_ADMINISTRATION'));*/
    }
}
?>