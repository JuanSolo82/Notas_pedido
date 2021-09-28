<?php
defined('_JEXEC') or die('Restricted access');
require_once(JPATH_COMPONENT_SITE.'/assets/helper.php');

	define('TRAMO_EDEN_NATA',51);
	define('TRAMO_TORT_NATA',52);
	define('TRAMO_YUNG_NATA',53);
	define('TRAMO_TORT_EDEN',54);
	define('TRAMO_YUNG_EDEN',55);
	define('TRAMO_YUNG_TORT',56);

	define('RUTA_ADJ', JPATH_SITE . '/media/com_reservas/adj');

	define('SQL_HOST', 'ventas.tabsa.cl'); // 10.0.0.21

	define('SQL_USER', 'compra');
	define('SQL_PASS', '[Samurai100%Letal?=');
	define('SQL_DB', NotaHelper::isTestSite() ? 'Compras_dev' : 'Compras');

