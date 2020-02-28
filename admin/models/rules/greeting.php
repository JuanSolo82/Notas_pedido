<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla formrule library
jimport('joomla.form.formrule');

class JFormRuleGreeting extends JFormRule{
        protected $regex = '^[^0-9]+$';
}