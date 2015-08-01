<?php

// bootstrap

define('BASE', dirname(dirname(__FILE__)));

date_default_timezone_set('Africa/Johannesburg');

set_include_path(
	get_include_path() . PATH_SEPARATOR .
	'/usr/share/php5/ZendFramework/library' . PATH_SEPARATOR .
	BASE . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'models' . PATH_SEPARATOR .
	BASE . DIRECTORY_SEPARATOR . 'lib'
);

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

// load config

$config = new Zend_Config_Ini(
	BASE . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config.ini',
	'live'
);
Zend_Registry::set('config', $config);

// connect to the database

$db = Zend_Db::factory(
	$config->db->adapter,
	$config->db->conn->toArray()
);

Zend_Db_Table::setDefaultAdapter($db);
Zend_Registry::set('db', $db);

// view

Zend_Layout::startMVC();

// dispatch

$front = Zend_Controller_Front::getInstance();
$front->setRouter(new App_Controller_Router());
$front->setControllerDirectory(BASE . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'controllers');
$front->dispatch();
