<?php
defined('_JEXEC') or die('Restricted access');

class NotaHelper {
	public static function isTestSite() {
		if (JURI::base() == 'http://192.168.20.150/portal/') return true;
		else return false;
	}

	public static function fechamysql($fecha,$modo=1) {
		if (empty($fecha)) return '';
		if ($modo == 2) { // conversion HACIA mysql
			list($dia, $mes, $ano) = explode('-', $fecha);
			return "$ano-$mes-$dia";
		}
		else { // conversion DESDE mysql
			list($ano, $mes, $dia) = explode('-', $fecha);
			return "$dia-$mes-$ano";
		}
	}

	public static function checkFecha($date) {
		$tempDate = explode('-', $date);
		if (checkdate($tempDate[1], $tempDate[0], $tempDate[2])) {//checkdate(month, day, year)
			return true;
		}
		else return false;
	}
	
	public static function getTexto_fecha($fecha){
		$f = explode("-", $fecha);
		$mes = array('01' => 'enero', '02' => 'febrero', '03' => 'marzo', '04' => 'abril', '05' => 'mayo',
				'06' => 'junio', '07' => 'julio', '08' => 'agosto', '09' => 'septiembre', '10' => 'octubre', '11' => 'noviembre', '12' => 'diciembre');
		return $f[0]." de ".$mes[$f[1]]." de ".$f[2];
	}

	public static function msquote($txt, $filtrar=true, $anularvacio=false) {
		if ($txt=='NULL') return $txt;
		if ($txt=='' AND $anularvacio) return 'NULL';
		if ($filtrar) $txt = str_replace(array(';','\'','"','`','´','\x00','&'), '', $txt);
		// segunda pasada porque puede haber quedado después del filtro anterior
		if ($filtrar) $txt = str_replace('--', '', $txt);
		$txt = trim($txt);
		$txt = mb_convert_encoding( $txt, 'ISO-8859-1', 'UTF-8' );
		return "'$txt'";
	}

	public static function mail($subject, $body, $email){

		/*$user = JFactory::getUser();
		$mailer = JFactory::getMailer();
		$config = JFactory::getConfig();
		$sender = array( 
			$config->getValue( 'config.mailfrom' ),
			$config->getValue( 'config.fromname' )
		);
		$mailer->setSender($sender);
		if (NotaHelper::isTestSite()){
			$recipient = array('jmarinan@tabsa.cl');
			$body .= "<br><br>^ Correo de prueba ^<br>";
			$body .= "destinatarios reales: ";
			$body .= "[";
			foreach ($email as $e){
				$body .= $e['email'].', ';
				print_r($e['email']);
			}
			$body .= "]";
		}else{
			$recipient = array();
			$i=0;
			foreach ($email as $e){
				$recipient[$i] = $e['email'];
				$i++;
			}
			//$recipient[$i] = 'jmarinan@tabsa.cl';
		}
		$mailer->addRecipient($recipient);
		$mailer->setSubject($subject);
		$mailer->isHTML(true);
		$mailer->setBody($body);
		$res = $mailer->Send();*/
	}

	public function mailAdjunto($subject, $body, $email, $adjunto=''){
		/*$mailer = JFactory::getMailer();
		$config = JFactory::getConfig();
		$sender = array( 
			$config->getValue( 'config.mailfrom' ),
			$config->getValue( 'config.fromname' )
		);
		$mailer->setSender($sender);
		$recipient = array('jmarinan@tabsa.cl', 'fperez@tabsa.cl');
		$mailer->addRecipient($recipient);
		$mailer->setSubject($subject);
		$mailer->isHTML(true);
		$mailer->setBody($body);
		if ($adjunto=='')
			$body .= "<br><br><i>(Sin adjuntos)</i>";
		else
			$mailer->addAttachment($adjunto);
		$mailer->Send();*/
	}
	public static function getMssqlQuery($query, $db_handle = false) {
		if ( substr($query,-1)!=';' ) $query .= ';';
		// si no hay una conexion preestablecida, crearla
		if (!$db_handle) {
			// Connect to mssql server
			//$handle = mssql_connect(SQL_HOST,SQL_USER,SQL_PASS) or die("Cannot connect to server");
            $handle = mssql_connect('ventas.tabsa.cl','compra','[Samurai100%Letal?=') or die("Cannot connect to server");
			// Select a database
			//$db = mssql_select_db((R2Helper::isTestSite() ? SQL_DB_BAK : SQL_DB), $handle) or die("Cannot select database");
			$db = mssql_select_db('Compras_dev', $handle) or die("Cannot select database");
		}
		else $handle = $db_handle;
		// Execute a query
		$result = mssql_query($query, $handle);
		if (!$result AND NotaHelper::isTestSite()) print_r($query);
		$listado = array();
		if ($result===FALSE) $listado = false;
		elseif ($result===TRUE) $listado = true;
		else {
			while($row = mssql_fetch_array($result)) {
				foreach($row as &$column) {
					if ( is_string($column)) $column = mb_convert_encoding( $column, 'UTF-8', 'ISO-8859-1' );
				}
				$listado[] = $row;
			}
		}
		// Close the connection
		if (!$db_handle) {
			mssql_close($handle);
		}
		return $listado;
	}
}
