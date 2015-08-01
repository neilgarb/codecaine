<?php

class App_Controller_Router extends Zend_Controller_Router_Rewrite
{
	function __construct ()
	{
		parent::__construct();
		
		// index
		
		$this->addRoute(
			'home',
			new Zend_Controller_Router_Route(
				'',
				array(
					'controller' => 'posts',
					'action' => 'index'				
				)			
			)
		);
		
		// about
		
		$this->addRoute(
			'about',
			new Zend_Controller_Router_Route(
				'about',
				array(
					'controller' => 'index',
					'action' => 'about'				
				)			
			)
		);

		// spam

		$this->addRoute(
			'spam',
			new Zend_Controller_Router_Route(
				'spam',
				array(
					'controller' => 'index',
					'action' => 'spam'
				)
			)
		);
		
		// sitemap
		
		$this->addRoute(
			'sitemap',
			new Zend_Controller_Router_Route(
				'sitemap.xml',
				array(
					'controller' => 'index',
					'action' => 'sitemap'				
				)			
			)
		);
		
		// post > archive
		
		$this->addRoute(
			'post_archive_year',
			new Zend_Controller_Router_Route(
				'posts/:year/:month',
				array(
					'controller' => 'posts',
					'action' => 'archive'				
				),
				array(
					'year' => '\d{4,4}',
					'month' => '[01][0-9]'	
				)
			)		
		);

		// post > view
		
		$this->addRoute(
			'post',
			new Zend_Controller_Router_Route(
				'posts/:label',
				array(
					'controller' => 'posts',
					'action' => 'view'				
				)			
			)		
		);
		
		// post > comment
		
		$this->addRoute(
			'post_comment',
			new Zend_Controller_Router_Route(
				'posts/:label/comment',
				array(
					'controller' => 'posts',
					'action' => 'comment'				
				)			
			)		
		);
		
		// rss
		
		$this->addRoute(
			'rss',
			new Zend_Controller_Router_Route(
				'rss',
				array(
					'controller' => 'posts',
					'action' => 'rss'				
				)			
			)
		);
		
		// post > rss
		
		$this->addRoute(
			'post_rss',
			new Zend_Controller_Router_Route(
				'posts/:label/rss',
				array(
					'controller' => 'posts',
					'action' => 'commentrss'				
				)			
			)
		);
		
		// tag > view
		
		$this->addRoute(
			'tag',
			new Zend_Controller_Router_Route(
				'tags/:label',
				array(
					'controller' => 'tags',
					'action' => 'view'
				)			
			)		
		);
	}
}
