<?php
define('DEV',false);
define('TZ','America/New_York');
define('CURRENCY','en_US');
//setlocale(LC_MONETARY, 'en_US');
define('FEATURES','|USERS|');
if (!defined('ADMIN')) define('ADMIN','');
define('GLOBAL_TRAFFIC_CUTOFF',7);
define('GLOBAL_PER_PAGE',10);
define('GLOBAL_DEFAULT_CURRENCY_FORMAT','%(n');
define('GLOBAL_DEFAULT_DATE_FORMAT','d-M-Y');
define('GLOBAL_DEFAULT_DATETIME_FORMAT','d-M-Y h:i:s a');
define('GLOBAL_DEFAULT_TIME_FORMAT','h:i:s a');
define('GLOBAL_PASSWORD_REGEX','^.*(?=.{6,})(?=.*[a-zA-Zd!@#$*+-/&()]).*$^');
define('GLOBAL_USERNAME_REGEX','/^[a-zA-Z0-9.@_\-]{6,20}$/');
define('STATUS_INCOMPLETE',1);
define('STATUS_PROCESSING',2);
define('STATUS_BACKORDERED',4);
define('STATUS_CANCELLED',8);
define('STATUS_SHIPPED',16);
define('STATUS_RECURRING',32);
define('STATUS_CREDIT_HOLD',64);
define('STATUS_PARTIAL_SHIPMENT',128);
define('STATUS_EXPIRING',256);
define('STATUS_REAUTHORIZING',512);
$GLOBALS['recaptcha'] = array('url'=>'https://www.google.com/recaptcha/api/siteverify','src'=>'https://www.google.com/recaptcha/api.js');
if (defined('DEV') && DEV == true) {
	error_reporting(E_ALL & ~E_STRICT);
	define('HOSTNAME','leasing.ingagedigital.com');
	define('SITENAME','leasing');
	define('DBHOST','localhost');
	define('DBUSER','test');
	define('DBPASSWD','qwErty');
	define('DBNAME','leasing');
	define('DEBUGLOG','/home/vhosts/leasing/logs/');
	define('MAILTYPE','smtp');
	define('SITE_ROOT','leasing');
	if (!DEFINED('DEBUG')) define('DEBUG',2);
	$GLOBALS['google_api'] = array(
		'apikey'=>'AIzaSyADaGI4PSa2sMdjM6QiEb5rHODLk9hb_zE',
		'clientId'=>'388927777264-6thigqcnlktp626rk0jekqh1cjlvc16q.apps.googleusercontent.com',
		'serviceAccount'=>'388927777264-6thigqcnlktp626rk0jekqh1cjlvc16q@developer.gserviceaccount.com',
		'keyFile'=>'api-test-77166ce65704.p12',
		'accountId'=>'111850704'
	);
	$GLOBALS['other'] = array();
	$GLOBALS['curl_path'] = '/usr/bin/curl';
	define('SECURE_BACKEND',0);
}
else {
	error_reporting(0);
	define('MAILTYPE','mail');
	define('HOSTNAME','takemyleaseover.ca');
	define('SITENAME','Take My Lease Over');
	define('DBHOST','localhost');
	define('DBUSER','takemyle_usr');
	define('DBPASSWD','vSH!*PCRlTN~');
	define('DBNAME','takemyle_db');
	define('DEBUGLOG','/home/takemyleaseover/public_html/logs/');
	define('SITE_ROOT','public_html');
	if (!DEFINED('DEBUG')) define('DEBUG',0);
	$GLOBALS['google_api'] = array(
		'apikey'=>'AIzaSyADaGI4PSa2sMdjM6QiEb5rHODLk9hb_zE',
		'clientId'=>'388927777264-6thigqcnlktp626rk0jekqh1cjlvc16q.apps.googleusercontent.com',
		'serviceAccount'=>'388927777264-6thigqcnlktp626rk0jekqh1cjlvc16q@developer.gserviceaccount.com',
		'keyFile'=>'api-test-77166ce65704.p12',
		'accountId'=>'111850704'
	);
	$GLOBALS['other'] = array();
	define('SECURE_BACKEND',0);
}
$GALLERY = array();
$GLOBALS['gallery'] = $GALLERY;

$PRODUCT = array();
$GLOBALS['product'] = $PRODUCT;

?>
