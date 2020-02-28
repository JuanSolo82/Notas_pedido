<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * Nota View
 */
class NotaViewNota extends JView{
    public function display($tpl = null){
        /*$form = $this->get('Form');
        $item = $this->get('Item');
        $script = $this->get('Script');
        if (count($errors = $this->get('Errors'))){
			JError::raiseError(500, implode('<br />', $errors));
			return false;
        }
        $this->form = $form;
        $this->item = $item;
        $this->script = $script;*/
        //$this->addToolBar();
        parent::display($tpl);
        $this->setDocument();
    }

    /**
     * Setting the toolbar
     */
    protected function addToolBar(){
        $user = JFactory::getUser();
		$userId = $user->id;
		JToolBarHelper::title('Administración de módulo Notas', 'nota');
		//JToolBarHelper::title(JText::_('COM_HELLOWORLD_MANAGER_HELLOWORLD_NEW'), 'helloworld');
		JToolBarHelper::preferences('com_nota');
    }

    protected function setDocument(){
       /* $isNew = ($this->item->id < 1);
        $document = JFactory::getDocument();
        $document->setTitle($isNew ? JText::_('COM_NOTA_NOTA_CREATING')
                                : JText::_('COM_NOTA_NOTA_EDITING'));

        $document->addScript(JURI::root(). $this->script);
        $document->addScript(JURI::root()."administrator/components/com_nota"
                                         ."views/Nota/submibutton.js");
        JText::script('COM_NOTA_NOTA_ERROR_UNACCEPTABLE');*/
    }
}