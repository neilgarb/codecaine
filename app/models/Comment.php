<?php

class Comment extends Zend_Db_Table 
{
	protected $_name = 'comments';
	
	protected $_referenceMap = array(
		'Post' => array(
			'columns' => 'post_id',
			'refTableClass' => 'Post',
			'refColumns' => 'id'		
		)	
	);
}