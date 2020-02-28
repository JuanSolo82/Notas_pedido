<?php 
defined ('_JEXEC') or die;
jimport('joomla.application.component.controlleradmin');

class NotaControllerNotas extends JControllerAdmin
{
    public function getModel($name='Nota', $prefix='NotaModel')
    {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
    }
}    