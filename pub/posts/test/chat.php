<?php

define('BASE', dirname(dirname(dirname(dirname(__FILE__)))));

ini_set(
	'include_path',
	'/usr/share/php5/ZendFramework/library' . PATH_SEPARATOR . 
	BASE . DIRECTORY_SEPARATOR . 'lib'
);

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

$config = new Zend_Config_Ini(
	BASE . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config.ini',
	'live'	
);

$db = Zend_Db::factory(
	$config->db->adapter,
	$config->db->conn->toArray()
);

$server = new App_Chat_Server(new Room(), new Message());

$server->addRoom('test');
$server->addRoom('room2');

$server->handle();