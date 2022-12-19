<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class NotaViewAutorizaciones extends JView{
    function display($tpl = null){
        $jinput = JFactory::getApplication()->input;
        $user = JFactory::getUser();
        $layout = $jinput->get("layout","","string");
        if ($layout=="detalle_nota"){
            $this->detalle_nota = $jinput->get("detalle_nota",array(),"array");
            $this->lista_deptos = $jinput->get("lista_deptos",array(),"array");
            $this->items        = $jinput->get("items",array(),"array");
        }
        parent::display($tpl);
    }

    function notas_naves(){
        $jinput = JFactory::getApplication()->input;
    }
}