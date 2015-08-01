<?php

class PostsController extends App_Controller_Action 
{
	protected $_commentForm = array(
		'method' => 'post',
		'elements' => array(
			'name' => array(
				'type' => 'text',				
				'options' => array(
					'label' => 'Name (required)',
					'required' => true,
					'validators' => array(
						'notEmpty' => array(
							'validator' => 'notEmpty',
							'options' => array(
								'messages' => array(
									'isEmpty' => 'Please fill in your name'
								)
							)
						)
					)		
				)
			),
			'email' => array(
				'type' => 'text',				
				'options' => array(
					'label' => 'Email address (required)',
					'required' => true,
					'validators' => array(
						'notEmpty' => array(
							'validator' => 'notEmpty',
							'breakChainOnFailure' => true,
							'options' => array(
								'messages' => array(
									'isEmpty' => 'Please fill in your email address'
								)
							)
						),
						'emailAddress' => array(
							'validator' => 'emailAddress',
							'options' => array(
								'messages' => array(
									'emailAddressInvalid' => 'Please fill in a valid email address'
								)
							)						
						)					
					)				
				)			
			),
			'website' => array(
				'type' => 'text',
				'options' => array(
					'label' => 'Website',
					'validators' => array(
						'regex' => array(
							'validator' => 'regex',							
							'options' => array(
								'pattern' => '/^((https?):\/\/)(([A-z0-9_-]+\.)*)(([A-z0-9-]{2,})\.)([A-z]{2,})((\/[a-z0-9-_.%~]*)*)?(\?[^? ]*)?$/',
								'messages' => array(
									'regexNotMatch' => 'Please fill in a valid URL'
								)
							)						
						)					
					)				
				)			
			),
			'text' => array(
				'type' => 'textarea',
				'options' => array(
					'label' => 'Comment (required)',
					'required' => true,
					'rows' => '8',
					'cols' => '65',
					'validators' => array(
						'notEmpty' => array(
							'validator' => 'notEmpty',
							'options' => array(
								'messages' => array(
									'isEmpty' => 'Please fill in your comment'
								)
							)						
						)
					)				
				)			
			),
			'submit' => array(
				'type' => 'submit',
				'options' => array(
					'label' => 'Submit'
				)			
			)
		)
	);	
	
	public function indexAction ()
	{			
		$page = array_key_exists('p', $_GET) ? (int) $_GET['p'] : 1;
		$page = $page < 1 ? 1 : $page;
		$this->view->page = $page;
		
		$postManager = new Post();
		
		// get post count
		
		$postCount = $postManager->fetchAll(array('is_active = ?' => 1))->count();
		$this->view->pageCount = ceil($postCount / 10);
		
		// get 10 posts
		
		$this->view->posts = $postManager->fetchAll(
			array('is_active = ?' => 1),
			'posted_at DESC',
			10,
			($page - 1) * 10
		);
		
		$this->view->paginationBase = '/';
		
		// description
		
		$this->view->metaDescription = 'Read my most recent blog posts.';
		
		// set title

		$title = 'Recent Posts';
		if ($page > 1)
		{
			$title .= ' / Page ' . $page;
		}
		
		$this->_title($title);
		
		$this->_forward('listing');
	}
	
	public function listingAction ()
	{
		
	}
	
	public function viewAction ()
	{
		$label = $this->_getParam('label');
		if ($label === null)
		{
			throw new Zend_Exception('No label specified in PostsController::viewAction()');
		}
		
		$postManager = new Post();
		$post = $postManager->fetchRow(array(
			'label = ?' => $label,
			'is_active = ?' => 1		
		));
		
		if ($post === null)
		{
			throw new Zend_Exception('No post found with that label in PostsController::viewAction()');
		}
		
		$this->view->post = $post;
		
		// get comments
		
		$commentManager = new Comment();
		$this->view->comments = $commentManager->fetchAll(
			array('post_id = ?' => $post->id),
			'posted_at'		
		);
		
		// form
				
		$form = new Zend_Form($this->_commentForm);
		$form->setAction('/posts/'.$post->label.'/comment');		
		$this->view->form = $form;
		
		// description
		
		$this->view->metaDescription = $post->title . '. ' . substr(strip_tags($post->blurb), 0, 100);
		
		// keywords 
		
		foreach ($post->findTagViaPostTag() as $tag)
		{
			$this->view->metaKeywords[] = $tag->title;
		}
		
		// set title
		
		$this->_title($post->title);

		// get 5 most recent posts that aren't this one

		$this->view->recentPosts  = $postManager->fetchAll(
			array(
				'is_active = ?' => 1,
				'id != ?' => $post->id
			),
			'posted_at DESC',
			5
		);
	}
	
	public function commentAction ()
	{
		$label = $this->_getParam('label');
		if ($label === null)
		{
			throw new Zend_Exception('No label specified in PostsController::commentAction()');
		}
		
		$postManager = new Post();
		$post = $postManager->fetchRow(array(
			'label = ?' => $label,
			'is_active = ?' => 1		
		));
		
		if ($post === null)
		{
			throw new Zend_Exception('No post found with that label in PostsController::commentAction()');
		}
		
		$this->view->post = $post;
		
		// form
		
		$form = new Zend_Form($this->_commentForm);
		$form->setAction('/posts/'.$post->label.'/comment');
		$this->view->form = $form;
		
		if ($this->getRequest()->isPost())
		{
			if ($form->isValid($_POST))
			{
				$config = Zend_Registry::get('config');
				$akismet = new Zend_Service_Akismet($config->akismet->key, 'http://codecaine.co.za');

				$akismetData = array(
					'user_ip' => $_SERVER['REMOTE_ADDR'],
					'user_agent' => $_SERVER['HTTP_USER_AGENT'],
					'referrer' => $_SERVER['HTTP_REFERER'],
					'permalink' => 'http://codecaine.co.za/posts/'.$post->label,
					'comment_type' => 'comment',
					'comment_author' => $_POST['name'],
					'comment_author_email' => $_POST['email'],
					'comment_author_url' => $_POST['website'],
					'comment_content' => $_POST['text']
				);

				if ($akismet->isSpam($akismetData))
				{
					$this->_redirect('/spam');
				}				

				$commentManager = new Comment();
				
				$data = $_POST;
				unset($data['submit']);
				$data['post_id'] = $post->id;
				$data['posted_at'] = new Zend_Db_Expr('NOW()');
				
				$id = $commentManager->insert($data);
				
				$this->_redirect('/posts/'.$post->label.'#comment-'.$id);
			}
		}
		
		// description
		
		$this->view->metaDescription = 'Post your comments on '.$post->title;

		// set title
		
		$this->_title($post->title);
	}
	
	public function archiveAction ()
	{
		$year = $this->_getParam('year');
		$month = $this->_getParam('month');
		
		if ($year === null || $month === null)
		{
			throw new Zend_Exception('Year null or month null in PostsController::archiveAction()');
		}
		
		$page = array_key_exists('p', $_GET) ? (int) $_GET['p'] : 1;
		$page = $page < 1 ? 1 : $page;
		$this->view->page = $page;

		// find posts 
			
		$postManager = new Post();
		
		$postCount = $postManager->fetchAll(array(
			'DATE_FORMAT(posted_at, \'%Y-%m\') = ?' => $year . '-' . $month,
			'is_active = ?' => 1
		));
		
		$this->view->pageCount = ceil($postCount / 10);
		
		$posts = $postManager->fetchAll(
			array(
				'DATE_FORMAT(posted_at, \'%Y-%m\') = ?' => $year . '-' . $month,
				'is_active = ?' => 1
			),
			'posted_at DESC',
			10,
			($page - 1) * 10		
		);
		
		$this->view->posts = $posts;
		
		$this->view->paginationBase = '/posts/' . $year.'/' . $month;
		
		$date = new Zend_Date($year . '-01-' . $month);
		
		// description
		
		$this->view->metaDescription = 'Read all my posts from ' . $date->toString('MMMM') . ' ' . $year . '.';

		// set title
				
		$this->_title('Posts from '.$date->toString('MMMM') . ' ' . $year);
		
		$this->_forward('listing');
	}
	
	public function rssAction ()
	{
		header('Content-Type: application/xhtml+xml');
		
		// get 10 most recent posts
		
		$postManager = new Post();
		$posts = $postManager->fetchAll(
			array('is_active = ?' => 1),
			'posted_at DESC',
			10		
		);
		
		echo
			'<' . '?xml version="1.0"?' . '>' . "\n" .
			'<rss version="2.0">' . "\n" .
			"\t" . '<channel>' . "\n" .
			"\t\t" . '<title>Codecaine.co.za</title>' . "\n" . 
			"\t\t" . '<link>http://www.codecaine.co.za/</link>' . "\n" . 
			"\t\t" . '<description>Hi, my name is Neil Garb and I\'m a web developer.</description>' . "\n" .
			"\t\t" . '<language>en-us</language>' . "\n" .
			"\t\t" . '<pubDate>' . new Zend_Date() . '</pubDate>' . "\n" .
			"\t\t" . '<lastBuildDate>' . new Zend_Date() . '</lastBuildDate>' . "\n";
			
		foreach ($posts as $post)
		{
			echo
				"\t\t" . '<item>' . "\n" .
				"\t\t\t" . '<title><![CDATA[' . $post->title . ']]></title>' . "\n" .
				"\t\t\t" . '<link>http://www.codecaine.co.za/posts/' . $post->label . '</link>' . "\n" .
				"\t\t\t" . '<description><![CDATA[' . $post->blurb . ']]></description>' . "\n" .
				"\t\t\t" . '<pubDate>' . new Zend_Date($post->posted_at, Zend_Date::ISO_8601) . '</pubDate>' . "\n" .
				"\t\t" . '</item>' . "\n";
		}			
			
		echo
			"\t" . '</channel>' . "\n" .
			'</rss>';
			
		die;
	}
	
	public function commentrssAction ()
	{
		$label = $this->_getParam('label');
		if ($label === null)
		{
			throw new Zend_Exception('No label specified in PostsController::commentRssAction()');
		}
		
		$postManager = new Post();
		$post = $postManager->fetchRow(array(
			'label = ?' => $label,
			'is_active = ?' => 1		
		));
		
		if ($post === null)
		{
			throw new Zend_Exception('No post found with that label in PostsController::commentRssAction()');
		}	
		
		// get 10 most recent comments
		
		$commentManager = new Comment();
		
		$comments = $commentManager->fetchAll(
			array('post_id = ?' => $post->id),
			'posted_at DESC', 
			10		
		);

		header('Content-Type: application/xhtml+xml');

		echo
			'<' . '?xml version="1.0"?' . '>' . "\n" .
			'<rss version="2.0">' . "\n" .
			"\t" . '<channel>' . "\n" .
			"\t\t" . '<title><![CDATA[Codecaine.co.za - ' . $post->title . ']]></title>' . "\n" . 
			"\t\t" . '<link>http://www.codecaine.co.za/posts/' . $post->label . '</link>' . "\n" . 
			"\t\t" . '<description><![CDATA[' . $post->blurb . ']]></description>' . "\n" .
			"\t\t" . '<language>en-us</language>' . "\n" .
			"\t\t" . '<pubDate>' . new Zend_Date() . '</pubDate>' . "\n" .
			"\t\t" . '<lastBuildDate>' . new Zend_Date() . '</lastBuildDate>' . "\n";
			
		foreach ($comments as $comment)
		{
			echo
				"\t\t" . '<item>' . "\n" .
				"\t\t\t" . '<title><![CDATA[' . $comment->name . ']]></title>' . "\n" .
				"\t\t\t" . '<link>http://www.codecaine.co.za/posts/' . $post->label . '/#comment-' . $comment->id . '</link>' . "\n" .
				"\t\t\t" . '<description><![CDATA[' . $comment->text . ']]></description>' . "\n" .
				"\t\t\t" . '<pubDate>' . new Zend_Date($comment->posted_at, Zend_Date::ISO_8601) . '</pubDate>' . "\n" .
				"\t\t" . '</item>' . "\n";
		}			
			
		echo
			"\t" . '</channel>' . "\n" .
			'</rss>';
			
		die;
	}
}
