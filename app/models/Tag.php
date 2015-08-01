<?php

class Tag extends Zend_Db_Table 
{
	protected $_name = 'tags';
	
	protected $_dependentTables = array(
		'PostTag'
	);
}