<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
JLoader::register('NotaHelper', dirname(__FILE__) . DS . 'helpers' . DS . 'nota.php');

// import joomla controller library
jimport('joomla.application.component.controller');
 
// Get an instance of the controller prefixed by Nota
$controller = JController::getInstance('Nota');
JFactory::getApplication()->JComponentTitle = "<h1>Nota</h1>";
 
// Perform the Request task
$controller->execute(JRequest::getCmd('task'));
 
// Redirect if set by the controller
$controller->redirect();
