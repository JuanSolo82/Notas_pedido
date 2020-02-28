<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
 
// load tooltip behavior
JHtml::_('behavior.tooltip');
echo JFactory::getUser()->authorise('core.admin', 'com_nota');
?>

Gestion
