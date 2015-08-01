<?php

class PostTag extends Zend_Db_Table
{
	protected $_name = 'posts_tags';
	
	protected $_referenceMap = array(
		'Post' => array(
			'columns' => 'post_id',
			'refTableClass' => 'Post',
			'refColumns' => 'id'		
		),
		'Tag' => array(
			'columns' => 'tag_id',
			'refTableClass' => 'Tag',
			'refColumns' => 'id'		
		)	
	);
}