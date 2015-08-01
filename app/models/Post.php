<?php

class Post extends Zend_Db_Table 
{
	protected $_name = 'posts';

	protected $_dependentTables = array(
		'Comment',
		'PostTag'
	);
}