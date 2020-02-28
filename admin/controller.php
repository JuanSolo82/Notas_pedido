<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');
 
/**
 * General Controller of Nota component
 */
class NotaController extends JController{
    function display($cachable = false, $urlparams = false)  {
        JRequest::setVar('view', JRequest::getCmd('view', 'Notas'));
        parent::display($cachable);
        NotaHelper::addSubmenu('messages');
    }
}