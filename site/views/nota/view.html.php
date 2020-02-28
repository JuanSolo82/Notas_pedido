<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
class NotaViewNota extends JView{
    function display($tpl = null){
		$jinput = JFactory::getApplication()->input;
		$layout = $jinput->get("layout", "", "string");
		$this->datos_user 	= $jinput->get("datos_user", array(), "array");
		$user = JFactory::getUser();
		if ($user->authorise('jefe.depto', 'com_nota') || $user->authorise('capitan.jefe', 'com_nota') || $user->authorise('capitan.sin_jefe', 'com_nota')){
			$this->notas_pendientes = $jinput->get("notas_pendientes", 0, "int");
			$this->pendientes_depto = $jinput->get("pendientes_depto", 0, "int");
		}
		if ($user->authorise("jefe.delgada", "com_nota")){
			$this->pendientes_naves = $jinput->get("pendientes_naves", 0, "int");
			$this->layout			= $layout;
		}
		if ($layout=="nueva_nota"){
			$this->deptos 			= $jinput->get("departamentos", array(), "array");
			$this->centros_costos 	= $jinput->get("centros_costos", array(), "array");
			$this->prioridad 		= $jinput->get("prioridad", array(), "array");
		}
		if ($layout=="notas_propias"){
			$this->notas		= $jinput->get("notas", array(), "array");
		}
		if ($layout=="detalle_nota"){
			$this->id_remitente	= $jinput->get("id_remitente", 0, "int");
			$this->detalle_nota = $jinput->get("detalle_nota", array(), "array");
			$this->items		= $jinput->get("items", array(), "array");
			$this->datos_user	= $jinput->get("datos_user", array(), "array");
			$this->datos_jefe	= $jinput->get('datos_jefe', array(), 'array');
			$this->datos_nota	= $jinput->get('datos_nota', array(), 'datos_nota');
			$this->id_user		= $jinput->get('id_user_actual', 0, 'int');
			$this->lista_deptos	= $jinput->get('lista_deptos', array(), 'array');
		}
		if ($layout=="notas_jefe"){
			$this->notas_jefe = $jinput->get("notas_jefe", array(), "array");
		}
		if ($layout=="detalle_notajefe"){
			$this->id_remitente	= $jinput->get("id_remitente", 0, "int");
			$this->detalle_nota = $jinput->get("detalle_nota", array(), "array");
			$this->items		= $jinput->get("items", array(), "array");
			$this->datos_user	= $jinput->get("datos_user", array(), "array");
		}
		if ($layout=="nota_guardada"){
			$this->datos_nota	= $jinput->get("datos_nota", array(), "array");
			$this->items_nota	= $jinput->get("items_nota", array(), "array");
		}
		if ($layout=="notas_depto"){
			$this->notas_depto 	= $jinput->get("notas_depto", array(), "array");
		}
		if ($layout=="notas_naves")
			$this->notas_naves = $jinput->get("notas_naves", array(), "array");
        parent::display($tpl);
    }
	
	function setMenu(){
		$user = JFactory::getUser();
		$menu = $this->getBoton('Nueva nota', 'article-add', 'nueva_nota','');
		$menu .= $this->getBoton('Notas propias', 'article', 'notas_propias','');
		
		if ($user->authorise('jefe.depto', 'com_nota') || $user->authorise('capitan.jefe', 'com_nota') || $user->authorise('capitan.sin_jefe', 'com_nota')){
			$txt = "Notas recibidas del departamento";
			if ($user->authorise('capitan.jefe', 'com_nota') || $user->authorise('capitan.sin_jefe', 'com_nota'))
				$txt = "Notas de la nave";
			$menu .= $this->getBoton($txt, 'download', 'notas_jefe','',1);
			if (!$user->authorise('capitan.jefe', 'com_nota') && !$user->authorise('capitan.sin_jefe', 'com_nota'))
				$menu .= $this->getBoton('Notas enviadas al departamento', 'article-add', 'notas_depto','',1);
		}
		if ($user->authorise('jefe.delgada', 'com_nota'))
			$menu .= $this->getBoton('Notas de naves', 'cpanel', 'notas_naves', '',1);
		if ($user->authorise('adquisiciones.jefe', 'com_nota')){
			$menu .= $this->getBoton('Notas recibidas', 'category', '','adquisiciones');		
		}
		if ($user->authorise('core.admin', 'com_nota')){
			$menu .= $this->getBoton('Gestion de usuarios', 'user', 'gestion_usuarios','');
		}
		if ($user->authorise('procedimientos', 'com_nota') || $user->authorise('adquisiciones.jefe', 'com_nota')){
			//$menu .= $this->getBoton('Reportes', 'levels', 'busqueda_notas','reportes');
			$menu .= $this->getBoton('Reportes', 'levels', 'reportes.busqueda_notas','');
		}
		if ($user->authorise('facturacion', 'com_nota')){
			$menu .= $this->getBoton('Buscar OC', 'article-add','reportes.facturados', 'reportes');
		}

		return $menu;
	}
	function getBoton($label, $icono, $task='', $modulo='', $notificacion=0) {
		if (trim($modulo)==''){
			$boton = '<a href="'.JRoute::_('index.php?option=com_nota&view=com_nota&task='.$task).'">';
		}else{
			$boton = '<a href="'.JRoute::_('index.php?option=com_nota&view='.$modulo.'&task='.$task).'">';
		}
		
		$boton .= "<div class='boton'>";
		if ($notificacion){
			if ($task=='notas_jefe')
				$boton .= '<div class="notificacion" '.($this->notas_pendientes ? '' : 'style="background: grey;"').'>'.$this->notas_pendientes.'</div>';
			if ($task=='notas_depto')
				$boton .= '<div class="notificacion" '.($this->pendientes_depto ? '' : 'style="background: grey;"').'>'.$this->pendientes_depto.'</div>';
			if ($task=='notas_naves')
				$boton .= '<div class="notificacion" '.($this->pendientes_naves ? '' : 'style="background: grey;"').'>'.$this->pendientes_naves.'</div>';
		}
		$boton .= '  <img src="/portal/administrator/templates/hathor/images/header/icon-48-' . $icono . '.png" />' . PHP_EOL;
		$boton .= '  <br><span class="titulo_item">' . $label . '</span>' . PHP_EOL;
		$boton .= '</div></a>';
        return $boton;
    }
}