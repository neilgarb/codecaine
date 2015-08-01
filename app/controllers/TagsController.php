<?php

class TagsController extends App_Controller_Action 
{
	public function viewAction ()
	{
		$label = $this->_getParam('label');
		if ($label === null)
		{
			throw new Zend_Exception('No label specified in TagsController::viewAction()');
		}
		
		$tagManager = new Tag();
		
		$tag = $tagManager->fetchRow(array('label = ?' => $label));
		if ($tag === null)
		{
			throw new Zend_Exception('Tag not found in TagsController::viewAction()');
		}
		
		$this->view->tag = $tag;
		
		// get posts
		
		$page = array_key_exists('p', $_GET) ? $_GET['p'] : 1;
		$page = $page < 1 ? 1 : $page;
		$this->view->page = $page;
		
		$postManager = new Post();
		
		$postCount = $postManager->fetchAll(array(
			'id IN (SELECT post_id FROM posts_tags WHERE tag_id = ?)' => $tag->id,
			'is_active = ?' => 1
		))->count();
		$this->view->pageCount = ceil($postCount / 10);
		
		$this->view->posts = $postManager->fetchAll(
			array(
				'id IN (SELECT post_id FROM posts_tags WHERE tag_id = ?)' => $tag->id,
				'is_active = ?' => 1
			),
			'posted_at DESC',
			10,
			($page - 1) * 10		
		);
		
		$this->view->paginationBase = '/tags/'.$tag->label;
		
		// description
		
		$this->view->metaDescription = 'Read my posts tagged with \'' . $tag->title . '\'.';
		
		// set title
		
		$this->_title('Posts tagged with \''.$tag->title.'\'');
		
		$this->_forward('listing', 'posts');
	}
}