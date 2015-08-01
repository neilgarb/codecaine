<?php

class App_Controller_Action extends Zend_Controller_Action 
{
	public function init ()
	{
		// doctype
		
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$viewRenderer->view->doctype('XHTML1_STRICT');
		
		// js + css
		
		$this->view->headScript()->prependFile('/js/swfobject/swfobject.js');
		$this->view->headLink()->prependStylesheet('/css/base.css?' . date('Ymd'));
		
		// tags
		
		$tagManager = new Tag();
		$this->view->tags = $tagManager->fetchAll(
			null,
			"(SELECT COUNT(*) FROM posts_tags WHERE tag_id = tags.id) DESC"
		);
		
		// description
		
		if ($this->view->metaDescription === NULL)
		{
			$this->view->metaDescription = 'Hi, my name is Neil Garb and I\'m a web developer.';
		}
		
		// keywords
		
		if ($this->view->metaKeywords === NULL)
		{
			$this->view->metaKeywords = array(
				'codecaine',
				'codecaine.co.za',
				'neil garb',
				'php',
				'php blog',
				'web developer',
				'web development',
				'blog',
				'cape town',
				'south africa'		
			);
		}
	}
	
	protected function _title ($title)
	{
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$viewRenderer->view->headTitle('Codecaine.co.za / ' . $title);
		
		$this->view->title = $title;
	}
}