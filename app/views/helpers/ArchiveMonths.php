<?php

class Zend_View_Helper_ArchiveMonths
{
	public function archiveMonths ()
	{
		$postManager = new Post();
		
		$sql = "SELECT DISTINCT DATE_FORMAT(posted_at, '%Y') AS year, DATE_FORMAT(posted_at, '%m') AS month FROM posts WHERE is_active = 1 ORDER BY year DESC, month DESC";		
		$res = $postManager->getAdapter()->query($sql)->fetchAll();
		
		return $res;
	}
}