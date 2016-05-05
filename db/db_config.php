<?php
error_reporting(-1);
ini_set('display_errors', 'On');

include_once dirname(__FILE__) . '/config.php';

function webAutoload($class){
	if(file_exists(dirname(__FILE__). "/../class/".$class.".php")){
		include_once(dirname(__FILE__) . "/../class/".$class.".php");
	}
}

spl_autoload_register('webAutoload');

if(isset($_SERVER['HTTP_HOST'])){
	switch($_SERVER['HTTP_HOST']) {
		case "riddlehouse.appluco.com":
			$db_host = "127.0.0.1";
			$db_username = "web-design_test";
			$db_password = "RA6RcRBkNM0m3JSgk";
			$db_name = "riddle_house";
			break;
		default:
			ini_set('display_errors', 1);
			error_reporting(~E_WARNING);
			$db_host = DB_HOST;
			$db_username = DB_USERNAME;
			$db_password = DB_PASSWORD;
			$db_name = DB_NAME;
			break;
	}
}
$db = new PDO_DB("mysql", $db_host, $db_username, $db_password, $db_name);
?>