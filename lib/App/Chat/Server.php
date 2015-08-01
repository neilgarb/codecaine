<?php

class App_Chat_Server
{
	protected $_roomsTable;
	protected $_messagesTable;
	
	function __construct (Zend_Db_Table $roomsTable, Zend_Db_Table $messagesTable)
	{
		$this->_roomsTable = $roomsTable;
		$this->_messagesTable = $messagesTable;		
	}	
	
	public function addRoom ($name)
	{
		
	}
	
	public function handle ()
	{
		
	}
}