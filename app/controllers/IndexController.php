<?php

class IndexController extends App_Controller_Action 
{
	public function aboutAction ()
	{
		// description
		
		$this->view->metaDescription = 'Read more about me and why I blog.';
		
		// title
		
		$this->_title('About me');	
	}

	public function spamAction ()
	{	
		// description
		
		$this->view->metaDescription = 'Your comment has been found to be spammy.';

		$this->_title('SPAM');
	}
	
	public function sitemapAction ()
	{
		header('Content-Type: application/xhtml+xml');

		$urls = array(
			'/' => mktime(0, 0, 0, date('m'), date('d'), date('Y')),
			'/about' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))		
		);
		
		$postManager = new Post();
		$posts = $postManager->fetchAll(
			array('is_active = ?' => 1),
			'posted_at DESC'
		);
		
		foreach ($posts as $post)
		{
			$urls['/posts/' . $post->label] = strtotime($post->posted_at);
		}
		
		$tagManager = new Tag();
		$tags = $tagManager->fetchAll(
			null, 'title'
		);
		
		foreach ($tags as $tag)
		{
			$urls['/tags/' . $tag->label] = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		}
		
		echo
			'<' . '?xml version="1.0"?' . '>' . "\n" .
			'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

		foreach ($urls as $u => $t)
		{
			echo
				"\t" . '<url>' . "\n" .
				"\t\t" . '<loc>http://codecaine.co.za' . $u . '</loc>' . "\n" .
				"\t\t" . '<lastMod>' . date('Y-m-d', $t) . 'T' . date('H:i:s', $t) . '+2:00</lastMod>' . "\n" .
				"\t" . '</url>' . "\n";
		}			
				
		echo
			'</urlset>';
			
		die;

	}
}
