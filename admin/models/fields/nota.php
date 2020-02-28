<?php
defined('_JEXEC') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldNota extends JFormFieldList{
    protected $type = 'Nota';
    protected function getOptions(){
        $db = JFactory::getDBO();
        //$query = $db->getQuery(true);
		/*
        $query->select('#__nota.id as id,greeting, #__categories.title as category,catid ');
        $query->from('#__nota');
        $query->leftJoin('#__categories on catid=#__categories.id');
        $db->setQuery((string)$query);
        $messages = $db->loadObjectList();
        $options = array();
        if($messages){
            foreach($messages as $message){
                $options[] = JHtml::_('select.option', $message->id, $message->greeting . 
                        ($message->catid ? ' ('.$message->category.')' : ''));
            }
        }
        $options = array_merge(parent::getOptions(), $options);
        return $options;*/
    }
}
?>